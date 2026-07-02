<?php

namespace App\Observers;

use App\Models\AssessmentForm;
use App\Services\AuditService;
use App\Services\NotificationService;

class AssessmentFormObserver
{
    public function __construct(
        protected NotificationService $notifications,
        protected AuditService $audit,
    ) {}

    public function created(AssessmentForm $form): void
    {
        $stage = $form->stage->value;

        $this->audit->log(
            actor: $form->assessor,
            action: "assessment_{$stage}_submitted",
            targetType: 'assessment_form',
            targetId: $form->id,
            new: $form->getChanges(),
        );

        if (in_array($stage, ['third_validation', 'third_oral'], true)) {
            $this->notifications->sendToRole(
                'qa_officer',
                "assessment_{$stage}_submitted",
                'F.3 Assessment Submitted',
                "A {$form->stage->value} assessment has been submitted for application {$form->application_id}.",
            );
        }
    }
}
