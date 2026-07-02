<?php

namespace App\Enums;

enum AssessmentVerdict: string
{
    case Pass = 'pass';
    case Fail = 'fail';
    case Conditional = 'conditional';

    public function label(): string
    {
        return match ($this) {
            self::Pass => 'Pass',
            self::Fail => 'Fail',
            self::Conditional => 'Conditional',
        };
    }
}
