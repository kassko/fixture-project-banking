<?php

declare(strict_types=1);

namespace App\Model\Common;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a physical address with geographic coordinates.
 */
#[ORM\Embeddable]
class Address
{
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $city = null;
    
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $zipCode = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $country = null;
    
    #[ORM\Embedded(class: GeoCoordinates::class, columnPrefix: 'geo_')]
    private ?GeoCoordinates $geoCoords = null;
    
    public function __construct(
        ?string $street = null,
        ?string $city = null,
        ?string $zipCode = null,
        ?string $country = null,
        ?GeoCoordinates $geoCoords = null
    ) {
        $this->street = $street;
        $this->city = $city;
        $this->zipCode = $zipCode;
        $this->country = $country;
        $this->geoCoords = $geoCoords;
    }
    
    public function getStreet(): ?string
    {
        return $this->street;
    }
    
    public function setStreet(?string $street): static
    {
        $this->street = $street;
        return $this;
    }
    
    public function getCity(): ?string
    {
        return $this->city;
    }
    
    public function setCity(?string $city): static
    {
        $this->city = $city;
        return $this;
    }
    
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }
    
    public function setZipCode(?string $zipCode): static
    {
        $this->zipCode = $zipCode;
        return $this;
    }
    
    public function getCountry(): ?string
    {
        return $this->country;
    }
    
    public function setCountry(?string $country): static
    {
        $this->country = $country;
        return $this;
    }
    
    public function getGeoCoords(): ?GeoCoordinates
    {
        return $this->geoCoords;
    }
    
    public function setGeoCoords(?GeoCoordinates $geoCoords): static
    {
        $this->geoCoords = $geoCoords;
        return $this;
    }
    
    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->street,
            $this->city,
            $this->zipCode,
            $this->country,
        ]);
        
        return implode(', ', $parts);
    }
    
    public function hasCoordinates(): bool
    {
        return $this->geoCoords !== null && $this->geoCoords->isValid();
    }
}
