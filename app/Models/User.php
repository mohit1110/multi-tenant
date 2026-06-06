<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * Filament: only super_admin can access the admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isSuperAdmin();
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'role',
        'tenant',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get tasks created by this user.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is a tenant admin.
     */
    public function isTenantAdmin(): bool
    {
        return $this->role === 'tenant_admin';
    }

    /**
     * Check if user belongs to a tenant.
     */
    public function hasTenant(): bool
    {
        return ! is_null($this->tenant);
    }
}
