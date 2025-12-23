# Risk Assessment & Payment Scheduling Endpoints - Implementation Summary

## Overview
This implementation adds two comprehensive business endpoint features to the banking fixture project:
1. **Risk Assessment Endpoint** - Credit risk evaluation and scoring
2. **Payment Scheduling Endpoint** - Recurring payment management

---

## 1. Risk Assessment Endpoint

### Purpose
Evaluate customer credit risk through comprehensive analysis of financial factors, generating detailed risk reports and classifications.

### Controllers
- **File**: `src/Controller/Api/RiskController.php`
- **Base Route**: `/api/v1/risk`
- **Tag**: `Risk Assessment`

### Endpoints

#### 1.1 POST /api/v1/risk/assess
Launch a complete risk assessment for a customer.

**Request Body:**
```json
{
  "customerId": 1,
  "includeFactors": ["credit_score", "income_stability", "debt_ratio"],
  "generateReport": true
}
```

**Response:**
```json
{
  "customer_id": 1,
  "risk_score": 75.50,
  "risk_level": "LOW",
  "risk_factors": {
    "credit_score": {
      "value": 85,
      "weight": 0.30,
      "status": "excellent",
      "description": "Credit score based on payment history and creditworthiness"
    },
    ...
  },
  "report": { ... }
}
```

#### 1.2 GET /api/v1/risk/score/{customerId}
Get the risk score for a specific customer.

**Response:**
```json
{
  "customer_id": 1,
  "risk_score": 75.50,
  "scale": "0-100 (higher is better)",
  "calculated_at": "2024-12-23 12:30:00"
}
```

#### 1.3 GET /api/v1/risk/report/{customerId}
Get a detailed risk report for a customer.

**Response:** Complete risk assessment with detailed report section.

#### 1.4 GET /api/v1/risk/factors/{customerId}
Analyze individual risk factors for a customer.

**Response:**
```json
{
  "customer_id": 1,
  "risk_factors": { ... },
  "overall_score": 75.50
}
```

#### 1.5 GET /api/v1/risk/classification/{customerId}
Get risk classification with limits and recommendations.

**Response:**
```json
{
  "customer_id": 1,
  "risk_level": "LOW",
  "risk_score": 75.50,
  "classification": {
    "level": "LOW",
    "color": "green",
    "description": "Low risk - Excellent creditworthiness",
    "recommended_actions": [...],
    "restrictions": []
  },
  "limits": {
    "max_loan_amount": 500000,
    "max_credit_limit": 50000,
    "min_down_payment_percent": 10
  }
}
```

### Services

#### RiskAssessmentService
- **File**: `src/Service/Risk/RiskAssessmentService.php`
- **Purpose**: Main orchestration service for risk assessment
- **Dependencies**: CustomerRepository, RiskScoreCalculator, RiskFactorAnalyzer, RiskClassifier

#### RiskScoreCalculator
- **File**: `src/Service/Risk/RiskScoreCalculator.php`
- **Purpose**: Calculate weighted risk scores from multiple factors
- **Features**: 
  - Weighted score calculation (credit_score: 30%, debt_ratio: 25%, income_stability: 20%, payment_history: 15%, account_age: 10%)
  - Trend analysis with historical data

#### RiskFactorAnalyzer
- **File**: `src/Service/Risk/RiskFactorAnalyzer.php`
- **Purpose**: Analyze individual risk factors
- **Factors Analyzed**:
  - Credit score (0-100)
  - Income stability (0-10 scale)
  - Debt ratio (percentage)
  - Payment history (0-100)
  - Account age (years)

#### RiskClassifier
- **File**: `src/Service/Risk/RiskClassifier.php`
- **Purpose**: Classify risk into levels
- **Risk Levels**:
  - **LOW** (score >= 75): Excellent creditworthiness
  - **MEDIUM** (score 50-74): Good with minor concerns
  - **HIGH** (score 25-49): Creditworthiness concerns
  - **CRITICAL** (score < 25): Significant issues

### DTOs

- `src/DTO/Request/RiskAssessmentRequest.php` - Request parameters
- `src/DTO/Response/RiskAssessmentResponse.php` - Assessment results
- `src/DTO/Response/RiskReport.php` - Detailed risk report

