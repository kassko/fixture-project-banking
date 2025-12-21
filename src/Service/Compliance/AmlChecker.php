<?php

declare(strict_types=1);

namespace App\Service\Compliance;

use App\Entity\Customer;
use App\Repository\TransactionRepository;
use App\Context\UnifiedContext;

class AmlChecker
{
    private const HIGH_RISK_THRESHOLD = 50000;
    private const SUSPICIOUS_PATTERN_THRESHOLD = 10;
    private const PEP_CHECK_REQUIRED = true;

    public function __construct(
        private TransactionRepository $transactionRepository
    ) {
    }

    public function check(Customer $customer, ?array $transactionIds, UnifiedContext $context): array
    {
        $alerts = [];
        $checks = [];
        $riskLevel = 'LOW';

        // Check for politically exposed person (PEP)
        $pepCheck = $this->checkPEP($customer);
        $checks['pep'] = $pepCheck;
        if ($pepCheck['is_pep']) {
            $alerts[] = 'Customer is flagged as Politically Exposed Person';
            $riskLevel = 'HIGH';
        }

        // Check transaction patterns
        $transactionCheck = $this->checkTransactionPatterns($customer, $transactionIds);
        $checks['transaction_patterns'] = $transactionCheck;
        if ($transactionCheck['suspicious_count'] > 0) {
            $alerts[] = sprintf('%d suspicious transaction patterns detected', $transactionCheck['suspicious_count']);
            if ($transactionCheck['suspicious_count'] >= self::SUSPICIOUS_PATTERN_THRESHOLD) {
                $riskLevel = 'HIGH';
            } elseif ($riskLevel === 'LOW') {
                $riskLevel = 'MEDIUM';
            }
        }

        // Check for high-value transactions
        $highValueCheck = $this->checkHighValueTransactions($customer, $transactionIds);
        $checks['high_value_transactions'] = $highValueCheck;
        if ($highValueCheck['count'] > 0) {
            $alerts[] = sprintf('%d high-value transactions detected', $highValueCheck['count']);
            if ($riskLevel === 'LOW') {
                $riskLevel = 'MEDIUM';
            }
        }

        // Check geographic risk
        $geoRiskCheck = $this->checkGeographicRisk($customer);
        $checks['geographic_risk'] = $geoRiskCheck;
        if ($geoRiskCheck['risk_level'] !== 'LOW') {
            $alerts[] = 'Customer location has elevated AML risk';
            if ($geoRiskCheck['risk_level'] === 'HIGH' && $riskLevel !== 'HIGH') {
                $riskLevel = 'MEDIUM';
            }
        }

        $status = $riskLevel === 'HIGH' ? 'REQUIRES_REVIEW' : ($riskLevel === 'MEDIUM' ? 'MONITORING' : 'CLEAR');

        return [
            'status' => $status,
            'risk_level' => $riskLevel,
            'checks' => $checks,
            'alerts' => $alerts,
            'requires_manual_review' => $riskLevel === 'HIGH',
            'check_date' => $context->getTemporalContext()->getCurrentDateTime()->format('Y-m-d H:i:s'),
        ];
    }

    private function checkPEP(Customer $customer): array
    {
        // Simulate PEP check based on customer risk profile
        $riskProfile = $customer->getRiskProfile();
        $isPep = false;
        
        if ($riskProfile !== null) {
            $factors = $riskProfile->getFactors();
            $isPep = in_array('PEP', $factors ?? []);
        }

        return [
            'is_pep' => $isPep,
            'pep_category' => $isPep ? 'POLITICALLY_EXPOSED' : null,
            'check_performed' => true,
        ];
    }

    private function checkTransactionPatterns(Customer $customer, ?array $transactionIds): array
    {
        // Get recent transactions (or specific ones if provided)
        $suspiciousCount = 0;
        $patterns = [];

        // Simulate pattern detection
        // In a real implementation, this would analyze actual transaction data
        $randomSuspicious = rand(0, 5);
        
        if ($randomSuspicious > 0) {
            $suspiciousCount = $randomSuspicious;
            $patterns[] = 'Rapid succession of transactions';
        }

        return [
            'suspicious_count' => $suspiciousCount,
            'patterns_detected' => $patterns,
            'analyzed_transactions' => $transactionIds ? count($transactionIds) : 30,
        ];
    }

    private function checkHighValueTransactions(Customer $customer, ?array $transactionIds): array
    {
        // Simulate high-value transaction check
        $highValueCount = rand(0, 3);
        $totalAmount = $highValueCount * self::HIGH_RISK_THRESHOLD * 1.2;

        return [
            'count' => $highValueCount,
            'threshold' => self::HIGH_RISK_THRESHOLD,
            'total_amount' => round($totalAmount, 2),
            'requires_reporting' => $highValueCount > 0,
        ];
    }

    private function checkGeographicRisk(Customer $customer): array
    {
        // Simulate geographic risk assessment
        $country = 'UNKNOWN';
        $contactInfo = $customer->getContactInfo();
        
        if ($contactInfo !== null && $contactInfo->getAddress() !== null) {
            $country = $contactInfo->getAddress()->getCountry();
        }
        
        // High-risk countries list (simplified)
        $highRiskCountries = ['COUNTRY_X', 'COUNTRY_Y'];
        $mediumRiskCountries = ['COUNTRY_A', 'COUNTRY_B'];

        $riskLevel = 'LOW';
        if (in_array($country, $highRiskCountries)) {
            $riskLevel = 'HIGH';
        } elseif (in_array($country, $mediumRiskCountries)) {
            $riskLevel = 'MEDIUM';
        }

        return [
            'risk_level' => $riskLevel,
            'country' => $country,
            'sanctioned_country' => in_array($country, $highRiskCountries),
        ];
    }
}
