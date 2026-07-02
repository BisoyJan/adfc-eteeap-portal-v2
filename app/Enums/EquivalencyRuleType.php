<?php

namespace App\Enums;

enum EquivalencyRuleType: string
{
    case Direct = 'direct';
    case Experience = 'experience';
    case Certification = 'certification';
    case Training = 'training';
    case PriorEducation = 'prior_education';

    public function label(): string
    {
        return match ($this) {
            self::Direct => 'Direct Mapping',
            self::Experience => 'Experience-Based',
            self::Certification => 'Certification-Based',
            self::Training => 'Training-Based',
            self::PriorEducation => 'Prior Education',
        };
    }
}
