<?php

namespace App\Filament\Resources\Drops\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DropInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('launch_date')
                    ->dateTime(),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->badge(),
            ]);
    }
}
