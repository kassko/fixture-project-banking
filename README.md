# Fixture Project Banking

Un projet PHP/Symfony complet de fixture pour tester l'hydratation d'objets dans un contexte bancaire et d'assurance complexe.

## Vue d'ensemble

Ce projet modélise un système bancaire et d'assurance complet avec :
- Clients (particuliers, entreprises, premium, VIP)
- Comptes bancaires (courants, épargne, investissement)
- Transactions financières
- Polices d'assurance
- Bénéficiaires

Le projet est conçu pour tester des scénarios complexes d'hydratation d'objets avec :
- **Entités Doctrine complètes** avec relations ORM
- **Data objects legacy** SANS Doctrine (POPO)
- **Data objects hybrides** avec annotations Doctrine partielles
- **Objets imbriqués complexes** (3-4 niveaux de profondeur)
- **Héritage multi-niveaux** (jusqu'à 4 niveaux)
- **Traits réutilisables** pour les propriétés communes
- **DataSources legacy** retournant des tableaux complexes

## Structure du projet

```
src/
├── Entity/                          # Entités Doctrine pures
│   ├── Customer.php                 # Client standard
│   ├── BankAccount.php              # Compte bancaire
│   ├── Transaction.php              # Transaction financière
│   ├── InsurancePolicy.php          # Police d'assurance
│   ├── Beneficiary.php              # Bénéficiaire
│   ├── PremiumCustomer.php          # Client premium (niveau 3 héritage)
│   └── VIPCustomer.php              # Client VIP (niveau 4 héritage)
│
├── Legacy/
│   ├── DataObject/                  # Data objects legacy SANS Doctrine
│   │   ├── LegacyCustomerProfile.php
│   │   ├── LegacyRiskAssessment.php
│   │   └── LegacyPaymentSchedule.php
│   │
│   └── HybridDataObject/            # Data objects PARTIELLEMENT Doctrine
│       ├── HybridContract.php       # Certaines propriétés ORM, d'autres non
│       ├── HybridClaim.php
│       └── HybridInvestmentPortfolio.php
│
├── Model/                           # Objets imbriqués complexes (Value Objects)
│   ├── Financial/
│   │   ├── MoneyAmount.php          # Montant monétaire immutable
│   │   ├── InterestRate.php         # Taux d'intérêt
│   │   ├── AmortizationSchedule.php # Échéancier d'amortissement
│   │   └── PaymentPlan.php          # Plan de paiement
│   │
│   ├── Insurance/
│   │   ├── Coverage.php             # Couverture d'assurance
│   │   ├── Deductible.php           # Franchise
│   │   ├── ClaimDetails.php         # Détails de réclamation
│   │   └── RiskProfile.php          # Profil de risque
│   │
│   └── Common/
│       ├── Address.php              # Adresse avec coordonnées GPS
│       ├── ContactInfo.php          # Informations de contact
│       ├── DocumentReference.php    # Référence de document
│       └── AuditInfo.php            # Informations d'audit
│
├── Traits/                          # Traits réutilisables
│   ├── TimestampableTrait.php       # createdAt, updatedAt
│   ├── IdentifiableTrait.php        # id, uuid
│   ├── AuditableTrait.php           # createdBy, updatedBy, version
│   ├── SoftDeletableTrait.php       # deletedAt, isDeleted
│   ├── VersionableTrait.php         # version, previousVersionId
│   └── MetadataContainerTrait.php   # metadata array management
│
├── Abstract/                        # Classes abstraites (héritage multi-niveaux)
│   ├── AbstractPerson.php           # Niveau 1: Personne de base
│   ├── AbstractCustomer.php         # Niveau 2: extends AbstractPerson
│   ├── AbstractFinancialProduct.php # Produit financier de base
│   ├── AbstractContract.php         # Contrat de base
│   └── AbstractTransaction.php      # Transaction de base
│
├── Enum/                            # Énumérations
│   ├── CustomerType.php
│   ├── PremiumLevel.php
│   ├── AccountType.php
│   ├── TransactionType.php
│   ├── PolicyStatus.php
│   └── RiskCategory.php
│
├── Repository/                      # Repositories Doctrine
│   ├── CustomerRepository.php
│   ├── BankAccountRepository.php
│   ├── TransactionRepository.php
│   └── Fake/
│       └── FakeDataProvider.php     # Données en mémoire pour tests
│
├── DataSource/                      # Services sources de données legacy
│   ├── LegacyCustomerDataSource.php # Retourne des tableaux imbriqués
│   ├── LegacyPolicyDataSource.php
│   ├── ExternalRatingDataSource.php
│   └── HistoricalRatesDataSource.php
│
└── Fixture/                         # Données de fixture Doctrine
    ├── CustomerFixtures.php
    ├── ProductFixtures.php
    └── TransactionFixtures.php
```

## Caractéristiques techniques

### PHP 8.2+
- Typage strict (`declare(strict_types=1)`)
- Attributs PHP 8 pour Doctrine (pas d'annotations docblock)
- Enums natifs
- Propriétés promues dans les constructeurs

### Héritage multi-niveaux
Exemple de hiérarchie à 4 niveaux :
```
AbstractPerson (niveau 1)
    ↓
AbstractCustomer (niveau 2) extends AbstractPerson
    ↓
PremiumCustomer (niveau 3) extends AbstractCustomer
    ↓
VIPCustomer (niveau 4) extends PremiumCustomer
```

### Objets imbriqués complexes
Exemple d'imbrication profonde :
```
Customer
    → ContactInfo
        → Address
            → Coordonnées GPS (lat, lng)
        → Phones[] (tableau d'objets)
    → RiskProfile
        → Factors (array complexe)
```

### Data Objects Hybrides
Exemple avec `HybridContract` :
- `id`, `reference` : gérés par Doctrine (`#[ORM\Column]`)
- `legacyClausesData` : NON géré par Doctrine (pas d'annotation)
- `externalRating` : NON géré par Doctrine (objet externe)
- `calculatedRiskScore` : NON géré (propriété calculée)

### DataSources Legacy
Les DataSources retournent des **tableaux complexes imbriqués** simulant des systèmes legacy :
```php
$data = $legacySource->getCustomerData('CUST001');
// Retourne:
[
    'id' => 'CUST001',
    'personal_info' => [
        'first_name' => 'Jean',
        'contact' => [
            'email' => '...',
            'phones' => [...],
            'address' => [
                'street' => '...',
                'geo' => ['lat' => 48.8566, 'lng' => 2.3522]
            ]
        ]
    ],
    'accounts' => [...],
    'risk_profile' => [...]
]
```

## Installation

```bash
# Installer les dépendances
composer install

# Créer la base de données SQLite
php bin/console doctrine:database:create

# Créer le schéma
php bin/console doctrine:schema:create

# Charger les fixtures
php bin/console doctrine:fixtures:load
```

## Lancer le serveur

### Option 1: Avec Symfony CLI (recommandé)
```bash
symfony server:start
```

### Option 2: Avec le serveur PHP intégré
```bash
php -S localhost:8000 -t public
```

L'API sera accessible à l'adresse: `http://localhost:8000`

## Documentation API

### Swagger UI
Une fois le serveur lancé, accédez à la documentation interactive Swagger:
- **Interface Swagger UI**: http://localhost:8000/api/doc
- **Spécification OpenAPI JSON**: http://localhost:8000/api/doc.json

### API Platform
L'API Platform est également disponible pour les endpoints des entités:
- **API Platform UI**: http://localhost:8000/api (si configuré)

## Endpoints API REST

### Customers (Clients)
- `GET /api/v1/customers` - Liste tous les clients
- `GET /api/v1/customers/{id}` - Détail d'un client
- `POST /api/v1/customers/individual` - Créer un client individuel
- `POST /api/v1/customers/corporate` - Créer un client entreprise
- `POST /api/v1/customers/premium` - Créer un client premium
- `PUT /api/v1/customers/{id}` - Modifier un client
- `DELETE /api/v1/customers/{id}` - Supprimer un client

### Bank Accounts (Comptes bancaires)
- `GET /api/v1/accounts` - Liste des comptes
- `GET /api/v1/accounts/{id}` - Détail d'un compte
- `POST /api/v1/accounts/{id}/deposit` - Effectuer un dépôt
- `POST /api/v1/accounts/{id}/withdraw` - Effectuer un retrait
- `POST /api/v1/accounts/{id}/transfer` - Effectuer un virement
- `GET /api/v1/accounts/{id}/transactions` - Historique des transactions

### Transactions
- `GET /api/v1/transactions` - Liste des transactions
- `GET /api/v1/transactions/{id}` - Détail d'une transaction
- `GET /api/v1/transactions/reference/{reference}` - Recherche par référence

### Insurance Policies (Polices d'assurance)
- `GET /api/v1/policies` - Liste des polices
- `GET /api/v1/policies/{id}` - Détail d'une police
- `POST /api/v1/policies` - Créer une police
- `GET /api/v1/policies/{id}/coverages` - Couvertures d'une police
- `GET /api/v1/policies/{id}/beneficiaries` - Bénéficiaires d'une police

### Claims (Sinistres)
- `GET /api/v1/claims` - Liste des sinistres
- `GET /api/v1/claims/{id}` - Détail d'un sinistre
- `POST /api/v1/claims` - Déclarer un sinistre
- `PATCH /api/v1/claims/{id}/status` - Mettre à jour le statut

### Legacy Data (Données legacy - pour test hydratation)
- `GET /api/v1/legacy/customers/{legacyId}` - Données legacy client
- `GET /api/v1/legacy/customers/{legacyId}/history` - Historique legacy
- `GET /api/v1/legacy/policies/{policyId}` - Données legacy police
- `GET /api/v1/legacy/policies/{policyId}/claims` - Historique sinistres
- `GET /api/v1/legacy/ratings/credit/{customerId}` - Credit rating
- `GET /api/v1/legacy/ratings/market` - Données marché
- `GET /api/v1/legacy/rates/interest` - Taux d'intérêt
- `GET /api/v1/legacy/rates/exchange` - Taux de change
- `GET /api/v1/legacy/compliance/kyc/{customerId}` - Données KYC
- `GET /api/v1/legacy/compliance/aml/{customerId}` - Vérifications AML

## Exemples cURL

### Customers (Clients)

#### Lister tous les clients
```bash
curl -X GET http://localhost:8000/api/v1/customers
```

#### Obtenir un client par ID
```bash
curl -X GET http://localhost:8000/api/v1/customers/1
```

#### Créer un client individuel
```bash
curl -X POST http://localhost:8000/api/v1/customers/individual \
  -H "Content-Type: application/json" \
  -d '{
    "firstName": "Jean",
    "lastName": "Dupont",
    "customerNumber": "CUST-001"
  }'
```

#### Créer un client entreprise
```bash
curl -X POST http://localhost:8000/api/v1/customers/corporate \
  -H "Content-Type: application/json" \
  -d '{
    "companyName": "ACME Corp",
    "customerNumber": "CORP-001"
  }'
```

#### Créer un client premium
```bash
curl -X POST http://localhost:8000/api/v1/customers/premium \
  -H "Content-Type: application/json" \
  -d '{
    "firstName": "Marie",
    "lastName": "Martin",
    "customerNumber": "PREM-001"
  }'
```

#### Modifier un client
```bash
curl -X PUT http://localhost:8000/api/v1/customers/1 \
  -H "Content-Type: application/json" \
  -d '{
    "firstName": "Jean-Pierre",
    "lastName": "Dupont"
  }'
```

#### Supprimer un client
```bash
curl -X DELETE http://localhost:8000/api/v1/customers/1
```

### Bank Accounts (Comptes bancaires)

#### Lister tous les comptes
```bash
curl -X GET http://localhost:8000/api/v1/accounts
```

#### Obtenir un compte par ID
```bash
curl -X GET http://localhost:8000/api/v1/accounts/1
```

#### Effectuer un dépôt
```bash
curl -X POST http://localhost:8000/api/v1/accounts/1/deposit \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 500,
    "currency": "EUR"
  }'
```

#### Effectuer un retrait
```bash
curl -X POST http://localhost:8000/api/v1/accounts/1/withdraw \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 100,
    "currency": "EUR"
  }'
```

#### Effectuer un virement
```bash
curl -X POST http://localhost:8000/api/v1/accounts/1/transfer \
  -H "Content-Type: application/json" \
  -d '{
    "target_account_id": 2,
    "amount": 250,
    "currency": "EUR"
  }'
```

#### Historique des transactions d'un compte
```bash
curl -X GET http://localhost:8000/api/v1/accounts/1/transactions
```

### Transactions

#### Lister toutes les transactions
```bash
curl -X GET http://localhost:8000/api/v1/transactions
```

#### Obtenir une transaction par ID
```bash
curl -X GET http://localhost:8000/api/v1/transactions/1
```

#### Rechercher une transaction par référence
```bash
curl -X GET http://localhost:8000/api/v1/transactions/reference/TRF-12345
```

### Insurance Policies (Polices d'assurance)

#### Lister toutes les polices
```bash
curl -X GET http://localhost:8000/api/v1/policies
```

#### Obtenir une police par ID
```bash
curl -X GET http://localhost:8000/api/v1/policies/1
```

#### Créer une police
```bash
curl -X POST http://localhost:8000/api/v1/policies \
  -H "Content-Type: application/json" \
  -d '{
    "policyNumber": "POL-001",
    "customerId": 1,
    "premium": 1200.00
  }'
```

#### Obtenir les couvertures d'une police
```bash
curl -X GET http://localhost:8000/api/v1/policies/1/coverages
```

#### Obtenir les bénéficiaires d'une police
```bash
curl -X GET http://localhost:8000/api/v1/policies/1/beneficiaries
```

### Claims (Sinistres)

#### Lister tous les sinistres
```bash
curl -X GET http://localhost:8000/api/v1/claims
```

#### Obtenir un sinistre par ID
```bash
curl -X GET http://localhost:8000/api/v1/claims/1
```

#### Déclarer un sinistre
```bash
curl -X POST http://localhost:8000/api/v1/claims \
  -H "Content-Type: application/json" \
  -d '{
    "policyId": 1,
    "description": "Dégât des eaux dans la cuisine",
    "incidentDate": "2024-01-15"
  }'
```

#### Mettre à jour le statut d'un sinistre
```bash
curl -X PATCH http://localhost:8000/api/v1/claims/1/status \
  -H "Content-Type: application/json" \
  -d '{
    "status": "approved"
  }'
```

### Legacy Data (Données legacy - pour test hydratation)

#### Obtenir les données legacy d'un client
```bash
curl -X GET http://localhost:8000/api/v1/legacy/customers/CUST001
```

#### Obtenir l'historique legacy d'un client
```bash
curl -X GET http://localhost:8000/api/v1/legacy/customers/CUST001/history
```

#### Obtenir les données legacy d'une police
```bash
curl -X GET http://localhost:8000/api/v1/legacy/policies/POL001
```

#### Obtenir l'historique des sinistres d'une police
```bash
curl -X GET http://localhost:8000/api/v1/legacy/policies/POL001/claims
```

#### Obtenir le credit rating d'un client
```bash
curl -X GET http://localhost:8000/api/v1/legacy/ratings/credit/CUST001
```

#### Obtenir les données marché
```bash
curl -X GET http://localhost:8000/api/v1/legacy/ratings/market
```

#### Obtenir les taux d'intérêt
```bash
curl -X GET http://localhost:8000/api/v1/legacy/rates/interest
```

#### Obtenir les taux de change
```bash
curl -X GET http://localhost:8000/api/v1/legacy/rates/exchange
```

#### Obtenir les données KYC d'un client
```bash
curl -X GET http://localhost:8000/api/v1/legacy/compliance/kyc/CUST001
```

#### Obtenir les vérifications AML d'un client
```bash
curl -X GET http://localhost:8000/api/v1/legacy/compliance/aml/CUST001
```

## Utilisation

### Exemple 1 : Hydrater depuis DataSource legacy
```php
use App\DataSource\LegacyCustomerDataSource;

$source = new LegacyCustomerDataSource();
$data = $source->getCustomerData('CUST001');

// $data contient un tableau complexe imbriqué
// À utiliser pour tester un hydrateur d'objets
```

### Exemple 2 : Utiliser les entités Doctrine
```php
use App\Entity\Customer;
use App\Repository\CustomerRepository;

$customer = $customerRepository->findByCustomerNumber('CUST001');
$accounts = $customer->getAccounts();
```

### Exemple 3 : Travailler avec des objets hybrides
```php
use App\Legacy\HybridDataObject\HybridContract;

$contract = new HybridContract();
$contract->setReference('CNT-2024-001');
$contract->setMetadata(['version' => '1.0']); // Géré par Doctrine

// Propriétés NON gérées par Doctrine
$contract->setLegacyClausesData([...]);
$contract->setExternalRating($ratingObject);
$contract->setCalculatedRiskScore(7.5);
```

## Cas d'usage

Ce projet est conçu pour tester :

1. **Hydratation d'objets complexes** depuis des tableaux
2. **Mapping entre différents formats** (legacy → moderne)
3. **Gestion de l'héritage** multi-niveaux
4. **Objets imbriqués** profonds (3-4 niveaux)
5. **Mix Doctrine / Non-Doctrine** dans les mêmes classes
6. **Value Objects** immutables
7. **Traits** réutilisables
8. **Relations ORM** complexes (OneToMany, ManyToOne, etc.)

## Données de test

Le projet inclut :
- **4 clients** (2 standard, 1 premium, 1 VIP)
- **3 comptes bancaires** (checking, savings, investment)
- **6 transactions** (deposit, withdrawal, transfer, payment, fee)
- **2 polices d'assurance** (life, home)
- **Couvertures multiples** par police
- **Profils de risque** détaillés
- **Données de notation externe**
- **Historique de taux d'intérêt**

## Licence

MIT
