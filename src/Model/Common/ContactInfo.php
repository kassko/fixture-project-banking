<?php

declare(strict_types=1);

namespace App\Model\Common;

/**
 * Represents contact information with multiple phone numbers.
 */
class ContactInfo
{
    /**
     * @param array<array{type: string, number: string}> $phones
     */
    public function __construct(
        private string $email,
        private array $phones,
        private Address $address,
    ) {
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
    
    /**
     * @return array<array{type: string, number: string}>
     */
    public function getPhones(): array
    {
        return $this->phones;
    }
    
    public function getAddress(): Address
    {
        return $this->address;
    }
    
    public function getPrimaryPhone(): ?string
    {
        if (empty($this->phones)) {
            return null;
        }
        
        // Try to find mobile first
        foreach ($this->phones as $phone) {
            if ($phone['type'] === 'mobile') {
                return $phone['number'];
            }
        }
        
        // Return first phone if no mobile
        return $this->phones[0]['number'];
    }
    
    public function addPhone(string $type, string $number): void
    {
        $this->phones[] = ['type' => $type, 'number' => $number];
    }
}
