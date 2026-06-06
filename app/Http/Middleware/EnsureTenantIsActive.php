<?php

namespace App\Http\Middleware;

use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsActive
{
    public function __construct(protected TenantManager $tenantManager)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
       
        $tenant = $this->tenantManager->get();

        if (! $tenant) {
            abort(404, 'Tenant not found.');
        }

        if (! $tenant->isActive()) {
            return redirect()->route('tenant.suspended');
        }

        return $next($request);
    }
}
