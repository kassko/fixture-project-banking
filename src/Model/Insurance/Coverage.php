<?php

declare(strict_types=1);

namespace App\Model\Insurance;

use App\Enum\CoverageType;
use App\Model\Financial\MoneyAmount;

/**
 * Represents an insurance coverage with limits and exclusions.
 * Deep nesting: Coverage → Deductible → MoneyAmount
 */
class Coverage
{
    private CoverageType $type;
    
    private MoneyAmount $maxAmount;
    
    private Deductible $deductible;
    
    private array $conditions = [];
    
    private array $exclusions = [];
    
    private int $waitingPeriod = 0; // in days
    
    public function __construct(
        CoverageType $type,
        MoneyAmount $maxAmount,
        Deductible $deductible,
        array $conditions = [],
        array $exclusions = [],
        int $waitingPeriod = 0
    ) {
        $this->type = $type;
        $this->maxAmount = $maxAmount;
        $this->deductible = $deductible;
        $this->conditions = $conditions;
        $this->exclusions = $exclusions;
        $this->waitingPeriod = $waitingPeriod;
    }
    
    public function getType(): CoverageType
    {
        return $this->type;
    }
    
    public function setType(CoverageType $type): static
    {
        $this->type = $type;
        return $this;
    }
    
    public function getMaxAmount(): MoneyAmount
    {
        return $this->maxAmount;
    }
    
    public function setMaxAmount(MoneyAmount $maxAmount): static
    {
        $this->maxAmount = $maxAmount;
        return $this;
    }
    
    public function getDeductible(): Deductible
    {
        return $this->deductible;
    }
    
    public function setDeductible(Deductible $deductible): static
    {
        $this->deductible = $deductible;
        return $this;
    }
    
    public function getConditions(): array
    {
        return $this->conditions;
    }
    
    public function setConditions(array $conditions): static
    {
        $this->conditions = $conditions;
        return $this;
    }
    
    public function addCondition(string $condition): static
    {
        $this->conditions[] = $condition;
        return $this;
    }
    
    public function getExclusions(): array
    {
        return $this->exclusions;
    }
    
    public function setExclusions(array $exclusions): static
    {
        $this->exclusions = $exclusions;
        return $this;
    }
    
    public function addExclusion(string $exclusion): static
    {
        $this->exclusions[] = $exclusion;
        return $this;
    }
    
    public function getWaitingPeriod(): int
    {
        return $this->waitingPeriod;
    }
    
    public function setWaitingPeriod(int $waitingPeriod): static
    {
        $this->waitingPeriod = $waitingPeriod;
        return $this;
    }
    
    public function calculatePayout(MoneyAmount $claimAmount): MoneyAmount
    {
        // Calculate deductible
        $deductibleAmount = $this->deductible->calculateDeductible($claimAmount);
        
        // Subtract deductible from claim
        $afterDeductible = $claimAmount->getAmount() - $deductibleAmount->getAmount();
        
        if ($afterDeductible <= 0) {
            return new MoneyAmount(0, $claimAmount->getCurrency());
        }
        
        // Apply coverage limit
        if ($afterDeductible > $this->maxAmount->getAmount()) {
            return $this->maxAmount;
        }
        
        return new MoneyAmount($afterDeductible, $claimAmount->getCurrency());
    }
}
