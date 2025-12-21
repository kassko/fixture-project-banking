# Architecture du Modèle de Données - Système Banque/Assurance

Ce document décrit l'architecture complète du modèle de données utilisé pour tester l'hydrateur d'objets dans un contexte réaliste et complexe.

## Vue d'ensemble

```text
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                                    TRAITS                                                │
├─────────────────────────────────────────────────────────────────────────────────────────┤
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐ │
│  │ TimestampableTrait│  │ IdentifiableTrait│  │  AuditableTrait  │  │SoftDeletableTrait│ │
│  ├──────────────────┤  ├──────────────────┤  ├──────────────────┤  ├──────────────────┤ │
│  │ - createdAt      │  │ - uuid           │  │ - createdBy      │  │ - deletedAt      │ │
│  │ - updatedAt      │  │ - externalId     │  │ - updatedBy      │  │ - isDeleted      │ │
│  └──────────────────┘  └──────────────────┘  │ - auditLog[]     │  └──────────────────┘ │
│                                              └──────────────────┘                        │
│  ┌──────────────────┐  ┌──────────────────┐                                             │
│  │ VersionableTrait │  │MetadataContainer │                                             │
│  ├──────────────────┤  │     Trait        │                                             │
│  │ - version        │  ├──────────────────┤                                             │
│  │ - previousHash   │  │ - metadata[]     │                                             │
│  └──────────────────┘  └──────────────────┘                                             │
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

## Hiérarchie d'héritage des personnes (3+ niveaux)

```text
                    ┌─────────────────────────┐
                    │   <<abstract>>          │
                    │   AbstractPerson        │
                    │   (Niveau 1)            │
                    ├─────────────────────────┤
                    │ + IdentifiableTrait     │
                    │ + TimestampableTrait    │
                    ├─────────────────────────┤
                    │ - firstName: string     │
                    │ - lastName: string      │
                    │ - birthDate: DateTime   │
                    └───────────┬─────────────┘
                                │
                ┌───────────────┼───────────────┬───────────────┐
                │               │               │               │
                ▼               ▼               ▼               ▼
    ┌─────────────────┐  ┌──────────────┐  ┌────────────┐  ┌────────────┐
    │  <<abstract>>   │  │ Beneficiary  │  │  Employee  │  │   Agent    │
    │AbstractCustomer │  ├──────────────┤  ├────────────┤  ├────────────┤
    │  (Niveau 2)     │  │- relationship│  │- department│  │- agentCode │
    ├─────────────────┤  │- percentage  │  │- position  │  │- commission│
    │+AuditableTrait  │  │- isPrimary   │  │- salary    │  │- territory │
    ├─────────────────┤  └──────────────┘  └────────────┘  └────────────┘
    │- customerNumber │
    │- type: enum     │
    │- isActive: bool │
    └────────┬────────┘
             │
    ┌────────┼────────┬────────────────┐
    │        │        │                │
    ▼        ▼        ▼                ▼
┌──────────┐ ┌──────────────┐ ┌───────────────┐ ┌──────────────┐
│ Customer │ │ Individual   │ │  Corporate    │ │Premium       │
│(Standard)│ │  Customer    │ │  Customer     │ │Customer      │
├──────────┤ ├──────────────┤ ├───────────────┤ │(Niveau 3)    │
│- segment │ │- occupation  │ │- companyName  │ ├──────────────┤
│- rating  │ │- income      │ │- industry     │ │+Metadata     │
└──────────┘ │- maritalStat │ │- taxId        │ │ ContainerTr. │
             └──────────────┘ │- employees    │ ├──────────────┤
                              └───────────────┘ │- level: enum │
                                                │- advisor     │
                                                │- minBalance  │
                                                │- discountRate│
                                                └──────┬───────┘
                                                       │
                                                       ▼
                                               ┌───────────────┐
                                               │ VIPCustomer   │
                                               │ (Niveau 4)    │
                                               ├───────────────┤
                                               │+ SoftDeletable│
                                               │  Trait        │
                                               ├───────────────┤
                                               │- concierge    │
                                               │- limousine    │
                                               │- privateEvents│
                                               └───────────────┘
