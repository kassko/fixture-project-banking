<?php

namespace App\Model\Product;

use App\Traits\IdentifiableTrait;
use App\Traits\TimestampableTrait;
use App\Traits\ValidatableTrait;

class Product
{
    use IdentifiableTrait;
    use TimestampableTrait;
    use ValidatableTrait;

    private ?string $name = null;
    private ?string $description = null;
    private ?float $price = null;
    private string $productType = 'generic';
    private bool $isActive = true;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getProductType(): string
    {
        return $this->productType;
    }

    public function setProductType(string $productType): self
    {
        $this->productType = $productType;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function validate(): void
    {
        $this->validationErrors = [];
        
        if (empty($this->name)) {
            $this->addValidationError('name', 'Product name is required');
        }
        
        if ($this->price !== null && $this->price < 0) {
            $this->addValidationError('price', 'Price cannot be negative');
        }
    }
}
