<?php

declare(strict_types=1);

namespace App\Fixture;

use App\Entity\Customer;
use App\Entity\PremiumCustomer;
use App\Entity\VIPCustomer;
use App\Enum\CustomerType;
use App\Enum\PremiumLevel;
use App\Model\Common\Address;
use App\Model\Common\ContactInfo;
use App\Model\Insurance\RiskProfile;
use App\Enum\RiskCategory;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Customer fixtures with realistic banking data.
 */
class CustomerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create regular customers
        $customer1 = new Customer();
        $customer1
            ->setFirstName('Jean')
            ->setLastName('Dupont')
            ->setCustomerNumber('CUST001')
            ->setType(CustomerType::INDIVIDUAL)
            ->setBirthDate(new DateTimeImmutable('1985-03-15'))
            ->setIsActive(true)
            ->setCreatedBy('system')
            ->setUpdatedBy('system');
        
        $customer1->updateTimestamps();
        $customer1->generateUuid();
        
        // Add contact info
        $address1 = new Address(
            '123 Rue de la Paix',
            'Paris',
            '75001',
            'FR',
            'Île-de-France',
            48.8566,
            2.3522
        );
        
        $contactInfo1 = new ContactInfo(
            'jean.dupont@email.com',
            [
                ['type' => 'mobile', 'number' => '+33612345678'],
                ['type' => 'home', 'number' => '+33145678900'],
            ],
            $address1
        );
        
        $customer1->setContactInfo($contactInfo1);
        
        // Add risk profile
        $riskProfile1 = new RiskProfile(
            72,
            RiskCategory::MODERATE,
            [
                'income_stability' => 'high',
                'debt_ratio' => 0.25,
                'payment_history' => 'excellent',
                'credit_utilization' => 0.30,
            ],
            new DateTimeImmutable('2024-01-15')
        );
        
        $customer1->setRiskProfile($riskProfile1);
        
        $manager->persist($customer1);
        
        // Create second customer
        $customer2 = new Customer();
        $customer2
            ->setFirstName('Marie')
            ->setLastName('Martin')
            ->setCustomerNumber('CUST002')
            ->setType(CustomerType::INDIVIDUAL)
            ->setBirthDate(new DateTimeImmutable('1990-07-22'))
            ->setIsActive(true)
            ->setCreatedBy('system')
            ->setUpdatedBy('system');
        
        $customer2->updateTimestamps();
        $customer2->generateUuid();
        
        $address2 = new Address(
            '456 Avenue des Champs',
            'Lyon',
            '69001',
            'FR',
            'Auvergne-Rhône-Alpes',
            45.7640,
            4.8357
        );
        
        $contactInfo2 = new ContactInfo(
            'marie.martin@email.com',
            [
                ['type' => 'mobile', 'number' => '+33687654321'],
            ],
            $address2
        );
        
        $customer2->setContactInfo($contactInfo2);
        
        $riskProfile2 = new RiskProfile(
            85,
            RiskCategory::LOW,
            [
                'income_stability' => 'very_high',
                'debt_ratio' => 0.15,
                'payment_history' => 'excellent',
                'credit_utilization' => 0.20,
            ],
            new DateTimeImmutable('2024-02-01')
        );
        
        $customer2->setRiskProfile($riskProfile2);
        
        $manager->persist($customer2);
        
        // Create premium customer
        $premiumCustomer = new PremiumCustomer();
        $premiumCustomer
            ->setFirstName('Philippe')
            ->setLastName('Bernard')
            ->setCustomerNumber('PREM001')
            ->setType(CustomerType::INDIVIDUAL)
            ->setBirthDate(new DateTimeImmutable('1975-11-08'))
            ->setIsActive(true)
            ->setLevel(PremiumLevel::GOLD)
            ->setPersonalAdvisorName('Sophie Dubois')
            ->setMinimumBalance(50000.00)
            ->setDiscountRate(10.00)
            ->setCreatedBy('system')
            ->setUpdatedBy('system');
        
        $premiumCustomer->updateTimestamps();
        $premiumCustomer->generateUuid();
        $premiumCustomer->addMetadata('preferred_contact_time', 'morning');
        $premiumCustomer->addMetadata('investment_experience', 'advanced');
        
        $manager->persist($premiumCustomer);
        
        // Create VIP customer
        $vipCustomer = new VIPCustomer();
        $vipCustomer
            ->setFirstName('Isabelle')
            ->setLastName('Rousseau')
            ->setCustomerNumber('VIP001')
            ->setType(CustomerType::INDIVIDUAL)
            ->setBirthDate(new DateTimeImmutable('1968-04-12'))
            ->setIsActive(true)
            ->setLevel(PremiumLevel::PLATINUM)
            ->setPersonalAdvisorName('Marc Lefevre')
            ->setMinimumBalance(250000.00)
            ->setDiscountRate(20.00)
            ->setPrivateBankerName('Antoine Moreau')
            ->setPrivateBankerEmail('antoine.moreau@bank.fr')
            ->setPrivateBankerPhone('+33144556677')
            ->setAnnualFee(10000.00)
            ->setHasConciergeService(true)
            ->setCreatedBy('system')
            ->setUpdatedBy('system');
        
        $vipCustomer->updateTimestamps();
        $vipCustomer->generateUuid();
        $vipCustomer->addMetadata('wealth_tier', 'ultra_high_net_worth');
        $vipCustomer->addMetadata('portfolio_value', 5000000);
        $vipCustomer->addExclusiveService('Private Banking');
        $vipCustomer->addExclusiveService('Wealth Management');
        $vipCustomer->addExclusiveService('Tax Advisory');
        $vipCustomer->addExclusiveService('Estate Planning');
        
        $manager->persist($vipCustomer);
        
        $manager->flush();
    }
}