---

## 2. Payment Scheduling Endpoint

### Purpose
Manage recurring payment schedules with banking calendar support, including automatic business day adjustments.

### Controllers
- **File**: `src/Controller/Api/PaymentScheduleController.php`
- **Base Route**: `/api/v1/payments/schedule`
- **Tag**: `Payment Scheduling`

### Endpoints

#### 2.1 POST /api/v1/payments/schedule
Create a new payment schedule.

**Request Body:**
```json
{
  "customerId": 1,
  "amount": 500.00,
  "currency": "EUR",
  "frequency": "MONTHLY",
  "startDate": "2024-01-15",
  "endDate": "2024-12-31",
  "type": "RECURRING",
  "description": "Monthly rent payment"
}
```

**Supported Frequencies:** DAILY, WEEKLY, BIWEEKLY, MONTHLY, QUARTERLY, SEMIANNUAL, ANNUAL

**Response:**
```json
{
  "schedule_id": "SCH-123456",
  "customer_id": 1,
  "amount": 500.00,
  "currency": "EUR",
  "frequency": "MONTHLY",
  "start_date": "2024-01-15",
  "end_date": "2024-12-31",
  "status": "ACTIVE",
  "payments": [
    {
      "payment_date": "2024-01-15",
      "amount": 500.00,
      "status": "PENDING",
      "sequence_number": 1,
      "is_business_day": true
    },
    ...
  ],
  "summary": {
    "total_payments": 12,
    "total_amount": 6000.00,
    "frequency": "MONTHLY",
    "frequency_description": "Every month",
    "first_payment": "2024-01-15",
    "last_payment": "2024-12-15"
  }
}
```

#### 2.2 GET /api/v1/payments/schedule/{scheduleId}
Get details of a specific payment schedule.

#### 2.3 PUT /api/v1/payments/schedule/{scheduleId}
Update an existing payment schedule.

#### 2.4 DELETE /api/v1/payments/schedule/{scheduleId}
Cancel a payment schedule.

**Response:**
```json
{
  "schedule_id": "SCH-123456",
  "status": "CANCELLED",
  "cancelled_at": "2024-12-23 12:30:00",
  "message": "Payment schedule has been cancelled"
}
```

#### 2.5 GET /api/v1/payments/schedule/customer/{customerId}
List all payment schedules for a customer.

#### 2.6 POST /api/v1/payments/schedule/simulate
Simulate a payment schedule without creating it.

**Response:**
```json
{
  "simulation": { ... },
  "payments": [ ... ],
  "statistics": {
    "total_payments": 12,
    "total_amount": 6000.00,
    "average_payment": 500.00,
    "payments_on_business_days": 10,
    "payments_adjusted": 2,
    "first_payment": "2024-01-15",
    "last_payment": "2024-12-15"
  }
}
```

### Services

#### PaymentSchedulingService
- **File**: `src/Service/Payment/PaymentSchedulingService.php`
- **Purpose**: Main orchestration service for payment scheduling
- **Features**:
  - Create, read, update, delete schedules
  - Customer schedule management
  - Schedule simulation
- **Dependencies**: CustomerRepository, PaymentScheduleCalculator, RecurrenceManager, BankingCalendarService

#### PaymentScheduleCalculator
- **File**: `src/Service/Payment/PaymentScheduleCalculator.php`
- **Purpose**: Calculate payment schedules with business day adjustments
- **Features**:
  - Schedule calculation
  - Total amount calculation
  - End date estimation
  - Schedule validation

#### RecurrenceManager
- **File**: `src/Service/Payment/RecurrenceManager.php`
- **Purpose**: Handle payment recurrence patterns
- **Features**:
  - Calculate next occurrence
  - Generate occurrence sequences
  - Frequency descriptions
  - Occurrences per year calculations

