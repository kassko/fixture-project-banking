<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\DTO\Request\PaymentScheduleRequest;
use App\DTO\Response\PaymentScheduleResponse;
use App\Repository\CustomerRepository;
use DateTimeImmutable;

class PaymentSchedulingService
{
    private array $schedules = []; // In-memory storage for demo

    public function __construct(
        private CustomerRepository $customerRepository,
        private PaymentScheduleCalculator $scheduleCalculator,
        private RecurrenceManager $recurrenceManager,
        private BankingCalendarService $calendarService
    ) {
    }

    public function createSchedule(PaymentScheduleRequest $request): PaymentScheduleResponse
    {
        // Validate customer exists
        $customer = $this->customerRepository->find($request->getCustomerId());
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        // Validate schedule request
        $errors = $this->scheduleCalculator->validateSchedule($request);
        if (!empty($errors)) {
            throw new \InvalidArgumentException('Validation errors: ' . implode(', ', $errors));
        }

        // Calculate payment schedule
        $payments = $this->scheduleCalculator->calculateSchedule($request);

        // Generate schedule ID
        $scheduleId = 'SCH-' . uniqid();

        // Calculate summary
        $summary = [
            'total_payments' => count($payments),
            'total_amount' => $this->scheduleCalculator->calculateTotalAmount($payments),
            'frequency' => $request->getFrequency(),
            'frequency_description' => $this->recurrenceManager->getFrequencyDescription($request->getFrequency()),
            'first_payment' => $payments[0]->toArray()['payment_date'] ?? null,
            'last_payment' => end($payments)->toArray()['payment_date'] ?? null,
        ];

        $response = new PaymentScheduleResponse(
            $scheduleId,
            $request->getCustomerId(),
            $request->getAmount(),
            $request->getCurrency(),
            $request->getFrequency(),
            $request->getStartDate(),
            $request->getEndDate(),
            'ACTIVE',
            $payments,
            $summary
        );

        // Store in memory (in real app, would save to database)
        $this->schedules[$scheduleId] = $response;

        return $response;
    }

    public function getSchedule(string $scheduleId): ?PaymentScheduleResponse
    {
        return $this->schedules[$scheduleId] ?? null;
    }

    public function updateSchedule(string $scheduleId, PaymentScheduleRequest $request): PaymentScheduleResponse
    {
        if (!isset($this->schedules[$scheduleId])) {
            throw new \RuntimeException('Schedule not found');
        }

        // Validate customer exists
        $customer = $this->customerRepository->find($request->getCustomerId());
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        // Validate schedule request
        $errors = $this->scheduleCalculator->validateSchedule($request);
        if (!empty($errors)) {
            throw new \InvalidArgumentException('Validation errors: ' . implode(', ', $errors));
        }

        // Recalculate payment schedule
        $payments = $this->scheduleCalculator->calculateSchedule($request);

        // Calculate summary
        $summary = [
            'total_payments' => count($payments),
            'total_amount' => $this->scheduleCalculator->calculateTotalAmount($payments),
            'frequency' => $request->getFrequency(),
            'frequency_description' => $this->recurrenceManager->getFrequencyDescription($request->getFrequency()),
            'first_payment' => $payments[0]->toArray()['payment_date'] ?? null,
            'last_payment' => end($payments)->toArray()['payment_date'] ?? null,
        ];

        $response = new PaymentScheduleResponse(
            $scheduleId,
            $request->getCustomerId(),
            $request->getAmount(),
            $request->getCurrency(),
            $request->getFrequency(),
            $request->getStartDate(),
            $request->getEndDate(),
            'ACTIVE',
            $payments,
            $summary
        );

        $this->schedules[$scheduleId] = $response;

        return $response;
    }

    public function cancelSchedule(string $scheduleId): array
    {
        if (!isset($this->schedules[$scheduleId])) {
            throw new \RuntimeException('Schedule not found');
        }

        $schedule = $this->schedules[$scheduleId];
        unset($this->schedules[$scheduleId]);

        return [
            'schedule_id' => $scheduleId,
            'status' => 'CANCELLED',
            'cancelled_at' => date('Y-m-d H:i:s'),
            'message' => 'Payment schedule has been cancelled',
        ];
    }

    public function getCustomerSchedules(int $customerId): array
    {
        $customerSchedules = [];
        
        foreach ($this->schedules as $schedule) {
            $scheduleArray = $schedule->toArray();
            if ($scheduleArray['customer_id'] === $customerId) {
                $customerSchedules[] = $schedule;
            }
        }

        return $customerSchedules;
    }

    public function simulateSchedule(PaymentScheduleRequest $request): array
    {
        // Validate schedule request
        $errors = $this->scheduleCalculator->validateSchedule($request);
        if (!empty($errors)) {
            throw new \InvalidArgumentException('Validation errors: ' . implode(', ', $errors));
        }

        // Calculate payment schedule
        $payments = $this->scheduleCalculator->calculateSchedule($request);

        // Calculate statistics
        $totalAmount = $this->scheduleCalculator->calculateTotalAmount($payments);
        $businessDayPayments = array_filter(
            $payments,
            fn($p) => $p->toArray()['is_business_day']
        );
        $adjustedPayments = array_filter(
            $payments,
            fn($p) => !$p->toArray()['is_business_day']
        );

        return [
            'simulation' => [
                'amount' => $request->getAmount(),
                'currency' => $request->getCurrency(),
                'frequency' => $request->getFrequency(),
                'frequency_description' => $this->recurrenceManager->getFrequencyDescription($request->getFrequency()),
                'start_date' => $request->getStartDate(),
                'end_date' => $request->getEndDate(),
            ],
            'payments' => array_map(fn($p) => $p->toArray(), $payments),
            'statistics' => [
                'total_payments' => count($payments),
                'total_amount' => $totalAmount,
                'average_payment' => count($payments) > 0 ? $totalAmount / count($payments) : 0,
                'payments_on_business_days' => count($businessDayPayments),
                'payments_adjusted' => count($adjustedPayments),
                'first_payment' => $payments[0]->toArray()['payment_date'] ?? null,
                'last_payment' => end($payments)->toArray()['payment_date'] ?? null,
            ],
        ];
    }
}
