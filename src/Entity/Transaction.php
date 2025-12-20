<?php

declare(strict_types=1);

namespace App\Entity;

use App\Abstract\AbstractTransaction;
use App\Enum\TransactionType;
use App\Model\Financial\MoneyAmount;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'transactions')]
class Transaction extends AbstractTransaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 100, unique: true)]
    protected string $transactionId;
    
    #[ORM\Column(type: 'string', enumType: TransactionType::class)]
    protected TransactionType $type;
    
    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private string $amountValue;
    
    #[ORM\Column(length: 3)]
    private string $currency;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    protected DateTimeImmutable $transactionDate;
    
    #[ORM\Column(length: 50)]
    protected string $status = 'pending';
    
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $description = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    protected ?string $reference = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;
    
    #[ORM\ManyToOne(targetEntity: BankAccount::class, inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BankAccount $account = null;
    
    public function __construct()
    {
        $this->transactionDate = new DateTimeImmutable();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getAmount(): MoneyAmount
    {
        return new MoneyAmount((float) $this->amountValue, $this->currency);
    }
    
    public function setAmount(MoneyAmount $amount): static
    {
        $this->amountValue = (string) $amount->getAmount();
        $this->currency = $amount->getCurrency();
        $this->amount = $amount;
        return $this;
    }
    
    public function getAccount(): ?BankAccount
    {
        return $this->account;
    }
    
    public function setAccount(?BankAccount $account): static
    {
        $this->account = $account;
        return $this;
    }
}
