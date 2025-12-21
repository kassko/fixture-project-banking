<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\TimestampableTrait;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'beneficiaries')]
class Beneficiary
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 100)]
    private string $firstName;
    
    #[ORM\Column(length: 100)]
    private string $lastName;
    
    #[ORM\Column(length: 50)]
    private string $relationship;
    
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private string $percentage = '100.00';
    
    #[ORM\Column]
    private bool $isPrimary = false;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;
    
    #[ORM\ManyToOne(targetEntity: InsurancePolicy::class, inversedBy: 'beneficiaries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?InsurancePolicy $policy = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    
    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }
    
    public function getLastName(): string
    {
        return $this->lastName;
    }
    
    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }
    
    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
    
    public function getRelationship(): string
    {
        return $this->relationship;
    }
    
    public function setRelationship(string $relationship): static
    {
        $this->relationship = $relationship;
        return $this;
    }
    
    public function getPercentage(): float
    {
        return (float) $this->percentage;
    }
    
    public function setPercentage(float $percentage): static
    {
        if ($percentage < 0 || $percentage > 100) {
            throw new \InvalidArgumentException('Percentage must be between 0 and 100');
        }
        $this->percentage = (string) $percentage;
        return $this;
    }
    
    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }
    
    public function setIsPrimary(bool $isPrimary): static
    {
        $this->isPrimary = $isPrimary;
        return $this;
    }
    
    public function getPolicy(): ?InsurancePolicy
    {
        return $this->policy;
    }
    
    public function setPolicy(?InsurancePolicy $policy): static
    {
        $this->policy = $policy;
        return $this;
    }
}
