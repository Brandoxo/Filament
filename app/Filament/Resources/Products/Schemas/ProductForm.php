<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;


class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
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
        
                TextInput::make('description')
                    ->label('Descripción')
                    ->maxLength(65535),
                TextInput::make('price')
                    ->label('Precio')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                TextInput::make('original_price')
                    ->label('Precio Original')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('stock')
                    ->label('Stock')
                    ->required()
                    ->integer()
                    ->minValue(0),
                TextInput::make('brand')
                    ->label('Marca')
                    ->maxLength(255),
                TextInput::make('size')
                    ->label('Talla')
                    ->maxLength(255),
                FileUpload::make('image_url')
                    ->label('URL de la Imagen')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->getUploadedFileNameForStorageUsing(fn (Get $get, TemporaryUploadedFile $file): string =>
                        Str::slug($get('slug') ?: $get('title') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                        . '-' . Str::random(6)
                        . '.' . $file->getClientOriginalExtension()
                    ),
                FileUpload::make('image_url_2')
                    ->label('URL de la Imagen 2')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->getUploadedFileNameForStorageUsing(fn (Get $get, TemporaryUploadedFile $file): string =>
                        Str::slug($get('slug') ?: $get('title') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                        . '-2-' . Str::random(6)
                        . '.' . $file->getClientOriginalExtension()
                    ),
                FileUpload::make('image_url_3')
                    ->label('URL de la Imagen 3')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->getUploadedFileNameForStorageUsing(fn (Get $get, TemporaryUploadedFile $file): string =>
                        Str::slug($get('slug') ?: $get('title') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                        . '-3-' . Str::random(6)
                        . '.' . $file->getClientOriginalExtension()
                    ),
                FileUpload::make('image_url_4')
                    ->label('URL de la Imagen 4')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->getUploadedFileNameForStorageUsing(fn (Get $get, TemporaryUploadedFile $file): string =>
                        Str::slug($get('slug') ?: $get('title') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                        . '-4-' . Str::random(6)
                        . '.' . $file->getClientOriginalExtension()
                    ),
                TextInput::make('currency')
                    ->label('Moneda')
                    ->required()
                    ->maxLength(3),
                    
            ]);
    }
}
