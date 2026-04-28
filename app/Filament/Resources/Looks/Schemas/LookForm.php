<?php

namespace App\Filament\Resources\Looks\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

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

                Placeholder::make('image_url')
                    ->label('AI Generated Image')
                    ->content(fn ($record) => $record?->image_url
                        ? new HtmlString('<img src="' . e($record->image_url) . '" alt="Look image" class="max-w-sm rounded-lg shadow-md" />')
                        : new HtmlString('<span class="text-sm text-gray-400 italic">La imagen se generará automáticamente en segundo plano.</span>')
                    )
                    ->columnSpanFull()
                    ->visibleOn('edit'),
            ]);
    }
}