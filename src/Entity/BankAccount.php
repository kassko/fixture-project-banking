<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\AccountType;
use App\Model\Financial\MoneyAmount;
use App\Traits\TimestampableTrait;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'bank_accounts')]
class BankAccount
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 50, unique: true)]
    private string $accountNumber;
    
    #[ORM\Column(type: 'string', enumType: AccountType::class)]
    private AccountType $type;
    
    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private string $balance = '0.00';
    
    #[ORM\Column(length: 3)]
    private string $currency = 'EUR';
    
    #[ORM\Column]
    private bool $isActive = true;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;
    
    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;
    
    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'account')]
    private Collection $transactions;
    
    #[ORM\Column(type: Types::JSON)]
    private array $interestRateData = [];
    
    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }
    
    public function setAccountNumber(string $accountNumber): static
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }
    
    public function getType(): AccountType
    {
        return $this->type;
    }
    
    public function setType(AccountType $type): static
    {
        $this->type = $type;
        return $this;
    }
    
    public function getBalance(): MoneyAmount
    {
        return new MoneyAmount((float) $this->balance, $this->currency);
    }
    
    public function setBalance(MoneyAmount $balance): static
    {
        $this->balance = (string) $balance->getAmount();
        $this->currency = $balance->getCurrency();
        return $this;
    }
    
    public function getCurrency(): string
    {
        return $this->currency;
    }
    
    public function isActive(): bool
    {
        return $this->isActive;
    }
    
    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
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
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }
    
    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setAccount($this);
        }
        return $this;
    }
    
    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            if ($transaction->getAccount() === $this) {
                $transaction->setAccount(null);
            }
        }
        return $this;
    }
    
    public function deposit(MoneyAmount $amount): static
    {
        $this->setBalance($this->getBalance()->add($amount));
        return $this;
    }
    
    public function withdraw(MoneyAmount $amount): static
    {
        $newBalance = $this->getBalance()->subtract($amount);
        if ($newBalance->isNegative() && $this->type !== AccountType::LOAN) {
            throw new \LogicException('Insufficient funds');
        }
        $this->setBalance($newBalance);
        return $this;
    }
}
