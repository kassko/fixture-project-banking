# Project Summary: Fixture Project Banking

## Overview
A comprehensive PHP/Symfony banking/insurance fixture project designed to test object hydration in complex, real-world scenarios.

## Statistics
- **Total PHP Files**: 53
- **Lines of Code**: ~6,000+
- **PHP Version**: 8.2+
- **Framework**: Symfony 7.0
- **ORM**: Doctrine 3.0

## Architecture Highlights

### 1. Multi-Level Inheritance (4 Levels)
```
AbstractPerson (Level 1)
  ├─ IdentifiableTrait
  └─ TimestampableTrait
     ↓
AbstractCustomer (Level 2) extends AbstractPerson
  └─ AuditableTrait
     ↓
PremiumCustomer (Level 3) extends AbstractCustomer
  └─ MetadataContainerTrait
     ↓
VIPCustomer (Level 4) extends PremiumCustomer
  └─ SoftDeletableTrait
```

### 2. Complex Nested Objects (3-4 Levels Deep)
```
Customer
  └─ ContactInfo
      ├─ Address
      │   └─ GPS Coordinates (lat, lng)
      └─ Phones[] (array of phone objects)
  └─ RiskProfile
      └─ Factors (complex array)

AmortizationSchedule
  └─ PaymentPlan
      ├─ MoneyAmount
      └─ InterestRate
          └─ Calculation methods

InsurancePolicy
  └─ Coverage[]
      └─ Deductible
          └─ MoneyAmount
```

### 3. Hybrid Data Objects (Partial Doctrine)
Example: `HybridContract`
- **WITH Doctrine**: `id`, `reference`, `metadata`, `signedAt`, `signedBy`
- **WITHOUT Doctrine**: `legacyClausesData`, `externalRating`, `calculatedRiskScore`, `validationErrors`

This demonstrates real-world scenarios where:
- Some properties are persisted to database
- Others are managed manually or calculated
- External data is temporarily stored

### 4. Legacy DataSources (Complex Arrays)
Returns deeply nested arrays simulating legacy systems:

```php
$data = [
    'id' => 'CUST001',
    'personal_info' => [
        'first_name' => 'Jean',
        'contact' => [
            'email' => '...',
            'phones' => [
                ['type' => 'mobile', 'number' => '...'],
                ['type' => 'home', 'number' => '...']
            ],
            'address' => [
                'street' => '...',
                'city' => '...',
                'geo' => ['lat' => 48.8566, 'lng' => 2.3522]
            ]
        ]
    ],
    'accounts' => [...],
    'risk_profile' => [...]
];
```

## Domain Model

### Entities (Full Doctrine)
1. **Customer** - Standard customer with accounts and policies
2. **BankAccount** - Checking, savings, investment accounts
3. **Transaction** - Financial transactions (deposit, withdrawal, etc.)
4. **InsurancePolicy** - Insurance policies with coverages
5. **Beneficiary** - Policy beneficiaries
6. **PremiumCustomer** - Premium tier customers
7. **VIPCustomer** - Top-tier VIP customers

### Legacy Data Objects (NO Doctrine)
1. **LegacyCustomerProfile** - Pure POPO
2. **LegacyRiskAssessment** - Risk data
3. **LegacyPaymentSchedule** - Payment schedules

### Hybrid Data Objects (Partial Doctrine)
1. **HybridContract** - Mixed persistence
2. **HybridClaim** - Insurance claims
3. **HybridInvestmentPortfolio** - Investment portfolios

### Value Objects (Nested Models)
**Financial**:
- MoneyAmount (immutable)
- InterestRate
- PaymentPlan
- AmortizationSchedule

**Insurance**:
- Coverage
- Deductible
- ClaimDetails
- RiskProfile

**Common**:
- Address (with GPS)
- ContactInfo
- DocumentReference
- AuditInfo

### Enums
- CustomerType (individual, business, corporate)
- PremiumLevel (bronze, silver, gold, platinum)
- AccountType (checking, savings, investment, loan)
- TransactionType (deposit, withdrawal, transfer, payment, fee)
- PolicyStatus (active, suspended, cancelled, expired)
- RiskCategory (low, moderate, high, critical)

## Data Complexity Examples

### Example 1: Customer with 4-Level Nesting
```
Customer
  └─ ContactInfo
      └─ Address
          └─ GPS {lat: 48.8566, lng: 2.3522}
      └─ Phones
          └─ [{type: 'mobile', number: '+33...'}]
```

### Example 2: VIP Customer (4-Level Inheritance)
```
VIPCustomer
  extends PremiumCustomer
    extends AbstractCustomer
      extends AbstractPerson
        uses IdentifiableTrait, TimestampableTrait
      uses AuditableTrait
    uses MetadataContainerTrait
  uses SoftDeletableTrait
```

## Use Cases

This project is designed to test:

1. ✓ **Object hydration** from complex nested arrays
2. ✓ **Mapping** between legacy and modern formats
3. ✓ **Multi-level inheritance** handling
4. ✓ **Deep object nesting** (3-4 levels)
5. ✓ **Mixed persistence** strategies (Doctrine + manual)
6. ✓ **Value objects** and immutability
7. ✓ **Trait composition**
8. ✓ **Complex ORM relations** (OneToMany, ManyToOne)

## Testing Scenarios

The project includes realistic data for:
- 4 customers (standard, premium, VIP)
- 3 bank accounts
- 6 transactions
- 2 insurance policies
- Multiple coverages per policy
- Risk profiles
- External credit ratings
- Historical interest rates

## Code Quality

- ✓ PHP 8.2+ with strict typing
- ✓ Native enums
- ✓ Doctrine attributes (not annotations)
- ✓ PSR-4 autoloading
- ✓ PHPDoc comments
- ✓ Professional structure
- ✓ Realistic domain modeling

## Installation & Usage

See README.md for detailed instructions.

---

**Project Created**: December 2024
**Purpose**: Object hydration testing in complex banking/insurance domain
**Status**: Complete - All 13 requirements met ✓
