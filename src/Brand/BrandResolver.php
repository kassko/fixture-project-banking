<?php

declare(strict_types=1);

namespace App\Brand;

use Symfony\Component\HttpFoundation\Request;

class BrandResolver
{
    public function resolve(Request $request): ?string
    {
        // Try to get brand from header first
        $brandId = $request->headers->get('X-Brand-Id');
        
        if ($brandId) {
            return $brandId;
        }
        
        // Fallback to query parameter
        return $request->query->get('brand_id');
    }
}
