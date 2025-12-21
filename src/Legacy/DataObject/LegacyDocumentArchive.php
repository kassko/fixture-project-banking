<?php

declare(strict_types=1);

namespace App\Legacy\DataObject;

use DateTimeImmutable;

/**
 * Legacy document archive - NO Doctrine annotations.
 */
class LegacyDocumentArchive
{
    private string $archiveId;
    
    private string $customerId;
    
    /** @var DocumentVersion[] */
    private array $documents = [];
    
    private ?\DateInterval $retention = null;
    
    private DateTimeImmutable $createdAt;
    
    private array $metadata = [];
    
    public function __construct(string $archiveId, string $customerId)
    {
        $this->archiveId = $archiveId;
        $this->customerId = $customerId;
        $this->createdAt = new DateTimeImmutable();
    }
    
    public function getArchiveId(): string
    {
        return $this->archiveId;
    }
    
    public function setArchiveId(string $archiveId): static
    {
        $this->archiveId = $archiveId;
        return $this;
    }
    
    public function getCustomerId(): string
    {
        return $this->customerId;
    }
    
    public function setCustomerId(string $customerId): static
    {
        $this->customerId = $customerId;
        return $this;
    }
    
    public function getDocuments(): array
    {
        return $this->documents;
    }
    
    public function setDocuments(array $documents): static
    {
        $this->documents = $documents;
        return $this;
    }
    
    public function addDocument(DocumentVersion $document): static
    {
        $this->documents[] = $document;
        return $this;
    }
    
    public function getDocument(string $id): ?DocumentVersion
    {
        foreach ($this->documents as $doc) {
            if ($doc->getDocumentId() === $id) {
                return $doc;
            }
        }
        return null;
    }
    
    public function getRetention(): ?\DateInterval
    {
        return $this->retention;
    }
    
    public function setRetention(?\DateInterval $retention): static
    {
        $this->retention = $retention;
        return $this;
    }
    
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    public function getMetadata(): array
    {
        return $this->metadata;
    }
    
    public function setMetadata(array $metadata): static
    {
        $this->metadata = $metadata;
        return $this;
    }
    
    public function isExpired(): bool
    {
        if ($this->retention === null) {
            return false;
        }
        
        $expirationDate = $this->createdAt->add($this->retention);
        return new DateTimeImmutable() > $expirationDate;
    }
}