```

## Hiérarchie des produits financiers

```text
                    ┌───────────────────────────┐
                    │     <<abstract>>          │
                    │ AbstractFinancialProduct  │
                    │      (Niveau 1)           │
                    ├───────────────────────────┤
                    │ + TimestampableTrait      │
                    │ + AuditableTrait          │
                    ├───────────────────────────┤
                    │ - productCode: string     │
                    │ - productName: string     │
                    │ - amount: MoneyAmount     │
                    │ - interestRate: Interest  │
                    │ - isActive: bool          │
                    │ + calculateValue(): Money │
                    └─────────────┬─────────────┘
                                  │
        ┌─────────────────────────┼─────────────────────────┐
        │                         │                         │
        ▼                         ▼                         ▼
┌──────────────────┐   ┌──────────────────┐   ┌──────────────────┐
│  <<abstract>>    │   │  <<abstract>>    │   │  <<abstract>>    │
│AbstractBankProduct│  │AbstractInsurance │   │AbstractInvestment│
│   (Niveau 2)     │   │    Product       │   │    Product       │
├──────────────────┤   │   (Niveau 2)     │   │   (Niveau 2)     │
│- accountNumber   │   ├──────────────────┤   ├──────────────────┤
│- balance         │   │- policyNumber    │   │- portfolioId     │
│- accountType     │   │- premium         │   │- totalValue      │
│- overdraftLimit  │   │- coverageAmount  │   │- riskLevel       │
└────────┬─────────┘   │- deductible      │   │- diversification │
         │             └────────┬─────────┘   └────────┬─────────┘
    ┌────┴────┐            ┌────┴────┐            ┌────┴────┐
    ▼         ▼            ▼         ▼            ▼         ▼
┌──────────┐┌──────────┐┌──────────┐┌──────────┐┌──────────┐┌──────────┐
│Checking  ││Savings   ││Life      ││Home      ││Fund      ││Stocks    │
│Account   ││Account   ││Insurance ││Insurance ││Portfolio ││Portfolio │
│(Niveau 3)││(Niveau 3)││Policy    ││Policy    ││(Niveau 3)││(Niveau 3)│
├──────────┤├──────────┤│(Niveau 3)││(Niveau 3)│├──────────┤├──────────┤
│- checkFee││- minBal  │├──────────┤├──────────┤│- funds[] ││- stocks[]│
│- freeChk ││- apy     ││- benefic.││- property││- strategy││- broker  │
│- debitCrd││- withdraw││- medical ││- location││- manager ││- trades[]│
└──────────┘│  Limit   ││- cashVal ││- rebuild ││- fees    ││- dividend│
            └──────────┘│- maturity││- contents│└──────────┘└──────────┘
                        └──────────┘└──────────┘
