<?php

namespace App\Services;

use App\Enums\EnrollmentSubjectStatus;
use App\Enums\GradeStatus;
use App\Models\EnrollmentSubject;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class GradeService
{
    /**
     * Create a draft grade for a student's enrolled subject.
     *
     * @param  array{grade?: string, grade_point?: float|null, rubric_id?: int|null, rubric_score?: float|null}  $data
     */
    public function store(int $enrollmentSubjectId, User $adviser, User $student, array $data): Grade
    {
        $enrollmentSubject = EnrollmentSubject::query()
            ->where('id', $enrollmentSubjectId)
            ->whereHas('enrollment', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->with('enrollment')
            ->firstOrFail();

        $grade = Grade::create([
            'student_id' => $student->id,
            'curriculum_subject_id' => $enrollmentSubject->curriculum_subject_id,
            'enrollment_id' => $enrollmentSubject->enrollment_id,
            'adviser_id' => $adviser->id,
            'grade' => $data['grade'] ?? null,
            'grade_point' => $data['grade_point'] ?? null,
            'rubric_id' => $data['rubric_id'] ?? null,
            'rubric_score' => $data['rubric_score'] ?? null,
            'status' => GradeStatus::Draft,
        ]);

        return $grade->fresh()->load('curriculumSubject', 'rubric');
    }

    /**
     * Update a draft grade.
     *
     * @param  array{grade?: string, grade_point?: float|null, rubric_id?: int|null, rubric_score?: float|null}  $data
     */
    public function update(Grade $grade, array $data): Grade
    {
        if ($grade->status !== GradeStatus::Draft) {
            throw ValidationException::withMessages(['grade' => 'Only draft grades can be updated.']);
        }

        $grade->update([
            'grade' => $data['grade'] ?? $grade->grade,
            'grade_point' => array_key_exists('grade_point', $data) ? $data['grade_point'] : $grade->grade_point,
            'rubric_id' => array_key_exists('rubric_id', $data) ? $data['rubric_id'] : $grade->rubric_id,
            'rubric_score' => array_key_exists('rubric_score', $data) ? $data['rubric_score'] : $grade->rubric_score,
        ]);

        return $grade->fresh()->load('curriculumSubject', 'rubric');
    }

    /**
     * Submit a grade (Draft → Submitted).
     *
     * Business rule: if grade is not CE/T, a rubric_id must be set.
     *
     * @throws ValidationException
     */
    public function submit(Grade $grade): Grade
    {
        if ($grade->status !== GradeStatus::Draft) {
            throw ValidationException::withMessages(['grade' => 'Only draft grades can be submitted.']);
        }

        $gradeValue = $grade->grade;

        if ($gradeValue !== 'CE' && $gradeValue !== 'T' && blank($grade->rubric_id)) {
            throw ValidationException::withMessages([
                'rubric_id' => 'A rubric is required when the grade is not "CE" or "T".',
            ]);
        }

        $grade->update([
            'status' => GradeStatus::Submitted,
        ]);

        return $grade->fresh();
    }

    /**
     * Approve a grade (Submitted → Approved).
     * Also flips the matching EnrollmentSubject status to Completed.
     *
     * @throws ValidationException
     */
    public function approve(Grade $grade, User $approver): Grade
    {
        if ($grade->status !== GradeStatus::Submitted) {
            throw ValidationException::withMessages(['grade' => 'Only submitted grades can be approved.']);
        }

        $grade->update([
            'status' => GradeStatus::Approved,
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        EnrollmentSubject::query()
            ->where('enrollment_id', $grade->enrollment_id)
            ->where('curriculum_subject_id', $grade->curriculum_subject_id)
            ->update(['status' => EnrollmentSubjectStatus::Completed]);

        return $grade->fresh();
    }
}
