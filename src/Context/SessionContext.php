<?php

declare(strict_types=1);

namespace App\Context;

class SessionContext
{
    public function __construct(
        private ?string $sessionId = null,
        private array $data = []
    ) {
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }
}
