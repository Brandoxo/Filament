<?php

namespace App\Filament\Resources\Drops;

use App\Filament\Resources\Drops\Pages\CreateDrop;
use App\Filament\Resources\Drops\Pages\EditDrop;
use App\Filament\Resources\Drops\Pages\ListDrops;
use App\Filament\Resources\Drops\Pages\ViewDrop;
use App\Filament\Resources\Drops\Schemas\DropForm;
use App\Filament\Resources\Drops\Schemas\DropInfolist;
use App\Filament\Resources\Drops\Tables\DropsTable;
use App\Models\Drop;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DropResource extends Resource
{
    protected static ?string $model = Drop::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Drop';

    public static function form(Schema $schema): Schema
    {
        return DropForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DropInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DropsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDrops::route('/'),
            'create' => CreateDrop::route('/create'),
            'view' => ViewDrop::route('/{record}'),
            'edit' => EditDrop::route('/{record}/edit'),
        ];
    }
}
