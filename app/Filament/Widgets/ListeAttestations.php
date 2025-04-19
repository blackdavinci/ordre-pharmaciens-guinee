<?php

namespace App\Filament\Widgets;

use App\Models\Inscription;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use \Hugomyb\FilamentMediaAction\Tables\Actions\MediaAction;


class ListeAttestations extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Mes attestations')
            ->recordUrl(null) // Désactive la navigation en cliquant sur la ligne
            ->query(function () {
                // On récupère l'inscription de l'utilisateur connecté
                $inscription = Inscription::where('user_id', auth()->id())->first();

                if (!$inscription) {
                    // Si pas d'inscription, retourne une requête vide
                    return Media::where('id', 0);
                }

                // On récupère les médias de la collection 'attestations'
                $attestationIds = $inscription->getMedia('attestations')->pluck('id')->toArray();

                // On retourne une requête qui sera utilisée comme base pour la table
                return Media::query()->whereIn('id', $attestationIds);
            })
            ->columns([
                TextColumn::make('custom_properties.readable_name')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Date de création')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('year')
                    ->label('Année')
                    ->state(function ($record) {
                        // On essaie d'abord de récupérer l'année depuis les propriétés personnalisées
                        if (isset($record->custom_properties['year'])) {
                            return $record->custom_properties['year'];
                        }

                        // Sinon, on utilise l'année de création
                        return Carbon::parse($record->created_at)->year;
                    })
                    ->sortable(),
                TextColumn::make('size')
                    ->label('Taille')
                    ->formatStateUsing(fn ($record) => round($record->size / 1024, 2) . ' KB')
                    ->sortable(),
                // Utilisation du package Filament Media Action pour le téléchargement
                ViewColumn::make('actions')
                    ->label('Actions')
                    ->view('filament.tables.columns.attestation-download')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('Année')
                    ->options(function () {
                        // Récupérer l'inscription
                        $inscription = Inscription::where('user_id', auth()->id())->first();

                        if (!$inscription) {
                            return [];
                        }

                        // Récupérer les attestations
                        $attestations = $inscription->getMedia('attestations');

                        // Extraire les années (de custom_properties ou de created_at)
                        $years = $attestations->map(function ($item) {
                            return isset($item->custom_properties['year'])
                                ? $item->custom_properties['year']
                                : Carbon::parse($item->created_at)->year;
                        })->unique()->sort()->values();

                        // Créer un tableau associatif où la clé = valeur
                        return $years->mapWithKeys(function ($year) {
                            return [$year => $year];
                        })->toArray();
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'],
                                function (Builder $query, $year): Builder {
                                    return $query->where(function ($query) use ($year) {
                                        $query->whereJsonContains('custom_properties->year', $year)
                                            ->orWhereRaw("YEAR(created_at) = ?", [$year]);
                                    });
                                }
                            );
                    })
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Aucune attestation disponible')
            ->emptyStateDescription('Les attestations générées apparaîtront ici.');
    }
}
