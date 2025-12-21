<?php

declare(strict_types=1);

namespace App\Service\Compliance;

use App\Entity\Customer;
use App\Context\UnifiedContext;

class KycValidator
{
    private const REQUIRED_DOCUMENTS = ['ID', 'PROOF_OF_ADDRESS', 'PROOF_OF_INCOME'];
    private const MIN_AGE = 18;
    private const MAX_AGE = 100;

    public function validate(Customer $customer, UnifiedContext $context): array
    {
        $issues = [];
        $validations = [];
        $score = 100;

        // Validate identity
        $identityCheck = $this->validateIdentity($customer);
        $validations['identity'] = $identityCheck;
        if (!$identityCheck['passed']) {
            $issues[] = $identityCheck['issue'];
            $score -= 30;
        }

        // Validate address
        $addressCheck = $this->validateAddress($customer);
        $validations['address'] = $addressCheck;
        if (!$addressCheck['passed']) {
            $issues[] = $addressCheck['issue'];
            $score -= 20;
        }

        // Validate documents
        $documentsCheck = $this->validateDocuments($customer);
        $validations['documents'] = $documentsCheck;
        if (!$documentsCheck['passed']) {
            $issues[] = $documentsCheck['issue'];
            $score -= 30;
        }

        // Validate age
        $ageCheck = $this->validateAge($customer);
        $validations['age'] = $ageCheck;
        if (!$ageCheck['passed']) {
            $issues[] = $ageCheck['issue'];
            $score -= 20;
        }

        $status = empty($issues) ? 'COMPLIANT' : (count($issues) >= 3 ? 'NON_COMPLIANT' : 'PARTIAL');

        return [
            'status' => $status,
            'score' => max(0, $score),
            'validations' => $validations,
            'issues' => $issues,
            'last_review_date' => $context->getTemporalContext()->getCurrentDateTime()->format('Y-m-d'),
        ];
    }

    private function validateIdentity(Customer $customer): array
    {
        // Simulate identity validation
        $hasFirstName = !empty($customer->getFirstName());
        $hasLastName = !empty($customer->getLastName());
        
        $contactInfo = $customer->getContactInfo();
        $hasEmail = $contactInfo !== null && !empty($contactInfo->getEmail());

        $passed = $hasFirstName && $hasLastName && $hasEmail;

        return [
            'passed' => $passed,
            'checked_fields' => ['first_name', 'last_name', 'email'],
            'issue' => $passed ? null : 'Missing required identity information',
        ];
    }

    private function validateAddress(Customer $customer): array
    {
        // Simulate address validation
        $contactInfo = $customer->getContactInfo();
        $hasAddress = $contactInfo !== null && $contactInfo->getAddress() !== null;

        return [
            'passed' => $hasAddress,
            'checked_fields' => ['address'],
            'issue' => $hasAddress ? null : 'No address on file',
        ];
    }

    private function validateDocuments(Customer $customer): array
    {
        // Simulate document validation based on customer KYC status
        // In a real implementation, this would check a documents table
        $kycValidated = $customer->isKycValidated();
        
        // For simulation purposes, assume validated customers have all documents
        $documents = $kycValidated ? self::REQUIRED_DOCUMENTS : [];
        
        $missingDocs = [];
        foreach (self::REQUIRED_DOCUMENTS as $requiredDoc) {
            if (!in_array($requiredDoc, $documents)) {
                $missingDocs[] = $requiredDoc;
            }
        }

        $passed = empty($missingDocs);

        return [
            'passed' => $passed,
            'required_documents' => self::REQUIRED_DOCUMENTS,
            'provided_documents' => $documents,
            'missing_documents' => $missingDocs,
            'issue' => $passed ? null : 'Missing documents: ' . implode(', ', $missingDocs),
        ];
    }

    private function validateAge(Customer $customer): array
    {
        $age = $customer->getAge();
        
        if ($age === null) {
            return [
                'passed' => false,
                'issue' => 'Birth date not provided',
            ];
        }
        
        $passed = $age >= self::MIN_AGE && $age <= self::MAX_AGE;
        
        return [
            'passed' => $passed,
            'age' => $age,
            'min_age' => self::MIN_AGE,
            'max_age' => self::MAX_AGE,
            'issue' => $passed ? null : "Age $age is outside allowed range",
        ];
    }
}
