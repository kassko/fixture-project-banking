<?php

declare(strict_types=1);

namespace App\Model\Insurance;

use App\Enum\DeductibleType;
use App\Model\Financial\MoneyAmount;

/**
 * Represents a deductible amount for insurance coverage.
 */
class Deductible
{
    private DeductibleType $type;
    
    private MoneyAmount $amount;
    
    private float $percentage = 0.0;
    
    private array $applicableTo = [];
    
    public function __construct(
        DeductibleType $type,
        MoneyAmount $amount,
        float $percentage = 0.0,
        array $applicableTo = []
    ) {
        $this->type = $type;
        $this->amount = $amount;
        $this->percentage = $percentage;
        $this->applicableTo = $applicableTo;
    }
    
    public function getType(): DeductibleType
    {
        return $this->type;
    }
    
    public function setType(DeductibleType $type): static
    {
        $this->type = $type;
        return $this;
    }
    
    public function getAmount(): MoneyAmount
    {
        return $this->amount;
    }
    
    public function setAmount(MoneyAmount $amount): static
    {
        $this->amount = $amount;
        return $this;
    }
    
    public function getPercentage(): float
    {
        return $this->percentage;
    }
    
    public function setPercentage(float $percentage): static
    {
        $this->percentage = $percentage;
        return $this;
    }
    
    public function getApplicableTo(): array
    {
        return $this->applicableTo;
    }
    
    public function setApplicableTo(array $applicableTo): static
    {
        $this->applicableTo = $applicableTo;
        return $this;
    }
    
    public function calculateDeductible(MoneyAmount $claimAmount): MoneyAmount
    {
        return match($this->type) {
            DeductibleType::FIXED => $this->amount,
            DeductibleType::PERCENTAGE => new MoneyAmount(
                $claimAmount->getAmount() * ($this->percentage / 100),
                $claimAmount->getCurrency()
            ),
            DeductibleType::TIERED => $this->calculateTieredDeductible($claimAmount),
        };
    }
    
    private function calculateTieredDeductible(MoneyAmount $claimAmount): MoneyAmount
    {
        // Simple tiered calculation - could be more complex
        if ($claimAmount->getAmount() < 1000) {
            return $this->amount;
        }
        
        $percentageAmount = $claimAmount->getAmount() * ($this->percentage / 100);
        return new MoneyAmount(
            max($this->amount->getAmount(), $percentageAmount),
            $claimAmount->getCurrency()
        );
    }
}
