<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\PolicyStatus;
use App\Model\Insurance\Coverage;
use App\Traits\TimestampableTrait;
use App\Traits\VersionableTrait;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'insurance_policies')]
class InsurancePolicy
{
    use TimestampableTrait;
    use VersionableTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 50, unique: true)]
    private string $policyNumber;
    
    #[ORM\Column(type: 'string', enumType: PolicyStatus::class)]
    private PolicyStatus $status;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $effectiveDate;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $expirationDate = null;
    
    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private string $premium;
    
    #[ORM\Column(length: 3)]
    private string $currency = 'EUR';
    
    #[ORM\Column(type: Types::JSON)]
    private array $coverageData = [];
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;
    
    #[ORM\Column]
    private int $version = 1;
    
    #[ORM\Column(nullable: true)]
    private ?int $previousVersionId = null;
    
    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'insurancePolicies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;
    
    /**
     * @var Collection<int, Beneficiary>
     */
    #[ORM\OneToMany(targetEntity: Beneficiary::class, mappedBy: 'policy')]
    private Collection $beneficiaries;
    
    /**
     * @var array<Coverage>
     */
    private array $coverages = [];
    
    public function __construct()
    {
        $this->beneficiaries = new ArrayCollection();
        $this->effectiveDate = new DateTimeImmutable();
        $this->status = PolicyStatus::ACTIVE;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getPolicyNumber(): string
    {
        return $this->policyNumber;
    }
    
    public function setPolicyNumber(string $policyNumber): static
    {
        $this->policyNumber = $policyNumber;
        return $this;
    }
    
    public function getStatus(): PolicyStatus
    {
        return $this->status;
    }
    
    public function setStatus(PolicyStatus $status): static
    {
        $this->status = $status;
        return $this;
    }
    
    public function getEffectiveDate(): DateTimeImmutable
    {
        return $this->effectiveDate;
    }
    
    public function setEffectiveDate(DateTimeImmutable $effectiveDate): static
    {
        $this->effectiveDate = $effectiveDate;
        return $this;
    }
    
    public function getExpirationDate(): ?DateTimeImmutable
    {
        return $this->expirationDate;
    }
    
    public function setExpirationDate(?DateTimeImmutable $expirationDate): static
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }
    
    public function getPremium(): string
    {
        return $this->premium;
    }
    
    public function setPremium(string $premium): static
    {
        $this->premium = $premium;
        return $this;
    }
    
    public function getCurrency(): string
    {
        return $this->currency;
    }
    
    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;
        return $this;
    }
    
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }
    
    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;
        return $this;
    }
    
    /**
     * @return Collection<int, Beneficiary>
     */
    public function getBeneficiaries(): Collection
    {
        return $this->beneficiaries;
    }
    
    public function addBeneficiary(Beneficiary $beneficiary): static
    {
        if (!$this->beneficiaries->contains($beneficiary)) {
            $this->beneficiaries->add($beneficiary);
            $beneficiary->setPolicy($this);
        }
        return $this;
    }
    
    public function removeBeneficiary(Beneficiary $beneficiary): static
    {
        if ($this->beneficiaries->removeElement($beneficiary)) {
            if ($beneficiary->getPolicy() === $this) {
                $beneficiary->setPolicy(null);
            }
        }
        return $this;
    }
    
    /**
     * @return array<Coverage>
     */
    public function getCoverages(): array
    {
        return $this->coverages;
    }
    
    /**
     * @param array<Coverage> $coverages
     */
    public function setCoverages(array $coverages): static
    {
        $this->coverages = $coverages;
        // Store as JSON for persistence (simplified)
        $this->coverageData = array_map(function (Coverage $coverage) {
            return [
                'type' => $coverage->getCoverageType(),
                'limit' => $coverage->getCoverageLimit()->getAmount(),
                'currency' => $coverage->getCoverageLimit()->getCurrency(),
                'active' => $coverage->isActive(),
            ];
        }, $coverages);
        return $this;
    }
    
    public function addCoverage(Coverage $coverage): static
    {
        $this->coverages[] = $coverage;
        $this->setCoverages($this->coverages);
        return $this;
    }
}
