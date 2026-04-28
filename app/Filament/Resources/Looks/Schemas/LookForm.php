<?php

namespace App\Filament\Resources\Looks\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;

class LookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Nombre del Look')
                    ->required(),

                RichEditor::make('description')
                    ->label('Description')
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->label('Precio')
                    ->prefix('MXN $')
                    ->numeric()
                    ->required(),

                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),
                    
                Select::make('products')
                    ->label('Products')
                    ->multiple()
                    ->relationship('products', 'title')
                    ->preload()
                    ->required()
                    ->hint('Selecciona los productos que forman parte de este look.'),
            ]);
    }
}