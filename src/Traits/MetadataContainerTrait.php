<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Provides metadata container capabilities for entities.
 */
trait MetadataContainerTrait
{
    private array $metadata = [];
    
    public function getMetadata(): array
    {
        return $this->metadata;
    }
    
    public function setMetadata(array $metadata): static
    {
        $this->metadata = $metadata;
        return $this;
    }
    
    public function addMetadata(string $key, mixed $value): static
    {
        $this->metadata[$key] = $value;
        return $this;
    }
    
    public function getMetadataValue(string $key): mixed
    {
        return $this->metadata[$key] ?? null;
    }
    
    public function hasMetadata(string $key): bool
    {
        return array_key_exists($key, $this->metadata);
    }
    
    public function removeMetadata(string $key): static
    {
        unset($this->metadata[$key]);
        return $this;
    }
}
