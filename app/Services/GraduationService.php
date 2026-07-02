<?php

namespace App\Services;

use App\Enums\EnrollmentSubjectStatus;
use App\Enums\GraduationClearanceStatus;
use App\Models\ClearanceDocument;
use App\Models\GraduationClearance;
use App\Models\User;
use App\Support\ClearanceDocumentTypeRegistry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class GraduationService
{
    /**
     * Get students eligible for graduation clearance:
     * Users with role 'student', no existing GraduationClearance,
     * whose every enrollment_subject status is Completed/Credited/Transferred.
     *
     * @return Collection<int, User>
     */
    public function eligibleStudents(): Collection
    {
        $completeStatuses = [
            EnrollmentSubjectStatus::Completed->value,
            EnrollmentSubjectStatus::Credited->value,
            EnrollmentSubjectStatus::Transferred->value,
        ];

        return User::query()
            ->whereHas('roles', fn ($q) => $q->where('name', 'student'))
            ->whereDoesntHave('graduationClearance')
            ->whereHas('enrollments.subjects', fn ($q) => $q->whereIn('status', $completeStatuses))
            ->whereDoesntHave('enrollments.subjects', fn ($q) => $q->whereNotIn('status', $completeStatuses))
            ->get();
    }

    /**
     * Generate the next sequential registry number for the current year.
     */
    public function generateRegistryNumber(): string
    {
        $year = now()->year;
        $format = (string) config('eteeap.registry_number_format', 'RGN-{YEAR}-{NUMBER}');

        $pattern = str_replace(
            ['{YEAR}', '{NUMBER}'],
            [(string) $year, '%'],
            $format,
        );

        $count = GraduationClearance::query()
            ->where('registry_number', 'like', $pattern)
            ->count();

        return str_replace(
            ['{YEAR}', '{NUMBER}'],
            [(string) $year, sprintf('%04d', $count + 1)],
            $format,
        );
    }

    /**
     * Initiate a graduation clearance for a student.
     * Creates the clearance (status=pending) and seeds all clearance document rows.
     *
     * @throws ValidationException
     */
    public function initiate(User $student, User $coordinator): GraduationClearance
    {
        if (! $student->hasRole('student')) {
            throw ValidationException::withMessages(['student_id' => 'User does not have the student role.']);
        }

        if ($student->graduationClearance()->exists()) {
            throw ValidationException::withMessages(['student_id' => 'Student already has a graduation clearance.']);
        }

        $eligible = $this->eligibleStudents()->pluck('id')->contains($student->id);

        if (! $eligible) {
            throw ValidationException::withMessages(['student_id' => 'Student is not eligible for graduation clearance. All enrollment subjects must be completed, credited, or transferred.']);
        }

        $clearance = GraduationClearance::create([
            'student_id' => $student->id,
            'status' => GraduationClearanceStatus::Pending,
        ]);

        $documentTypes = ClearanceDocumentTypeRegistry::all();

        foreach ($documentTypes as $docType) {
            $clearance->documents()->create([
                'document_type' => $docType['key'],
                'is_verified' => false,
            ]);
        }

        return $clearance->fresh()->load('documents', 'student');
    }

    /**
     * Verify a clearance document.
     *
     * @throws ValidationException
     */
    public function verifyDocument(ClearanceDocument $document, User $verifier, ?UploadedFile $file = null): ClearanceDocument
    {
        if ($file) {
            $path = $file->store(
                sprintf('graduation/%d/clearance/%s', $document->clearance->student_id, $document->document_type),
                config('eteeap.document.storage_disk', 'local'),
            );

            $document->update(['file_path' => $path]);
        }

        $document->update([
            'is_verified' => true,
            'verified_by' => $verifier->id,
            'verified_at' => now(),
        ]);

        return $document->fresh()->load('verifier');
    }

    /**
     * Confirm a clearance (marks it in_review).
     * Requires all 12 documents to be verified.
     *
     * @throws ValidationException
     */
    public function confirm(GraduationClearance $clearance, User $coordinator): GraduationClearance
    {
        $documents = $clearance->documents()->get();
        $totalDocs = $documents->count();
        $verifiedCount = $documents->where('is_verified', true)->count();

        if ($verifiedCount < $totalDocs) {
            throw ValidationException::withMessages(['clearance' => sprintf(
                'All %d clearance documents must be verified before confirmation (only %d verified).',
                $totalDocs,
                $verifiedCount,
            )]);
        }

        $pdf = Pdf::loadView('pdf.confirmation-letter', [
            'studentName' => $clearance->student->name,
            'studentEmail' => $clearance->student->email,
            'date' => now()->format('F d, Y'),
        ]);

        $path = sprintf(
            'graduation/%d/clearance/confirmation-letter.pdf',
            $clearance->student_id,
        );

        Storage::disk(config('eteeap.document.storage_disk', 'local'))->put($path, $pdf->output());

        $clearance->update([
            'confirmation_letter_path' => $path,
            'status' => GraduationClearanceStatus::InReview,
        ]);

        return $clearance->fresh()->load('documents', 'student');
    }

    /**
     * VPAA approves a clearance.
     * Sets registry number, vpaa_approved_at, status=approved.
     * Also generates diploma and TOR PDFs immediately.
     *
     * @throws ValidationException
     */
    public function approve(GraduationClearance $clearance, User $vpaa): GraduationClearance
    {
        if ($clearance->status !== GraduationClearanceStatus::InReview) {
            throw ValidationException::withMessages(['clearance' => 'Only clearances in review can be approved.']);
        }

        $clearance->update([
            'registry_number' => $this->generateRegistryNumber(),
            'vpaa_approved_at' => now(),
            'status' => GraduationClearanceStatus::Approved,
        ]);

        $this->generateDiploma($clearance);
        $this->generateTor($clearance);

        return $clearance->fresh()->load('documents', 'student');
    }

    /**
     * Generate the diploma PDF.
     */
    public function generateDiploma(GraduationClearance $clearance): GraduationClearance
    {
        $pdf = Pdf::loadView('pdf.diploma', [
            'studentName' => $clearance->student->name,
            'registryNumber' => $clearance->registry_number,
            'date' => now()->format('F d, Y'),
        ]);

        $path = sprintf(
            'graduation/%d/clearance/diploma.pdf',
            $clearance->student_id,
        );

        Storage::disk(config('eteeap.document.storage_disk', 'local'))->put($path, $pdf->output());

        $clearance->update([
            'diploma_printed_at' => now(),
        ]);

        return $clearance;
    }

    /**
     * Generate the Transcript of Records (TOR) PDF.
     */
    public function generateTor(GraduationClearance $clearance): GraduationClearance
    {
        $student = $clearance->student;

        $checklist = app(EnrollmentService::class)->checklistFor($student);

        $subjectRows = $checklist->map(function ($item) {
            return [
                'code' => $item['code'],
                'title' => $item['title'],
                'units' => $item['units'],
                'grade' => $item['grade']['grade'] ?? '-',
                'status' => $item['status'],
            ];
        });

        $totalUnits = $checklist->sum('units');

        $pdf = Pdf::loadView('pdf.tor', [
            'studentName' => $student->name,
            'registryNumber' => $clearance->registry_number,
            'date' => now()->format('F d, Y'),
            'subjects' => $subjectRows,
            'totalUnits' => $totalUnits,
        ]);

        $path = sprintf(
            'graduation/%d/clearance/tor.pdf',
            $clearance->student_id,
        );

        Storage::disk(config('eteeap.document.storage_disk', 'local'))->put($path, $pdf->output());

        return $clearance;
    }

    /**
     * Submit the signed clearance form.
     *
     * @throws ValidationException
     */
    public function submitClearanceForm(GraduationClearance $clearance, UploadedFile $file): GraduationClearance
    {
        if ($clearance->status !== GraduationClearanceStatus::Approved) {
            throw ValidationException::withMessages(['clearance' => 'Clearance must be approved before submitting the clearance form.']);
        }

        $path = $file->store(
            sprintf('graduation/%d/clearance', $clearance->student_id),
            config('eteeap.document.storage_disk', 'local'),
        );

        $clearance->update([
            'clearance_form_path' => $path,
        ]);

        return $clearance->fresh();
    }

    /**
     * Release the TOR (mark clearance as completed).
     *
     * @throws ValidationException
     */
    public function releaseTor(GraduationClearance $clearance): GraduationClearance
    {
        if (blank($clearance->clearance_form_path)) {
            throw ValidationException::withMessages(['clearance' => 'Clearance form must be submitted before TOR can be released.']);
        }

        $clearance->update([
            'tor_released_at' => now(),
            'status' => GraduationClearanceStatus::Completed,
        ]);

        return $clearance->fresh();
    }
}
