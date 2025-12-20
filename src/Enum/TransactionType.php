<?php

declare(strict_types=1);

namespace App\Enum;

enum TransactionType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAWAL = 'withdrawal';
    case TRANSFER = 'transfer';
    case PAYMENT = 'payment';
    case FEE = 'fee';
}
