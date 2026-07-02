<?php

namespace App\Concerns;

use App\Services\AuditService;
use Illuminate\Support\Str;

trait HasAuditLog
{
    public static function bootHasAuditLog(): void
    {
        static::created(function (self $model) {
            if (! $model->shouldAudit('created')) {
                return;
            }

            $changes = $model->getChanges();

            if (empty($changes)) {
                return;
            }

            app(AuditService::class)->log(
                actor: null,
                action: $model->getAuditAction('created'),
                targetType: $model->getAuditTargetType(),
                targetId: $model->getAuditTargetId(),
                new: $changes,
            );
        });

        static::updated(function (self $model) {
            if (! $model->shouldAudit('updated')) {
                return;
            }

            $changes = $model->getChanges();

            if (empty($changes)) {
                return;
            }

            app(AuditService::class)->log(
                actor: null,
                action: $model->getAuditAction('updated'),
                targetType: $model->getAuditTargetType(),
                targetId: $model->getAuditTargetId(),
                old: $model->getOriginal(),
                new: $changes,
            );
        });

        static::deleted(function (self $model) {
            if (! $model->shouldAudit('deleted')) {
                return;
            }

            app(AuditService::class)->log(
                actor: null,
                action: $model->getAuditAction('deleted'),
                targetType: $model->getAuditTargetType(),
                targetId: $model->getAuditTargetId(),
                old: $model->getAttributes(),
            );
        });
    }

    protected function getAuditTargetType(): string
    {
        return Str::snake(class_basename($this));
    }

    protected function getAuditTargetId(): ?int
    {
        return $this->id;
    }

    protected function getAuditAction(string $event): string
    {
        return "{$this->getAuditTargetType()}_{$event}";
    }

    protected function shouldAudit(string $event): bool
    {
        return true;
    }
}
