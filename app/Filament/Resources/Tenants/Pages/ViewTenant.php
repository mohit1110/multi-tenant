<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Services\TenantService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTenant extends ViewRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),


            Action::make('activate')
                ->label('Activate')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => ! $this->record->is_active)
                ->requiresConfirmation()
                ->action(function (TenantService $service) {
                    $service->activate($this->record);
                    Notification::make()->title('Tenant Activated')->success()->send();
                    $this->refreshFormData(['is_active']);
                }),

            Action::make('deactivate')
                ->label('Deactivate')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn() => $this->record->is_active)
                ->requiresConfirmation()
                ->action(function (TenantService $service) {
                    $service->deactivate($this->record);
                    Notification::make()->title('Tenant Deactivated')->warning()->send();
                    $this->refreshFormData(['is_active']);
                }),
            Action::make('login')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->color('success')
                ->visible(fn() => $this->record->is_active)
                ->url(fn($record) => url('/login?tenant=' . $record->slug))
                ->openUrlInNewTab(),
        ];
    }
}
