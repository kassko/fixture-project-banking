<?php

declare(strict_types=1);

namespace App\Entity\Customer;

use App\Abstract\AbstractCustomer;
use App\Model\Financial\MoneyAmount;
use App\Traits\SoftDeletableTrait;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Individual customer entity with soft delete capability.
 */
#[ORM\Entity]
#[ORM\Table(name: 'individual_customers')]
class IndividualCustomer extends AbstractCustomer
{
    use SoftDeletableTrait;
    
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
    
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $ssn = null;
    
    #[ORM\Column(length: 150, nullable: true)]
    private ?string $occupation = null;
    
    #[ORM\Embedded(class: MoneyAmount::class, columnPrefix: 'income_')]
    private ?MoneyAmount $income = null;
    
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $maritalStatus = null;
    
    public function __construct()
    {
        $this->generateUuid();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getSsn(): ?string
    {
        return $this->ssn;
    }
    
    public function setSsn(?string $ssn): static
    {
        $this->ssn = $ssn;
        return $this;
    }
    
    public function getOccupation(): ?string
    {
        return $this->occupation;
    }
    
    public function setOccupation(?string $occupation): static
    {
        $this->occupation = $occupation;
        return $this;
    }
    
    public function getIncome(): ?MoneyAmount
    {
        return $this->income;
    }
    
    public function setIncome(?MoneyAmount $income): static
    {
        $this->income = $income;
        return $this;
    }
    
    public function getMaritalStatus(): ?string
    {
        return $this->maritalStatus;
    }
    
    public function setMaritalStatus(?string $maritalStatus): static
    {
        $this->maritalStatus = $maritalStatus;
        return $this;
    }
}
