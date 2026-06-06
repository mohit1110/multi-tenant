<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function __construct(protected TenantManager $tenantManager) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($tenantId = session('tenant_id')) {
            $tenant = Tenant::find($tenantId);

            if ($tenant) {
                $this->tenantManager->set($tenant);
                $this->switchToTenantSchema($tenant);
            }
        } elseif (auth('tenant')->check()) {
            $tenant = auth('tenant')->user()->tenant;
            $tenant = Tenant::where('slug', $tenant)->first();

            if ($tenant) {
                $this->tenantManager->set($tenant);
                $this->switchToTenantSchema($tenant);
            }
        }

        return $next($request);
    }

    /**
     * Switch database schema to tenant's schema.
     */
    protected function switchToTenantSchema(Tenant $tenant): void
    {

        config([
            'database.connections.pgsql.search_path' =>
            $tenant->database_schema . ',public',
        ]);

        DB::purge('pgsql');
        DB::reconnect('pgsql');
    }
}
