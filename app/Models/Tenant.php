<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'database_schema',
        'is_active',
        'plan',
        'settings',
        'owner_name',
        'owner_email',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

   

    /**
     * Get the schema name for this tenant.
     */
    public function getSchemaName(): string
    {
        return $this->database_schema ?? 'tenant_' . $this->slug;
    }

    /**
     * Check if tenant is active.
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Scope to only active tenants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (empty($tenant->database_schema)) {
                $tenant->database_schema = 'tenant_' . str_replace('-', '_', $tenant->slug);
            }
        });
    }
}
