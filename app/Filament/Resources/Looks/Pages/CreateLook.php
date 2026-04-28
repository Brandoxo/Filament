<?php

namespace App\Filament\Resources\Looks\Pages;

use App\Filament\Resources\Looks\LookResource;
use App\Jobs\GenerateLookImageJob;
use Filament\Resources\Pages\CreateRecord;

class CreateLook extends CreateRecord
{
    protected static string $resource = LookResource::class;

    protected function afterCreate(): void
    {
        GenerateLookImageJob::dispatch($this->record);
    }
}
