<?php

declare(strict_types=1);

namespace App\Model\Insurance;

use App\Model\Financial\MoneyAmount;

/**
 * Represents an insurance coverage with limits and exclusions.
 */
class Coverage
{
    /**
     * @param array<string> $exclusions
     */
    public function __construct(
        private string $coverageType,
        private MoneyAmount $coverageLimit,
        private Deductible $deductible,
        private array $exclusions = [],
        private bool $isActive = true,
    ) {
    }
    
    public function getCoverageType(): string
    {
        return $this->coverageType;
    }
    
    public function getCoverageLimit(): MoneyAmount
    {
        return $this->coverageLimit;
    }
    
    public function getDeductible(): Deductible
    {
        return $this->deductible;
    }
    
    /**
     * @return array<string>
     */
    public function getExclusions(): array
    {
        return $this->exclusions;
    }
    
    public function isActive(): bool
    {
        return $this->isActive;
    }
    
    public function calculatePayout(MoneyAmount $claimAmount): MoneyAmount
    {
        if (!$this->isActive) {
            return new MoneyAmount(0, $claimAmount->getCurrency());
        }
        
        // Subtract deductible
        $afterDeductible = $claimAmount->subtract($this->deductible->getAmount());
        
        if ($afterDeductible->isNegative() || $afterDeductible->isZero()) {
            return new MoneyAmount(0, $claimAmount->getCurrency());
        }
        
        // Apply coverage limit
        if ($afterDeductible->getAmount() > $this->coverageLimit->getAmount()) {
            return $this->coverageLimit;
        }
        
        return $afterDeductible;
    }
}
