<?php

declare(strict_types=1);

namespace App\Abstract;

/**
 * Abstract class for investment products.
 */
abstract class AbstractInvestmentProduct extends AbstractFinancialProduct
{
    protected string $riskLevel = 'MEDIUM';
    
    protected ?float $expectedReturn = null;
    
    protected ?int $lockPeriod = null; // in days
    
    public function getRiskLevel(): string
    {
        return $this->riskLevel;
    }
    
    public function setRiskLevel(string $riskLevel): static
    {
        $this->riskLevel = $riskLevel;
        return $this;
    }
    
    public function getExpectedReturn(): ?float
    {
        return $this->expectedReturn;
    }
    
    public function setExpectedReturn(?float $expectedReturn): static
    {
        $this->expectedReturn = $expectedReturn;
        return $this;
    }
    
    public function getLockPeriod(): ?int
    {
        return $this->lockPeriod;
    }
    
    public function setLockPeriod(?int $lockPeriod): static
    {
        $this->lockPeriod = $lockPeriod;
        return $this;
    }
}
