<?php

declare(strict_types=1);

namespace App\Traits;

use DateTimeImmutable;

/**
 * Provides timestamping capabilities for entities.
 */
trait TimestampableTrait
{
    private ?DateTimeImmutable $createdAt = null;
    
    private ?DateTimeImmutable $updatedAt = null;
    
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    
    public function updateTimestamps(): void
    {
        $now = new DateTimeImmutable();
        
        if ($this->createdAt === null) {
            $this->createdAt = $now;
        }
        
        $this->updatedAt = $now;
    }
}
