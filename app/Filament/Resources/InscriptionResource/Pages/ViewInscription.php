<?php

namespace App\Filament\Resources\InscriptionResource\Pages;

use App\Filament\Resources\InscriptionResource;
use App\Models\Inscription;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\View;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewInscription extends ViewRecord
{
    protected static string $resource = InscriptionResource::class;

    public function getTitle(): string | Htmlable
    {
        $name = '<span style="font-size:0.8em">'.$this->record->prenom.' '.$this->record->nom.'</span>';

        return new HtmlString($name);

    }

    protected function getHeaderActions(): array
    {
        return [
            // Inscription Status
            Actions\Action::make('approved_inscription')
                ->label('INSCRIPTION VALIDÉE')
                ->color('success')
                ->disabled()
                ->icon('heroicon-o-check-circle')
                ->visible(fn ($record) => $record->statut == 'approved'),
            Actions\Action::make('rejected_inscription')
                ->label('INSCRIPTION REJETÉE')
                ->color('warning')
                ->disabled()
                ->icon('heroicon-o-x-circle')
                ->visible(fn ($record) => $record->statut == 'rejected'),

            Actions\Action::make('updateOrdreNumero')
                ->label("Mise à jour numéro de l'ordre")
                ->icon('heroicon-o-arrow-path')
                ->form([
                    TextInput::make('numero_ordre')
                        ->label("Numéro d'ordre")
                        ->required(),
                ])
                ->action(function (array $data, Inscription $record): void {
                    $record->update($data);
                    $record->save();
                })
                ->visible(fn ($record) => $record->statut == 'approved'),

            // Inscription Actions
            Actions\Action::make('approved')
                ->label('Valider')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading("Validation Inscription")
                ->modalDescription('Voulez-vous vraiment valider cette inscprition?')
                ->modalIcon('heroicon-o-check-circle')
                ->modalIconColor('success')
                ->action(function (Inscription $record) {
                    // Appel à la méthode approveInscription depuis une instance de la ressource
                    (new InscriptionResource)->approveInscription($record);
                })
                ->visible(fn ($record) => !in_array($record->statut, ['approved', 'rejected'])),
            Actions\Action::make('rejected')
                ->label('Rejeter')
                ->color('warning')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading('Rejet Inscription')
                ->modalDescription('Voulez-vous vraiment rejeter cette inscription?')
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
                        ->title('Inscription Rejetée')
                        ->body("L'inscription a été rejeté pour les raisons : {$data['reason']}")
                        ->danger()
                        ->send();
                })
                ->visible(fn ($record) => !in_array($record->statut, ['approved', 'rejected'])),
            Actions\Action::make('delete')
                ->label('Supprimer')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Suppression Inscription')
                ->modalDescription('Voulez-vous vraiment supprimer cette inscription? Cette action est irréveresible.')
                ->modalIcon('heroicon-o-trash')
                ->modalIconColor('danger'),
        ];
    }

}
