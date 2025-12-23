<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\DTO\Request\PaymentScheduleRequest;
use App\DTO\Response\ScheduledPayment;
use DateTimeImmutable;

class PaymentScheduleCalculator
{
    public function __construct(
        private RecurrenceManager $recurrenceManager,
        private BankingCalendarService $calendarService
    ) {
    }

    public function calculateSchedule(PaymentScheduleRequest $request): array
    {
        $startDate = new DateTimeImmutable($request->getStartDate());
        $endDate = $request->getEndDate() ? new DateTimeImmutable($request->getEndDate()) : null;

        // Generate base occurrences
        $occurrences = $this->recurrenceManager->generateOccurrences(
            $startDate,
            $request->getFrequency(),
            $endDate,
            $request->getOccurrences()
        );

        // Convert to scheduled payments with business day adjustments
        $payments = [];
        foreach ($occurrences as $index => $occurrence) {
            $adjustedDate = $this->calendarService->adjustToBusinessDay($occurrence, 'modified_following');
            
            $payments[] = new ScheduledPayment(
                $adjustedDate->format('Y-m-d'),
                $request->getAmount(),
                $index === 0 ? 'PENDING' : 'SCHEDULED',
                $index + 1,
                $this->calendarService->isBusinessDay($occurrence)
            );
        }

        return $payments;
    }

    public function calculateTotalAmount(array $payments): float
    {
        return array_reduce(
            $payments,
            fn($total, ScheduledPayment $payment) => $total + $payment->toArray()['amount'],
            0.0
        );
    }

    public function estimateEndDate(DateTimeImmutable $startDate, string $frequency, int $occurrences): DateTimeImmutable
    {
        $currentDate = $startDate;
        
        for ($i = 1; $i < $occurrences; $i++) {
            $currentDate = $this->recurrenceManager->calculateNextOccurrence($currentDate, $frequency);
        }

        return $currentDate;
    }

    public function validateSchedule(PaymentScheduleRequest $request): array
    {
        $errors = [];

        if ($request->getAmount() <= 0) {
            $errors[] = 'Amount must be greater than zero';
        }

        if ($request->getEndDate() && $request->getOccurrences()) {
            $errors[] = 'Cannot specify both endDate and occurrences';
        }

        if (!$request->getEndDate() && !$request->getOccurrences()) {
            $errors[] = 'Must specify either endDate or occurrences';
        }

        try {
            new DateTimeImmutable($request->getStartDate());
        } catch (\Exception $e) {
            $errors[] = 'Invalid start date format';
        }

        if ($request->getEndDate()) {
            try {
                $endDate = new DateTimeImmutable($request->getEndDate());
                $startDate = new DateTimeImmutable($request->getStartDate());
                
                if ($endDate <= $startDate) {
                    $errors[] = 'End date must be after start date';
                }
            } catch (\Exception $e) {
                $errors[] = 'Invalid end date format';
            }
        }

        return $errors;
    }
}
