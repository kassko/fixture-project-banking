<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Provides identification capabilities with UUID and external ID.
 */
trait IdentifiableTrait
{
    private ?string $uuid = null;
    
    private ?string $externalId = null;
    
    public function getUuid(): ?string
    {
        return $this->uuid;
    }
    
    public function setUuid(?string $uuid): static
    {
        $this->uuid = $uuid;
        return $this;
    }
    
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
    
    public function setExternalId(?string $externalId): static
    {
        $this->externalId = $externalId;
        return $this;
    }
    
    public function generateUuid(): static
    {
        if ($this->uuid === null) {
            // Generate a UUID v4 using random_int for better security
            $this->uuid = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                random_int(0, 0xffff),
                random_int(0, 0xffff),
                random_int(0, 0xffff),
                random_int(0, 0x0fff) | 0x4000,
                random_int(0, 0x3fff) | 0x8000,
                random_int(0, 0xffff),
                random_int(0, 0xffff),
                random_int(0, 0xffff)
            );
        }
        return $this;
    }
}
