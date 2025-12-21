<?php

declare(strict_types=1);

namespace App\Tenant;

use Symfony\Component\HttpFoundation\Request;

class TenantResolver
{
    public function resolve(Request $request): ?string
    {
        // Try to get tenant from header first
        $tenantId = $request->headers->get('X-Tenant-Id');
        
        if ($tenantId) {
            return $tenantId;
        }
        
        // Fallback to query parameter
        return $request->query->get('tenant_id');
    }
}
