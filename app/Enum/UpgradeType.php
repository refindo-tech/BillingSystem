<?php

namespace App\Enum;

enum UpgradeType: string
{
    case UPGRADE = 'UPGRADE';
    case DOWNGRADE = 'DOWNGRADE';
    case RECHARGE = 'RECHARGE';
    // case DEACTIVATE = 'DEACTIVATE';

    public function description(): string
    {
        return match ($this) {
            self::UPGRADE => 'Meningkatkan level paket layanan',
            self::DOWNGRADE => 'Menurunkan level paket layanan',
            self::RECHARGE => 'Perpanjang masa aktif layanan',
            // self::DEACTIVATE => 'Nonaktifkan layanan',
        };
    }
}
