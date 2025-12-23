<?php

declare(strict_types=1);

namespace App\Service\Fraud;

use App\DTO\Response\FraudAlert;

class AlertManager
{
    // NOTE: In-memory storage for demonstration purposes only.
    // In production, this should be replaced with a proper repository.
    private array $alerts = [];
    private int $nextAlertId = 1;

    public function createAlert(
        int $transactionId,
        int $customerId,
        float $fraudScore,
        array $patterns,
        bool $isBlocked
    ): FraudAlert {
        $severity = $this->determineSeverity($fraudScore);
        $reason = $this->generateReason($patterns);
        
        $alert = new FraudAlert(
            $this->nextAlertId++,
            $transactionId,
            $customerId,
            $severity,
            $reason,
            $fraudScore,
            $patterns,
            $isBlocked ? 'blocked' : 'pending',
            date('Y-m-d H:i:s')
        );

        $this->alerts[$alert->getAlertId()] = $alert;
        
        return $alert;
    }

    public function getAlert(int $alertId): ?FraudAlert
    {
        return $this->alerts[$alertId] ?? null;
    }

    public function getCustomerAlerts(int $customerId): array
    {
        return array_filter($this->alerts, function ($alert) use ($customerId) {
            return $alert->getCustomerId() === $customerId;
        });
    }

    public function resolveAlert(int $alertId): bool
    {
        if (isset($this->alerts[$alertId])) {
            $alert = $this->alerts[$alertId];
            // In a real implementation, we would update the alert status
            // For now, we just return success
            return true;
        }
        
        return false;
    }

    public function generateRecommendations(float $fraudScore, array $patterns): array
    {
        $recommendations = [];

        if ($fraudScore >= 80) {
            $recommendations[] = 'Block transaction immediately and contact customer';
            $recommendations[] = 'Verify customer identity before allowing further transactions';
            $recommendations[] = 'Review recent transaction history for similar patterns';
        } elseif ($fraudScore >= 60) {
            $recommendations[] = 'Request additional authentication from customer';
            $recommendations[] = 'Monitor account closely for 24-48 hours';
            $recommendations[] = 'Send fraud alert notification to customer';
        } elseif ($fraudScore >= 40) {
            $recommendations[] = 'Send notification to customer for verification';
            $recommendations[] = 'Enable enhanced monitoring for this account';
        } else {
            $recommendations[] = 'Continue normal monitoring';
        }

        // Add pattern-specific recommendations
        foreach ($patterns as $pattern) {
            if ($pattern['type'] === 'unusual_location') {
                $recommendations[] = 'Verify transaction location with customer';
            }
            if ($pattern['type'] === 'unusual_amount') {
                $recommendations[] = 'Confirm large transaction amount with customer';
            }
        }

        return array_unique($recommendations);
    }

    private function determineSeverity(float $fraudScore): string
    {
        return match (true) {
            $fraudScore >= 80 => 'CRITICAL',
            $fraudScore >= 60 => 'HIGH',
            $fraudScore >= 40 => 'MEDIUM',
            default => 'LOW',
        };
    }

    private function generateReason(array $patterns): string
    {
        if (empty($patterns)) {
            return 'No specific fraud patterns detected';
        }

        $descriptions = array_map(fn($p) => $p['description'], $patterns);
        return implode('; ', array_slice($descriptions, 0, 3));
    }
}
