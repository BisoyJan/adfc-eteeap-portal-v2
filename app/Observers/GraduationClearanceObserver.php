<?php

namespace App\Observers;

use App\Models\GraduationClearance;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Request;

class GraduationClearanceObserver
{
    public function __construct(
        protected NotificationService $notifications,
        protected AuditService $audit,
    ) {}

    public function updated(GraduationClearance $clearance): void
    {
        if (! $clearance->wasChanged('status')) {
            return;
        }

        $status = $clearance->status->value;

        if ($status === 'in_review') {
            $this->notifications->sendToRole(
                'vpaa',
                'clearance_in_review',
                'Clearance Ready for VPAA Review',
                "Graduation clearance for {$clearance->student->name} is ready for your review.",
                ['url' => route('vpaa.approvals.index', absolute: false)],
            );

            $this->audit->log(
                actor: Request::user(),
                action: 'clearance_in_review',
                targetType: 'graduation_clearance',
                targetId: $clearance->id,
                old: $clearance->getOriginal(),
                new: $clearance->getChanges(),
            );
        } elseif ($status === 'approved') {
            $this->notifications->send(
                $clearance->student,
                'graduation_approved',
                'Graduation Approved',
                "Congratulations! Your graduation has been approved."
                    . ($clearance->registry_number ? " Your RGN is {$clearance->registry_number}." : ''),
                email: true,
            );

            $this->audit->log(
                actor: Request::user(),
                action: 'clearance_approved',
                targetType: 'graduation_clearance',
                targetId: $clearance->id,
                old: $clearance->getOriginal(),
                new: $clearance->getChanges(),
            );
        } elseif ($status === 'completed') {
            $this->notifications->send(
                $clearance->student,
                'clearance_completed',
                'Clearance Complete',
                'Your clearance is complete. Your TOR has been released.',
                ['url' => route('student.graduation.show', absolute: false)],
                email: true,
            );

            $this->audit->log(
                actor: Request::user(),
                action: 'clearance_completed',
                targetType: 'graduation_clearance',
                targetId: $clearance->id,
                old: $clearance->getOriginal(),
                new: $clearance->getChanges(),
            );
        }
    }
}
