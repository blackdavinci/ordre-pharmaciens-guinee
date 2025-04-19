<?php

namespace App\Filament\Resources\ReinscriptionResource\Pages;

use App\Filament\Resources\ReinscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReinscription extends EditRecord
{
    protected static string $resource = ReinscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
