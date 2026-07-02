<?php

namespace App\Enums;

enum SubjectCategory: string
{
    case Core = 'core';
    case Major = 'major';
    case Ge = 'ge';
    case Elective = 'elective';
    case Pe = 'pe';
    case Nstp = 'nstp';

    public function label(): string
    {
        return match ($this) {
            self::Core => 'Core',
            self::Major => 'Major',
            self::Ge => 'General Education',
            self::Elective => 'Elective',
            self::Pe => 'Physical Education',
            self::Nstp => 'NSTP',
        };
    }
}
