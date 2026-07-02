<?php

namespace App\Enums;

enum EquivalencyMatrixStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
}
