<?php

declare(strict_types=1);

namespace App\Service\Claims;

use App\DTO\Request\ClaimRequest;
use App\DTO\Request\ClaimStatusUpdate;
use App\DTO\Response\ClaimResponse;
use DateTimeImmutable;

class ClaimManagementService
{
    private array $claims = [];
    private int $nextId = 1;

    public function __construct(
        private ClaimWorkflowManager $workflowManager,
        private SlaCalculator $slaCalculator
    ) {
    }

    public function createClaim(ClaimRequest $request): ClaimResponse
    {
        $claimId = $this->nextId++;
        $createdAt = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        
        $claim = [
            'claim_id' => $claimId,
            'customer_id' => $request->customerId,
            'type' => $request->type,
            'status' => 'OPEN',
            'description' => $request->description,
            'incident_date' => $request->incidentDate,
            'created_at' => $createdAt,
            'resolved_at' => null,
            'amount' => $request->amount,
            'policy_id' => $request->policyId,
            'attachments' => $request->attachments ?? [],
            'status_history' => [
                [
                    'status' => 'OPEN',
                    'timestamp' => $createdAt,
                    'comment' => 'Réclamation créée',
                ],
            ],
            'first_response_at' => null,
        ];

        $this->claims[$claimId] = $claim;

        return $this->buildClaimResponse($claim);
    }

    public function getClaim(int $claimId): ?ClaimResponse
    {
        if (!isset($this->claims[$claimId])) {
            return null;
        }

        return $this->buildClaimResponse($this->claims[$claimId]);
    }

    public function updateClaimStatus(int $claimId, ClaimStatusUpdate $statusUpdate): ClaimResponse
    {
        if (!isset($this->claims[$claimId])) {
            throw new \RuntimeException('Claim not found');
        }

        $claim = &$this->claims[$claimId];
        
        $transition = $this->workflowManager->applyTransition(
            $claim['status'],
            $statusUpdate->status,
            $statusUpdate->comment,
            $statusUpdate->assignedTo
        );

        $claim['status'] = $statusUpdate->status;
        $claim['status_history'][] = $transition;

        // Track first response
        if ($claim['first_response_at'] === null && $statusUpdate->status === 'IN_PROGRESS') {
            $claim['first_response_at'] = $transition['timestamp'];
        }

        // Track resolution
        if (in_array($statusUpdate->status, ['RESOLVED', 'REJECTED'], true)) {
            $claim['resolved_at'] = $transition['timestamp'];
        }

        return $this->buildClaimResponse($claim);
    }

    public function getCustomerClaims(int $customerId): array
    {
        $customerClaims = array_filter(
            $this->claims,
            fn($claim) => $claim['customer_id'] === $customerId
        );

        return array_map(
            fn($claim) => $this->buildClaimResponse($claim),
            $customerClaims
        );
    }

    public function getClaimSlaMetrics(int $claimId): array
    {
        if (!isset($this->claims[$claimId])) {
            throw new \RuntimeException('Claim not found');
        }

        $claim = $this->claims[$claimId];
        
        return $this->slaCalculator->calculateSlaMetrics(
            $claim['type'],
            $claim['created_at'],
            $claim['first_response_at'],
            $claim['resolved_at']
        );
    }

    private function buildClaimResponse(array $claim): ClaimResponse
    {
        $slaMetrics = $this->slaCalculator->calculateSlaMetrics(
            $claim['type'],
            $claim['created_at'],
            $claim['first_response_at'],
            $claim['resolved_at']
        );

        return new ClaimResponse(
            $claim['claim_id'],
            $claim['customer_id'],
            $claim['type'],
            $claim['status'],
            $claim['description'],
            $claim['incident_date'],
            $claim['created_at'],
            $claim['resolved_at'],
            $claim['amount'],
            $claim['status_history'],
            $slaMetrics
        );
    }
}
