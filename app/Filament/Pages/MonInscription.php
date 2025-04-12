<?php

namespace App\Filament\Pages;

use App\Models\Inscription;
use App\Settings\IdentificationSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Parfaitementweb\FilamentCountryField\Infolists\Components\CountryEntry;

class MonInscription extends Page
{
    use HasPageShield;
    protected static ?string $navigationLabel = 'Mon Inscription';

    protected static ?string $title = "Mon inscription";
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.mon-inscription';

    // Déclaration explicite de la propriété record
    public $record;

    public function mount()
    {
        // Vérifier si l'utilisateur est connecté
        if (!auth()->check()) {
            abort(403, 'Vous devez être connecté pour accéder à cette page.');
        }

        // Récupérer l'inscription de l'utilisateur connecté (utilise l'ID de l'utilisateur)
        $this->record = Inscription::where('user_id', auth()->id())->first();

        // Vérifier si l'inscription existe pour l'utilisateur connecté
        if (!$this->record) {
            abort(404, 'Inscription non trouvée.');
        }
        // Si l'inscription existe, elle est maintenant disponible dans la propriété $this->record
    }


    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Identité personnelle')
                            ->schema([
                                Grid::make(12)
                                    ->schema([
                                        Grid::make()
                                            ->columnSpan(2)
                                            ->schema([
                                                SpatieMediaLibraryImageEntry::make('photo_identite')
                                                    ->collection('photo_identite')
                                                    ->label('Photo ID')
                                                    ->hiddenLabel()
                                                    ->square()
                                                    ->extraImgAttributes([
                                                        'style' => 'object-fit: contain; width: 100%; height: 100%;'
                                                    ])
                                                    ->columnSpan(2),

                                            ]),
                                        Grid::make()
                                            ->columnSpan(10)
                                            ->schema([
                                                TextEntry::make('numero_rngps')
                                                    ->label('Numéro RNGPS')
                                                    ->badge()
                                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                                TextEntry::make('numero_medecin')
                                                    ->label('Numéro Médécin')
                                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
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
                                                        $date = \Carbon\Carbon::parse($state);
                                                        return $date->isoFormat('D MMMM YYYY');  // Example: 25 janvier 2023
                                                    }),
                                                CountryEntry::make('pays_naissance')
                                                    ->label('Pays de naissance'),
                                                CountryEntry::make('lieu_naissance')
                                                    ->label('Lieu de naissance'),
                                                CountryEntry::make('nationalite')
                                                    ->label('Nationalité'),
                                                TextEntry::make('citoyen_guineen')
                                                    ->label("Etes-vous citoyen(ne) guinéen(ne) ?")
                                                    ->formatStateUsing(function ($state) {
                                                        // Convert $state to boolean and display the corresponding text
                                                        $state = filter_var($state, FILTER_VALIDATE_BOOLEAN); // Ensures it's treated as a boolean
                                                        return strtoupper($state ? 'Oui' : 'Non'); // Return 'Oui' for true, 'Non' for false
                                                    }),
                                                TextEntry::make('type_piece_identite')
                                                    ->label("Pièce d'identité")
                                                    ->formatStateUsing(function ($state) {
                                                        // Vérifier la valeur et afficher le texte correspondant
                                                        return $state === 'cin' ? 'Carte d\'identité' : ($state === 'passeport' ? 'Passeport' : '');  // Valeur par défaut si non trouvé
                                                    }),
                                            ])
                                    ]),
                            ]),
                        Tabs\Tab::make('Coordonnées')
                            ->schema([
                                Grid::make(12)
                                    ->schema([
                                        Grid::make()
                                            ->columnSpan(12)
                                            ->schema([
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
                            ]),
                        Tabs\Tab::make('Informations académiques')
                            ->schema([
                                Grid::make(12)
                                    ->schema([
                                        Grid::make()
                                            ->columnSpan(12)
                                            ->schema([
                                                TextEntry::make('annee_obtention_diplome')
                                                    ->label("Année d'obtention du diplôme")
                                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                                TextEntry::make('code_etablissement')
                                                    ->label("Etablissement d'enseignement")
                                                    ->formatStateUsing(function ($state) {
                                                        // Retrieve the list of establishments from settings
                                                        $etablissements = app(IdentificationSettings::class)->code_etablissement;

                                                        // Find the matching establishment name based on the 'code'
                                                        $matchingEtablissement = collect($etablissements)->firstWhere('code', $state);

                                                        // Return the name (nom) if found, otherwise return the code
                                                        return $matchingEtablissement ? ucfirst($matchingEtablissement['nom']) : $state;
                                                    }),
                                                TextEntry::make('diplome_etranger')
                                                    ->label("Votre diplome a t'il été délivré hors de la Guinée ?")
                                                    ->formatStateUsing(function ($state) {
                                                        // Convert $state to boolean and display the corresponding text
                                                        $state = filter_var($state, FILTER_VALIDATE_BOOLEAN); // Ensures it's treated as a boolean
                                                        return strtoupper($state ? 'Oui' : 'Non'); // Return 'Oui' for true, 'Non' for false
                                                    }),
                                            ])
                                    ]),
                            ]),
                        Tabs\Tab::make('Situation professionnelle')
                            ->schema([
                                Grid::make(12)
                                    ->schema([
                                        Grid::make(3)
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
                                                        return strtoupper($state ? 'Oui' : 'Non'); // Return 'Oui' for true, 'Non' for false
                                                    }),
                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('Documents')
                            ->badge(function ($record) {
                                $count = 0;

                                // Check each document type and increment count if exists
                                if ($record->type_piece_identite === 'cin') {
                                    if ($record->getFirstMedia('cin_recto')) $count++;
                                    if ($record->getFirstMedia('cin_verso')) $count++;
                                } elseif ($record->type_piece_identite === 'passeport') {
                                    if ($record->getFirstMedia('passeport_premiere_page')) $count++;
                                    if ($record->getFirstMedia('passeport_page_infos')) $count++;
                                }

                                // Common documents
                                $documents = [
                                    'certificat_nationalite',
                                    'certificat_fin_cycle',
                                    'diplome',
                                    'equivalence_diplome',
                                    'extrait_naissance',
                                    'casier_judiciaire',
                                    'attestation_moralite',
                                    'lettre_manuscrite',
                                    'attestation_emploi'
                                ];

                                foreach ($documents as $doc) {
                                    if ($record->getFirstMedia($doc)) {
                                        // Handle conditional documents
                                        if ($doc === 'certificat_nationalite' && !filter_var($record->citoyen_guineen, FILTER_VALIDATE_BOOLEAN)) continue;
                                        if ($doc === 'equivalence_diplome' && !filter_var($record->diplome_etranger, FILTER_VALIDATE_BOOLEAN)) continue;
                                        if ($doc === 'attestation_emploi' && !filter_var($record->salarie, FILTER_VALIDATE_BOOLEAN)) continue;

                                        $count++;
                                    }
                                }

                                return $count;
                            })
                            ->badgeColor('danger')
                            ->schema([
                                Grid::make(12)
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                // CIN Recto
                                                IconEntry::make('cin_recto')
                                                    ->label('CIN Recto')
                                                    ->visible(function ($record) {
                                                        return $record->type_piece_identite === 'cin';
                                                    })
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->getFirstMediaUrl('cin_recto')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // CIN Verso
                                                IconEntry::make('cin_verso')
                                                    ->label('CIN Verso')
                                                    ->visible(function ($record) {
                                                        return $record->type_piece_identite === 'cin';
                                                    })
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->getFirstMediaUrl('cin_verso')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Passeport Première Page
                                                IconEntry::make('passeport_premiere_page')
                                                    ->label('Passeport première page')
                                                    ->visible(function ($record) {
                                                        return $record->type_piece_identite === 'passeport';
                                                    })
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->getFirstMediaUrl('passeport_premiere_page')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Passeport Page Infos
                                                IconEntry::make('passeport_page_infos')
                                                    ->label('Passeport page informations')
                                                    ->visible(function ($record) {
                                                        return $record->type_piece_identite === 'passeport';
                                                    })
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->getFirstMediaUrl('passeport_page_infos')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Certificat de nationalité
                                                IconEntry::make('certificat_nationalite')
                                                    ->label('Certificat de nationalité')
                                                    ->visible(function ($record) {
                                                        return filter_var($record->citoyen_guineen, FILTER_VALIDATE_BOOLEAN);
                                                    })
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->getFirstMediaUrl('certificat_nationalite')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Certificat de fin de cycle
                                                IconEntry::make('certificat_fin_cycle')
                                                    ->label('Certificat de fin de cycle')
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->getFirstMediaUrl('certificat_fin_cycle')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Diplome d'études
                                                IconEntry::make('diplome')
                                                    ->label('Diplôme')
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->getFirstMediaUrl('diplome')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Equivalence diplôme d'études
                                                IconEntry::make('equivalence_diplome')
                                                    ->label("Attestation d'équivalence")
                                                    ->visible(function ($record) {
                                                        return filter_var($record->diplome_etranger, FILTER_VALIDATE_BOOLEAN);
                                                    })
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->getFirstMediaUrl('equivalence_diplome')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Extrait de naissance
                                                IconEntry::make('extrait_naissance')
                                                    ->label('Extrait de naissance')
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->getFirstMediaUrl('extrait_naissance')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Casier judiciare
                                                IconEntry::make('casier_judiciaire')
                                                    ->label('Casier judiciaire')
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->getFirstMediaUrl('casier_judiciaire')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Attestation de moralité
                                                IconEntry::make('attestation_moralite')
                                                    ->label('Attestation de moralité')
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->getFirstMediaUrl('attestation_moralite')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Lettre manuscrite
                                                IconEntry::make('lettre_manuscrite')
                                                    ->label('Lettre manuscrite au Président')
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->getFirstMediaUrl('lettre_manuscrite')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Attestation d'emploi
                                                IconEntry::make('attestation_emploi')
                                                    ->label("Attestation d'emploi")
                                                    ->visible(function ($record) {
                                                        return filter_var($record->salarie, FILTER_VALIDATE_BOOLEAN);
                                                    })
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->getFirstMediaUrl('attestation_emploi')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                            ]),
                                    ]),
                            ]),

                    ])->columnSpanFull(),

            ]);
    }
}
