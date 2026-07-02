<?php

namespace App\Observers;

use App\Models\Interview;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Request;

class InterviewObserver
{
    public function __construct(
        protected NotificationService $notifications,
        protected AuditService $audit,
    ) {}

    public function created(Interview $interview): void
    {
        if ($interview->scheduled_at) {
            $this->notifySchedule($interview);
        }

        $this->audit->log(
            actor: Request::user(),
            action: 'interview_scheduled',
            targetType: 'interview',
            targetId: $interview->id,
            new: $interview->getChanges(),
        );
    }

    public function updated(Interview $interview): void
    {
        if ($interview->wasChanged('scheduled_at')) {
            $this->notifySchedule($interview);
        }

        if ($interview->wasChanged()) {
            $this->audit->log(
                actor: Request::user(),
                action: 'interview_updated',
                targetType: 'interview',
                targetId: $interview->id,
                old: $interview->getOriginal(),
                new: $interview->getChanges(),
            );
        }
    }

    protected function notifySchedule(Interview $interview): void
    {
        $formattedDate = $interview->scheduled_at->format('F j, Y g:i A');

        $this->notifications->send(
            $interview->application->user,
            'interview_scheduled',
            'Interview Scheduled',
            "Your interview has been scheduled on {$formattedDate} at {$interview->location}.",
            ['url' => route('student.dashboard', absolute: false)],
            email: true,
        );

        foreach ($interview->panelists as $panelist) {
            $this->notifications->send(
                $panelist,
                'interview_scheduled_panelist',
                'Interview Assignment',
                "You have been assigned as a panelist for an interview scheduled on {$formattedDate}.",
                ['url' => route('assessor.interviews.show', $interview->id, absolute: false)],
                email: true,
            );
        }
    }
}
