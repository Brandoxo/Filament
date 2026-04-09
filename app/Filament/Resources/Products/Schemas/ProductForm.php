<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use App\Models\Shop;
use App\Models\Category;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('shop_id')
                    ->label('Tienda')
                    ->options(Shop::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
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
                RichEditor::make('description')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('original_price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('brand'),
                TextInput::make('size'),
                Select::make('category_id')
                    ->label('Categoría')
                    ->options(Category::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
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
                    )
                    ->image(),
                    TextInput::make('currency')
                    ->required()
                    ->default('MXN'),
                    TextInput::make('variant_options'),
                    Toggle::make('is_new')
                    ->default(true),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
            ]);
    }
}
