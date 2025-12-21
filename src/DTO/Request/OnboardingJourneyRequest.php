<?php

declare(strict_types=1);

namespace App\DTO\Request;

class OnboardingJourneyRequest
{
    public function __construct(
        public readonly ?int $customerId,
        public readonly string $customerType,
        public readonly string $targetProduct,
        public readonly string $channel = 'WEB',
        public readonly ?string $campaignCode = null,
        public readonly array $existingDocuments = []
    ) {
    }
}
