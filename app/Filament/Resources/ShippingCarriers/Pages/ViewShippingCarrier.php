<?php

namespace App\Filament\Resources\ShippingCarriers\Pages;

use App\Filament\Resources\ShippingCarriers\ShippingCarrierResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewShippingCarrier extends ViewRecord
{
    protected static string $resource = ShippingCarrierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
