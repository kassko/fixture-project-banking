<?php

declare(strict_types=1);

namespace App\Model\Common;

/**
 * Represents a physical address with geographic coordinates.
 */
class Address
{
    public function __construct(
        private string $street,
        private string $city,
        private string $postalCode,
        private string $country,
        private ?string $state = null,
        private ?float $latitude = null,
        private ?float $longitude = null,
    ) {
    }
    
    public function getStreet(): string
    {
        return $this->street;
    }
    
    public function getCity(): string
    {
        return $this->city;
    }
    
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }
    
    public function getCountry(): string
    {
        return $this->country;
    }
    
    public function getState(): ?string
    {
        return $this->state;
    }
    
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }
    
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }
    
    public function getFullAddress(): string
    {
        $parts = [$this->street, $this->city];
        
        if ($this->state) {
            $parts[] = $this->state;
        }
        
        $parts[] = $this->postalCode;
        $parts[] = $this->country;
        
        return implode(', ', $parts);
    }
    
    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }
}
