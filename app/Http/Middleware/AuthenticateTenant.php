<?php

namespace App\Http\Middleware;

use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateTenant
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

        if (! auth('tenant')->check()) {
            return redirect()->route('tenant.login')
                ->with('error', 'Please log in to access this area.');
        }

        $user = auth('tenant')->user();

        if (! $user->is_active) {
            // auth('tenant')->logout();

            return redirect()->route('tenant.login')
                ->with('error', 'Your account has been deactivated. Please contact your administrator.');
        }

        if ($tenant && ! $tenant->isActive()) {
            // auth('tenant')->logout();

            return redirect()->route('tenant.login')
                ->with('error', 'Your organization account is currently suspended.');
        }

        return $next($request);
    }
}
