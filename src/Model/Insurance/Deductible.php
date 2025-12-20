<?php

declare(strict_types=1);

namespace App\Model\Insurance;

use App\Model\Financial\MoneyAmount;

/**
 * Represents a deductible amount for insurance coverage.
 */
class Deductible
{
    public function __construct(
        private MoneyAmount $amount,
        private string $type = 'per_claim', // per_claim, annual, percentage
        private ?float $percentage = null,
    ) {
        if ($type === 'percentage' && ($percentage === null || $percentage < 0 || $percentage > 100)) {
            throw new \InvalidArgumentException('Percentage deductible must be between 0 and 100');
        }
    }
    
    public function getAmount(): MoneyAmount
    {
        return $this->amount;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function getPercentage(): ?float
    {
        return $this->percentage;
    }
    
    public function calculateDeductible(MoneyAmount $claimAmount): MoneyAmount
    {
        if ($this->type === 'percentage' && $this->percentage !== null) {
            $deductibleAmount = $claimAmount->getAmount() * ($this->percentage / 100);
            return new MoneyAmount($deductibleAmount, $claimAmount->getCurrency());
        }
        
        return $this->amount;
    }
}
