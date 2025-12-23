<?php

declare(strict_types=1);

namespace App\DTO\Response;

class RecommendationResponse
{
    public function __construct(
        private int $customerId,
        private array $recommendations,
        private array $customerProfile,
        private ?array $optimizationSuggestions = null
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getRecommendations(): array
    {
        return $this->recommendations;
    }

    public function getCustomerProfile(): array
    {
        return $this->customerProfile;
    }

    public function getOptimizationSuggestions(): ?array
    {
        return $this->optimizationSuggestions;
    }

    public function toArray(): array
    {
        $result = [
            'customer_id' => $this->customerId,
            'recommendations' => $this->recommendations,
            'customer_profile' => $this->customerProfile,
        ];

        if ($this->optimizationSuggestions !== null) {
            $result['optimization_suggestions'] = $this->optimizationSuggestions;
        }

        return $result;
    }
}
