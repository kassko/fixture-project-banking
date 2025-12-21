<?php

declare(strict_types=1);

namespace App\Model\Common;

use Doctrine\ORM\Mapping as ORM;

/**
 * Geographic coordinates embeddable value object.
 */
#[ORM\Embeddable]
class GeoCoordinates
{
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;
    
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;
    
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $accuracy = null;
    
    public function __construct(?float $latitude = null, ?float $longitude = null, ?float $accuracy = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->accuracy = $accuracy;
    }
    
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }
    
    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }
    
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }
    
    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }
    
    public function getAccuracy(): ?float
    {
        return $this->accuracy;
    }
    
    public function setAccuracy(?float $accuracy): static
    {
        $this->accuracy = $accuracy;
        return $this;
    }
    
    public function isValid(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }
    
    public function toString(): string
    {
        if (!$this->isValid()) {
            return 'Invalid coordinates';
        }
        
        return sprintf('%.6f, %.6f', $this->latitude, $this->longitude);
    }
}
