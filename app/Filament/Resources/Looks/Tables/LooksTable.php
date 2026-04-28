<?php

namespace App\Filament\Resources\Looks\Tables;

use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class LooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description') // Corregido a minúscula para coincidir con BD
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('products.title') // Relación directa más eficiente
                    ->label('Productos')
                    ->badge() // Se ve más limpio como etiquetas
                    ->separator(',')
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Precio')
                    ->money('MXN') // Puedes cambiar a 'MXN', 'EUR', etc.
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
            ]);
    }
}
            