#### BankingCalendarService
- **File**: `src/Service/Payment/BankingCalendarService.php`
- **Purpose**: Banking calendar and business day management
- **Features**:
  - Business day validation (excludes weekends and French holidays)
  - Next/previous business day calculation
  - Business day adjustment conventions:
    - **following**: Move to next business day
    - **preceding**: Move to previous business day
    - **modified_following**: Move to next business day, unless it's in the next month, then use previous
  - Business days per month calculation

### DTOs

- `src/DTO/Request/PaymentScheduleRequest.php` - Schedule request parameters
- `src/DTO/Response/PaymentScheduleResponse.php` - Schedule details
- `src/DTO/Response/ScheduledPayment.php` - Individual payment details

---

## Technical Implementation Details

### Design Patterns
1. **Service Layer Pattern**: Business logic separated into dedicated services
2. **DTO Pattern**: Request/Response objects for clean API contracts
3. **Dependency Injection**: Constructor injection with autowiring
4. **Repository Pattern**: Data access through repositories

### Code Standards
- **PSR-12**: Code follows PSR-12 coding standards
- **PHP 8.2+**: Uses modern PHP features (constructor property promotion, match expressions, nullable types)
- **Type Safety**: Strict typing enabled with declare(strict_types=1)
- **OpenAPI Documentation**: All endpoints documented with OA attributes

### Consistency with Existing Code
- Controllers follow the same pattern as EligibilityController, SimulationController, ClaimController
- Services use the same dependency injection approach
- DTOs follow the fromArray/toArray pattern
- Route structure matches existing endpoints (/api/v1/...)

### Dependencies
All services use Symfony's autowiring and autoconfigure, so no manual service registration is required.

---

## Testing the Endpoints

### Risk Assessment Examples

```bash
# Assess risk for customer
curl -X POST http://localhost:8000/api/v1/risk/assess \
  -H "Content-Type: application/json" \
  -d '{"customerId": 1, "generateReport": true}'

# Get risk score
curl http://localhost:8000/api/v1/risk/score/1

# Get risk classification
curl http://localhost:8000/api/v1/risk/classification/1
```

### Payment Scheduling Examples

```bash
# Create a monthly payment schedule
curl -X POST http://localhost:8000/api/v1/payments/schedule \
  -H "Content-Type: application/json" \
  -d '{
    "customerId": 1,
    "amount": 500.00,
    "currency": "EUR",
    "frequency": "MONTHLY",
    "startDate": "2024-01-15",
    "occurrences": 12
  }'

# Simulate a payment schedule
curl -X POST http://localhost:8000/api/v1/payments/schedule/simulate \
  -H "Content-Type: application/json" \
  -d '{
    "customerId": 1,
    "amount": 1000.00,
    "frequency": "QUARTERLY",
    "startDate": "2024-01-01",
    "endDate": "2024-12-31"
  }'

# Get customer's schedules
curl http://localhost:8000/api/v1/payments/schedule/customer/1
```

---

## Implementation Summary

### Files Created
**Controllers (2)**:
- src/Controller/Api/RiskController.php
- src/Controller/Api/PaymentScheduleController.php

**Services (8)**:
- src/Service/Risk/RiskAssessmentService.php
- src/Service/Risk/RiskScoreCalculator.php
- src/Service/Risk/RiskFactorAnalyzer.php
- src/Service/Risk/RiskClassifier.php
- src/Service/Payment/PaymentSchedulingService.php
- src/Service/Payment/PaymentScheduleCalculator.php
- src/Service/Payment/RecurrenceManager.php
- src/Service/Payment/BankingCalendarService.php

**DTOs (8)**:
- src/DTO/Request/RiskAssessmentRequest.php
- src/DTO/Request/PaymentScheduleRequest.php
- src/DTO/Response/RiskAssessmentResponse.php
- src/DTO/Response/RiskReport.php
- src/DTO/Response/PaymentScheduleResponse.php
- src/DTO/Response/ScheduledPayment.php

**Total**: 16 new files with comprehensive business logic

### Features Delivered
✅ Risk Assessment with 5 endpoints
✅ Payment Scheduling with 6 endpoints
✅ Complete service layer implementation
✅ Realistic business logic
✅ OpenAPI documentation
✅ PSR-12 compliant code
✅ Type-safe implementation
✅ Follows existing project patterns
