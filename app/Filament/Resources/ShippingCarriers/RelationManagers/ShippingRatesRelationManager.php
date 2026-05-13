<?php

namespace App\Filament\Resources\ShippingCarriers\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShippingRatesRelationManager extends RelationManager
{
    protected static string $relationship = 'shippingRates';

    protected static ?string $title = 'Tarifas de Envío';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('price')
                    ->label('Precio')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0),

                TextInput::make('free_from')
                    ->label('Gratis desde')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->nullable()
                    ->helperText('Dejar vacío para no ofrecer envío gratis'),

                TextInput::make('estimated_days_min')
                    ->label('Días mín.')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(365),

                TextInput::make('estimated_days_max')
                    ->label('Días máx.')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(365),

                TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->label('Activa')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('price')
                    ->label('Precio')
                    ->money('MXN')
                    ->sortable(),

                TextColumn::make('free_from')
                    ->label('Gratis desde')
                    ->money('MXN')
                    ->placeholder('—'),

                TextColumn::make('estimated_days_min')
                    ->label('Días estimados')
                    ->formatStateUsing(fn ($record) => "{$record->estimated_days_min}–{$record->estimated_days_max} días"),

                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()->label('Nueva tarifa'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }
}
