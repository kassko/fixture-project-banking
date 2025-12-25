<?php

namespace App\Model\Product;

class LoanProduct extends Product
{
    private ?float $interestRate = null;
    private ?int $termMonths = null;
    private ?float $maxAmount = null;
    private ?float $minAmount = null;

    public function __construct()
    {
        $this->setProductType('loan');
    }

    public function getInterestRate(): ?float
    {
        return $this->interestRate;
    }

    public function setInterestRate(?float $interestRate): self
    {
        $this->interestRate = $interestRate;
        return $this;
    }

    public function getTermMonths(): ?int
    {
        return $this->termMonths;
    }

    public function setTermMonths(?int $termMonths): self
    {
        $this->termMonths = $termMonths;
        return $this;
    }

    public function getMaxAmount(): ?float
    {
        return $this->maxAmount;
    }

    public function setMaxAmount(?float $maxAmount): self
    {
        $this->maxAmount = $maxAmount;
        return $this;
    }

    public function getMinAmount(): ?float
    {
        return $this->minAmount;
    }

    public function setMinAmount(?float $minAmount): self
    {
        $this->minAmount = $minAmount;
        return $this;
    }

    public function validate(): void
    {
        parent::validate();
        
        if ($this->interestRate !== null && $this->interestRate < 0) {
            $this->addValidationError('interestRate', 'Interest rate cannot be negative');
        }
        
        if ($this->termMonths !== null && $this->termMonths <= 0) {
            $this->addValidationError('termMonths', 'Term must be positive');
        }
    }
}
