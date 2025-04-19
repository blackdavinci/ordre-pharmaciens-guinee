<?php

namespace App\Filament\Resources\ReinscriptionResource\Pages;

use App\Filament\Resources\ReinscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReinscriptions extends ListRecords
{
    protected static string $resource = ReinscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
