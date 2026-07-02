<?php

namespace App\Observers;

use App\Models\EnhancementActivity;
use App\Services\AuditService;
use App\Services\NotificationService;

class EnhancementActivityObserver
{
    public function __construct(
        protected NotificationService $notifications,
        protected AuditService $audit,
    ) {}

    public function created(EnhancementActivity $activity): void
    {
        $dueText = $activity->due_date ? $activity->due_date->format('F j, Y') : 'No due date';

        $this->notifications->send(
            $activity->student,
            'activity_assigned',
            'Activity Assigned',
            "A new activity \"{$activity->title}\" has been assigned to you. Due: {$dueText}.",
            ['url' => route('student.activities.index', absolute: false)],
        );

        $this->audit->log(
            actor: $activity->adviser,
            action: 'activity_assigned',
            targetType: 'enhancement_activity',
            targetId: $activity->id,
            new: $activity->getChanges(),
        );
    }

    public function updated(EnhancementActivity $activity): void
    {
        if ($activity->wasChanged('status') && $activity->status->value === 'submitted') {
            $this->notifications->send(
                $activity->adviser,
                'activity_submitted',
                'Activity Submitted',
                "{$activity->student->name} has submitted \"{$activity->title}\" for review.",
                ['url' => route('adviser.activities.index', absolute: false)],
            );

            $this->audit->log(
                actor: $activity->student,
                action: 'activity_submitted',
                targetType: 'enhancement_activity',
                targetId: $activity->id,
                old: $activity->getOriginal(),
                new: $activity->getChanges(),
            );
        }
    }
}
