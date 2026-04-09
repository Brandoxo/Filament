<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                ->required()
                ->live(onBlur: true) // 1. Hace el campo reactivo
                ->afterStateUpdated(function (Set $set, ?string $state, string $operation) {
                if ($operation === 'create') {
                    $set('slug', Str::slug($state));
                }
                }), // 2. Genera el slug automáticamente
                TextInput::make('slug')
                ->required()
                ->readOnly() // 3. Bloquea la edición manual
                ->unique(ignoreRecord: true), // 4. Previene errores de base de datos
                RichEditor::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image_url')
                    ->image(),
                TextInput::make('col_span')
                    ->required()
                    ->default('col-span-1'),
            ]);
    }
}
