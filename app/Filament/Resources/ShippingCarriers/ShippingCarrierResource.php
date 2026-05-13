<?php

namespace App\Filament\Resources\ShippingCarriers;

use App\Filament\Resources\ShippingCarriers\Pages\CreateShippingCarrier;
use App\Filament\Resources\ShippingCarriers\Pages\EditShippingCarrier;
use App\Filament\Resources\ShippingCarriers\Pages\ListShippingCarriers;
use App\Filament\Resources\ShippingCarriers\Pages\ViewShippingCarrier;
use App\Filament\Resources\ShippingCarriers\RelationManagers\ShippingRatesRelationManager;
use App\Filament\Resources\ShippingCarriers\Schemas\ShippingCarrierForm;
use App\Filament\Resources\ShippingCarriers\Schemas\ShippingCarrierInfolist;
use App\Filament\Resources\ShippingCarriers\Tables\ShippingCarriersTable;
use UnitEnum;
use App\Models\ShippingCarrier;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ShippingCarrierResource extends Resource
{
    protected static ?string $model = ShippingCarrier::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $navigationLabel = 'Paqueterías';

    protected static ?string $pluralModelLabel = 'Paqueterías';

    protected static ?string $modelLabel = 'Paquetería';

    protected static string|UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return ShippingCarrierForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ShippingCarrierInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShippingCarriersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ShippingRatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListShippingCarriers::route('/'),
            'create' => CreateShippingCarrier::route('/create'),
            'view'   => ViewShippingCarrier::route('/{record}'),
            'edit'   => EditShippingCarrier::route('/{record}/edit'),
        ];
    }
}
