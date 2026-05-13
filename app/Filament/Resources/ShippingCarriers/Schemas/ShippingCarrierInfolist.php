<?php

namespace App\Filament\Resources\ShippingCarriers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ShippingCarrierInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nombre'),

                TextEntry::make('code')
                    ->label('Código')
                    ->badge()
                    ->color('gray'),

                TextEntry::make('description')
                    ->label('Descripción')
                    ->placeholder('—')
                    ->columnSpanFull(),

                ImageEntry::make('logo_url')
                    ->label('Logo')
                    ->placeholder('—')
                    ->columnSpanFull(),

                TextEntry::make('sort_order')
                    ->label('Orden')
                    ->numeric(),

                IconEntry::make('is_active')
                    ->label('Activa')
                    ->boolean(),

                TextEntry::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i'),

                TextEntry::make('updated_at')
                    ->label('Actualizada')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}
