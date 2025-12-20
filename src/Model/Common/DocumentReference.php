<?php

declare(strict_types=1);

namespace App\Model\Common;

use DateTimeImmutable;

/**
 * Represents a reference to an external document.
 */
class DocumentReference
{
    public function __construct(
        private string $documentId,
        private string $documentType,
        private string $url,
        private DateTimeImmutable $uploadedAt,
        private ?string $description = null,
    ) {
    }
    
    public function getDocumentId(): string
    {
        return $this->documentId;
    }
    
    public function getDocumentType(): string
    {
        return $this->documentType;
    }
    
    public function getUrl(): string
    {
        return $this->url;
    }
    
    public function getUploadedAt(): DateTimeImmutable
    {
        return $this->uploadedAt;
    }
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
