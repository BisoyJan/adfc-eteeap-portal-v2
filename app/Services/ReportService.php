<?php

namespace App\Services;

use App\Enums\ActivityStatus;
use App\Enums\ApplicationStatus;
use App\Enums\EnrollmentSubjectStatus;
use App\Enums\GraduationClearanceStatus;
use App\Enums\GradeStatus;
use App\Models\AdviserAssignment;
use App\Models\Application;
use App\Models\CurriculumSubject;
use App\Models\Document;
use App\Models\EnhancementActivity;
use App\Models\EnrollmentSubject;
use App\Models\Grade;
use App\Models\GraduationClearance;
use App\Models\Interview;
use App\Models\User;
use App\Enums\DocumentStatus;

class ReportService
{
    /**
     * @return array{headers: string[], rows: array<int, array<int, string>>}
     */
    public function pipelineReport(): array
    {
        $headers = ['Status', 'Count'];

        $rows = collect(ApplicationStatus::cases())
            ->map(fn ($status) => [
                $status->label(),
                (string) Application::where('status', $status)->count(),
            ])
            ->values()
            ->toArray();

        $rows[] = ['Total', (string) Application::count()];

        return compact('headers', 'rows');
    }

    /**
     * @return array{headers: string[], rows: array<int, array<int, string>>}
     */
    public function equivalencyDistributionReport(): array
    {
        $headers = ['Subject Code', 'Subject Title', 'Competency Level', 'Equivalent Credits', 'Count'];

        $rows = CurriculumSubject::where('is_active', true)
            ->limit(50)
            ->get()
            ->map(fn ($subject) => [
                $subject->code,
                $subject->title,
                $subject->category->label(),
                (string) $subject->units,
                (string) EnrollmentSubject::where('curriculum_subject_id', $subject->id)
                    ->where('status', EnrollmentSubjectStatus::Credited)
                    ->count(),
            ])
            ->values()
            ->toArray();

        return compact('headers', 'rows');
    }

    /**
     * @return array{headers: string[], rows: array<int, array<int, string>>}
     */
    public function adviserLoadReport(): array
    {
        $headers = ['Adviser Name', 'Email', 'Active Students', 'Max Load', 'Status'];

        $maxLoad = (int) config('eteeap.adviser_max_students', 3);

        $rows = User::whereHas('roles', fn ($q) => $q->where('name', 'adviser'))
            ->withCount(['adviserAssignments' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->map(fn ($adviser) => [
                $adviser->name,
                $adviser->email,
                (string) $adviser->adviser_assignments_count,
                (string) $maxLoad,
                $adviser->adviser_assignments_count > $maxLoad ? 'Overloaded' : 'OK',
            ])
            ->values()
            ->toArray();

        return compact('headers', 'rows');
    }

    /**
     * @return array{headers: string[], rows: array<int, array<int, string>>}
     */
    public function enhancementProgressReport(): array
    {
        $headers = ['Student', 'Activity', 'Type', 'Status', 'Score', 'Due Date'];

        $rows = EnhancementActivity::with('student', 'curriculumSubject')
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn ($activity) => [
                $activity->student->name ?? 'N/A',
                $activity->title,
                $activity->activity_type->label(),
                $activity->status->label(),
                (string) ($activity->score ?? 'N/A'),
                $activity->due_date?->format('Y-m-d') ?? 'N/A',
            ])
            ->values()
            ->toArray();

        return compact('headers', 'rows');
    }

    /**
     * @return array{headers: string[], rows: array<int, array<int, string>>}
     */
    public function graduationAuditReport(): array
    {
        $headers = ['Student', 'Registry Number', 'Status', 'VPAA Approved At', 'Diploma Printed', 'TOR Released'];

        $rows = GraduationClearance::with('student')
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn ($c) => [
                $c->student->name ?? 'N/A',
                $c->registry_number ?? '—',
                $c->status->label(),
                $c->vpaa_approved_at?->format('Y-m-d') ?? '—',
                $c->diploma_printed_at?->format('Y-m-d') ?? '—',
                $c->tor_released_at?->format('Y-m-d') ?? '—',
            ])
            ->values()
            ->toArray();

        return compact('headers', 'rows');
    }

    /**
     * @return array{headers: string[], rows: array<int, array<int, string>>}
     */
    public function timelineAnalysisReport(): array
    {
        $headers = ['Application #', 'Applicant', 'Submitted', 'Approved', 'Days to Approval'];

        $rows = Application::where('status', ApplicationStatus::Approved)
            ->whereNotNull('submitted_at')
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn ($app) => [
                $app->application_number,
                $app->user->name ?? 'N/A',
                $app->submitted_at?->format('Y-m-d') ?? '—',
                $app->updated_at->format('Y-m-d'),
                (string) ($app->submitted_at?->diffInDays($app->updated_at) ?? 'N/A'),
            ])
            ->values()
            ->toArray();

        return compact('headers', 'rows');
    }

    /**
     * @return array{headers: string[], rows: array<int, array<int, string>>}
     */
    public function chedComplianceReport(): array
    {
        $headers = ['Metric', 'Value'];

        $totalApps = Application::count();
        $approvedApps = Application::where('status', ApplicationStatus::Approved)->count();
        $passRate = $totalApps > 0 ? round(($approvedApps / $totalApps) * 100, 1) . '%' : 'N/A';

        $totalInterviews = Interview::count();
        $compliantPanels = Interview::whereHas('panelists', function ($q) {
            $q->groupBy('interview_id')->havingRaw('COUNT(*) >= ?', [3]);
        })->count();
        $panelCompliance = $totalInterviews > 0 ? round(($compliantPanels / $totalInterviews) * 100, 1) . '%' : 'N/A';

        $allDocs = Document::count();
        $verifiedDocs = Document::where('status', DocumentStatus::Verified)->count();
        $docCompleteness = $allDocs > 0 ? round(($verifiedDocs / $allDocs) * 100, 1) . '%' : 'N/A';

        $avgDays = Application::whereNotNull('submitted_at')
            ->where('status', ApplicationStatus::Approved)
            ->selectRaw('AVG(DATEDIFF(updated_at, submitted_at)) as avg_days')
            ->value('avg_days');

        $graduateCount = GraduationClearance::where('status', GraduationClearanceStatus::Completed)->count();

        $rows = [
            ['Qualification Pass Rate', $passRate],
            ['Total Applications', (string) $totalApps],
            ['Approved Applications', (string) $approvedApps],
            ['Panel Composition Compliance (≥3)', $panelCompliance],
            ['Document Completeness Rate', $docCompleteness],
            ['Average Processing Time (days)', $avgDays ? round((float) $avgDays, 1) . ' days' : 'N/A'],
            ['Graduates', (string) $graduateCount],
        ];

        return compact('headers', 'rows');
    }
}
