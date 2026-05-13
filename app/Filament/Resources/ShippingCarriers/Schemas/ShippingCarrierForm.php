<?php

namespace App\Filament\Resources\ShippingCarriers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ShippingCarrierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText('Identificador único, sin espacios. Ej: fedex, dhl'),

                Textarea::make('description')
                    ->label('Descripción')
                    ->nullable()
                    ->rows(2)
                    ->columnSpanFull(),

                TextInput::make('logo_url')
                    ->label('URL del Logo')
                    ->nullable()
                    ->url()
                    ->maxLength(500)
                    ->columnSpanFull(),

                TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                Toggle::make('is_active')
                    ->label('Activa')
                    ->default(true),
            ]);
    }
}
