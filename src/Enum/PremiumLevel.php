<?php

declare(strict_types=1);

namespace App\Enum;

enum PremiumLevel: string
{
    case BRONZE = 'bronze';
    case SILVER = 'silver';
    case GOLD = 'gold';
    case PLATINUM = 'platinum';
}
