<?php

declare(strict_types=1);

namespace App\DTO\Request;

class RecommendationRequest
{
    public function __construct(
        public readonly int $customerId,
        public readonly ?array $productCategories = null,
        public readonly ?string $context = null,
        public readonly bool $includeOptimization = false
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['customerId'] ?? 0,
            $data['productCategories'] ?? null,
            $data['context'] ?? null,
            $data['includeOptimization'] ?? false
        );
    }
}
