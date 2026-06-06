<?php

namespace App\Filament\Resources\Tenants\Schemas;

use App\Models\Tenant;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Organization Details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Organization Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->label('Slug / Identifier')
                            ->required()
                            ->maxLength(100)
                            ->unique(Tenant::class, 'slug', ignoreRecord: true)
                            ->helperText('Used in URLs and as the organization identifier. Lowercase letters, numbers and hyphens only.')
                            ->rules(['regex:/^[a-z0-9\-]+$/'])
                            ->dehydrateStateUsing(fn($state) => Str::slug($state)),


                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Deactivating will prevent all tenant users from logging in.'),
                    ])->columns(2),

                Section::make('Owner Details')
                    ->description('The primary admin user for this tenant')
                    ->schema([
                        TextInput::make('owner_name')
                            ->label('Owner Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('owner_email')
                            ->label('Owner Email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('owner_password')
                            ->label('Owner Password')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->helperText('Required when creating a new tenant.')
                            ->visibleOn('create'),
                    ])->columns(2),
            ]);
    }
}
