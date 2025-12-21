<?php

declare(strict_types=1);

namespace App\Enum;

enum PolicyType: string
{
    case LIFE = 'LIFE';
    case HOME = 'HOME';
    case AUTO = 'AUTO';
    case HEALTH = 'HEALTH';
}
