<?php

declare(strict_types=1);

namespace App\DTO\Response;

class WelcomeOffer
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $description,
        public readonly float $value,
        public readonly int $validDays
    ) {
    }
}
