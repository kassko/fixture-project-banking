<?php

declare(strict_types=1);

namespace App\DTO\Response;

class EligibleProduct
{
    public function __construct(
        public readonly string $productCode,
        public readonly string $productName,
        public readonly string $category,
        public readonly array $conditions = [],
        public readonly ?array $specialOffer = null,
        public readonly int $priority = 50
    ) {
    }
}
