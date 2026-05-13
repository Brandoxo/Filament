<?php

namespace App\Filament\Resources\Coupons\Tables;

use App\Models\Coupon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => Coupon::TYPES[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => $state === 'percentage' ? 'success' : 'primary'),

                TextColumn::make('value')
                    ->label('Descuento')
                    ->formatStateUsing(fn ($record): string => $record->type === 'percentage'
                        ? "{$record->value}%"
                        : '$' . number_format((float) $record->value, 2)),

                TextColumn::make('uses_count')
                    ->label('Usos')
                    ->formatStateUsing(fn ($record): string => $record->max_uses
                        ? "{$record->uses_count}/{$record->max_uses}"
                        : (string) $record->uses_count)
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Expira')
                    ->dateTime('d/m/Y')
                    ->placeholder('Sin expiración')
                    ->sortable()
                    ->color(fn ($record): string => $record->expires_at && $record->expires_at->isPast() ? 'danger' : 'gray'),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(Coupon::TYPES),

                TernaryFilter::make('is_active')
                    ->label('Activo'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
