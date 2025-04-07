<?php

namespace App\Filament\Resources\InscriptionResource\Pages;

use App\Filament\Resources\InscriptionResource;
use App\Models\Inscription;
use Carbon\Carbon;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Parfaitementweb\FilamentCountryField\Infolists\Components\CountryEntry;

class MembreInscription extends Page
{
    protected static string $resource = InscriptionResource::class;

    protected static string $view = 'filament.resources.inscription-resource.pages.membre-inscription';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Actions::make([
                    // Approve Action
                    Infolists\Components\Actions\Action::make('approved')
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
                        ->visible(fn ($record) => $record->status !== 'approved'),
                    // Reject Action
                    Infolists\Components\Actions\Action::make('rejected')
                        ->label('Rejeter')
                        ->color('danger')
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
                        ->visible(fn ($record) => $record->status !== 'rejected'),
                    // Delete Action
                    Infolists\Components\Actions\Action::make('Supprimer')
                        ->label('Delete')
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Suppression Inscription')
                        ->modalDescription('Voulez-vous vraiment supprimer cette inscription? Cette action est irréveresible.')
                        ->modalIcon('heroicon-o-trash')
                        ->modalIconColor('danger')
                        ->action(function ($record) {
                            $record->delete();

                            Notification::make()
                                ->title('Inscription Supprimée')
                                ->body("Cette inscription a été définitivement supprimée.")
                                ->danger()
                                ->send();

                            return redirect(static::getUrl('index'));
                        })
                        ->visible(fn (): bool => auth()->user()->can('delete', Inscription::class)),
                ])
                    ->columnSpanFull()
                    ->alignEnd(),
                Section::make('Identité personnelles & Coordonnées')
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                Grid::make()
                                    ->columnSpan(2)
                                    ->schema([
                                        Infolists\Components\SpatieMediaLibraryImageEntry::make('photo_identite')
                                            ->collection('photo_identite')
                                            ->label('Photo ID')
                                            ->square()
                                            ->extraImgAttributes([
                                                'style' => 'object-fit: contain; width: 100%; height: 100%;'
                                            ])
                                            ->columnSpan(2),

                                    ]),
                                Grid::make()
                                    ->columnSpan(10)
                                    ->schema([
                                        TextEntry::make('prenom')
                                            ->label('Prénom')
                                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                                        TextEntry::make('nom')
                                            ->label('Nom')
                                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                                        TextEntry::make('genre')
                                            ->label('Gentilité')
                                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                                        TextEntry::make('date_naissance')
                                            ->label('Date de naissance')
                                            ->formatStateUsing(function ($state) {
                                                // Format the date in French format
                                                $date = Carbon::parse($state);
                                                return $date->isoFormat('D MMMM YYYY');  // Example: 25 janvier 2023
                                            }),
                                        CountryEntry::make('pays_naissance')
                                            ->label('Pays de naissance'),
                                        CountryEntry::make('lieu_naissance')
                                            ->label('Lieu de naissance'),
                                        CountryEntry::make('nationalite')
                                            ->label('Nationalité'),
                                        TextEntry::make('type_piece_identite')
                                            ->label("Pièce d'identité")
                                            ->formatStateUsing(function ($state) {
                                                // Vérifier la valeur et afficher le texte correspondant
                                                return $state === 'cin' ? 'Carte d\'identité' : ($state === 'passeport' ? 'Passeport' : '');  // Valeur par défaut si non trouvé
                                            }),
                                        // Champs conditionnels
                                        IconEntry::make('cin_recto')
                                            ->label('CIN Recto')
                                            ->visible(function ($record) {
                                                return $record->type_piece_identite === 'cin';
                                            })
                                            ->hintAction(
                                                \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                ->label('Voir le fichier')
                                                    ->icon('heroicon-o-photo') // Use a relevant icon
                                                    ->media(fn($record) => $record->getFirstMediaUrl('cin_recto')) // Media URL for the CIN Recto image
                                                    ->modalHeading('Aperçu du fichier') // Modal heading
                                            ),
                                        IconEntry::make('cin_verso')
                                            ->label('CIN Verso')
                                            ->visible(function ($record) {
                                                return $record->type_piece_identite === 'cin';
                                            })
                                            ->hintAction(
                                                \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                ->label('Voir le fichier')
                                                    ->icon('heroicon-o-photo') // Use a relevant icon
                                                    ->media(fn($record) => $record->getFirstMediaUrl('cin_verso')) // Media URL for the CIN Recto image
                                                    ->modalHeading('Aperçu du fichier') // Modal heading
                                            ),
                                        IconEntry::make('passeport_premiere_page')
                                            ->label('Passeport première page')
                                            ->visible(function ($record) {
                                                return $record->type_piece_identite === 'passeport';
                                            })
                                            ->hintAction(
                                                \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                ->label('Voir le fichier')
                                                    ->icon('heroicon-o-photo') // Use a relevant icon
                                                    ->media(fn($record) => $record->getFirstMediaUrl('passeport_premiere_page')) // Media URL for the CIN Recto image
                                                    ->modalHeading('Aperçu du fichier') // Modal heading
                                            ),
                                        IconEntry::make('passeport_page_infos')
                                            ->label('Passeport page informations')
                                            ->visible(function ($record) {
                                                return $record->type_piece_identite === 'passeport';
                                            })
                                            ->hintAction(
                                                \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                ->label('Voir le fichier')
                                                    ->icon('heroicon-o-photo') // Use a relevant icon
                                                    ->media(fn($record) => $record->getFirstMediaUrl('passeport_page_infos')) // Media URL for the CIN Recto image
                                                    ->modalHeading('Aperçu du fichier') // Modal heading
                                            ),
                                        TextEntry::make('email')
                                            ->label('E-mail')
                                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                                        TextEntry::make('telephone_mobile')
                                            ->label('Téléphone mobile')
                                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                                        CountryEntry::make('pays_residence')
                                            ->label('Pays de résidence'),
                                        TextEntry::make('ville_residence')
                                            ->label('Ville de résidence')
                                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                                        TextEntry::make('adresse_residence')
                                            ->label('Adresse de résidence')
                                            ->columnSpan(2)
                                            ->formatStateUsing(fn ($state) => ucfirst($state)),

                                    ])
                            ]),
                    ])
                    ->collapsed(false),

                Section::make("Documents d'état civil & académiques")
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('citoyen_guineen')
                                            ->label("Etes-vous citoyen(ne) guinéen(ne) ?")
                                            ->formatStateUsing(function ($state) {
                                                // Convert $state to boolean and display the corresponding text
                                                $state = filter_var($state, FILTER_VALIDATE_BOOLEAN); // Ensures it's treated as a boolean
                                                return $state ? 'Oui' : 'Non'; // Return 'Oui' for true, 'Non' for false
                                            }),
                                        TextEntry::make('diplome_etranger')
                                            ->label("Votre diplome a t'il été délivré hors de la Guinée ?")
                                            ->formatStateUsing(function ($state) {
                                                // Convert $state to boolean and display the corresponding text
                                                $state = filter_var($state, FILTER_VALIDATE_BOOLEAN); // Ensures it's treated as a boolean
                                                return $state ? 'Oui' : 'Non'; // Return 'Oui' for true, 'Non' for false
                                            }),
                                        TextEntry::make('annee_obtention_diplome')
                                            ->label("Année d'obtention du diplôme")
                                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                                        // Champs conditionnels
                                        IconEntry::make('certificat_nationalite')
                                            ->label('Certificat de nationalité')
                                            ->visible(function ($record) {
                                                return filter_var($record->citoyen_guineen, FILTER_VALIDATE_BOOLEAN);
                                            })
                                            ->hintAction(
                                                \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                ->label('Voir le fichier')
                                                    ->icon('heroicon-o-photo') // Use a relevant icon
                                                    ->media(fn($record) => $record->getFirstMediaUrl('certificat_nationalite')) // Media URL for the CIN Recto image
                                                    ->modalHeading('Aperçu du fichier') // Modal heading
                                            ),

                                        IconEntry::make('certificat_fin_cycle')
                                            ->label('Certificat de fin de cycle')
                                            ->hintAction(
                                                \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                ->label('Voir le fichier')
                                                    ->icon('heroicon-o-photo') // Use a relevant icon
                                                    ->media(fn($record) => $record->getFirstMediaUrl('certificat_fin_cycle')) // Media URL for the CIN Recto image
                                                    ->modalHeading('Aperçu du fichier') // Modal heading
                                            ),
                                        IconEntry::make('diplome')
                                            ->label('Diplôme')
                                            ->hintAction(
                                                \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                ->label('Voir le fichier')
                                                    ->icon('heroicon-o-photo') // Use a relevant icon
                                                    ->media(fn($record) => $record->getFirstMediaUrl('diplome')) // Media URL for the CIN Recto image
                                                    ->modalHeading('Aperçu du fichier') // Modal heading
                                            ),
                                        // Champs conditionnels
                                        IconEntry::make('equivalence_diplome')
                                            ->label("Attestation d'équivalence")
                                            ->visible(function ($record) {
                                                return filter_var($record->diplome_etranger, FILTER_VALIDATE_BOOLEAN);
                                            })
                                            ->hintAction(
                                                \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                ->label('Voir le fichier')
                                                    ->icon('heroicon-o-photo') // Use a relevant icon
                                                    ->media(fn($record) => $record->getFirstMediaUrl('equivalence_diplome')) // Media URL for the CIN Recto image
                                                    ->modalHeading('Aperçu du fichier') // Modal heading
                                            ),

                                        IconEntry::make('extrait_naissance')
                                            ->label('Extrait de naissance')
                                            ->hintAction(
                                                \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                ->label('Voir le fichier')
                                                    ->icon('heroicon-o-photo') // Use a relevant icon
                                                    ->media(fn($record) => $record->getFirstMediaUrl('extrait_naissance')) // Media URL for the CIN Recto image
                                                    ->modalHeading('Aperçu du fichier') // Modal heading
                                            ),
                                        IconEntry::make('casier_judiciaire')
                                            ->label('Casier judiciaire')
                                            ->hintAction(
                                                \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                ->label('Voir le fichier')
                                                    ->icon('heroicon-o-photo') // Use a relevant icon
                                                    ->media(fn($record) => $record->getFirstMediaUrl('casier_judiciaire')) // Media URL for the CIN Recto image
                                                    ->modalHeading('Aperçu du fichier') // Modal heading
                                            ),
                                        IconEntry::make('attestation_moralite')
                                            ->label('Attestation de moralité')
                                            ->hintAction(
                                                \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                ->label('Voir le fichier')
                                                    ->icon('heroicon-o-photo') // Use a relevant icon
                                                    ->media(fn($record) => $record->getFirstMediaUrl('attestation_moralite')) // Media URL for the CIN Recto image
                                                    ->modalHeading('Aperçu du fichier') // Modal heading
                                            ),
                                        IconEntry::make('lettre_manuscrite')
                                            ->label('Lettre manuscrite au Président')
                                            ->hintAction(
                                                \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                ->label('Voir le fichier')
                                                    ->icon('heroicon-o-photo') // Use a relevant icon
                                                    ->media(fn($record) => $record->getFirstMediaUrl('lettre_manuscrite')) // Media URL for the CIN Recto image
                                                    ->modalHeading('Aperçu du fichier') // Modal heading
                                            ),
                                    ]),
                            ]),
                    ])
                    ->collapsed(false),

                Section::make("Situation professionnelle")
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                Grid::make()
                                    ->columnSpan(12)
                                    ->schema([
                                        TextEntry::make('profil')
                                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                                        TextEntry::make('section')
                                            ->formatStateUsing(fn ($state) => strtoupper($state)),
                                        TextEntry::make('salarie')
                                            ->label("Etes-vous salarié ?")
                                            ->formatStateUsing(function ($state) {
                                                // Convert $state to boolean and display the corresponding text
                                                $state = filter_var($state, FILTER_VALIDATE_BOOLEAN); // Ensures it's treated as a boolean
                                                return $state ? 'Oui' : 'Non'; // Return 'Oui' for true, 'Non' for false
                                            }),
                                        // Champs conditionnels
                                        IconEntry::make('attestation_emploi')
                                            ->label("Attestation d'emploi")
                                            ->visible(function ($record) {
                                                return filter_var($record->salarie, FILTER_VALIDATE_BOOLEAN);
                                            })
                                            ->hintAction(
                                                \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                ->label('Voir le fichier')
                                                    ->icon('heroicon-o-photo') // Use a relevant icon
                                                    ->media(fn($record) => $record->getFirstMediaUrl('attestation_emploi')) // Media URL for the CIN Recto image
                                                    ->modalHeading('Aperçu du fichier') // Modal heading
                                            ),
                                    ])
                            ]),
                    ])
                    ->collapsed(false)

            ]);
    }
}
