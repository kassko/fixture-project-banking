<?php

declare(strict_types=1);

namespace App\Entity\Customer;

use App\Abstract\AbstractCustomer;
use App\Traits\SoftDeletableTrait;
use App\Traits\VersionableTrait;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Premium customer with VIP services - demonstrates combined traits and 3-level inheritance.
 */
#[ORM\Entity]
#[ORM\Table(name: 'premium_customers')]
class PremiumCustomer extends AbstractCustomer
{
    use SoftDeletableTrait;
    use VersionableTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 100)]
    protected string $firstName;
    
    #[ORM\Column(length: 100)]
    protected string $lastName;
    
    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    protected ?DateTimeImmutable $birthDate = null;
    
    #[ORM\Column(length: 50, unique: true)]
    protected string $customerNumber;
    
    #[ORM\Column]
    protected bool $isActive = true;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;
    
    #[ORM\Column]
    private bool $isDeleted = false;
    
    #[ORM\Column]
    private int $version = 1;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $previousHash = null;
    
    #[ORM\Column(length: 50)]
    private string $vipLevel = 'BRONZE';
    
    #[ORM\Column(length: 150, nullable: true)]
    private ?string $dedicatedAdvisor = null;
    
    #[ORM\Column(type: Types::JSON)]
    private array $specialRates = [];
    
    #[ORM\Column(type: Types::JSON)]
    private array $perks = [];
    
    public function __construct()
    {
        $this->generateUuid();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getVipLevel(): string
    {
        return $this->vipLevel;
    }
    
    public function setVipLevel(string $vipLevel): static
    {
        $this->vipLevel = $vipLevel;
        return $this;
    }
    
    public function getDedicatedAdvisor(): ?string
    {
        return $this->dedicatedAdvisor;
    }
    
    public function setDedicatedAdvisor(?string $dedicatedAdvisor): static
    {
        $this->dedicatedAdvisor = $dedicatedAdvisor;
        return $this;
    }
    
    public function getSpecialRates(): array
    {
        return $this->specialRates;
    }
    
    public function setSpecialRates(array $specialRates): static
    {
        $this->specialRates = $specialRates;
        return $this;
    }
    
    public function addSpecialRate(string $productType, float $rate): static
    {
        $this->specialRates[$productType] = $rate;
        return $this;
    }
    
    public function getPerks(): array
    {
        return $this->perks;
    }
    
    public function setPerks(array $perks): static
    {
        $this->perks = $perks;
        return $this;
    }
    
    public function addPerk(string $perk): static
    {
        if (!in_array($perk, $this->perks, true)) {
            $this->perks[] = $perk;
        }
        return $this;
    }
}
