<?php

declare(strict_types=1);

namespace App\Abstract;

use App\Enum\CustomerType;
use App\Traits\AuditableTrait;

/**
 * Abstract class for customer entities (level 2 inheritance).
 */
abstract class AbstractCustomer extends AbstractPerson
{
    use AuditableTrait;
    
    protected string $customerNumber;
    
    protected CustomerType $type;
    
    protected bool $isActive = true;
    
    public function getCustomerNumber(): string
    {
        return $this->customerNumber;
    }
    
    public function setCustomerNumber(string $customerNumber): static
    {
        $this->customerNumber = $customerNumber;
        return $this;
    }
    
    public function getType(): CustomerType
    {
        return $this->type;
    }
    
    public function setType(CustomerType $type): static
    {
        $this->type = $type;
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
    
    public function deactivate(): static
    {
        $this->isActive = false;
        return $this;
    }
    
    public function activate(): static
    {
        $this->isActive = true;
        return $this;
    }
}
