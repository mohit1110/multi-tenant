<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Models\Tenant;
use App\Services\TenantService;
use Exception;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function handleRecordCreation(array $data): Tenant
    {
        $service = app(TenantService::class);

        try {
            $tenant = $service->createTenant($data);

            Notification::make()
                ->title('Tenant Created')
                ->body("Organization '{$tenant->name}' has been created with its database schema and admin user.")
                ->success()
                ->send();

            return $tenant;
        } catch (Exception $e) {
            Notification::make()
                ->title('Creation Failed')
                ->body('Failed to create tenant:')
                ->danger()
                ->send();

            throw $e;
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
