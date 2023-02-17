<?php

namespace App\Enums;

enum PackageStatus: int
{
    case PENDING = 0;

    case QUEUED = 1;

    case PROCESSING = 2;

    case FINISHED = 3;

    case FAILED = 4;

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'pending',
            self::QUEUED => 'queued',
            self::PROCESSING => 'processing',
            self::FINISHED => 'finished',
            self::FAILED => 'failed',
        };
    }
}
