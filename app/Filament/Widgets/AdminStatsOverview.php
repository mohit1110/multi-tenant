<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::active()->count();
        
        $totalUsers = new TenantService();
        $totalUsers = collect($totalUsers->allUsers())->count();
        $recentTenants = Tenant::where('created_at', '>=', now()->subDays(30))->count();

        return [
            Stat::make('Total Tenants', $totalTenants)
                ->description("{$activeTenants} active")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary')
                ->icon('heroicon-o-building-office'),

            Stat::make('Active Tenants', $activeTenants)
                ->description('Currently operational')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Total Users', $totalUsers)
                ->description('Across all tenants')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->icon('heroicon-o-users'),

            Stat::make('New This Month', $recentTenants)
                ->description('Tenants created in last 30 days')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning')
                ->icon('heroicon-o-calendar'),
        ];
    }
}
