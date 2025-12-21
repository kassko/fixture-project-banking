<?php

declare(strict_types=1);

namespace App\Context;

class CampaignContext
{
    public function __construct(
        private array $activeCampaigns = []
    ) {
    }

    public function getActiveCampaigns(): array
    {
        return $this->activeCampaigns;
    }

    public function hasCampaign(string $campaignId): bool
    {
        return isset($this->activeCampaigns[$campaignId]);
    }

    public function getCampaign(string $campaignId): ?array
    {
        return $this->activeCampaigns[$campaignId] ?? null;
    }

    public function getId(): ?string
    {
        // Return first active campaign ID if any
        $keys = array_keys($this->activeCampaigns);
        return $keys[0] ?? null;
    }

    public function getEligibleProducts(): array
    {
        $firstCampaign = $this->getFirstCampaign();
        return $firstCampaign['eligible_products'] ?? [];
    }

    public function getDiscountValue(): float
    {
        $firstCampaign = $this->getFirstCampaign();
        return $firstCampaign['discount'] ?? 0.0;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        $firstCampaign = $this->getFirstCampaign();
        if (isset($firstCampaign['end_date'])) {
            return new \DateTimeImmutable($firstCampaign['end_date']);
        }
        return null;
    }

    public function getWelcomeOffer(): ?array
    {
        $firstCampaign = $this->getFirstCampaign();
        return $firstCampaign['welcome_offer'] ?? null;
    }

    private function getFirstCampaign(): array
    {
        return !empty($this->activeCampaigns) 
            ? reset($this->activeCampaigns) 
            : [];
    }
}
