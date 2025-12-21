<?php

declare(strict_types=1);

namespace App\Entity;

use App\Abstract\AbstractCustomer;
use App\Enum\CustomerType;
use App\Model\Common\ContactInfo;
use App\Model\Insurance\RiskProfile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'customers')]
class Customer extends AbstractCustomer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 100)]
    protected string $firstName;
    
    #[ORM\Column(length: 100)]
    protected string $lastName;
    
    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    protected ?\DateTimeImmutable $birthDate = null;
    
    #[ORM\Column(length: 50, unique: true)]
    protected string $customerNumber;
    
    #[ORM\Column(type: 'string', enumType: CustomerType::class)]
    protected CustomerType $type;
    
    #[ORM\Column]
    protected bool $isActive = true;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $uuid = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $createdBy = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $updatedBy = null;
    
    #[ORM\Column]
    private int $version = 1;
    
    #[ORM\Column(type: Types::JSON)]
    private array $contactInfoData = [];
    
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $riskProfileData = null;
    
    /**
     * @var Collection<int, BankAccount>
     */
    #[ORM\OneToMany(targetEntity: BankAccount::class, mappedBy: 'customer')]
    private Collection $accounts;
    
    /**
     * @var Collection<int, InsurancePolicy>
     */
    #[ORM\OneToMany(targetEntity: InsurancePolicy::class, mappedBy: 'customer')]
    private Collection $insurancePolicies;
    
    private ?ContactInfo $contactInfo = null;
    
    private ?RiskProfile $riskProfile = null;
    
    public function __construct()
    {
        $this->accounts = new ArrayCollection();
        $this->insurancePolicies = new ArrayCollection();
        $this->generateUuid();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getContactInfo(): ?ContactInfo
    {
        return $this->contactInfo;
    }
    
    public function setContactInfo(ContactInfo $contactInfo): static
    {
        $this->contactInfo = $contactInfo;
        // Store as JSON for persistence
        $this->contactInfoData = [
            'email' => $contactInfo->getEmail(),
            'phones' => $contactInfo->getPhones(),
            'address' => [
                'street' => $contactInfo->getAddress()->getStreet(),
                'city' => $contactInfo->getAddress()->getCity(),
                'postalCode' => $contactInfo->getAddress()->getPostalCode(),
                'country' => $contactInfo->getAddress()->getCountry(),
                'state' => $contactInfo->getAddress()->getState(),
                'latitude' => $contactInfo->getAddress()->getLatitude(),
                'longitude' => $contactInfo->getAddress()->getLongitude(),
            ],
        ];
        return $this;
    }
    
    public function getRiskProfile(): ?RiskProfile
    {
        return $this->riskProfile;
    }
    
    public function setRiskProfile(?RiskProfile $riskProfile): static
    {
        $this->riskProfile = $riskProfile;
        if ($riskProfile !== null) {
            $this->riskProfileData = [
                'score' => $riskProfile->getScore(),
                'category' => $riskProfile->getCategory()->value,
                'factors' => $riskProfile->getFactors(),
                'lastAssessment' => $riskProfile->getLastAssessment()?->format('Y-m-d H:i:s'),
            ];
        } else {
            $this->riskProfileData = null;
        }
        return $this;
    }
    
    /**
     * @return Collection<int, BankAccount>
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }
    
    public function addAccount(BankAccount $account): static
    {
        if (!$this->accounts->contains($account)) {
            $this->accounts->add($account);
            $account->setCustomer($this);
        }
        return $this;
    }
    
    public function removeAccount(BankAccount $account): static
    {
        if ($this->accounts->removeElement($account)) {
            if ($account->getCustomer() === $this) {
                $account->setCustomer(null);
            }
        }
        return $this;
    }
    
    /**
     * @return Collection<int, InsurancePolicy>
     */
    public function getInsurancePolicies(): Collection
    {
        return $this->insurancePolicies;
    }
    
    public function addInsurancePolicy(InsurancePolicy $policy): static
    {
        if (!$this->insurancePolicies->contains($policy)) {
            $this->insurancePolicies->add($policy);
            $policy->setCustomer($this);
        }
        return $this;
    }
    
    public function removeInsurancePolicy(InsurancePolicy $policy): static
    {
        if ($this->insurancePolicies->removeElement($policy)) {
            if ($policy->getCustomer() === $this) {
                $policy->setCustomer(null);
            }
        }
        return $this;
    }

    /**
     * Get all products (accounts and insurance policies combined).
     */
    public function getProducts(): array
    {
        $products = [];
        
        foreach ($this->accounts as $account) {
            $products[] = $account;
        }
        
        foreach ($this->insurancePolicies as $policy) {
            $products[] = $policy;
        }
        
        return $products;
    }
}
