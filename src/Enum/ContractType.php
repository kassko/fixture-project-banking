<?php

declare(strict_types=1);

namespace App\Enum;

enum ContractType: string
{
    case LOAN = 'LOAN';
    case MORTGAGE = 'MORTGAGE';
    case INSURANCE = 'INSURANCE';
    case INVESTMENT = 'INVESTMENT';
}
