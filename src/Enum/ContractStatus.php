<?php

declare(strict_types=1);

namespace App\Enum;

enum ContractStatus: string
{
    case DRAFT = 'DRAFT';
    case PENDING_APPROVAL = 'PENDING_APPROVAL';
    case ACTIVE = 'ACTIVE';
    case TERMINATED = 'TERMINATED';
}
