<?php

declare(strict_types=1);

namespace App\Entity\Customer;

use App\Abstract\AbstractCustomer;
use App\Model\Financial\MoneyAmount;
use App\Traits\VersionableTrait;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Corporate customer entity with version tracking.
 */
#[ORM\Entity]
#[ORM\Table(name: 'corporate_customers')]
class CorporateCustomer extends AbstractCustomer
{
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
    
    #[ORM\Column]
    private int $version = 1;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $previousHash = null;
    
    #[ORM\Column(length: 200)]
    private string $companyName;
    
    #[ORM\Column(length: 14, nullable: true)]
    private ?string $siret = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $legalForm = null;
    
    #[ORM\Embedded(class: MoneyAmount::class, columnPrefix: 'capital_')]
    private ?MoneyAmount $capital = null;
    
    #[ORM\Column(type: Types::JSON)]
    private array $representatives = [];
    
    public function __construct()
    {
        $this->generateUuid();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getCompanyName(): string
    {
        return $this->companyName;
    }
    
    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;
        return $this;
    }
    
    public function getSiret(): ?string
    {
        return $this->siret;
    }
    
    public function setSiret(?string $siret): static
    {
        $this->siret = $siret;
        return $this;
    }
    
    public function getLegalForm(): ?string
    {
        return $this->legalForm;
    }
    
    public function setLegalForm(?string $legalForm): static
    {
        $this->legalForm = $legalForm;
        return $this;
    }
    
    public function getCapital(): ?MoneyAmount
    {
        return $this->capital;
    }
    
    public function setCapital(?MoneyAmount $capital): static
    {
        $this->capital = $capital;
        return $this;
    }
    
    public function getRepresentatives(): array
    {
        return $this->representatives;
    }
    
    public function setRepresentatives(array $representatives): static
    {
        $this->representatives = $representatives;
        return $this;
    }
    
    public function addRepresentative(array $representative): static
    {
        $this->representatives[] = $representative;
        return $this;
    }
}
