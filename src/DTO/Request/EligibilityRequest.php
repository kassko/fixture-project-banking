<?php

declare(strict_types=1);

namespace App\DTO\Request;

class EligibilityRequest
{
    public function __construct(
        public readonly int $customerId,
        public readonly ?array $productCategories = null,
        public readonly bool $includeReasons = true
    ) {
    }
}
