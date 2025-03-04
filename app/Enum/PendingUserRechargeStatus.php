<?php

namespace App\Enum;

enum PendingUserRechargeStatus: string
{
    case WAITING = 'waiting';
    case CONFIRMED = 'confirmed';
    case CANCELED = 'cancelled';
}
