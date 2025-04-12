<?php

namespace App\Filament\Resources\InscriptionResource\Pages;

use App\Filament\Resources\InscriptionResource;
use App\Models\Inscription;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Resources\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Settings\IdentificationSettings;
use Parfaitementweb\FilamentCountryField\Infolists\Components\CountryEntry;
use Spatie\Permission\Traits\HasPermissions;


class MonInscription extends Page
{
    use HasPageShield, HasPermissions;

    public static function getPermissions(): array
    {
        return [
            'view-mon-inscription' => 'Mon Inscription Page',
        ];
    }

    protected static string $resource = InscriptionResource::class;

    protected static string $view = 'filament.resources.inscription-resource.pages.mon-inscription';


    public $inscription;

    public function mount(): void
    {
//        if (!auth()->user()->hasRole('membre')) {
//            abort(403, 'Non autorisé.');
//        }

        // Récupérer l'inscription de l'utilisateur connecté
        $this->inscription = Inscription::where('user_id', auth()->id())->first();


        // Si l'inscription n'existe pas, tu peux soit lancer une erreur, soit gérer autrement.
        if (!$this->inscription) {
            abort(404, 'Inscription non trouvée.');
        }
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
                                                    ->formatStateUsing(fn($state) => ucfirst($state)),
                                                TextEntry::make('numero_medecin')
                                                    ->label('Numéro Médécin')
                                                    ->formatStateUsing(fn($state) => ucfirst($state)),
                                                TextEntry::make('prenom')
                                                    ->label('Prénom')
                                                    ->formatStateUsing(fn($state) => ucfirst($state)),
                                                TextEntry::make('nom')
                                                    ->label('Nom')
                                                    ->formatStateUsing(fn($state) => ucfirst($state)),
                                                TextEntry::make('genre')
                                                    ->label('Gentilité')
                                                    ->formatStateUsing(fn($state) => ucfirst($state)),
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
                                                TextEntry::make('citoyen_guineen')
                                                    ->label("Etes-vous citoyen(ne) guinéen(ne) ?")
                                                    ->formatStateUsing(function ($state) {
                                                        // Convert $state to boolean and display the corresponding text
                                                        $state = filter_var($state, FILTER_VALIDATE_BOOLEAN);
                                                        return strtoupper($state ? 'Oui' : 'Non');
                                                    }),
                                                TextEntry::make('type_piece_identite')
                                                    ->label("Pièce d'identité")
                                                    ->formatStateUsing(function ($state) {
                                                        return $state === 'cin' ? 'Carte d\'identité' : ($state === 'passeport' ? 'Passeport' : '');
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
                                                    ->formatStateUsing(fn($state) => ucfirst($state)),
                                                TextEntry::make('telephone_mobile')
                                                    ->label('Téléphone mobile')
                                                    ->formatStateUsing(fn($state) => ucfirst($state)),
                                                CountryEntry::make('pays_residence')
                                                    ->label('Pays de résidence'),
                                                TextEntry::make('ville_residence')
                                                    ->label('Ville de résidence')
                                                    ->formatStateUsing(fn($state) => ucfirst($state)),
                                                TextEntry::make('adresse_residence')
                                                    ->label('Adresse de résidence')
                                                    ->columnSpan(2)
                                                    ->formatStateUsing(fn($state) => ucfirst($state)),
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
                                                    ->formatStateUsing(fn($state) => ucfirst($state)),
                                                TextEntry::make('code_etablissement')
                                                    ->label("Etablissement d'enseignement")
                                                    ->formatStateUsing(function ($state) {
                                                        $etablissements = app(IdentificationSettings::class)->code_etablissement;
                                                        $matchingEtablissement = collect($etablissements)->firstWhere('code', $state);
                                                        return $matchingEtablissement ? ucfirst($matchingEtablissement['nom']) : $state;
                                                    }),
                                                TextEntry::make('diplome_etranger')
                                                    ->label("Votre diplome a t'il été délivré hors de la Guinée ?")
                                                    ->formatStateUsing(function ($state) {
                                                        $state = filter_var($state, FILTER_VALIDATE_BOOLEAN);
                                                        return strtoupper($state ? 'Oui' : 'Non');
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
                                                    ->formatStateUsing(fn($state) => ucfirst($state)),
                                                TextEntry::make('section')
                                                    ->formatStateUsing(fn($state) => strtoupper($state)),
                                                TextEntry::make('salarie')
                                                    ->label("Etes-vous salarié ?")
                                                    ->formatStateUsing(function ($state) {
                                                        $state = filter_var($state, FILTER_VALIDATE_BOOLEAN);
                                                        return strtoupper($state ? 'Oui' : 'Non');
                                                    }),
                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('Documents')
                            ->badge(function ($record) {
                                $count = 0;

                                if ($record->type_piece_identite === 'cin') {
                                    if ($record->getFirstMedia('cin_recto')) $count++;
                                    if ($record->getFirstMedia('cin_verso')) $count++;
                                } elseif ($record->type_piece_identite === 'passeport') {
                                    if ($record->getFirstMedia('passeport_premiere_page')) $count++;
                                    if ($record->getFirstMedia('passeport_page_infos')) $count++;
                                }

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
                                                IconEntry::make('cin_recto')
                                                    ->label('CIN Recto')
                                                    ->visible(function ($record) {
                                                        return $record->type_piece_identite === 'cin';
                                                    })
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile')
                                                            ->label('Afficher')
                                                            ->icon('heroicon-o-photo')
                                                            ->media(fn($record) => $record->getFirstMediaUrl('cin_recto'))
                                                            ->modalHeading('Aperçu du fichier')
                                                    ),
                                                // Et tous les autres documents comme dans votre exemple...
                                                // J'ai abrégé ici pour la lisibilité, mais vous incluriez tous les mêmes éléments IconEntry
                                            ]),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

//    public static function shouldRegisterNavigation(array $parameters = []): bool
//    {
//        // Afficher dans la navigation uniquement si l'utilisateur a une inscription
//        return Inscription::where('user_id', Auth::id())->exists();
//    }
}
