<?php

namespace App\Observers;

use App\Models\Application;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Request;

class ApplicationObserver
{
    public function __construct(
        protected NotificationService $notifications,
        protected AuditService $audit,
    ) {}

    public function created(Application $application): void
    {
        if ($application->status->value === 'submitted') {
            $this->notifications->sendToRole(
                'coordinator',
                'application_submitted',
                'New Application Submitted',
                "Application {$application->application_number} has been submitted for review.",
            );

            $this->audit->log(
                actor: $application->user,
                action: 'application_submitted',
                targetType: 'application',
                targetId: $application->id,
                new: $application->getChanges(),
            );
        }
    }

    public function updated(Application $application): void
    {
        if (! $application->wasChanged('status')) {
            return;
        }

        $status = $application->status->value;

        if ($status === 'approved') {
            $this->notifications->send(
                $application->user,
                'application_approved',
                'Application Approved',
                "Your application {$application->application_number} has been approved. You may proceed to enrollment.",
                ['url' => route('student.dashboard', absolute: false)],
                email: true,
            );

            $this->audit->log(
                actor: Request::user(),
                action: 'application_approved',
                targetType: 'application',
                targetId: $application->id,
                old: $application->getOriginal(),
                new: $application->getChanges(),
            );
        } elseif ($status === 'rejected') {
            $this->notifications->send(
                $application->user,
                'application_rejected',
                'Application Rejected',
                "Your application {$application->application_number} has been rejected."
                    . ($application->rejection_reason ? " Reason: {$application->rejection_reason}" : ''),
                email: true,
            );

            $this->audit->log(
                actor: Request::user(),
                action: 'application_rejected',
                targetType: 'application',
                targetId: $application->id,
                old: $application->getOriginal(),
                new: $application->getChanges(),
            );
        } elseif ($status === 'revision_requested') {
            $this->notifications->send(
                $application->user,
                'application_revision',
                'Application Revision Requested',
                "Your application {$application->application_number} needs revisions."
                    . ($application->review_notes ? " Notes: {$application->review_notes}" : ''),
                ['url' => route('applicant.application.edit', absolute: false)],
            );

            $this->audit->log(
                actor: Request::user(),
                action: 'application_revision_requested',
                targetType: 'application',
                targetId: $application->id,
                old: $application->getOriginal(),
                new: $application->getChanges(),
            );
        }
    }
}
