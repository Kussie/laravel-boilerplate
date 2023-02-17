<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait InteractsWithUuid
{
    public static function bootInteractsWithUuid(): void
    {
        self::creating(function ($model): void {
            $model->keyType = 'string';
            $model->incrementing = false;

            $model->{$model->getKeyName()} = $model->{$model->getKeyName()} ?: (string) Str::orderedUuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return $this->getKeyName();
    }

    public function scopeFindByUuid(Builder $query, string $uuid): Model | null
    {
        return $query->where($this->getKeyName(), $uuid)->first();
    }

    public function scopeFindOrFailByUuid(Builder $query, string $uuid): Model
    {
        return $query->where($this->getKeyName(), $uuid)->firstOrFail();
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }
}
