<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Models\Coupon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100)
                    ->suffixAction(
                        \Filament\Forms\Components\Actions\Action::make('generate')
                            ->label('Generar')
                            ->icon('heroicon-o-arrow-path')
                            ->action(fn (Set $set) => $set('code', strtoupper(Str::random(8))))
                    ),

                Select::make('type')
                    ->label('Tipo de descuento')
                    ->options(Coupon::TYPES)
                    ->required()
                    ->native(false),

                TextInput::make('value')
                    ->label('Valor')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->helperText('Porcentaje (0–100) o monto fijo en MXN'),

                Textarea::make('description')
                    ->label('Descripción')
                    ->nullable()
                    ->rows(2)
                    ->columnSpanFull(),

                TextInput::make('min_order_amount')
                    ->label('Monto mínimo de orden')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->nullable(),

                TextInput::make('max_uses')
                    ->label('Usos máximos totales')
                    ->numeric()
                    ->minValue(1)
                    ->nullable()
                    ->helperText('Dejar vacío para usos ilimitados'),

                TextInput::make('max_uses_per_user')
                    ->label('Usos máximos por usuario')
                    ->numeric()
                    ->minValue(1)
                    ->nullable()
                    ->helperText('Dejar vacío para sin límite por usuario'),

                DateTimePicker::make('starts_at')
                    ->label('Válido desde')
                    ->nullable()
                    ->native(false),

                DateTimePicker::make('expires_at')
                    ->label('Expira el')
                    ->nullable()
                    ->native(false),

                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }
}
