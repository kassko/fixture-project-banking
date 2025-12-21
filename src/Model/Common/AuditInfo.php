<?php

declare(strict_types=1);

namespace App\Model\Common;

use DateTimeImmutable;

/**
 * Represents audit information for an entity.
 */
class AuditInfo
{
    public function __construct(
        private string $createdBy,
        private DateTimeImmutable $createdAt,
        private ?string $updatedBy = null,
        private ?DateTimeImmutable $updatedAt = null,
        private int $version = 1,
    ) {
    }
    
    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }
    
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }
    
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
    
    public function getVersion(): int
    {
        return $this->version;
    }
    
    public function recordUpdate(string $updatedBy): self
    {
        return new self(
            $this->createdBy,
            $this->createdAt,
            $updatedBy,
            new DateTimeImmutable(),
            $this->version + 1
        );
    }
}
