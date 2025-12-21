<?php

declare(strict_types=1);

namespace App\DTO\Response;

class OnboardingStep
{
    public function __construct(
        public readonly int $order,
        public readonly string $code,
        public readonly string $name,
        public readonly string $status,
        public readonly bool $required,
        public readonly int $estimatedMinutes,
        public readonly array $config = []
    ) {
    }
}
