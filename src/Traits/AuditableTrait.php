<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Provides audit trail capabilities for entities.
 */
trait AuditableTrait
{
    private ?string $createdBy = null;
    
    private ?string $updatedBy = null;
    
    private int $version = 1;
    
    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }
    
    public function setCreatedBy(string $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }
    
    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }
    
    public function setUpdatedBy(string $updatedBy): static
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }
    
    public function getVersion(): int
    {
        return $this->version;
    }
    
    public function setVersion(int $version): static
    {
        $this->version = $version;
        return $this;
    }
    
    public function incrementVersion(): static
    {
        $this->version++;
        return $this;
    }
}
