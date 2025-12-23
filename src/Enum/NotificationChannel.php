<?php

declare(strict_types=1);

namespace App\Enum;

enum NotificationChannel: string
{
    case EMAIL = 'EMAIL';
    case SMS = 'SMS';
    case PUSH = 'PUSH';
    case IN_APP = 'IN_APP';
}
