<?php

namespace App\Filament\Resources\Drops\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DropForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                DateTimePicker::make('launch_date')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(['upcoming' => 'Upcoming', 'live' => 'Live', 'ended' => 'Ended'])
                    ->default('upcoming')
                    ->required(),
            ]);
    }
}
