<?php

namespace App\Filament\Resources\InscriptionResource\Pages;

use App\Filament\Resources\InscriptionResource;
use App\Models\Inscription;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListInscriptions extends ListRecords
{
    protected static string $resource = InscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    public function getTabs(): array
    {

        return [
            'pending' => Tab::make('En attente de validation')
                ->icon('heroicon-o-clock')
                ->iconPosition('before')
                ->badge(Inscription::query()->where('statut', 'pending')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('statut', 'pending')),
            'approved' => Tab::make('Validées')
                ->icon('heroicon-o-check-circle')
                ->iconPosition('before')
                ->badge(Inscription::query()->where('statut', 'approved')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('statut', 'approved')),
            'rejected' => Tab::make('Rejetées')
                ->icon('heroicon-o-x-circle')
                ->iconPosition('before')
                ->badge(Inscription::query()->where('statut', 'rejected')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('statut', 'rejected')),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'pending';
    }


}
