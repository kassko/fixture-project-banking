<?php

declare(strict_types=1);

namespace App\Enum;

enum AccountType: string
{
    case CHECKING = 'checking';
    case SAVINGS = 'savings';
    case INVESTMENT = 'investment';
    case LOAN = 'loan';
}
