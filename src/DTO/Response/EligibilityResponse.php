<?php

declare(strict_types=1);

namespace App\DTO\Response;

class EligibilityResponse
{
    public function __construct(
        private array $eligibleProducts,
        private array $ineligibleProducts,
        private array $recommendations,
        private int $totalEvaluated,
        private array $rulesApplied
    ) {
    }

    public function getEligibleProducts(): array
    {
        return $this->eligibleProducts;
    }

    public function getIneligibleProducts(): array
    {
        return $this->ineligibleProducts;
    }

    public function getRecommendations(): array
    {
        return $this->recommendations;
    }

    public function getTotalEvaluated(): int
    {
        return $this->totalEvaluated;
    }

    public function getRulesApplied(): array
    {
        return $this->rulesApplied;
    }
}
