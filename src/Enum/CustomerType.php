<?php

declare(strict_types=1);

namespace App\Enum;

enum CustomerType: string
{
    case INDIVIDUAL = 'individual';
    case BUSINESS = 'business';
    case CORPORATE = 'corporate';
}
