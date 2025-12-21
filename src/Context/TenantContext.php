<?php

declare(strict_types=1);

namespace App\Context;

class TenantContext
{
    public function __construct(
        private ?string $tenantId = null,
        private array $configuration = []
    ) {
    }

    public function getTenantId(): ?string
    {
        return $this->tenantId;
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
