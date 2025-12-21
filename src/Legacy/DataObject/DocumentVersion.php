<?php

declare(strict_types=1);

namespace App\Legacy\DataObject;

use DateTimeImmutable;

/**
 * Document version - NO Doctrine annotations.
 */
class DocumentVersion
{
    private string $documentId;
    
    private int $version;
    
    private string $type;
    
    private string $path;
    
    private DateTimeImmutable $uploadedAt;
    
    private int $size;
    
    private string $hash;
    
    private string $uploadedBy;
    
    private array $metadata = [];
    
    public function __construct(
        string $documentId,
        int $version,
        string $type,
        string $path,
        int $size
    ) {
        $this->documentId = $documentId;
        $this->version = $version;
        $this->type = $type;
        $this->path = $path;
        $this->size = $size;
        $this->uploadedAt = new DateTimeImmutable();
        $this->hash = '';
        $this->uploadedBy = '';
    }
    
    public function getDocumentId(): string
    {
        return $this->documentId;
    }
    
    public function setDocumentId(string $documentId): static
    {
        $this->documentId = $documentId;
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
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }
    
    public function getPath(): string
    {
        return $this->path;
    }
    
    public function setPath(string $path): static
    {
        $this->path = $path;
        return $this;
    }
    
    public function getUploadedAt(): DateTimeImmutable
    {
        return $this->uploadedAt;
    }
    
    public function setUploadedAt(DateTimeImmutable $uploadedAt): static
    {
        $this->uploadedAt = $uploadedAt;
        return $this;
    }
    
    public function getSize(): int
    {
        return $this->size;
    }
    
    public function setSize(int $size): static
    {
        $this->size = $size;
        return $this;
    }
    
    public function getHash(): string
    {
        return $this->hash;
    }
    
    public function setHash(string $hash): static
    {
        $this->hash = $hash;
        return $this;
    }
    
    public function getUploadedBy(): string
    {
        return $this->uploadedBy;
    }
    
    public function setUploadedBy(string $uploadedBy): static
    {
        $this->uploadedBy = $uploadedBy;
        return $this;
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
}
