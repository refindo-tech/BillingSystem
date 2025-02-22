<?php

namespace App\Enum;

enum ValidityCycle: string
{
    case PROFILE = 'Profil';
    case MONTHLY = 'Bulanan';
    case FIXED = 'Tetap';
}
