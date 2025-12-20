<?php

declare(strict_types=1);

namespace App\Enum;

enum PolicyStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
}
