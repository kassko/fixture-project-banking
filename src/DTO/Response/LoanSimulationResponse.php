<?php

declare(strict_types=1);

namespace App\DTO\Response;

class LoanSimulationResponse
{
    public function __construct(
        private int $customerId,
        private float $requestedAmount,
        private string $currency,
        private string $purpose,
        private array $scenarios,
        private array $customerProfile,
        private string $recommendedScenario
    ) {
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customerId,
            'requested_amount' => $this->requestedAmount,
            'currency' => $this->currency,
            'purpose' => $this->purpose,
            'scenarios' => array_map(fn(LoanScenario $s) => $s->toArray(), $this->scenarios),
            'customer_profile' => $this->customerProfile,
            'recommended_scenario' => $this->recommendedScenario,
        ];
    }
}
