# Fixture Project Banking

A lightweight Symfony 7 project demonstrating complex hydration patterns **without Doctrine**.

## Features

- **Object-Oriented Models**: 10+ models with inheritance hierarchies
- **Multi-level Traits**: 5 traits including nested trait usage
- **Multiple Data Sources**: 10 data sources with varying array depths
- **Smart Resolvers**: Runtime source resolution, fallback chains, conflict resolution, data masking
- **Feature Flags**: Dynamic behavior control
- **REST API**: 3 controllers with 14 total endpoints

## Project Structure

```
src/
├── Model/              # Domain models with inheritance
│   ├── Customer/       # Customer, PremiumCustomer, CorporateCustomer
│   ├── Product/        # Product, LoanProduct, InsuranceProduct
│   ├── Risk/           # RiskProfile, RiskScore, RiskAssessment
│   └── Common/         # Address
├── Traits/             # Reusable traits (5 total, multi-level)
│   ├── IdentifiableTrait.php
│   ├── TimestampableTrait.php
│   ├── AuditableTrait.php (uses TimestampableTrait)
│   ├── ValidatableTrait.php
│   └── SerializableTrait.php
├── DataSource/         # 10 data sources with different array structures
│   ├── Primary/        # InternalApiSource, CacheSource, ConfigSource
│   ├── External/       # PartnerApiSource, CreditBureauSource, MarketDataSource
│   ├── Legacy/         # LegacyDatabaseSource, FlatFileSource
│   └── Fallback/       # DefaultValuesSource, CachedFallbackSource
├── Hydrator/           # Transform arrays to objects
├── Resolver/           # Complex resolution logic
│   ├── SourceResolver.php        # Runtime source selection
│   ├── FallbackResolver.php      # Fallback chain management
│   ├── ConflictResolver.php      # Multi-source conflict resolution
│   └── DataMaskingResolver.php   # Role/feature-based masking
├── Service/            # Business logic
└── Controller/Api/     # REST API endpoints
```

## Installation

```bash
# Clone the repository
git clone https://github.com/kassko/fixture-project-banking.git
cd fixture-project-banking

# Install dependencies
composer install

# The project is ready to run (no database needed!)
```

## Running the Application

```bash
# Start Symfony development server
symfony server:start

# Or use PHP built-in server
php -S localhost:8000 -t public/
```

## API Endpoints & Examples

### Customer API

#### 1. Get Customer
```bash
curl http://localhost:8000/api/customers/1
```

#### 2. Get Customer Full (multi-source)
```bash
curl http://localhost:8000/api/customers/1/full
```

#### 3. Get Customer Profile (with permissions)
```bash
# As user (limited view)
curl "http://localhost:8000/api/customers/1/profile?role=user"

# As manager (more data)
curl "http://localhost:8000/api/customers/1/profile?role=manager"

# As admin (all data)
curl "http://localhost:8000/api/customers/1/profile?role=admin"
```

#### 4. Search Customers
```bash
curl -X POST http://localhost:8000/api/customers/search \
  -H "Content-Type: application/json" \
  -d '{"id": 1}'
```

#### 5. Get Risk Summary for Customer
```bash
curl http://localhost:8000/api/customers/1/risk-summary
```

### Product API

#### 1. Get Product
```bash
curl http://localhost:8000/api/products/1
```

#### 2. Get Eligible Products for Customer
```bash
curl http://localhost:8000/api/products/eligible/1
```

#### 3. Get Product Pricing (with conflict resolution)
```bash
# Using average strategy (default)
curl http://localhost:8000/api/products/1/pricing

# Using priority strategy
curl "http://localhost:8000/api/products/1/pricing?strategy=priority"

# Using conservative strategy
curl "http://localhost:8000/api/products/1/pricing?strategy=conservative"
```

#### 4. Compare Products
```bash
curl -X POST http://localhost:8000/api/products/compare \
  -H "Content-Type: application/json" \
  -d '{"productIds": [1, 2, 3]}'
```

### Risk API

#### 1. Assess Risk (Use Case 1: Runtime Source Resolution)
```bash
# Standard customer (uses InternalApiSource)
curl "http://localhost:8000/api/risk/assess/1?customerType=standard"

# Premium customer (uses CreditBureauSource + PartnerApiSource)
curl "http://localhost:8000/api/risk/assess/1?customerType=premium"

# Corporate customer (uses PartnerApiSource + MarketDataSource)
curl "http://localhost:8000/api/risk/assess/1?customerType=corporate"
```

#### 2. Get Risk Score (Use Case 2: Fallback Chain)
```bash
# Automatically tries: CreditBureau → Cache → Legacy → Default
curl http://localhost:8000/api/risk/score/1
```

#### 3. Simulate Risk Scenarios
```bash
curl -X POST http://localhost:8000/api/risk/simulate \
  -H "Content-Type: application/json" \
  -d '{
    "customerId": 1,
    "scenarios": [
      {"name": "standard", "customerType": "standard"},
      {"name": "premium", "customerType": "premium"},
      {"name": "corporate", "customerType": "corporate"}
    ]
  }'
```