```

## Entités Doctrine principales

```text
┌──────────────────────────────────────────────────────────────────────────────┐
│                        ENTITÉS DOCTRINE COMPLÈTES                             │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  ┌────────────────────┐          ┌────────────────────┐                      │
│  │   BankAccount      │          │  InsurancePolicy   │                      │
│  │   [Entity/ORM]     │          │   [Entity/ORM]     │                      │
│  ├────────────────────┤          ├────────────────────┤                      │
│  │ #[ORM\Entity]      │          │ #[ORM\Entity]      │                      │
│  │ #[ORM\Table(...)]  │          │ #[ORM\Table(...)]  │                      │
│  ├────────────────────┤          ├────────────────────┤                      │
│  │ - id: int          │          │ - id: int          │                      │
│  │ - accountNumber    │          │ - policyNumber     │                      │
│  │ - type: AccountType│          │ - status: enum     │                      │
│  │ - balance: decimal │          │ - premium: Money   │                      │
│  │ - currency: string │          │ - startDate        │                      │
│  │ - customer ──────┐ │          │ - endDate          │                      │
│  │ - transactions[] │ │          │ - coverages[]      │                      │
│  └────────────────┬─┘ │          │ - beneficiaries[]  │                      │
│                   │   │          │ - customer ────┐   │                      │
│                   │   │          └────────────────┼───┘                      │
│  ┌────────────────┼───┼──────────────────────┐   │                          │
│  │   Transaction  │   │                      │   │                          │
│  │   [Entity/ORM] │   │                      │   │                          │
│  ├────────────────┤   │                      │   │                          │
│  │ #[ORM\Entity]  │   │                      │   │                          │
│  ├────────────────┤   │                      │   │                          │
│  │ - id: int      │   │                      │   │                          │
│  │ - reference    │   │                      │   │                          │
│  │ - type: enum   │   │                      │   │                          │
│  │ - amount       │   │                      │   │                          │
│  │ - date         │   │                      │   │                          │
│  │ - account ─────┘   │                      │   │                          │
│  │ - description      │                      │   │                          │
│  └────────────────────┘                      │   │                          │
│                                               │   │                          │
│  ┌───────────────────────────────────────────┼───┘                          │
│  │   Customer                                │                              │
│  │   [Entity/ORM]                            │                              │
│  │   extends AbstractCustomer                │                              │
│  ├───────────────────────────────────────────┤                              │
│  │ #[ORM\Entity]                             │                              │
│  ├───────────────────────────────────────────┤                              │
│  │ - id: int                                 │                              │
│  │ - accounts[] ──────────────────────────┐  │                              │
│  │ - policies[] ──────────────────────────┼──┘                              │
│  │ - contactInfo: ContactInfo             │                                 │
│  │ - riskProfile: RiskProfile             │                                 │
│  └────────────────────────────────────────┘                                 │
│                                                                               │
│  ┌─────────────────────┐                                                     │
│  │   Claim             │                                                     │
│  │   [Entity/ORM]      │                                                     │
│  ├─────────────────────┤                                                     │
│  │ - id: int           │                                                     │
│  │ - claimNumber       │                                                     │
│  │ - status: enum      │                                                     │
│  │ - amount: Money     │                                                     │
│  │ - claimDate         │                                                     │
│  │ - description       │                                                     │
│  │ - policy            │                                                     │
│  │ - details: ClaimDet.│                                                     │
│  └─────────────────────┘                                                     │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Value Objects / Objets imbriqués

