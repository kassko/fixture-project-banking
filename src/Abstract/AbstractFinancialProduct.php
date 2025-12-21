<?php

declare(strict_types=1);

namespace App\Abstract;

use App\Model\Financial\MoneyAmount;
use App\Traits\MetadataContainerTrait;
use App\Traits\TimestampableTrait;
use DateTimeImmutable;

/**
 * Base abstract class for all financial products.
 */
abstract class AbstractFinancialProduct
{
    use TimestampableTrait;
    use MetadataContainerTrait;
    
    protected string $productCode;
    
    protected string $name;
    
    protected string $description = '';
    
    protected bool $isActive = true;
    
    protected ?DateTimeImmutable $launchDate = null;
    
    public function getProductCode(): string
    {
        return $this->productCode;
    }
    
    public function setProductCode(string $productCode): static
    {
        $this->productCode = $productCode;
        return $this;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }
    
    public function getDescription(): string
    {
        return $this->description;
    }
    
    public function setDescription(string $description): static
    {
        $this->description = $description;
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
    
    public function getLaunchDate(): ?DateTimeImmutable
    {
        return $this->launchDate;
    }
    
    public function setLaunchDate(?DateTimeImmutable $launchDate): static
    {
        $this->launchDate = $launchDate;
        return $this;
    }
    
    abstract public function calculateValue(): MoneyAmount;
}
