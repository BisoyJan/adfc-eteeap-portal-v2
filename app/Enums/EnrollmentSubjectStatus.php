<?php

namespace App\Enums;

enum EnrollmentSubjectStatus: string
{
    case Credited = 'credited';
    case Transferred = 'transferred';
    case Enrolled = 'enrolled';
    case Pending = 'pending';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Credited => 'Credited',
            self::Transferred => 'Transferred',
            self::Enrolled => 'Enrolled',
            self::Pending => 'Pending',
            self::Completed => 'Completed',
        };
    }
}
