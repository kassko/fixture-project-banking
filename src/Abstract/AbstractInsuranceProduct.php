<?php

declare(strict_types=1);

namespace App\Abstract;

use App\Model\Financial\MoneyAmount;

/**
 * Abstract class for insurance products.
 */
abstract class AbstractInsuranceProduct extends AbstractFinancialProduct
{
    protected ?MoneyAmount $premiumBase = null;
    
    protected array $coverageTypes = [];
    
    protected ?MoneyAmount $maxCoverage = null;
    
    public function getPremiumBase(): ?MoneyAmount
    {
        return $this->premiumBase;
    }
    
    public function setPremiumBase(?MoneyAmount $premiumBase): static
    {
        $this->premiumBase = $premiumBase;
        return $this;
    }
    
    public function getCoverageTypes(): array
    {
        return $this->coverageTypes;
    }
    
    public function setCoverageTypes(array $coverageTypes): static
    {
        $this->coverageTypes = $coverageTypes;
        return $this;
    }
    
    public function addCoverageType(string $type): static
    {
        if (!in_array($type, $this->coverageTypes, true)) {
            $this->coverageTypes[] = $type;
        }
        return $this;
    }
    
    public function getMaxCoverage(): ?MoneyAmount
    {
        return $this->maxCoverage;
    }
    
    public function setMaxCoverage(?MoneyAmount $maxCoverage): static
    {
        $this->maxCoverage = $maxCoverage;
        return $this;
    }
}
