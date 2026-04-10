<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('shop_id')
                    ->numeric(),
                TextEntry::make('title'),
                TextEntry::make('slug'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('price')
                    ->money(),
                TextEntry::make('original_price')
                    ->money(),
                TextEntry::make('stock')
                    ->numeric(),
                TextEntry::make('brand')
                    ->placeholder('-'),
                TextEntry::make('size')
                    ->placeholder('-'),
                TextEntry::make('category_id')
                    ->numeric()
                    ->placeholder('-'),
                ImageEntry::make('image_url')
                    ->disk('r2')
                    ->placeholder('-'),
                ImageEntry::make('image_url_2')
                    ->disk('r2')
                    ->placeholder('-'),
                ImageEntry::make('image_url_3')
                    ->disk('r2')
                    ->placeholder('-'),
                ImageEntry::make('image_url_4')
                    ->disk('r2')
                    ->placeholder('-'),
                IconEntry::make('is_new')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('currency'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
