<?php

declare(strict_types=1);

namespace App\Abstract;

use App\Traits\IdentifiableTrait;
use App\Traits\TimestampableTrait;

/**
 * Base abstract class for all person entities.
 */
abstract class AbstractPerson
{
    use IdentifiableTrait;
    use TimestampableTrait;
    
    protected string $firstName;
    
    protected string $lastName;
    
    protected ?\DateTimeImmutable $birthDate = null;
    
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
    
    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birthDate;
    }
    
    public function setBirthDate(?\DateTimeImmutable $birthDate): static
    {
        $this->birthDate = $birthDate;
        return $this;
    }
    
    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
    
    public function getAge(): ?int
    {
        if ($this->birthDate === null) {
            return null;
        }
        
        return (new \DateTime())->diff($this->birthDate->toDateTime())->y;
    }
}
