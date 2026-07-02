<?php

namespace App\Enums;

enum AssessmentStage: string
{
    case First = 'first';
    case Second = 'second';
    case ThirdValidation = 'third_validation';
    case ThirdOral = 'third_oral';

    public function label(): string
    {
        return match ($this) {
            self::First => 'F.1 Initial Screening',
            self::Second => 'F.2 Portfolio Evaluation',
            self::ThirdValidation => 'F.3 Panel Validation',
            self::ThirdOral => 'F.3 Oral Competency',
        };
    }
}
