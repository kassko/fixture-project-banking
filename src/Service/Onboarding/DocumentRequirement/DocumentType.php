<?php

declare(strict_types=1);

namespace App\Service\Onboarding\DocumentRequirement;

class DocumentType
{
    public const PASSPORT = 'PASSPORT';
    public const NATIONAL_ID = 'NATIONAL_ID';
    public const DRIVER_LICENSE = 'DRIVER_LICENSE';
    public const PROOF_OF_ADDRESS = 'PROOF_OF_ADDRESS';
    public const INCOME_PROOF = 'INCOME_PROOF';
    public const TAX_RETURN = 'TAX_RETURN';
    public const COMPANY_REGISTRATION = 'COMPANY_REGISTRATION';
    public const COMPANY_STATUTES = 'COMPANY_STATUTES';
    public const BENEFICIAL_OWNER_DECLARATION = 'BENEFICIAL_OWNER_DECLARATION';
}
