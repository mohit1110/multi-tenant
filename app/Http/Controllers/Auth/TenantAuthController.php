<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TenantLoginRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TenantAuthController extends Controller
{
    /**
     * Show the tenant login form.
     */
    public function showLoginForm(Request $request): View
    {
        $tenantSlug = $request->query('tenant');
        $tenant = null;

        if ($tenantSlug) {
            $tenant = Tenant::where('slug', $tenantSlug)->active()->first();
        }


        return view('auth.tenant-login', compact('tenant'));
    }

    /**
     * Handle tenant login.
     */
    public function login(TenantLoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();
        $tenantSlug = $request->input('tenant_slug');

   
        $tenant = Tenant::where('slug', $tenantSlug)->first();

        if (! $tenant) {
            return back()->withErrors([
                'tenant_slug' => 'Organization not found.',
            ])->withInput();
        }

        if (! $tenant->isActive()) {
            return back()->withErrors([
                'tenant_slug' => 'This organization account has been suspended.',
            ])->withInput();
        }

        config([
            'database.connections.pgsql.search_path' =>
            $tenant->database_schema . ',public',
        ]);

        DB::purge('pgsql');
        DB::reconnect('pgsql');

       
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ])->withInput();
        }

        if (! $user->is_active) {
            return back()->withErrors([
                'email' => 'Your account has been deactivated.',
            ])->withInput();
        }



        Auth::guard('tenant')->login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        session([
            'tenant_id' => $tenant->id,
            'tenant_schema' => $tenant->database_schema,
            'tenant_name' => $tenant->name,
        ]);

        return redirect()->intended(route('tenant.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('tenant')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('tenant.login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show tenant registration form.
     */
    public function showRegisterForm(Request $request): View
    {
        $tenantSlug = $request->query('tenant');
        $tenant = null;

        if ($tenantSlug) {
            $tenant = Tenant::where('slug', $tenantSlug)->active()->first();
        }

        return view('auth.tenant-register', compact('tenant'));
    }

    /**
     * Handle tenant user registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
            'tenant_slug' => ['required', 'string', 'exists:tenants,slug'],
        ]);

        $tenant = Tenant::where('slug', $request->tenant_slug)->active()->firstOrFail();

        if (! $tenant) {
            return back()->withErrors([
                'tenant_slug' => 'Organization not found.',
            ])->withInput();
        }

        if (! $tenant->isActive()) {
            return back()->withErrors([
                'tenant_slug' => 'This organization account has been suspended.',
            ])->withInput();
        }

        config([
            'database.connections.pgsql.search_path' =>
            $tenant->database_schema . ',public',
        ]);

        DB::purge('pgsql');
        DB::reconnect('pgsql');

        // Check if email already exists in this tenant
        $existingUser = User::where('email', $request->email)
            // ->where('tenant_id', $tenant->id)
            ->first();

        if ($existingUser) {
            return back()->withErrors([
                'email' => 'This email is already registered for this organization.',
            ])->withInput();
        }

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'tenant' => $tenant->slug,
            'role'      => 'member',
            'is_active' => true,
        ]);

        Auth::guard('tenant')->login($user);

        session(['tenant_id' => $tenant->id]);

        return redirect()->route('tenant.dashboard')
            ->with('success', 'Account created successfully. Welcome!');
    }
}
