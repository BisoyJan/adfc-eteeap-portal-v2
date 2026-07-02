<?php

namespace App\Enums;

enum EnrollmentStatus: string
{
    case Pending = 'pending';
    case Enrolled = 'enrolled';
    case Completed = 'completed';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Enrolled => 'Enrolled',
            self::Completed => 'Completed',
            self::Withdrawn => 'Withdrawn',
        };
    }
}
