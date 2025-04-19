<?php

namespace App\Filament\Resources\ReinscriptionResource\Pages;

use App\Filament\Resources\ReinscriptionResource;
use App\Models\Reinscription;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewReinscription extends ViewRecord
{
    protected static string $resource = ReinscriptionResource::class;

    public function getTitle(): string | Htmlable
    {
        $name = '<span style="font-size:0.8em">'.$this->record->prenom.' '.$this->record->nom.'</span>';

        return new HtmlString($name);

    }

    protected function getHeaderActions(): array
    {
        return [

            // Inscription Actions
            Actions\Action::make('approved')
                ->label('Valider')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading("Validation Réinscription")
                ->modalDescription('Voulez-vous vraiment valider cette réinscprition?')
                ->modalIcon('heroicon-o-check-circle')
                ->modalIconColor('success')
                ->action(function (Reinscription $record) {
                    // Appel à la méthode approveInscription depuis une instance de la ressource
                    (new ReinscriptionResource)->approveReinscription($record);
                })
                ->visible(fn ($record) => !in_array($record->statut, ['approved', 'rejected']) && auth()->user()->hasRole('president')),
            Actions\Action::make('rejected')
                ->label('Rejeter')
                ->color('warning')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading('Rejet Réinscription')
                ->modalDescription('Voulez-vous vraiment rejeter cette reinscription?')
                ->modalIcon('heroicon-o-x-circle')
                ->modalIconColor('danger')
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Motif du rejet')
                        ->required(),
                ])
                ->action(function ($record, array $data) {
                    $record->update([
                        'statut' => 'rejected',
                        'motif_rejet' => $data['reason']
                    ]);

                    Notification::make()
                        ->title('Réinscription Rejetée')
                        ->body("La réinscription a été rejeté pour les raisons : {$data['reason']}")
                        ->danger()
                        ->send();
                })
                ->visible(fn ($record) => !in_array($record->statut, ['approved', 'rejected']) && auth()->user()->hasRole('president') ),
            Actions\Action::make('delete')
                ->label('Supprimer')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Suppression Réinscription')
                ->modalDescription('Voulez-vous vraiment supprimer cette réinscription? Cette action est irréveresible.')
                ->modalIcon('heroicon-o-trash')
                ->modalIconColor('danger')
                ->visible(fn ($record) => auth()->user()->hasRole('president')),
        ];
    }
}
