<?php

namespace App\Resolver;

use App\Context\UserContext;
use App\Context\FeatureFlagContext;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class DataMaskingResolver
{
    private LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Use Case 3: Data masking based on user permissions and feature flags
     * - Admin → all data
     * - Manager → data without PII
     * - User → aggregated data only
     * + Feature flag "show_detailed_risk" controls detailed risk data
     */
    public function maskData(
        array $data,
        UserContext $userContext,
        FeatureFlagContext $featureFlagContext,
        string $dataType = 'general'
    ): array {
        $masked = $data;

        // Admin sees everything
        if ($userContext->isAdmin()) {
            $this->logger->debug('Admin user - no masking applied');
            return $masked;
        }

        // Apply role-based masking
        if ($userContext->isManager()) {
            $masked = $this->maskPII($masked, $featureFlagContext);
            $this->logger->info('Manager role - PII masked');
        } elseif ($userContext->isUser()) {
            $masked = $this->maskPII($masked, $featureFlagContext);
            $masked = $this->aggregateData($masked);
            $this->logger->info('User role - PII masked and data aggregated');
        }

        // Apply feature flag specific masking
        if ($dataType === 'risk') {
            if (!$featureFlagContext->isEnabled('show_detailed_risk')) {
                $masked = $this->maskDetailedRisk($masked);
                $this->logger->info('Feature flag show_detailed_risk disabled - detailed risk masked');
            }

            if (!$featureFlagContext->isEnabled('show_credit_score') && !$userContext->isManager()) {
                $masked = $this->maskCreditScore($masked);
                $this->logger->info('Feature flag show_credit_score disabled - credit score masked');
            }
        }

        return $masked;
    }

    private function maskPII(array $data, FeatureFlagContext $featureFlagContext): array
    {
        // Mask personally identifiable information
        $piiFields = ['email', 'phone', 'address', 'ssn', 'taxId', 'registrationNumber'];

        foreach ($piiFields as $field) {
            if (isset($data[$field])) {
                if ($featureFlagContext->isEnabled('mask_pii_data')) {
                    $data[$field] = $this->mask($data[$field]);
                } else {
                    // Partial masking
                    if (is_string($data[$field])) {
                        $data[$field] = $this->partialMask($data[$field]);
                    }
                }
            }
        }

        return $data;
    }

    private function aggregateData(array $data): array
    {
        // Remove detailed fields, keep only aggregated/summary data
        $detailFields = [
            'scores',
            'history',
            'transactions',
            'riskFactors',
            'inquiries',
            'accounts'
        ];

        foreach ($detailFields as $field) {
            if (isset($data[$field])) {
                // Convert to count or summary
                if (is_array($data[$field])) {
                    $data[$field . 'Count'] = count($data[$field]);
                    unset($data[$field]);
                }
            }
        }

        return $data;
    }

    private function maskDetailedRisk(array $data): array
    {
        $detailedFields = ['riskFactors', 'factors', 'indicators', 'history'];

        foreach ($detailedFields as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }

        return $data;
    }

    private function maskCreditScore(array $data): array
    {
        $scoreFields = ['creditScore', 'riskScore', 'score', 'scores'];

        foreach ($scoreFields as $field) {
            if (isset($data[$field])) {
                if (is_numeric($data[$field])) {
                    // Replace with range
                    $data[$field . 'Range'] = $this->getScoreRange($data[$field]);
                    unset($data[$field]);
                } else {
                    unset($data[$field]);
                }
            }
        }

        return $data;
    }

    private function mask(string $value): string
    {
        return str_repeat('*', min(strlen($value), 10));
    }

    private function partialMask(string $value): string
    {
        $length = strlen($value);
        if ($length <= 3) {
            return str_repeat('*', $length);
        }

        $visible = min(3, (int) ($length * 0.3));
        $masked = $length - $visible;
        
        return substr($value, 0, $visible) . str_repeat('*', $masked);
    }

    private function getScoreRange($score): string
    {
        $score = (int) $score;
        
        if ($score >= 750) return 'excellent';
        if ($score >= 700) return 'good';
        if ($score >= 650) return 'fair';
        if ($score >= 600) return 'poor';
        
        return 'very_poor';
    }
}
