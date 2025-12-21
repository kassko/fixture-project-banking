<?php

declare(strict_types=1);

namespace App\Abstract;

use App\Model\Financial\InterestRate;
use App\Model\Financial\MoneyAmount;

/**
 * Abstract class for bank products.
 */
abstract class AbstractBankProduct extends AbstractFinancialProduct
{
    protected ?InterestRate $interestRate = null;
    
    protected array $fees = [];
    
    protected string $currency = 'EUR';
    
    public function getInterestRate(): ?InterestRate
    {
        return $this->interestRate;
    }
    
    public function setInterestRate(?InterestRate $interestRate): static
    {
        $this->interestRate = $interestRate;
        return $this;
    }
    
    public function getFees(): array
    {
        return $this->fees;
    }
    
    public function setFees(array $fees): static
    {
        $this->fees = $fees;
        return $this;
    }
    
    public function addFee(string $type, MoneyAmount $amount): static
    {
        $this->fees[$type] = $amount;
        return $this;
    }
    
    public function getCurrency(): string
    {
        return $this->currency;
    }
    
    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;
        return $this;
    }
}
