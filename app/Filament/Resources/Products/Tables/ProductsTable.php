<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                textColumn::make('title')
                    ->searchable(),
                textColumn::make('description')
                    ->searchable()
                    ->limit(50),
                textColumn::make('price')
                    ->money('usd')
                    ->sortable(),
                TextColumn::make('stock')
                    ->sortable(),
                textColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                ImageColumn::make('image_url')
                    ->label('Imagen')
                    ->square(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
