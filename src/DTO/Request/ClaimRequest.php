<?php

declare(strict_types=1);

namespace App\DTO\Request;

class ClaimRequest
{
    public function __construct(
        public readonly int $customerId,
        public readonly string $type,
        public readonly string $description,
        public readonly string $incidentDate,
        public readonly ?int $policyId = null,
        public readonly ?float $amount = null,
        public readonly ?array $attachments = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['customerId'] ?? 0,
            $data['type'] ?? 'GENERAL',
            $data['description'] ?? '',
            $data['incidentDate'] ?? date('Y-m-d'),
            $data['policyId'] ?? null,
            $data['amount'] ?? null,
            $data['attachments'] ?? null
        );
    }
}
