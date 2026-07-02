<?php

namespace App\Enums;

enum GraduationClearanceStatus: string
{
    case Pending = 'pending';
    case InReview = 'in_review';
    case Approved = 'approved';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InReview => 'In Review',
            self::Approved => 'Approved',
            self::Completed => 'Completed',
        };
    }
}
