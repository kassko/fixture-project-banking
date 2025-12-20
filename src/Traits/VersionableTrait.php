<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Provides versioning capabilities for entities.
 */
trait VersionableTrait
{
    private int $version = 1;
    
    private ?int $previousVersionId = null;
    
    public function getVersion(): int
    {
        return $this->version;
    }
    
    public function setVersion(int $version): static
    {
        $this->version = $version;
        return $this;
    }
    
    public function getPreviousVersionId(): ?int
    {
        return $this->previousVersionId;
    }
    
    public function setPreviousVersionId(?int $previousVersionId): static
    {
        $this->previousVersionId = $previousVersionId;
        return $this;
    }
    
    public function createNewVersion(?int $previousId = null): static
    {
        $this->version++;
        $this->previousVersionId = $previousId;
        return $this;
    }
}
