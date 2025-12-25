<?php

namespace App\Model\Customer;

class PremiumCustomer extends Customer
{
    private ?float $annualRevenue = null;
    private ?int $loyaltyPoints = null;
    private ?string $accountManager = null;

    public function __construct()
    {
        $this->setCustomerType('premium');
    }

    public function getAnnualRevenue(): ?float
    {
        return $this->annualRevenue;
    }

    public function setAnnualRevenue(?float $annualRevenue): self
    {
        $this->annualRevenue = $annualRevenue;
        return $this;
    }

    public function getLoyaltyPoints(): ?int
    {
        return $this->loyaltyPoints;
    }

    public function setLoyaltyPoints(?int $loyaltyPoints): self
    {
        $this->loyaltyPoints = $loyaltyPoints;
        return $this;
    }

    public function getAccountManager(): ?string
    {
        return $this->accountManager;
    }

    public function setAccountManager(?string $accountManager): self
    {
        $this->accountManager = $accountManager;
        return $this;
    }
}
