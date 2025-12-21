<?php

declare(strict_types=1);

namespace App\Abstract;

use App\Enum\TransactionType;
use App\Model\Financial\MoneyAmount;
use App\Traits\TimestampableTrait;
use DateTimeImmutable;

/**
 * Base abstract class for all transaction entities.
 */
abstract class AbstractTransaction
{
    use TimestampableTrait;
    
    protected string $transactionId;
    
    protected TransactionType $type;
    
    protected MoneyAmount $amount;
    
    protected DateTimeImmutable $transactionDate;
    
    protected string $status = 'pending';
    
    protected ?string $description = null;
    
    protected ?string $reference = null;
    
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }
    
    public function setTransactionId(string $transactionId): static
    {
        $this->transactionId = $transactionId;
        return $this;
    }
    
    public function getType(): TransactionType
    {
        return $this->type;
    }
    
    public function setType(TransactionType $type): static
    {
        $this->type = $type;
        return $this;
    }
    
    public function getAmount(): MoneyAmount
    {
        return $this->amount;
    }
    
    public function setAmount(MoneyAmount $amount): static
    {
        $this->amount = $amount;
        return $this;
    }
    
    public function getTransactionDate(): DateTimeImmutable
    {
        return $this->transactionDate;
    }
    
    public function setTransactionDate(DateTimeImmutable $transactionDate): static
    {
        $this->transactionDate = $transactionDate;
        return $this;
    }
    
    public function getStatus(): string
    {
        return $this->status;
    }
    
    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }
    
    public function getReference(): ?string
    {
        return $this->reference;
    }
    
    public function setReference(?string $reference): static
    {
        $this->reference = $reference;
        return $this;
    }
    
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
    
    public function complete(): static
    {
        $this->status = 'completed';
        return $this;
    }
    
    public function fail(string $reason): static
    {
        $this->status = 'failed';
        $this->description = ($this->description ?? '') . ' | Failed: ' . $reason;
        return $this;
    }
}
