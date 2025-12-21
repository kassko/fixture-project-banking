<?php

declare(strict_types=1);

namespace App\DataSource;

/**
 * Compliance data source - returns key-value arrays for hydration.
 */
class ComplianceDataSource
{
    /**
     * Get KYC (Know Your Customer) data.
     */
    public function getKYCData(string $customerId): array
    {
        return [
            'customer_id' => $customerId,
            'kyc_status' => 'VERIFIED',
            'verification_date' => '2024-01-15',
            'verification_level' => 'ENHANCED',
            'documents' => [
                [
                    'type' => 'PASSPORT',
                    'number' => 'AB123456',
                    'country' => 'FR',
                    'expiry_date' => '2030-12-31',
                    'verified' => true,
                ],
                [
                    'type' => 'PROOF_OF_ADDRESS',
                    'document_type' => 'UTILITY_BILL',
                    'date' => '2024-01-10',
                    'verified' => true,
                ],
            ],
            'pep_status' => false,
            'risk_rating' => 'LOW',
            'next_review_date' => '2025-01-15',
        ];
    }
    
    /**
     * Get AML (Anti-Money Laundering) checks.
     */
    public function getAMLChecks(string $customerId): array
    {
        return [
            'customer_id' => $customerId,
            'aml_status' => 'CLEAR',
            'last_check_date' => '2024-01-20',
            'screening_results' => [
                'sanctions_lists' => 'NO_MATCH',
                'adverse_media' => 'NO_MATCH',
                'pep_screening' => 'NO_MATCH',
            ],
            'transaction_monitoring' => [
                'alerts_count' => 0,
                'suspicious_activity' => false,
                'last_alert_date' => null,
            ],
            'risk_score' => 15,
            'risk_category' => 'LOW',
            'next_check_date' => '2024-04-20',
        ];
    }
    
    /**
     * Get sanctions lists check.
     */
    public function getSanctionsList(string $customerId): array
    {
        return [
            'customer_id' => $customerId,
            'check_date' => '2024-01-20',
            'lists_checked' => [
                'OFAC_SDN',
                'EU_SANCTIONS',
                'UN_SANCTIONS',
                'UK_HMT',
            ],
            'matches' => [],
            'status' => 'CLEAR',
            'confidence_level' => 'HIGH',
            'false_positive_check' => true,
        ];
    }
    
    /**
     * Get comprehensive compliance checks.
     */
    public function getComplianceChecks(string $entityId, string $entityType = 'CUSTOMER'): array
    {
        return [
            'entity_id' => $entityId,
            'entity_type' => $entityType,
            'check_date' => '2024-01-20T10:30:00Z',
            'kyc' => $this->getKYCData($entityId),
            'aml' => $this->getAMLChecks($entityId),
            'sanctions' => $this->getSanctionsList($entityId),
            'gdpr_compliance' => [
                'consent_obtained' => true,
                'consent_date' => '2024-01-10',
                'data_processing_agreement' => true,
                'right_to_be_forgotten_requests' => 0,
                'data_portability_requests' => 0,
            ],
            'regulatory_reporting' => [
                'crs_reportable' => false,
                'fatca_reportable' => false,
                'emir_reportable' => false,
            ],
            'overall_status' => 'COMPLIANT',
            'issues' => [],
            'recommendations' => [
                'Schedule next KYC review for 2025-01-15',
            ],
        ];
    }
}
