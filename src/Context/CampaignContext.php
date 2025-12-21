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
}