#### 4. Get Risk Report (Use Case 4: Conflict Resolution)
```bash
# Conservative strategy (most cautious)
curl "http://localhost:8000/api/risk/report/1?customerType=premium&strategy=conservative"

# Average strategy
curl "http://localhost:8000/api/risk/report/1?customerType=premium&strategy=average"

# Priority strategy (highest priority source wins)
curl "http://localhost:8000/api/risk/report/1?customerType=premium&strategy=priority"

# Merge strategy (combine all sources)
curl "http://localhost:8000/api/risk/report/1?customerType=premium&strategy=merge"
```

#### 5. Get Risk Factors (Use Case 3: Data Masking)
```bash
# As user (aggregated data only, PII masked, no detailed risk)
curl "http://localhost:8000/api/risk/factors/1?role=user"

# As manager (full data except PII)
curl "http://localhost:8000/api/risk/factors/1?role=manager"

# As admin (all data including PII)
curl "http://localhost:8000/api/risk/factors/1?role=admin"
```

## Complex Use Cases Demonstrated

### Use Case 1: Runtime Source Resolution
**Endpoint**: `GET /api/risk/assess/{customerId}`

The system automatically selects data sources based on customer type:
- **Standard customers**: Uses `InternalApiSource` (basic data)
- **Premium customers**: Uses `CreditBureauSource` + `PartnerApiSource` (enhanced credit data)
- **Corporate customers**: Uses `PartnerApiSource` + `MarketDataSource` (market intelligence)

### Use Case 2: Fallback Chain
**Endpoint**: `GET /api/risk/score/{customerId}`

Automatic fallback cascade when sources are unavailable:
1. Try `CreditBureauSource` (highest quality)
2. If unavailable → try `CacheSource`
3. If unavailable → try `LegacyDatabaseSource`
4. If unavailable → use `DefaultValuesSource` (guaranteed)

### Use Case 3: Data Masking by Role & Feature Flags
**Endpoint**: `GET /api/risk/factors/{customerId}`

Data visibility controlled by user role and feature flags:
- **Admin role**: Sees all data including PII and detailed risk factors
- **Manager role**: Sees all data except PII (emails/phones masked)
- **User role**: Sees only aggregated data (no details, no PII)
- **Feature flags**: Controls visibility of credit scores and detailed risk data

### Use Case 4: Multi-Source Conflict Resolution
**Endpoint**: `GET /api/risk/report/{customerId}`

When multiple sources return different data, the system resolves conflicts using:
- **Priority strategy**: Use highest priority source
- **Merge strategy**: Combine all data (last wins)
- **Average strategy**: Average numeric values
- **Conservative strategy**: Most cautious values (lowest scores, highest risks)

## Data Source Array Structures

### Flat Arrays
```php
// InternalApiSource, CacheSource, LegacyDatabaseSource
['id' => 1, 'name' => 'John', 'email' => 'john@example.com']
```

### Nested Arrays
```php
// ConfigSource, PartnerApiSource
[
  'customer' => [
    'identity' => ['id' => 1, 'name' => 'John'],
    'contact' => ['email' => 'john@example.com']
  ]
]
```

### Deep Nested with Lists
```php
// CreditBureauSource, MarketDataSource
[
  'report' => [
    'subject' => ['id' => 1],
    'scores' => [
      ['type' => 'credit', 'value' => 750],
      ['type' => 'risk', 'value' => 25]
    ]
  ]
]
```

## Traits Hierarchy

- `IdentifiableTrait`: Provides `id` property and methods
- `TimestampableTrait`: Provides `createdAt`, `updatedAt`
- `AuditableTrait`: **Uses** `TimestampableTrait` + adds `createdBy`, `updatedBy` (multi-level trait)
- `ValidatableTrait`: Provides validation logic
- `SerializableTrait`: Provides `toArray()`, `fromArray()`

### Trait Usage Examples
- `Customer`: Uses `IdentifiableTrait`, `AuditableTrait`, `SerializableTrait`
- `Product`: Uses `IdentifiableTrait`, `TimestampableTrait`, `ValidatableTrait`
- `RiskProfile`: Uses `IdentifiableTrait`, `TimestampableTrait`, `SerializableTrait`

## Configuration

### Feature Flags (`config/feature_flags.yaml`)
- `show_detailed_risk`: Enable/disable detailed risk data
- `show_credit_score`: Enable/disable credit score visibility
- `mask_pii_data`: Enable/disable PII masking
- `enable_fallback_chain`: Enable/disable fallback mechanism
- etc.

### Data Sources (`config/sources.yaml`)
Configure priority and availability for each data source.

## Code Quality

- **PSR-12 Compliant**: Follows PHP coding standards
- **No Doctrine**: Pure PHP object hydration
- **Type Safety**: PHP 8.2+ with strict types
- **Separation of Concerns**: Clear layered architecture

## License

MIT

