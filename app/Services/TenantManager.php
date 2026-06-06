<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantManager
{
    protected ?Tenant $currentTenant = null;

    /**
     * Set the current tenant.
     */
    public function set(Tenant $tenant): void
    {
        $this->currentTenant = $tenant;
    }

    /**
     * Get the current tenant.
     */
    public function get(): ?Tenant
    {
        return $this->currentTenant;
    }

    /**
     * Check if a tenant is currently set.
     */
    public function hasTenant(): bool
    {
        return ! is_null($this->currentTenant);
    }

    /**
     * Clear the current tenant.
     */
    public function clear(): void
    {
        $this->currentTenant = null;
    }

    /**
     * Get tenant by slug.
     */
    public function findBySlug(string $slug): ?Tenant
    {
        return Tenant::where('slug', $slug)->first();
    }

   
}
