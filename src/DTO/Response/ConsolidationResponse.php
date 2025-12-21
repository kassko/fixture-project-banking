<?php

declare(strict_types=1);

namespace App\DTO\Response;

class ConsolidationResponse
{
    public function __construct(
        private int $customerId,
        private array $accounts,
        private array $aggregatedBalances,
        private array $assetLiabilityView,
        private array $statistics,
        private string $consolidationDate
    ) {
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customerId,
            'accounts' => $this->accounts,
            'aggregated_balances' => $this->aggregatedBalances,
            'asset_liability_view' => $this->assetLiabilityView,
            'statistics' => $this->statistics,
            'consolidation_date' => $this->consolidationDate,
        ];
    }
}
