<?php

declare(strict_types=1);

namespace App\Traits;

use DateTimeImmutable;

/**
 * Provides soft deletion capabilities for entities.
 */
trait SoftDeletableTrait
{
    private ?DateTimeImmutable $deletedAt = null;
    
    private bool $isDeleted = false;
    
    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }
    
    public function setDeletedAt(?DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;
        $this->isDeleted = $deletedAt !== null;
        return $this;
    }
    
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }
    
    public function softDelete(): static
    {
        $this->deletedAt = new DateTimeImmutable();
        $this->isDeleted = true;
        return $this;
    }
    
    public function restore(): static
    {
        $this->deletedAt = null;
        $this->isDeleted = false;
        return $this;
    }
}
