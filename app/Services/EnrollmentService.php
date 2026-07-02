<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\EnrollmentSubjectStatus;
use App\Enums\EquivalencyMatrixStatus;
use App\Enums\EquivalencyMatrixType;
use App\Enums\RecommendedAction;
use App\Models\Application;
use App\Models\CurriculumSubject;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class EnrollmentService
{
    /**
     * Generate the next sequential student ID for the current year.
     */
    public function generateStudentIdNumber(): string
    {
        $year = now()->year;
        $format = (string) config('eteeap.id_format', 'ETEEAP-{YEAR}-{NUMBER}');

        $pattern = str_replace(
            ['{YEAR}', '{NUMBER}'],
            [(string) $year, '%'],
            $format,
        );

        $count = Enrollment::query()
            ->where('student_id_number', 'like', $pattern)
            ->count();

        return str_replace(
            ['{YEAR}', '{NUMBER}'],
            [(string) $year, sprintf('%04d', $count + 1)],
            $format,
        );
    }

    /**
     * Get applications eligible for enrollment:
     * Approved status + an approved final EquivalencyMatrix, not yet enrolled.
     *
     * @return Collection<int, Application>
     */
    public function eligibleApplications(): Collection
    {
        return Application::query()
            ->where('status', ApplicationStatus::Approved)
            ->whereDoesntHave('enrollment')
            ->whereHas('equivalencyMatrices', function ($q) {
                $q->where('type', EquivalencyMatrixType::Final)
                    ->where('status', EquivalencyMatrixStatus::Approved);
            })
            ->with('user')
            ->get();
    }

    /**
     * Create an enrollment from an approved application, auto-encoding subjects
     * from the approved final equivalency matrix.
     *
     * @throws ValidationException
     */
    public function createFromApplication(Application $application, User $coordinator, ?string $term): Enrollment
    {
        if ($application->status !== ApplicationStatus::Approved) {
            throw ValidationException::withMessages(['application_id' => 'Application must be approved before enrollment.']);
        }

        if ($application->enrollment()->exists()) {
            throw ValidationException::withMessages(['application_id' => 'Application already has an enrollment.']);
        }

        $finalMatrix = $application->equivalencyMatrices()
            ->where('type', EquivalencyMatrixType::Final)
            ->where('status', EquivalencyMatrixStatus::Approved)
            ->with('items.curriculumSubject')
            ->first();

        if (! $finalMatrix) {
            throw ValidationException::withMessages(['application_id' => 'No approved final equivalency matrix found for this application.']);
        }

        $user = $application->user;

        $enrollment = Enrollment::create([
            'student_id' => $user->id,
            'application_id' => $application->id,
            'academic_term' => $term,
            'enrollment_date' => now(),
            'student_id_number' => $this->generateStudentIdNumber(),
            'status' => 'enrolled',
        ]);

        $studentRole = Role::where('name', 'student')->first();
        if ($studentRole) {
            $user->roles()->syncWithoutDetaching([$studentRole->id]);
        }

        $this->autoEncodeSubjects($enrollment, $finalMatrix->items);

        return $enrollment->fresh()->load('subjects.curriculumSubject');
    }

    /**
     * Auto-encode enrollment subjects from matrix items.
     *
     * - credit  → Credited
     * - enroll/enhance → capped at student_max_subjects as Enrolled, rest as Pending
     */
    /**
     * @param  Collection<int, \App\Models\EquivalencyMatrixItem>  $matrixItems
     */
    protected function autoEncodeSubjects(Enrollment $enrollment, Collection $matrixItems): void
    {
        $credited = [];
        $toEnroll = [];

        foreach ($matrixItems as $item) {
            $action = $item->recommended_action;

            if ($action === RecommendedAction::Credit) {
                $credited[] = [
                    'curriculum_subject_id' => $item->curriculum_subject_id,
                    'status' => EnrollmentSubjectStatus::Credited,
                ];
            } elseif (in_array($action, [RecommendedAction::Enroll, RecommendedAction::Enhance], true)) {
                $toEnroll[] = $item;
            }
        }

        // Create credited subjects
        foreach ($credited as $data) {
            if ($data['curriculum_subject_id']) {
                $enrollment->subjects()->create($data);
            }
        }

        // Order enroll/enhance items by year_level → semester → subject_code
        $sorted = collect($toEnroll)->sortBy(function ($item) {
            $subject = $item->curriculumSubject;
            if (! $subject) {
                return [999, 999, $item->subject_code ?? ''];
            }

            return [$subject->year_level ?? 999, $subject->semester ?? 999, $subject->code];
        })->values();

        $maxSubjects = (int) config('eteeap.student_max_subjects', 3);

        $sorted->each(function ($item, $index) use ($enrollment, $maxSubjects) {
            $subjectId = $item->curriculum_subject_id;
            if (! $subjectId) {
                return;
            }

            $status = $index < $maxSubjects
                ? EnrollmentSubjectStatus::Enrolled
                : EnrollmentSubjectStatus::Pending;

            $enrollment->subjects()->create([
                'curriculum_subject_id' => $subjectId,
                'status' => $status,
            ]);
        });
    }

    /**
     * Generate the curriculum checklist for a student.
     *
     * Full active CurriculumSubject list left-joined with the student's
     * enrollment_subjects and latest Grade per subject.
     */
    /**
     * @return Collection<int, array{id: int, code: string, title: string, units: float, category: string, year_level: int|null, semester: int|null, status: string, grade: array|null}>
     */
    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function checklistFor(User $student): Collection
    {
        $subjects = CurriculumSubject::query()
            ->where('is_active', true)
            ->orderBy('year_level')
            ->orderBy('semester')
            ->orderBy('code')
            ->get();

        /** @var \Illuminate\Support\Collection<int, \App\Models\EnrollmentSubject> $enrollmentSubjects */
        $enrollmentSubjects = $student->enrollments()
            ->with('subjects.curriculumSubject', 'subjects.enrollment.grades')
            ->get()
            ->pluck('subjects')
            ->flatten()
            ->keyBy('curriculum_subject_id');

        $items = [];

        foreach ($subjects as $subject) {
            /** @var \App\Models\EnrollmentSubject|null $enrollmentSubject */
            $enrollmentSubject = $enrollmentSubjects->get($subject->id);

            $grade = null;
            $status = 'not_taken';

            if ($enrollmentSubject) {
                $status = $enrollmentSubject->status->value;
                $latestGrade = $enrollmentSubject->enrollment->grades()
                    ->where('curriculum_subject_id', $subject->id)
                    ->latest()
                    ->first();

                if ($latestGrade) {
                    $grade = [
                        'id' => $latestGrade->id,
                        'grade' => $latestGrade->grade,
                        'grade_point' => $latestGrade->grade_point,
                        'status' => $latestGrade->status->value,
                    ];
                }
            }

            $items[] = [
                'id' => $subject->id,
                'code' => $subject->code,
                'title' => $subject->title,
                'units' => $subject->units,
                'category' => $subject->category->value,
                'year_level' => $subject->year_level,
                'semester' => $subject->semester,
                'status' => $status,
                'grade' => $grade,
            ];
        }

        /** @var \Illuminate\Support\Collection<int, array<string, mixed>> $collection */
        $collection = collect($items);

        return $collection;
    }
}
