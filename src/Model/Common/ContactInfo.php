<?php

declare(strict_types=1);

namespace App\Model\Common;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents contact information with multiple phone numbers.
 */
#[ORM\Embeddable]
class ContactInfo
{
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;
    
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;
    
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $mobile = null;
    
    #[ORM\Embedded(class: Address::class, columnPrefix: 'address_')]
    private ?Address $address = null;
    
    #[ORM\Column(type: Types::JSON)]
    private array $preferences = [];
    
    public function __construct(
        ?string $email = null,
        ?string $phone = null,
        ?string $mobile = null,
        ?Address $address = null,
        array $preferences = []
    ) {
        $this->email = $email;
        $this->phone = $phone;
        $this->mobile = $mobile;
        $this->address = $address;
        $this->preferences = $preferences;
    }
    
    public function getEmail(): ?string
    {
        return $this->email;
    }
    
    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }
    
    public function getPhone(): ?string
    {
        return $this->phone;
    }
    
    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }
    
    public function getMobile(): ?string
    {
        return $this->mobile;
    }
    
    public function setMobile(?string $mobile): static
    {
        $this->mobile = $mobile;
        return $this;
    }
    
    public function getAddress(): ?Address
    {
        return $this->address;
    }
    
    public function setAddress(?Address $address): static
    {
        $this->address = $address;
        return $this;
    }
    
    public function getPreferences(): array
    {
        return $this->preferences;
    }
    
    public function setPreferences(array $preferences): static
    {
        $this->preferences = $preferences;
        return $this;
    }
    
    public function addPreference(string $key, mixed $value): static
    {
        $this->preferences[$key] = $value;
        return $this;
    }
    
    public function getPrimaryPhone(): ?string
    {
        return $this->mobile ?? $this->phone;
    }
}
