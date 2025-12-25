<?php

namespace App\Model\Product;

class InsuranceProduct extends Product
{
    private ?float $premium = null;
    private ?float $coverageAmount = null;
    private ?string $coverageType = null;
    private ?int $policyTermYears = null;

    public function __construct()
    {
        $this->setProductType('insurance');
    }

    public function getPremium(): ?float
    {
        return $this->premium;
    }

    public function setPremium(?float $premium): self
    {
        $this->premium = $premium;
        return $this;
    }

    public function getCoverageAmount(): ?float
    {
        return $this->coverageAmount;
    }

    public function setCoverageAmount(?float $coverageAmount): self
    {
        $this->coverageAmount = $coverageAmount;
        return $this;
    }

    public function getCoverageType(): ?string
    {
        return $this->coverageType;
    }

    public function setCoverageType(?string $coverageType): self
    {
        $this->coverageType = $coverageType;
        return $this;
    }

    public function getPolicyTermYears(): ?int
    {
        return $this->policyTermYears;
    }

    public function setPolicyTermYears(?int $policyTermYears): self
    {
        $this->policyTermYears = $policyTermYears;
        return $this;
    }

    public function validate(): void
    {
        $this->validationErrors = [];
        
        if ($this->premium !== null && $this->premium <= 0) {
            $this->addValidationError('premium', 'Premium must be positive');
        }
        
        if ($this->coverageAmount !== null && $this->coverageAmount <= 0) {
            $this->addValidationError('coverageAmount', 'Coverage amount must be positive');
        }
        
        if (empty($this->getName())) {
            $this->addValidationError('name', 'Product name is required');
        }
        
        if ($this->getPrice() !== null && $this->getPrice() < 0) {
            $this->addValidationError('price', 'Price cannot be negative');
        }
    }
}
