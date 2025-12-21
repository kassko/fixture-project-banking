<?php

declare(strict_types=1);

namespace App\Context;

class BrandContext
{
    public function __construct(
        private ?string $brandId = null,
        private array $configuration = []
    ) {
    }

    public function getBrandId(): ?string
    {
        return $this->brandId;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->configuration[$key] ?? $default;
    }
}