```text
┌──────────────────────────────────────────────────────────────────────────────┐
│                     VALUE OBJECTS (Objets imbriqués)                          │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  FINANCIAL (Model/Financial/)                                                │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐              │
│  │  MoneyAmount    │  │  InterestRate   │  │  PaymentPlan    │              │
│  ├─────────────────┤  ├─────────────────┤  ├─────────────────┤              │
│  │ - amount: float │  │ - rate: float   │  │ - frequency     │              │
│  │ - currency: str │  │ - type: enum    │  │ - startDate     │              │
│  │ + format()      │  │ - basis: string │  │ - endDate       │              │
│  │ + add()         │  │ + calculate()   │  │ - payments[]    │              │
│  │ + subtract()    │  │ + toDecimal()   │  │ + calculate()   │              │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘              │
│                                                                               │
│  COMMON (Model/Common/)                                                      │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐              │
│  │    Address      │  │   ContactInfo   │  │   AuditInfo     │              │
│  ├─────────────────┤  ├─────────────────┤  ├─────────────────┤              │
│  │ - street        │  │ - email         │  │ - createdBy     │              │
│  │ - city          │  │ - phones[]      │  │ - createdAt     │              │
│  │ - postalCode    │  │ - address ───┐  │  │ - updatedBy     │              │
│  │ - country       │  │ - preferred  │  │  │ - updatedAt     │              │
│  │ - latitude      │  │              │  │  │ - ipAddress     │              │
│  │ - longitude     │  │              │  │  │ - userAgent     │              │
│  │ + getFullAddr() │  └──────────────┼──┘  │ + getHistory()  │              │
│  │ + hasCoords()   │                 │     └─────────────────┘              │
│  └─────────────────┘◄────────────────┘                                       │
│                                                                               │
│  ┌──────────────────┐                                                        │
│  │ GeoCoordinates   │                                                        │
│  ├──────────────────┤                                                        │
│  │ - latitude       │                                                        │
│  │ - longitude      │                                                        │
│  │ - altitude       │                                                        │
│  │ + distance()     │                                                        │
│  └──────────────────┘                                                        │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Objets imbriqués profonds (Insurance)

```text
┌──────────────────────────────────────────────────────────────────────────────┐
│                  OBJETS IMBRIQUÉS PROFONDS (3-4 niveaux)                      │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  InsurancePolicy                                                             │
│    │                                                                          │
│    ├─► Coverage (Model/Insurance/)                                           │
│    │   ┌──────────────────────────────────────┐                              │
│    │   │ - coverageType: string               │                              │
│    │   │ - coverageAmount: MoneyAmount        │                              │
│    │   │ - deductible: Deductible ──┐         │                              │
│    │   │ - premium: MoneyAmount      │         │                              │
│    │   │ - isActive: bool            │         │                              │
│    │   └─────────────────────────────┼─────────┘                              │
│    │                                 │                                        │
│    │                        ┌────────▼─────────┐                              │
│    │                        │   Deductible     │                              │
│    │                        ├──────────────────┤                              │
│    │                        │ - amount: Money  │                              │
│    │                        │ - type: enum     │                              │
│    │                        │ - percentage     │                              │
│    │                        │ + calculate()    │                              │
│    │                        └──────────────────┘                              │
│    │                                                                          │
│    └─► ClaimDetails (Model/Insurance/)                                       │
│        ┌───────────────────────────────────────┐                             │
│        │ - claimDate: DateTime                 │                             │
│        │ - reportedDate: DateTime              │                             │
│        │ - incidentDescription: text           │                             │
│        │ - estimatedAmount: MoneyAmount        │                             │
│        │ - damageAssessment: DamageAssess. ──┐ │                             │
│        │ - documents: DocumentRef[]          │ │                             │
│        │ - status: enum                      │ │                             │
│        └─────────────────────────────────────┼─┘                             │
│                                              │                               │
│                                     ┌────────▼─────────────┐                 │
│                                     │ DamageAssessment     │                 │
│                                     ├──────────────────────┤                 │
│                                     │ - assessmentDate     │                 │
│                                     │ - assessor: string   │                 │
│                                     │ - damageType: enum   │                 │
│                                     │ - severity: enum     │                 │
│                                     │ - estimatedCost      │                 │
│                                     │ - repairDuration     │                 │
│                                     │ - photos: string[]   │                 │
│                                     │ + generateReport()   │                 │
│                                     └──────────────────────┘                 │
│                                                                               │
│  RiskProfile (Model/Insurance/)                                              │
│  ┌────────────────────────────────────────┐                                  │
│  │ - riskCategory: RiskCategory           │                                  │
│  │ - score: float                         │                                  │
│  │ - factors: array                       │                                  │
│  │   ├─ age_factor                        │                                  │
│  │   ├─ location_risk                     │                                  │
│  │   ├─ claims_history                    │                                  │
│  │   └─ credit_score                      │                                  │
│  │ - lastUpdated: DateTime                │                                  │
│  │ + calculateScore(): float              │                                  │
│  │ + isHighRisk(): bool                   │                                  │
│  └────────────────────────────────────────┘                                  │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Data Objects Legacy (SANS Doctrine)

