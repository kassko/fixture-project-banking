<?php

declare(strict_types=1);

namespace App\Service\Simulation\PricingEngine;

class CampaignDiscountApplier
{
    public function applyDiscounts(
        float $basePremium,
        array $activeCampaigns,
        string $productType,
        string $period
    ): array {
        $finalPremium = $basePremium;
        $discounts = [];

        // Period-based discounts
        if ($period === 'end_of_year_promotion') {
            $discount = 0.15;
            $finalPremium *= (1 - $discount);
            $discounts[] = [
                'type' => 'seasonal',
                'description' => 'Promotion de fin d\'année',
                'discount_percentage' => $discount * 100,
                'amount_saved' => $basePremium * $discount,
            ];
        }

        // Campaign-based discounts
        foreach ($activeCampaigns as $campaignId => $campaign) {
            if ($this->campaignApplies($campaign, $productType)) {
                $discount = $campaign['discount'] ?? 0.1;
                $finalPremium *= (1 - $discount);
                $discounts[] = [
                    'type' => 'campaign',
                    'description' => $campaign['name'] ?? "Campagne $campaignId",
                    'discount_percentage' => $discount * 100,
                    'amount_saved' => $basePremium * $discount,
                ];
            }
        }

        return [
            'final_premium' => $finalPremium,
            'discounts' => $discounts,
        ];
    }

    private function campaignApplies(array $campaign, string $productType): bool
    {
        $applicableProducts = $campaign['applicable_products'] ?? [];
        
        if (empty($applicableProducts)) {
            return true;
        }
        
        return in_array($productType, $applicableProducts, true);
    }
}
