<?php

namespace App\Observers;

use App\Models\Document;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Request;

class DocumentObserver
{
    public function __construct(
        protected NotificationService $notifications,
        protected AuditService $audit,
    ) {}

    public function updated(Document $document): void
    {
        if (! $document->wasChanged('status')) {
            return;
        }

        $status = $document->status->value;

        if ($status === 'verified') {
            $this->notifications->send(
                $document->user,
                'document_verified',
                'Document Verified',
                "Your document \"{$document->file_name}\" has been verified.",
            );

            $this->audit->log(
                actor: Request::user(),
                action: 'document_verified',
                targetType: 'document',
                targetId: $document->id,
                old: $document->getOriginal(),
                new: $document->getChanges(),
            );
        } elseif ($status === 'rejected') {
            $this->notifications->send(
                $document->user,
                'document_rejected',
                'Document Rejected',
                "Your document \"{$document->file_name}\" has been rejected."
                    . ($document->rejection_reason ? " Reason: {$document->rejection_reason}" : ''),
                email: true,
            );

            $this->audit->log(
                actor: Request::user(),
                action: 'document_rejected',
                targetType: 'document',
                targetId: $document->id,
                old: $document->getOriginal(),
                new: $document->getChanges(),
            );
        }
    }
}
