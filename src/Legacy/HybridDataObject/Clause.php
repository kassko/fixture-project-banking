<?php

declare(strict_types=1);

namespace App\Legacy\HybridDataObject;

/**
 * Contract clause - Legacy nested object (NO Doctrine).
 */
class Clause
{
    private string $id;
    
    private string $title;
    
    private string $content;
    
    private int $order;
    
    private bool $isMandatory = false;
    
    public function __construct(string $id, string $title, string $content, int $order = 0)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->order = $order;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getTitle(): string
    {
        return $this->title;
    }
    
    public function getContent(): string
    {
        return $this->content;
    }
    
    public function getOrder(): int
    {
        return $this->order;
    }
    
    public function isMandatory(): bool
    {
        return $this->isMandatory;
    }
    
    public function setIsMandatory(bool $isMandatory): static
    {
        $this->isMandatory = $isMandatory;
        return $this;
    }
}
