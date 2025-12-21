<?php

declare(strict_types=1);

namespace App\Entity;

use App\Abstract\AbstractPerson;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Employee entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'employees')]
class Employee extends AbstractPerson
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
    protected ?DateTimeImmutable $birthDate = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;
    
    #[ORM\Column(length: 50, unique: true)]
    private string $employeeNumber;
    
    #[ORM\Column(length: 100)]
    private string $department;
    
    #[ORM\Column(length: 100)]
    private string $position;
    
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $hireDate;
    
    public function __construct()
    {
        $this->generateUuid();
        $this->hireDate = new DateTimeImmutable();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getEmployeeNumber(): string
    {
        return $this->employeeNumber;
    }
    
    public function setEmployeeNumber(string $employeeNumber): static
    {
        $this->employeeNumber = $employeeNumber;
        return $this;
    }
    
    public function getDepartment(): string
    {
        return $this->department;
    }
    
    public function setDepartment(string $department): static
    {
        $this->department = $department;
        return $this;
    }
    
    public function getPosition(): string
    {
        return $this->position;
    }
    
    public function setPosition(string $position): static
    {
        $this->position = $position;
        return $this;
    }
    
    public function getHireDate(): DateTimeImmutable
    {
        return $this->hireDate;
    }
    
    public function setHireDate(DateTimeImmutable $hireDate): static
    {
        $this->hireDate = $hireDate;
        return $this;
    }
}
