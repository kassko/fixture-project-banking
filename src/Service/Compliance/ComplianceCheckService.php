<?php

declare(strict_types=1);

namespace App\Service\Compliance;

use App\Context\UnifiedContext;
use App\DTO\Request\ComplianceCheckRequest;
use App\DTO\Response\ComplianceCheckResponse;
use App\Repository\CustomerRepository;

class ComplianceCheckService
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private KycValidator $kycValidator,
        private AmlChecker $amlChecker
    ) {
    }

    /**
     * Perform comprehensive compliance check for a customer.
     * Executes KYC, AML, and regulatory checks based on request parameters.
     * 
     * @param ComplianceCheckRequest $request Request specifying checks to perform
     * @param UnifiedContext $context Multi-tenant/brand context
     * @return ComplianceCheckResponse Complete compliance status with recommendations
     * @throws \RuntimeException if customer not found
     */
    public function check(ComplianceCheckRequest $request, UnifiedContext $context): ComplianceCheckResponse
    {
        // Verify customer exists
        $customer = $this->customerRepository->find($request->getCustomerId());
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        $checkTypes = $request->getCheckTypes();
        
        // Initialize results
        $kycStatus = [];
        $amlStatus = [];
        $regulatoryStatus = [];
        $recommendations = [];
        
        // Perform requested checks
        if (in_array('KYC', $checkTypes)) {
            $kycStatus = $this->kycValidator->validate($customer, $context);
            if ($kycStatus['status'] !== 'COMPLIANT') {
                $recommendations = array_merge($recommendations, $this->getKycRecommendations($kycStatus));
            }
        }

        if (in_array('AML', $checkTypes)) {
            $amlStatus = $this->amlChecker->check($customer, $request->getTransactionIds(), $context);
            if ($amlStatus['status'] !== 'CLEAR') {
                $recommendations = array_merge($recommendations, $this->getAmlRecommendations($amlStatus));
            }
        }

        if (in_array('REGULATORY', $checkTypes)) {
            $regulatoryStatus = $this->performRegulatoryCheck($customer, $context);
            if ($regulatoryStatus['status'] !== 'COMPLIANT') {
                $recommendations = array_merge($recommendations, $this->getRegulatoryRecommendations($regulatoryStatus));
            }
        }

        // Calculate overall status
        $overallStatus = $this->calculateOverallStatus($kycStatus, $amlStatus, $regulatoryStatus);
        
        // Calculate risk score
        $riskScore = $this->calculateRiskScore($kycStatus, $amlStatus, $regulatoryStatus);

        $checkDate = $context->getTemporalContext()->getCurrentDateTime()->format('Y-m-d H:i:s');

        return new ComplianceCheckResponse(
            $request->getCustomerId(),
            $overallStatus,
            $kycStatus,
            $amlStatus,
            $regulatoryStatus,
            $request->isIncludeRecommendations() ? $recommendations : [],
            $riskScore,
            $checkDate
        );
    }

    public function getStatus(int $customerId, UnifiedContext $context): array
    {
        $customer = $this->customerRepository->find($customerId);
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        // Get quick status overview
        $kycStatus = $this->kycValidator->validate($customer, $context);
        $amlStatus = $this->amlChecker->check($customer, null, $context);
        $regulatoryStatus = $this->performRegulatoryCheck($customer, $context);

        $overallStatus = $this->calculateOverallStatus($kycStatus, $amlStatus, $regulatoryStatus);
        $riskScore = $this->calculateRiskScore($kycStatus, $amlStatus, $regulatoryStatus);

        return [
            'customer_id' => $customerId,
            'overall_status' => $overallStatus,
            'kyc_compliant' => $kycStatus['status'] === 'COMPLIANT',
            'aml_clear' => $amlStatus['status'] === 'CLEAR',
            'regulatory_compliant' => $regulatoryStatus['status'] === 'COMPLIANT',
            'risk_score' => $riskScore,
            'requires_action' => $overallStatus !== 'COMPLIANT',
            'status_date' => $context->getTemporalContext()->getCurrentDateTime()->format('Y-m-d H:i:s'),
        ];
    }

    public function verifyKyc(int $customerId, UnifiedContext $context): array
    {
        $customer = $this->customerRepository->find($customerId);
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        $kycStatus = $this->kycValidator->validate($customer, $context);
        
        return [
            'customer_id' => $customerId,
            'kyc_status' => $kycStatus,
            'is_compliant' => $kycStatus['status'] === 'COMPLIANT',
            'recommendations' => $this->getKycRecommendations($kycStatus),
            'verification_date' => $context->getTemporalContext()->getCurrentDateTime()->format('Y-m-d H:i:s'),
        ];
    }

    private function performRegulatoryCheck($customer, UnifiedContext $context): array
    {
        $issues = [];
        $checks = [];

        // Check account limits
        $limitsCheck = $this->checkAccountLimits($customer);
        $checks['account_limits'] = $limitsCheck;
        if (!$limitsCheck['compliant']) {
            $issues[] = $limitsCheck['issue'];
        }

        // Check reporting requirements
        $reportingCheck = $this->checkReportingRequirements($customer);
        $checks['reporting'] = $reportingCheck;
        if (!$reportingCheck['compliant']) {
            $issues[] = $reportingCheck['issue'];
        }

        $status = empty($issues) ? 'COMPLIANT' : 'NON_COMPLIANT';

        return [
            'status' => $status,
            'checks' => $checks,
            'issues' => $issues,
            'check_date' => $context->getTemporalContext()->getCurrentDateTime()->format('Y-m-d'),
        ];
    }

    private function checkAccountLimits($customer): array
    {
        // Simulate account limits check
        $compliant = rand(0, 100) > 10; // 90% compliance rate

        return [
            'compliant' => $compliant,
            'max_accounts_allowed' => 10,
            'current_accounts' => rand(1, 8),
            'issue' => $compliant ? null : 'Account limits may be exceeded',
        ];
    }

    private function checkReportingRequirements($customer): array
    {
        // Simulate reporting requirements check
        $compliant = rand(0, 100) > 5; // 95% compliance rate

        return [
            'compliant' => $compliant,
            'required_reports' => ['ANNUAL_STATEMENT', 'TAX_DECLARATION'],
            'issue' => $compliant ? null : 'Missing required reports',
        ];
    }

    private function calculateOverallStatus(array $kycStatus, array $amlStatus, array $regulatoryStatus): string
    {
        $allStatuses = [];
        
        if (!empty($kycStatus)) {
            $allStatuses[] = $kycStatus['status'];
        }
        if (!empty($amlStatus)) {
            $allStatuses[] = $amlStatus['status'];
        }
        if (!empty($regulatoryStatus)) {
            $allStatuses[] = $regulatoryStatus['status'];
        }

        // If any check failed significantly, overall is non-compliant
        if (in_array('NON_COMPLIANT', $allStatuses) || in_array('REQUIRES_REVIEW', $allStatuses)) {
            return 'NON_COMPLIANT';
        }

        // If any check has warnings, overall is partial
        if (in_array('PARTIAL', $allStatuses) || in_array('MONITORING', $allStatuses)) {
            return 'PARTIAL_COMPLIANCE';
        }

        return 'COMPLIANT';
    }

    private function calculateRiskScore(array $kycStatus, array $amlStatus, array $regulatoryStatus): array
    {
        $totalScore = 0;
        $maxScore = 0;
        $components = [];

        if (!empty($kycStatus) && isset($kycStatus['score'])) {
            $kycScore = $kycStatus['score'];
            $components['kyc'] = $kycScore;
            $totalScore += $kycScore;
            $maxScore += 100;
        }

        if (!empty($amlStatus) && isset($amlStatus['risk_level'])) {
            $amlScore = match($amlStatus['risk_level']) {
                'LOW' => 100,
                'MEDIUM' => 60,
                'HIGH' => 20,
                default => 50,
            };
            $components['aml'] = $amlScore;
            $totalScore += $amlScore;
            $maxScore += 100;
        }

        if (!empty($regulatoryStatus)) {
            $regulatoryScore = $regulatoryStatus['status'] === 'COMPLIANT' ? 100 : 40;
            $components['regulatory'] = $regulatoryScore;
            $totalScore += $regulatoryScore;
            $maxScore += 100;
        }

        $overallScore = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0;

        return [
            'overall' => $overallScore,
            'components' => $components,
            'risk_level' => $overallScore >= 80 ? 'LOW' : ($overallScore >= 50 ? 'MEDIUM' : 'HIGH'),
        ];
    }

    private function getKycRecommendations(array $kycStatus): array
    {
        $recommendations = [];
        
        foreach ($kycStatus['issues'] ?? [] as $issue) {
            if (str_contains($issue, 'identity')) {
                $recommendations[] = 'Please update customer identity information';
            } elseif (str_contains($issue, 'address')) {
                $recommendations[] = 'Request proof of address from customer';
            } elseif (str_contains($issue, 'documents')) {
                $recommendations[] = 'Request missing KYC documents';
            } elseif (str_contains($issue, 'age')) {
                $recommendations[] = 'Verify customer age eligibility';
            }
        }

        return $recommendations;
    }

    private function getAmlRecommendations(array $amlStatus): array
    {
        $recommendations = [];

        if ($amlStatus['risk_level'] === 'HIGH') {
            $recommendations[] = 'Escalate to compliance officer for manual review';
        }

        if ($amlStatus['requires_manual_review'] ?? false) {
            $recommendations[] = 'Conduct enhanced due diligence';
        }

        foreach ($amlStatus['alerts'] ?? [] as $alert) {
            if (str_contains($alert, 'PEP')) {
                $recommendations[] = 'Apply enhanced monitoring for PEP customer';
            } elseif (str_contains($alert, 'suspicious')) {
                $recommendations[] = 'Review flagged transactions for suspicious activity';
            } elseif (str_contains($alert, 'high-value')) {
                $recommendations[] = 'Verify source of funds for high-value transactions';
            }
        }

        return $recommendations;
    }

    private function getRegulatoryRecommendations(array $regulatoryStatus): array
    {
        $recommendations = [];

        foreach ($regulatoryStatus['issues'] ?? [] as $issue) {
            if (str_contains($issue, 'limits')) {
                $recommendations[] = 'Review account limits and consolidate if necessary';
            } elseif (str_contains($issue, 'reports')) {
                $recommendations[] = 'Submit outstanding regulatory reports';
            }
        }

        return $recommendations;
    }
}
