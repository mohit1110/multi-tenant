<?php

namespace App\Filament\Resources\Tenants\Schemas;

use App\Models\Tenant;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Organization Information')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('name')->label('Organization Name')->weight('bold'),
                            TextEntry::make('slug')->label('Identifier')->badge()->color('gray'),

                        ]),

                        IconEntry::make('is_active')
                            ->label('Status')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                    ]),

                Section::make('Owner Information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('owner_name')->label('Owner Name'),
                            TextEntry::make('owner_email')->label('Owner Email')
                                ->icon('heroicon-m-envelope'),
                        ]),
                    ]),

                Section::make('Statistics')
                    ->schema([
                        Grid::make(3)->schema([
                         
                            TextEntry::make('created_at')
                                ->label('Created At')
                                ->dateTime(),

                            TextEntry::make('updated_at')
                                ->label('Last Updated')
                                ->dateTime(),
                        ]),
                    ]),

            ]);
    }
}
