<?php

declare(strict_types=1);

namespace App\Service\Onboarding\DocumentRequirement;

class DocumentRequirementResolver
{
    public function resolve(
        string $customerType,
        string $targetProduct,
        string $country,
        array $existingDocuments
    ): array {
        $required = [];
        
        // Base documents for all customers
        $required[] = [
            'type' => DocumentType::PASSPORT,
            'alternatives' => [DocumentType::NATIONAL_ID, DocumentType::DRIVER_LICENSE],
            'description' => 'Valid ID document',
        ];
        
        $required[] = [
            'type' => DocumentType::PROOF_OF_ADDRESS,
            'description' => 'Proof of address (less than 3 months old)',
        ];
        
        // Corporate-specific documents
        if ($customerType === 'CORPORATE') {
            $required[] = [
                'type' => DocumentType::COMPANY_REGISTRATION,
                'description' => 'Company registration certificate',
            ];
            
            $required[] = [
                'type' => DocumentType::COMPANY_STATUTES,
                'description' => 'Company statutes',
            ];
            
            $required[] = [
                'type' => DocumentType::BENEFICIAL_OWNER_DECLARATION,
                'description' => 'Beneficial owner declaration',
            ];
        }
        
        // Product-specific requirements
        if (in_array($targetProduct, ['LOAN_PERSONAL', 'LOAN_HOME', 'PREMIUM_ACCOUNT'])) {
            $required[] = [
                'type' => DocumentType::INCOME_PROOF,
                'description' => 'Proof of income (last 3 months)',
            ];
            
            if ($targetProduct === 'LOAN_HOME') {
                $required[] = [
                    'type' => DocumentType::TAX_RETURN,
                    'description' => 'Last tax return',
                ];
            }
        }
        
        // Filter out already provided documents
        $required = array_filter($required, function ($doc) use ($existingDocuments) {
            return !in_array($doc['type'], $existingDocuments);
        });
        
        return array_values($required);
    }
}