```text
┌──────────────────────────────────────────────────────────────────────────────┐
│              LEGACY DATA OBJECTS (Legacy/DataObject/)                         │
│              Pure POPO - AUCUNE annotation Doctrine                          │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  ┌────────────────────────────────────────────────────────┐                  │
│  │  LegacyCustomerProfile                                 │                  │
│  │  [Pure PHP Object]                                     │                  │
│  ├────────────────────────────────────────────────────────┤                  │
│  │ - customerId: string                                   │                  │
│  │ - profileData: array                                   │                  │
│  │   ├─ personal_info                                     │                  │
│  │   ├─ financial_data                                    │                  │
│  │   ├─ preferences                                       │                  │
│  │   └─ marketing_consent                                 │                  │
│  │ - lastSync: DateTime                                   │                  │
│  │ - source: string                                       │                  │
│  │ + toArray(): array                                     │                  │
│  │ + fromArray(array): self                               │                  │
│  └────────────────────────────────────────────────────────┘                  │
│                                                                               │
│  ┌────────────────────────────────────────────────────────┐                  │
│  │  LegacyRiskAssessment                                  │                  │
│  │  [Pure PHP Object]                                     │                  │
│  ├────────────────────────────────────────────────────────┤                  │
│  │ - assessmentId: string                                 │                  │
│  │ - riskData: array                                      │                  │
│  │   ├─ credit_score                                      │                  │
│  │   ├─ debt_ratio                                        │                  │
│  │   ├─ employment_history                                │                  │
│  │   └─ previous_claims                                   │                  │
│  │ - calculatedScore: float                               │                  │
│  │ - assessedAt: DateTime                                 │                  │
│  │ + calculateRisk(): float                               │                  │
│  └────────────────────────────────────────────────────────┘                  │
│                                                                               │
│  ┌────────────────────────────────────────────────────────┐                  │
│  │  LegacyPaymentSchedule                                 │                  │
│  │  [Pure PHP Object]                                     │                  │
│  ├────────────────────────────────────────────────────────┤                  │
│  │ - scheduleId: string                                   │                  │
│  │ - contractId: string                                   │                  │
│  │ - installments: array                                  │                  │
│  │   └─ [{date, amount, status, fee}...]                 │                  │
│  │ - frequency: string                                    │                  │
│  │ - totalAmount: float                                   │                  │
│  │ + getNextPayment(): array                              │                  │
│  │ + calculateRemaining(): float                          │                  │
│  └────────────────────────────────────────────────────────┘                  │
│                                                                               │
│  ┌────────────────────────────────────────────────────────┐                  │
│  │  LegacyDocumentArchive                                 │                  │
│  │  [Pure PHP Object]                                     │                  │
│  ├────────────────────────────────────────────────────────┤                  │
│  │ - archiveId: string                                    │                  │
│  │ - documents: array                                     │                  │
│  │   └─ [{id, type, path, uploadedAt, size}...]          │                  │
│  │ - customerId: string                                   │                  │
│  │ - retention: DateInterval                              │                  │
│  │ + getDocument(id): array                               │                  │
│  │ + isExpired(): bool                                    │                  │
│  └────────────────────────────────────────────────────────┘                  │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Data Objects Hybrides (Doctrine partiel)

```text
┌──────────────────────────────────────────────────────────────────────────────┐
│           HYBRID DATA OBJECTS (Legacy/HybridDataObject/)                      │
│           Certaines propriétés avec #[ORM\...], d'autres sans                │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  ┌────────────────────────────────────────────────────────┐                  │
│  │  HybridContract                                        │                  │
│  ├────────────────────────────────────────────────────────┤                  │
│  │ AVEC Doctrine (#[ORM\Column]):                        │                  │
│  │   - id: int                    #[ORM\Id]              │                  │
│  │   - reference: string          #[ORM\Column]          │                  │
│  │   - metadata: array            #[ORM\Column(JSON)]    │                  │
│  │   - signedAt: DateTime         #[ORM\Column]          │                  │
│  │   - signedBy: string           #[ORM\Column]          │                  │
│  ├────────────────────────────────────────────────────────┤                  │
│  │ SANS Doctrine (propriétés normales):                  │                  │
│  │   - legacyClausesData: array   (pas d'annotation)     │                  │
│  │   - externalRating: object     (pas d'annotation)     │                  │
│  │   - calculatedRiskScore: float (pas d'annotation)     │                  │
│  │   - validationErrors: array    (pas d'annotation)     │                  │
│  └────────────────────────────────────────────────────────┘                  │
│                                                                               │
│  ┌────────────────────────────────────────────────────────┐                  │
│  │  HybridInvestmentPortfolio                             │                  │
│  ├────────────────────────────────────────────────────────┤                  │
│  │ AVEC Doctrine:                                         │                  │
│  │   - id: int                    #[ORM\Id]              │                  │
│  │   - portfolioId: string        #[ORM\Column]          │                  │
│  │   - name: string               #[ORM\Column]          │                  │
│  │   - createdAt: DateTime        #[ORM\Column]          │                  │
│  ├────────────────────────────────────────────────────────┤                  │
│  │ SANS Doctrine:                                         │                  │
│  │   - holdings: array            (données en mémoire)   │                  │
│  │   - marketData: object         (API externe)          │                  │
│  │   - performanceMetrics: array  (calculé)              │                  │
│  │   - rebalancingSuggestions     (IA/algorithme)        │                  │
│  └────────────────────────────────────────────────────────┘                  │
│                                                                               │
│  ┌────────────────────────────────────────────────────────┐                  │
│  │  HybridClaim                                           │                  │
│  ├────────────────────────────────────────────────────────┤                  │
│  │ AVEC Doctrine:                                         │                  │
│  │   - id: int                    #[ORM\Id]              │                  │
│  │   - claimNumber: string        #[ORM\Column]          │                  │
│  │   - status: enum               #[ORM\Column]          │                  │
│  │   - submittedAt: DateTime      #[ORM\Column]          │                  │
│  ├────────────────────────────────────────────────────────┤                  │
│  │ SANS Doctrine:                                         │                  │
│  │   - legacyAdjusterNotes: text  (système legacy)       │                  │
│  │   - externalInspectionReport   (tiers)                │                  │
│  │   - fraudDetectionScore: float (ML model)             │                  │
│  │   - workflowState: object      (état runtime)         │                  │
│  └────────────────────────────────────────────────────────┘                  │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Sources de données

```text
┌──────────────────────────────────────────────────────────────────────────────┐
│                    DATA SOURCES (DataSource/)                                 │
│            Retournent des tableaux complexes imbriqués                       │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  ┌────────────────────────────────────────────────────────┐                  │
│  │  LegacyCustomerDataSource                              │                  │
│  ├────────────────────────────────────────────────────────┤                  │
│  │ + getCustomerData(id): array                           │                  │
│  │   Retourne:                                            │                  │
│  │   [                                                    │                  │
│  │     'id' => 'CUST001',                                 │                  │
│  │     'personal_info' => [                               │                  │
│  │       'first_name' => '...',                           │                  │
│  │       'contact' => [                                   │                  │
│  │         'email' => '...',                              │                  │
│  │         'phones' => [...],                             │                  │
│  │         'address' => [                                 │                  │
│  │           'street' => '...',                           │                  │
│  │           'geo' => ['lat' => x, 'lng' => y]            │                  │
│  │         ]                                              │                  │
│  │       ]                                                │                  │
│  │     ],                                                 │                  │
│  │     'accounts' => [...],                               │                  │
│  │     'risk_profile' => [...]                            │                  │
│  │   ]                                                    │                  │
│  └────────────────────────────────────────────────────────┘                  │
│                                                                               │
│  ┌────────────────────────────────────────────────────────┐                  │
│  │  LegacyPolicyDataSource                                │                  │
│  ├────────────────────────────────────────────────────────┤                  │
│  │ + getPolicyData(policyNumber): array                   │                  │
│  │   Retourne structure complexe avec:                    │                  │
│  │   - Informations de base                               │                  │
│  │   - Coverages imbriqués                                │                  │
│  │   - Bénéficiaires                                      │                  │
│  │   - Historique des primes                              │                  │
│  │   - Documents associés                                 │                  │
│  └────────────────────────────────────────────────────────┘                  │
│                                                                               │
│  ┌────────────────────────────────────────────────────────┐                  │
│  │  ExternalRatingDataSource                              │                  │
│  ├────────────────────────────────────────────────────────┤                  │
│  │ + getCreditRating(customerId): array                   │                  │
│  │   Simule appel API externe:                            │                  │
│  │   - Score de crédit                                    │                  │
│  │   - Historique de paiement                             │                  │
│  │   - Dettes actuelles                                   │                  │
│  │   - Recommandations                                    │                  │
│  └────────────────────────────────────────────────────────┘                  │
│                                                                               │
│  ┌────────────────────────────────────────────────────────┐                  │
│  │  HistoricalRatesDataSource                             │                  │
│  ├────────────────────────────────────────────────────────┤                  │
│  │ + getInterestRates(period): array                      │                  │
│  │   Historique des taux:                                 │                  │
│  │   - Taux de base                                       │                  │
│  │   - Taux préférentiels                                 │                  │
│  │   - Taux hypothécaires                                 │                  │
│  │   - Évolution dans le temps                            │                  │
│  └────────────────────────────────────────────────────────┘                  │
│                                                                               │
│  ┌────────────────────────────────────────────────────────┐                  │
│  │  ComplianceDataSource                                  │                  │
│  ├────────────────────────────────────────────────────────┤                  │
│  │ + getComplianceChecks(entity): array                   │                  │
│  │   Vérifications réglementaires:                        │                  │
│  │   - KYC (Know Your Customer)                           │                  │
│  │   - AML (Anti-Money Laundering)                        │                  │
│  │   - Sanctions lists                                    │                  │
│  │   - GDPR compliance                                    │                  │
│  └────────────────────────────────────────────────────────┘                  │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Légende

```text
┌──────────────────────────────────────────────────────────────────────────────┐
│                              LÉGENDE                                          │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  Symboles utilisés:                                                          │
│                                                                               │
│  <<abstract>>     = Classe abstraite (ne peut être instanciée)              │
│  [Entity/ORM]     = Entité Doctrine complète avec annotations               │
│  [Pure PHP]       = POPO sans Doctrine (Plain Old PHP Object)               │
│  [Hybrid]         = Mix de propriétés Doctrine et non-Doctrine              │
│                                                                               │
│  #[ORM\Entity]    = Attribut PHP 8 Doctrine                                 │
│  #[ORM\Column]    = Propriété persistée en base de données                  │
│                                                                               │
│  ─────►           = Composition / Contient                                   │
│  ──┐              = Association / Relation                                   │
│  │                                                                            │
│  ▼                                                                            │
│                                                                               │
│  extends          = Héritage (is-a relationship)                             │
│  uses             = Utilise un Trait                                         │
│                                                                               │
│  Types de données:                                                           │
│  - string         = Chaîne de caractères                                     │
│  - int            = Entier                                                   │
│  - float          = Nombre à virgule flottante                               │
│  - decimal        = Nombre décimal (précision fixe)                          │
│  - bool           = Booléen (true/false)                                     │
│  - enum           = Énumération (valeurs prédéfinies)                        │
│  - DateTime       = Date et heure                                            │
│  - array          = Tableau                                                  │
│  - object         = Objet (classe non spécifiée)                             │
│  - ?type          = Type nullable (peut être null)                           │
│  - type[]         = Collection/Tableau de type                               │
│                                                                               │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Tableau résumé des complexités

```text
┌──────────────────────────────────────────────────────────────────────────────┐
│                    RÉSUMÉ DES COMPLEXITÉS DU MODÈLE                           │
├─────────────────────────────────┬────────────────────────────────────────────┤
│ Caractéristique                 │ Détail                                     │
├─────────────────────────────────┼────────────────────────────────────────────┤
│ Niveaux d'héritage maximum      │ 4 niveaux                                  │
│                                 │ (AbstractPerson → AbstractCustomer →       │
│                                 │  PremiumCustomer → VIPCustomer)            │
├─────────────────────────────────┼────────────────────────────────────────────┤
│ Profondeur d'imbrication max    │ 4 niveaux                                  │
│                                 │ (Customer → ContactInfo → Address →        │
│                                 │  GeoCoordinates)                           │
├─────────────────────────────────┼────────────────────────────────────────────┤
│ Traits réutilisables            │ 6 traits                                   │
│                                 │ (Timestampable, Identifiable, Auditable,   │
│                                 │  SoftDeletable, Versionable, Metadata)     │
├─────────────────────────────────┼────────────────────────────────────────────┤
│ Entités Doctrine complètes      │ 7 entités                                  │
│                                 │ (Customer, BankAccount, Transaction,       │
│                                 │  InsurancePolicy, Beneficiary, Premium,    │
│                                 │  VIP, Claim)                               │
├─────────────────────────────────┼────────────────────────────────────────────┤
│ Data Objects Legacy (POPO)      │ 4 classes                                  │
│                                 │ (LegacyCustomerProfile, LegacyRisk,        │
│                                 │  LegacyPayment, LegacyDocument)            │
├─────────────────────────────────┼────────────────────────────────────────────┤
│ Data Objects Hybrides           │ 3 classes                                  │
│                                 │ (HybridContract, HybridInvestment,         │
│                                 │  HybridClaim)                              │
├─────────────────────────────────┼────────────────────────────────────────────┤
│ Value Objects                   │ 12+ classes                                │
│                                 │ (MoneyAmount, InterestRate, Address,       │
│                                 │  ContactInfo, Coverage, Deductible,        │
│                                 │  ClaimDetails, DamageAssessment, etc.)     │
├─────────────────────────────────┼────────────────────────────────────────────┤
│ Sources de données externes     │ 5 sources                                  │
│                                 │ (LegacyCustomer, LegacyPolicy, External    │
│                                 │  Rating, HistoricalRates, Compliance)      │
├─────────────────────────────────┼────────────────────────────────────────────┤
│ Énumérations                    │ 6 enums                                    │
│                                 │ (CustomerType, PremiumLevel, AccountType,  │
│                                 │  TransactionType, PolicyStatus, Risk)      │
├─────────────────────────────────┼────────────────────────────────────────────┤
│ Relations ORM                   │ OneToMany, ManyToOne, ManyToMany           │
│                                 │ + Cascade operations                       │
├─────────────────────────────────┼────────────────────────────────────────────┤
│ Stratégies de persistance       │ 3 stratégies                               │
│                                 │ - Complète (Doctrine ORM)                  │
│                                 │ - Aucune (POPO legacy)                     │
│                                 │ - Hybride (mix)                            │
├─────────────────────────────────┼────────────────────────────────────────────┤
│ Types de produits financiers    │ 3 catégories × 2-3 types                   │
│                                 │ - Banking (Checking, Savings)              │
│                                 │ - Insurance (Life, Home)                   │
│                                 │ - Investment (Funds, Stocks)               │
├─────────────────────────────────┼────────────────────────────────────────────┤
│ Hiérarchies de personnes        │ 3 branches principales                     │
│                                 │ - Customers (4 niveaux)                    │
│                                 │ - Beneficiaries                            │
│                                 │ - Employees / Agents                       │
└─────────────────────────────────┴────────────────────────────────────────────┘
```

## Cas d'usage du modèle

Ce modèle de données complexe est conçu pour tester et valider :

### 1. Hydratation d'objets complexes
- ✓ Conversion de tableaux imbriqués en objets structurés
- ✓ Gestion de données provenant de sources legacy
- ✓ Mapping entre différents formats de données

### 2. Gestion de l'héritage multi-niveaux
- ✓ Propriétés héritées à travers 4 niveaux
- ✓ Traits composés à différents niveaux
- ✓ Polymorphisme et substitution

### 3. Objets imbriqués profonds
- ✓ Navigation dans des structures à 3-4 niveaux
- ✓ Value Objects immutables
- ✓ Objets composites complexes

### 4. Mix de stratégies de persistance
- ✓ Entités Doctrine complètes
- ✓ POPO sans persistence
- ✓ Objets hybrides (mix Doctrine/non-Doctrine)

### 5. Relations ORM complexes
- ✓ OneToMany / ManyToOne bidirectionnelles
- ✓ Collections ordonnées et filtrées
- ✓ Cascade et orphan removal

### 6. Intégration de données externes
- ✓ Sources de données legacy (tableaux)
- ✓ APIs externes simulées
- ✓ Données calculées et enrichies

---

**Note**: Ce modèle représente un système bancaire/assurance réaliste et complet, conçu pour tester des scénarios d'hydratation d'objets dans toute leur complexité.
