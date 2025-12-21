<?php

declare(strict_types=1);

namespace App\Legacy\DataObject;

/**
 * Risk factor - NO Doctrine annotations.
 */
class RiskFactor
{
    private string $category;
    
    private float $weight;
    
    private string $description;
    
    private array $dataPoints = [];
    
    public function __construct(string $category, float $weight, string $description = '')
    {
        $this->category = $category;
        $this->weight = $weight;
        $this->description = $description;
    }
    
    public function getCategory(): string
    {
        return $this->category;
    }
    
    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }
    
    public function getWeight(): float
    {
        return $this->weight;
    }
    
    public function setWeight(float $weight): static
    {
        $this->weight = $weight;
        return $this;
    }
    
    public function getDescription(): string
    {
        return $this->description;
    }
    
    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }
    
    public function getDataPoints(): array
    {
        return $this->dataPoints;
    }
    
    public function setDataPoints(array $dataPoints): static
    {
        $this->dataPoints = $dataPoints;
        return $this;
    }
    
    public function addDataPoint(string $key, mixed $value): static
    {
        $this->dataPoints[$key] = $value;
        return $this;
    }
}
