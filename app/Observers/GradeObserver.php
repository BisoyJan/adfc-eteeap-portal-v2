<?php

namespace App\Observers;

use App\Models\Grade;
use App\Services\AuditService;
use App\Services\NotificationService;

class GradeObserver
{
    public function __construct(
        protected NotificationService $notifications,
        protected AuditService $audit,
    ) {}

    public function updated(Grade $grade): void
    {
        if ($grade->wasChanged('status') && $grade->status->value === 'approved') {
            $this->notifications->send(
                $grade->student,
                'grade_posted',
                'Grade Posted',
                "Your grade for {$grade->curriculumSubject->title} has been posted: {$grade->grade}.",
                ['url' => route('student.grades.index', absolute: false)],
                email: true,
            );

            $this->audit->log(
                actor: $grade->approver,
                action: 'grade_approved',
                targetType: 'grade',
                targetId: $grade->id,
                old: $grade->getOriginal(),
                new: $grade->getChanges(),
            );
        }
    }
}
