<?php

namespace App\Observers;

use App\Models\AdviserAssignment;
use App\Services\AuditService;
use App\Services\NotificationService;

class AdviserAssignmentObserver
{
    public function __construct(
        protected NotificationService $notifications,
        protected AuditService $audit,
    ) {}

    public function created(AdviserAssignment $assignment): void
    {
        $this->notifications->send(
            $assignment->student,
            'adviser_assigned',
            'Adviser Assigned',
            "{$assignment->adviser->name} has been assigned as your adviser.",
        );

        $this->notifications->send(
            $assignment->adviser,
            'student_assigned',
            'Student Assigned',
            "{$assignment->student->name} has been assigned to you for advisement.",
            ['url' => route('adviser.dashboard', absolute: false)],
        );

        $this->audit->log(
            actor: $assignment->assigner,
            action: 'adviser_assigned',
            targetType: 'adviser_assignment',
            targetId: $assignment->id,
            new: $assignment->getChanges(),
        );
    }
}
