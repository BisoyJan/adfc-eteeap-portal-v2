<?php

namespace App\Observers;

use App\Models\Enrollment;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Request;

class EnrollmentObserver
{
    public function __construct(
        protected NotificationService $notifications,
        protected AuditService $audit,
    ) {}

    public function created(Enrollment $enrollment): void
    {
        $this->notifications->send(
            $enrollment->student,
            'enrollment_ready',
            'Enrollment Ready',
            "You have been enrolled for {$enrollment->academic_term}. Your student ID is {$enrollment->student_id_number}.",
            ['url' => route('student.curriculum-checklist', absolute: false)],
        );

        $this->audit->log(
            actor: Request::user(),
            action: 'enrollment_created',
            targetType: 'enrollment',
            targetId: $enrollment->id,
            new: $enrollment->getChanges(),
        );
    }
}
