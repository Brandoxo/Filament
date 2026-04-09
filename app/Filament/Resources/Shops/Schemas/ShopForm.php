<?php

namespace App\Filament\Resources\Shops\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ShopForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('domain')
                    ->required(),
                TextInput::make('theme_config'),
                TextInput::make('currency')
                    ->required()
                    ->default('MXN'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
            ]);
    }
}
