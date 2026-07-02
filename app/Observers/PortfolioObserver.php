<?php

namespace App\Observers;

use App\Models\Portfolio;
use App\Services\AuditService;
use App\Services\NotificationService;

class PortfolioObserver
{
    public function __construct(
        protected NotificationService $notifications,
        protected AuditService $audit,
    ) {}

    public function updated(Portfolio $portfolio): void
    {
        if ($portfolio->wasChanged('status') && $portfolio->status->value === 'submitted') {
            $this->notifications->sendToRole(
                'coordinator',
                'portfolio_submitted',
                'Portfolio Submitted',
                "A portfolio for application {$portfolio->application_id} has been submitted for review.",
            );

            $this->audit->log(
                actor: $portfolio->application?->user,
                action: 'portfolio_submitted',
                targetType: 'portfolio',
                targetId: $portfolio->id,
                new: $portfolio->getChanges(),
            );
        }
    }
}
