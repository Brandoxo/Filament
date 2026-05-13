<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Models\Coupon;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CouponInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code')
                    ->label('Código')
                    ->badge()
                    ->color('info')
                    ->copyable(),

                TextEntry::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => Coupon::TYPES[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => $state === 'percentage' ? 'success' : 'primary'),

                TextEntry::make('value')
                    ->label('Valor')
                    ->formatStateUsing(fn ($record): string => $record->type === 'percentage'
                        ? "{$record->value}%"
                        : '$' . number_format((float) $record->value, 2)),

                TextEntry::make('description')
                    ->label('Descripción')
                    ->placeholder('—')
                    ->columnSpanFull(),

                TextEntry::make('min_order_amount')
                    ->label('Monto mínimo')
                    ->money('MXN')
                    ->placeholder('Sin mínimo'),

                TextEntry::make('max_uses')
                    ->label('Usos máximos')
                    ->placeholder('Ilimitado'),

                TextEntry::make('uses_count')
                    ->label('Usos realizados')
                    ->numeric(),

                TextEntry::make('max_uses_per_user')
                    ->label('Máx. por usuario')
                    ->placeholder('Sin límite'),

                TextEntry::make('starts_at')
                    ->label('Válido desde')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—'),

                TextEntry::make('expires_at')
                    ->label('Expira el')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sin expiración'),

                IconEntry::make('is_active')
                    ->label('Activo')
                    ->boolean(),

                TextEntry::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}
