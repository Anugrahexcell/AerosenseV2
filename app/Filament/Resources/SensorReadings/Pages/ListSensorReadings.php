<?php

namespace App\Filament\Resources\SensorReadings\Pages;

use App\Filament\Resources\SensorReadings\SensorReadingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSensorReadings extends ListRecords
{
    protected static string $resource = SensorReadingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
