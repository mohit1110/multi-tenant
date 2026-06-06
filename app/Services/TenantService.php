<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TenantService
{
    /**
     * Create a new tenant with its database schema.
     */
    public function createTenant(array $data): Tenant
    {
        $slug = $data['slug'] ?? Str::slug($data['name']);

        $tenant = Tenant::create([
            'name'            => $data['name'],
            'slug'            => $slug,          
            'database_schema' => 'tenant_' . str_replace('-', '_', $slug),
            'is_active'       => $data['is_active'] ?? true,
            'owner_name'      => $data['owner_name'],
            'owner_email'     => $data['owner_email'],
            'settings'        => $data['settings'] ?? [],
        ]);

        $this->createTenantSchema($tenant);
        $this->runTenantMigrations($tenant);
        $this->createTenantAdminUser($tenant, $data);

        return $tenant;
    }

    /**
     * Create the PostgreSQL schema for the tenant.
     */
    public function createTenantSchema(Tenant $tenant): void
    {
        $schemaName = $tenant->getSchemaName();

        DB::statement("CREATE SCHEMA IF NOT EXISTS \"{$schemaName}\"");

        Log::info("Schema created for tenant: {$tenant->name}", ['schema' => $schemaName]);
    }

    /**
     * Drop the PostgreSQL schema for the tenant.
     */
    public function dropTenantSchema(Tenant $tenant): void
    {
        $schemaName = $tenant->getSchemaName();

        DB::statement("DROP SCHEMA IF EXISTS \"{$schemaName}\" CASCADE");

        Log::info("Schema dropped for tenant: {$tenant->name}", ['schema' => $schemaName]);
    }

    /**
     * Run migrations for a specific tenant schema.
     */
    public function runTenantMigrations(Tenant $tenant): void
    {
       
        config([
            'database.connections.pgsql.search_path' =>
            $tenant->database_schema . ',public',
        ]);

        DB::purge('pgsql');
        DB::reconnect('pgsql');

        try {
            Artisan::call('migrate', [
                '--path'     => 'database/migrations/tenant',
                '--force'    => true,
                // '--database' => 'pgsql',
            ]);

            Log::info("Migrations run for tenant: {$tenant->name}");
        } catch (Exception $e) {
            Log::error("Migration failed for tenant: {$tenant->name}", ['error' => $e->getMessage()]);
            throw $e;
        } finally {
            // Reset search path
            DB::statement("SET search_path TO public");
        }
    }

    /**
     * Create the admin user for a tenant.
     */
    public function createTenantAdminUser(Tenant $tenant, array $data): User
    {
 
        config([
            'database.connections.pgsql.search_path' =>
            $tenant->database_schema . ',public',
        ]);

        DB::purge('pgsql');
        DB::reconnect('pgsql');


        return User::create([
            'name'      => $data['owner_name'],
            'email'     => $data['owner_email'],
            'password'  => Hash::make($data['owner_password'] ?? 'password'),
            'role'      => 'tenant_admin',
            'tenant'     => $tenant->slug,
            'is_active' => true,
        ]);
    }

    /**
     * Switch database connection to a tenant's schema.
     */
    public function switchToTenant(Tenant $tenant): void
    {
        $schemaName = $tenant->getSchemaName();

        // Update the pgsql connection to use tenant schema
        config(['database.connections.pgsql.search_path' => $schemaName]);

        DB::purge('pgsql');
        DB::reconnect('pgsql');

        DB::statement("SET search_path TO \"{$schemaName}\"");
    }

    /**
     * Switch back to the public schema.
     */
    public function switchToPublic(): void
    {
        config(['database.connections.pgsql.search_path' => 'public']);

        DB::purge('pgsql');
        DB::reconnect('pgsql');

        DB::statement('SET search_path TO public');
    }

    /**
     * Activate a tenant.
     */
    public function activate(Tenant $tenant): void
    {
        $tenant->update(['is_active' => true]);

    }

    /**
     * Deactivate a tenant.
     */
    public function deactivate(Tenant $tenant): void
    {
        $tenant->update(['is_active' => false]);

    }

    /**
     * Get all schemas from PostgreSQL.
     */
    public function getAllSchemas(): array
    {
        $schemas = DB::select("
            SELECT schema_name 
            FROM information_schema.schemata 
            WHERE schema_name LIKE 'tenant_%'
        ");

        return array_column($schemas, 'schema_name');
    }


    public function allUsers(): array
    {
        $users = [];

        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {

            $tenantUsers = DB::select("
                SELECT
                    id,
                    name,
                    email,
                    '{$tenant->name}' as tenant_name,
                    '{$tenant->slug}' as tenant_slug
                FROM {$tenant->database_schema}.users
            ");

            $users = array_merge($users, $tenantUsers);
        }

    
        return $users;
    }
}
