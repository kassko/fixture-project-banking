<?php

declare(strict_types=1);

namespace App\DTO\Response;

class IneligibilityReason
{
    public function __construct(
        public readonly string $productCode,
        public readonly string $productName,
        public readonly array $reasons,
        public readonly bool $canBeRemediated = false,
        public readonly array $remediationSteps = []
    ) {
    }
}
