<?php

declare(strict_types=1);

namespace App\Abstract;

use App\Model\Financial\InterestRate;
use App\Model\Financial\MoneyAmount;
use App\Traits\AuditableTrait;
use App\Traits\TimestampableTrait;

/**
 * Base abstract class for all financial products.
 */
abstract class AbstractFinancialProduct
{
    use TimestampableTrait;
    use AuditableTrait;
    
    protected string $productCode;
    
    protected string $productName;
    
    protected ?MoneyAmount $amount = null;
    
    protected ?InterestRate $interestRate = null;
    
    protected bool $isActive = true;
    
    public function getProductCode(): string
    {
        return $this->productCode;
    }
    
    public function setProductCode(string $productCode): static
    {
        $this->productCode = $productCode;
        return $this;
    }
    
    public function getProductName(): string
    {
        return $this->productName;
    }
    
    public function setProductName(string $productName): static
    {
        $this->productName = $productName;
        return $this;
    }
    
    public function getAmount(): ?MoneyAmount
    {
        return $this->amount;
    }
    
    public function setAmount(MoneyAmount $amount): static
    {
        $this->amount = $amount;
        return $this;
    }
    
    public function getInterestRate(): ?InterestRate
    {
        return $this->interestRate;
    }
    
    public function setInterestRate(?InterestRate $interestRate): static
    {
        $this->interestRate = $interestRate;
        return $this;
    }
    
    public function isActive(): bool
    {
        return $this->isActive;
    }
    
    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }
    
    abstract public function calculateValue(): MoneyAmount;
}
