<?php

namespace App\Model\Customer;

use App\Traits\IdentifiableTrait;
use App\Traits\AuditableTrait;
use App\Traits\SerializableTrait;
use App\Model\Common\Address;

class Customer
{
    use IdentifiableTrait;
    use AuditableTrait;
    use SerializableTrait;

    private ?string $name = null;
    private ?string $email = null;
    private ?string $phone = null;
    private ?Address $address = null;
    private string $customerType = 'standard';

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getCustomerType(): string
    {
        return $this->customerType;
    }

    public function setCustomerType(string $customerType): self
    {
        $this->customerType = $customerType;
        return $this;
    }
}
