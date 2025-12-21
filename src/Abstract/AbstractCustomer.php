<?php

declare(strict_types=1);

namespace App\Abstract;

use App\Traits\AuditableTrait;
use DateTimeImmutable;

/**
 * Abstract class for customer entities (level 2 inheritance).
 */
abstract class AbstractCustomer extends AbstractPerson
{
    use AuditableTrait;
    
    protected string $customerNumber;
    
    protected ?DateTimeImmutable $registrationDate = null;
    
    protected string $status = 'ACTIVE';
    
    protected bool $kycValidated = false;
    
    public function getCustomerNumber(): string
    {
        return $this->customerNumber;
    }
    
    public function setCustomerNumber(string $customerNumber): static
    {
        $this->customerNumber = $customerNumber;
        return $this;
    }
    
    public function getRegistrationDate(): ?DateTimeImmutable
    {
        return $this->registrationDate;
    }
    
    public function setRegistrationDate(?DateTimeImmutable $registrationDate): static
    {
        $this->registrationDate = $registrationDate;
        return $this;
    }
    
    public function getStatus(): string
    {
        return $this->status;
    }
    
    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }
    
    public function isKycValidated(): bool
    {
        return $this->kycValidated;
    }
    
    public function setKycValidated(bool $kycValidated): static
    {
        $this->kycValidated = $kycValidated;
        return $this;
    }
    
    public function isActive(): bool
    {
        return $this->status === 'ACTIVE';
    }
    
    public function deactivate(): static
    {
        $this->status = 'INACTIVE';
        return $this;
    }
    
    public function activate(): static
    {
        $this->status = 'ACTIVE';
        return $this;
    }
}
