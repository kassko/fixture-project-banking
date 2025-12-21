<?php

declare(strict_types=1);

namespace App\Model\Insurance;

use App\Model\Common\Address;
use App\Model\Common\DocumentReference;
use DateTimeImmutable;

/**
 * Represents detailed information about an insurance claim with deep nesting.
 * Deep nesting: ClaimDetails → DamageAssessment → MoneyAmount + DocumentReference[]
 */
class ClaimDetails
{
    private string $description;
    
    private DateTimeImmutable $incidentDate;
    
    private Address $incidentLocation;
    
    private array $witnesses = [];
    
    private DamageAssessment $damageAssessment;
    
    private ?DocumentReference $policeReport = null;
    
    /** @var DocumentReference[] */
    private array $photos = [];
    
    public function __construct(
        string $description,
        DateTimeImmutable $incidentDate,
        Address $incidentLocation,
        DamageAssessment $damageAssessment
    ) {
        $this->description = $description;
        $this->incidentDate = $incidentDate;
        $this->incidentLocation = $incidentLocation;
        $this->damageAssessment = $damageAssessment;
    }
    
    public function getDescription(): string
    {
        return $this->description;
    }
    
    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }
    
    public function getIncidentDate(): DateTimeImmutable
    {
        return $this->incidentDate;
    }
    
    public function setIncidentDate(DateTimeImmutable $incidentDate): static
    {
        $this->incidentDate = $incidentDate;
        return $this;
    }
    
    public function getIncidentLocation(): Address
    {
        return $this->incidentLocation;
    }
    
    public function setIncidentLocation(Address $incidentLocation): static
    {
        $this->incidentLocation = $incidentLocation;
        return $this;
    }
    
    public function getWitnesses(): array
    {
        return $this->witnesses;
    }
    
    public function setWitnesses(array $witnesses): static
    {
        $this->witnesses = $witnesses;
        return $this;
    }
    
    public function addWitness(array $witness): static
    {
        $this->witnesses[] = $witness;
        return $this;
    }
    
    public function getDamageAssessment(): DamageAssessment
    {
        return $this->damageAssessment;
    }
    
    public function setDamageAssessment(DamageAssessment $damageAssessment): static
    {
        $this->damageAssessment = $damageAssessment;
        return $this;
    }
    
    public function getPoliceReport(): ?DocumentReference
    {
        return $this->policeReport;
    }
    
    public function setPoliceReport(?DocumentReference $policeReport): static
    {
        $this->policeReport = $policeReport;
        return $this;
    }
    
    public function getPhotos(): array
    {
        return $this->photos;
    }
    
    public function setPhotos(array $photos): static
    {
        $this->photos = $photos;
        return $this;
    }
    
    public function addPhoto(DocumentReference $photo): static
    {
        $this->photos[] = $photo;
        return $this;
    }
}
