<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case Uploaded = 'uploaded';
    case Verified = 'verified';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Uploaded => 'Uploaded',
            self::Verified => 'Verified',
            self::Rejected => 'Rejected',
        };
    }
}
