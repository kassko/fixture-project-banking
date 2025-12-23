<?php

declare(strict_types=1);

namespace App\Service\Claims;

use DateTimeImmutable;

class ClaimWorkflowManager
{
    private const VALID_TRANSITIONS = [
        'OPEN' => ['IN_PROGRESS', 'REJECTED'],
        'IN_PROGRESS' => ['RESOLVED', 'PENDING_INFO', 'ESCALATED'],
        'PENDING_INFO' => ['IN_PROGRESS', 'REJECTED'],
        'ESCALATED' => ['IN_PROGRESS', 'RESOLVED'],
        'RESOLVED' => ['CLOSED'],
        'REJECTED' => ['CLOSED'],
        'CLOSED' => [],
    ];

    private const STATUS_DESCRIPTIONS = [
        'OPEN' => 'Réclamation nouvellement créée',
        'IN_PROGRESS' => 'En cours de traitement',
        'PENDING_INFO' => 'En attente d\'informations complémentaires',
        'ESCALATED' => 'Escaladée à un niveau supérieur',
        'RESOLVED' => 'Réclamation résolue',
        'REJECTED' => 'Réclamation rejetée',
        'CLOSED' => 'Réclamation clôturée',
    ];

    public function validateTransition(string $currentStatus, string $newStatus): bool
    {
        if (!isset(self::VALID_TRANSITIONS[$currentStatus])) {
            return false;
        }
        
        return in_array($newStatus, self::VALID_TRANSITIONS[$currentStatus], true);
    }

    public function getAvailableTransitions(string $currentStatus): array
    {
        return self::VALID_TRANSITIONS[$currentStatus] ?? [];
    }

    public function applyTransition(
        string $currentStatus,
        string $newStatus,
        ?string $comment = null,
        ?string $assignedTo = null
    ): array {
        if (!$this->validateTransition($currentStatus, $newStatus)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid status transition from %s to %s', $currentStatus, $newStatus)
            );
        }

        $timestamp = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        
        return [
            'status' => $newStatus,
            'previous_status' => $currentStatus,
            'comment' => $comment,
            'assigned_to' => $assignedTo,
            'timestamp' => $timestamp,
            'description' => self::STATUS_DESCRIPTIONS[$newStatus] ?? 'Statut mis à jour',
        ];
    }

    public function getStatusDescription(string $status): string
    {
        return self::STATUS_DESCRIPTIONS[$status] ?? 'Statut inconnu';
    }

    public function getWorkflowStage(string $status): string
    {
        return match ($status) {
            'OPEN', 'IN_PROGRESS', 'PENDING_INFO', 'ESCALATED' => 'active',
            'RESOLVED', 'REJECTED' => 'completed',
            'CLOSED' => 'archived',
            default => 'unknown',
        };
    }

    public function requiresAction(string $status): bool
    {
        return in_array($status, ['OPEN', 'IN_PROGRESS', 'ESCALATED'], true);
    }

    public function isTerminalStatus(string $status): bool
    {
        return in_array($status, ['CLOSED'], true);
    }
}
