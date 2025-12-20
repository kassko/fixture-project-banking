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

# Créer la base de données
php bin/console doctrine:database:create

# Créer le schéma
php bin/console doctrine:schema:create

# Charger les fixtures
php bin/console doctrine:fixtures:load
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
