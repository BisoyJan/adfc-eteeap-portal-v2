<?php

namespace App\Enums;

enum PortfolioStatus: string
{
    case Building = 'building';
    case Complete = 'complete';
    case Submitted = 'submitted';

    public function label(): string
    {
        return match ($this) {
            self::Building => 'Building',
            self::Complete => 'Complete',
            self::Submitted => 'Submitted',
        };
    }
}
