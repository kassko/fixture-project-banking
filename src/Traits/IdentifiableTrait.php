<?php

declare(strict_types=1);

namespace App\Traits;

use Symfony\Component\Uid\Uuid;

/**
 * Provides identification capabilities with both integer ID and UUID.
 */
trait IdentifiableTrait
{
    private ?int $id = null;
    
    private ?string $uuid = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getUuid(): ?string
    {
        return $this->uuid;
    }
    
    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;
        return $this;
    }
    
    public function generateUuid(): static
    {
        if ($this->uuid === null) {
            $this->uuid = Uuid::v4()->toRfc4122();
        }
        return $this;
    }
}
