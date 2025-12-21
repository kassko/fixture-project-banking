<?php

declare(strict_types=1);

namespace App\Legacy\DataObject;

/**
 * Legacy customer profile data object WITHOUT Doctrine annotations.
 * Represents data from a legacy system.
 */
class LegacyCustomerProfile
{
    private ?string $customerId = null;
    
    private ?string $firstName = null;
    
    private ?string $lastName = null;
    
    private ?string $email = null;
    
    private ?string $phone = null;
    
    private ?array $addressData = null;
    
    private ?string $accountStatus = null;
    
    private ?int $creditScore = null;
    
    private ?array $preferences = null;
    
    private ?string $lastContactDate = null;
    
    private ?string $registrationDate = null;
    
    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }
    
    public function setCustomerId(?string $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }
    
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }
    
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }
    
    public function getLastName(): ?string
    {
        return $this->lastName;
    }
    
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
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
    
    public function getAddressData(): ?array
    {
        return $this->addressData;
    }
    
    public function setAddressData(?array $addressData): self
    {
        $this->addressData = $addressData;
        return $this;
    }
    
    public function getAccountStatus(): ?string
    {
        return $this->accountStatus;
    }
    
    public function setAccountStatus(?string $accountStatus): self
    {
        $this->accountStatus = $accountStatus;
        return $this;
    }
    
    public function getCreditScore(): ?int
    {
        return $this->creditScore;
    }
    
    public function setCreditScore(?int $creditScore): self
    {
        $this->creditScore = $creditScore;
        return $this;
    }
    
    public function getPreferences(): ?array
    {
        return $this->preferences;
    }
    
    public function setPreferences(?array $preferences): self
    {
        $this->preferences = $preferences;
        return $this;
    }
    
    public function getLastContactDate(): ?string
    {
        return $this->lastContactDate;
    }
    
    public function setLastContactDate(?string $lastContactDate): self
    {
        $this->lastContactDate = $lastContactDate;
        return $this;
    }
    
    public function getRegistrationDate(): ?string
    {
        return $this->registrationDate;
    }
    
    public function setRegistrationDate(?string $registrationDate): self
    {
        $this->registrationDate = $registrationDate;
        return $this;
    }
}
