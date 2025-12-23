<?php

declare(strict_types=1);

namespace App\Enum;

enum NotificationStatus: string
{
    case PENDING = 'PENDING';
    case SENT = 'SENT';
    case DELIVERED = 'DELIVERED';
    case FAILED = 'FAILED';
    case READ = 'READ';
}
