<?php

declare(strict_types=1);

namespace App\Context;

class UserContext
{
    public function __construct(
        private ?int $userId = null,
        private ?string $userType = null,
        private array $attributes = []
    ) {
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }
}
