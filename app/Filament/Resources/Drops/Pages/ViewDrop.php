<?php

namespace App\Filament\Resources\Drops\Pages;

use App\Filament\Resources\Drops\DropResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDrop extends ViewRecord
{
    protected static string $resource = DropResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
