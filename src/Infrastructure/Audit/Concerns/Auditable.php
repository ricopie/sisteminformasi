<?php

declare(strict_types=1);

namespace Infrastructure\Audit\Concerns;

use Infrastructure\Audit\Models\AuditLogModel;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (self $model) {
            $model->logAudit('created');
        });

        static::updated(function (self $model) {
            $model->logAudit('updated');
        });

        static::deleted(function (self $model) {
            $model->logAudit('deleted');
        });
    }

    protected function logAudit(string $event): void
    {
        $old = match ($event) {
            'updated' => $this->getOriginal(),
            'deleted' => $this->getAttributes(),
            default => null,
        };

        $new = match ($event) {
            'created', 'updated' => $this->getAttributes(),
            default => null,
        };

        AuditLogModel::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }
}
