<?php

declare(strict_types=1);

namespace App\DTO\Request;

class ClaimStatusUpdate
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $comment = null,
        public readonly ?string $assignedTo = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['status'] ?? 'PENDING',
            $data['comment'] ?? null,
            $data['assignedTo'] ?? null
        );
    }
}
