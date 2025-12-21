<?php

declare(strict_types=1);

namespace App\Legacy\HybridDataObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Hybrid contract with SOME properties managed by Doctrine, others manually.
 * This demonstrates partial ORM usage.
 */
#[ORM\Entity]
#[ORM\Table(name: 'hybrid_contracts')]
class HybridContract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 50, unique: true)]
    private string $reference;
    
    #[ORM\Column(type: Types::JSON)]
    private array $metadata = [];
    
    // NOT managed by Doctrine - handled manually
    private array $legacyClausesData = [];
    
    // NOT managed by Doctrine - external data
    private ?object $externalRating = null;
    
    // NOT managed by Doctrine - calculated property
    private ?float $calculatedRiskScore = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $signedAt = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $signedBy = null;
    
    // NOT managed by Doctrine - temporary processing data
    private array $validationErrors = [];
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getReference(): string
    {
        return $this->reference;
    }
    
    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }
    
    public function getMetadata(): array
    {
        return $this->metadata;
    }
    
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }
    
    public function getLegacyClausesData(): array
    {
        return $this->legacyClausesData;
    }
    
    public function setLegacyClausesData(array $legacyClausesData): self
    {
        $this->legacyClausesData = $legacyClausesData;
        return $this;
    }
    
    public function getExternalRating(): ?object
    {
        return $this->externalRating;
    }
    
    public function setExternalRating(?object $externalRating): self
    {
        $this->externalRating = $externalRating;
        return $this;
    }
    
    public function getCalculatedRiskScore(): ?float
    {
        return $this->calculatedRiskScore;
    }
    
    public function setCalculatedRiskScore(?float $calculatedRiskScore): self
    {
        $this->calculatedRiskScore = $calculatedRiskScore;
        return $this;
    }
    
    public function getSignedAt(): ?\DateTimeImmutable
    {
        return $this->signedAt;
    }
    
    public function setSignedAt(?\DateTimeImmutable $signedAt): self
    {
        $this->signedAt = $signedAt;
        return $this;
    }
    
    public function getSignedBy(): ?string
    {
        return $this->signedBy;
    }
    
    public function setSignedBy(?string $signedBy): self
    {
        $this->signedBy = $signedBy;
        return $this;
    }
    
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
    
    public function setValidationErrors(array $validationErrors): self
    {
        $this->validationErrors = $validationErrors;
        return $this;
    }
    
    public function addValidationError(string $field, string $error): self
    {
        $this->validationErrors[$field] = $error;
        return $this;
    }
}
