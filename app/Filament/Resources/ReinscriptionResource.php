<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReinscriptionResource\Pages;
use App\Filament\Resources\ReinscriptionResource\RelationManagers;
use App\Models\Inscription;
use App\Models\Reinscription;
use App\Models\User;
use App\Services\AttestationPdfService;
use App\Settings\DocumentSettings;
use App\Settings\GeneralSettings;
use App\Settings\IdentificationSettings;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Parfaitementweb\FilamentCountryField\Infolists\Components\CountryEntry;
use Parfaitementweb\FilamentCountryField\Tables\Columns\CountryColumn;
use Snowfire\Beautymail\Beautymail;
use Spatie\Permission\Models\Role;

class ReinscriptionResource extends Resource
{

    protected static ?string $model = Reinscription::class;

    protected static ?string $navigationLabel = "Liste des réinscriptions";
    protected static ?string $modelLabel = "Réinscriptions";
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
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
                                               SpatieMediaLibraryImageEntry::make('inscription.photo_identite')
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
                                                TextEntry::make('inscription.numero_rngps')
                                                    ->label('Numéro RNGPS')
                                                    ->badge()
                                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                                TextEntry::make('inscription.numero_ordre')
                                                    ->label('Numéro Ordre')
                                                    ->badge()
                                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                                TextEntry::make('inscription.prenom')
                                                    ->label('Prénom')
                                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                                TextEntry::make('inscription.nom')
                                                    ->label('Nom')
                                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                                TextEntry::make('inscription.genre')
                                                    ->label('Gentilité')
                                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                                TextEntry::make('inscription.date_naissance')
                                                    ->label('Date de naissance')
                                                    ->formatStateUsing(function ($state) {
                                                        // Format the date in French format
                                                        $date = Carbon::parse($state);
                                                        return $date->isoFormat('D MMMM YYYY');  // Example: 25 janvier 2023
                                                    }),
                                                CountryEntry::make('inscription.pays_naissance')
                                                    ->label('Pays de naissance'),
                                                CountryEntry::make('inscription.lieu_naissance')
                                                    ->label('Lieu de naissance'),
                                                CountryEntry::make('inscription.nationalite')
                                                    ->label('Nationalité'),
                                                TextEntry::make('inscription.citoyen_guineen')
                                                    ->label("Etes-vous citoyen(ne) guinéen(ne) ?")
                                                    ->formatStateUsing(function ($state) {
                                                        // Convert $state to boolean and display the corresponding text
                                                        $state = filter_var($state, FILTER_VALIDATE_BOOLEAN); // Ensures it's treated as a boolean
                                                        return strtoupper($state ? 'Oui' : 'Non'); // Return 'Oui' for true, 'Non' for false
                                                    }),
                                                TextEntry::make('inscription.type_piece_identite')
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
                                                TextEntry::make('inscription.email')
                                                    ->label('E-mail'),
                                                TextEntry::make('inscription.telephone_mobile')
                                                    ->label('Téléphone mobile')
                                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                                CountryEntry::make('inscription.pays_residence')
                                                    ->label('Pays de résidence'),
                                                TextEntry::make('inscription.ville_residence')
                                                    ->label('Ville de résidence')
                                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                                TextEntry::make('inscription.adresse_residence')
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
                                                TextEntry::make('inscription.date_obtention_diplome')
                                                    ->label("Date d'obtention du diplôme")
                                                    ->formatStateUsing(function ($state) {
                                                        // Format the date in French format
                                                        $date = Carbon::parse($state);
                                                        return $date->isoFormat('D MMMM YYYY');  // Example: 25 janvier 2023
                                                    }),
                                                TextEntry::make('inscription.etablissement_etude')
                                                    ->label("Etablissement d'enseignement")
                                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                                TextEntry::make('inscription.diplome_etranger')
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
                                                TextEntry::make('inscription.profil')
                                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                                TextEntry::make('inscription.section')
                                                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                                                TextEntry::make('inscription.salarie')
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
                                if ($record->inscription->type_piece_identite === 'cin') {
                                    if ($record->inscription->getFirstMedia('cin_recto')) $count++;
                                    if ($record->inscription->getFirstMedia('cin_verso')) $count++;
                                } elseif ($record->inscription->type_piece_identite === 'passeport') {
                                    if ($record->inscription->getFirstMedia('passeport_premiere_page')) $count++;
                                    if ($record->inscription->getFirstMedia('passeport_page_infos')) $count++;
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
                                    if ($record->inscription->getFirstMedia($doc)) {
                                        // Handle conditional documents
                                        if ($doc === 'certificat_nationalite' && !filter_var($record->inscription->citoyen_guineen, FILTER_VALIDATE_BOOLEAN)) continue;
                                        if ($doc === 'equivalence_diplome' && !filter_var($record->inscription->diplome_etranger, FILTER_VALIDATE_BOOLEAN)) continue;
                                        if ($doc === 'attestation_emploi' && !filter_var($record->inscription->salarie, FILTER_VALIDATE_BOOLEAN)) continue;

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
                                                IconEntry::make('inscription.cin_recto')
                                                    ->label('CIN Recto')
                                                    ->visible(function ($record) {
                                                        return $record->inscription->type_piece_identite === 'cin';
                                                    })
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->inscription->getFirstMediaUrl('cin_recto')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // CIN Verso
                                                IconEntry::make('inscription.cin_verso')
                                                    ->label('CIN Verso')
                                                    ->visible(function ($record) {
                                                        return $record->inscription->type_piece_identite === 'cin';
                                                    })
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->inscription->getFirstMediaUrl('cin_verso')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Passeport Première Page
                                                IconEntry::make('inscription.passeport_premiere_page')
                                                    ->label('Passeport première page')
                                                    ->visible(function ($record) {
                                                        return $record->inscription->type_piece_identite === 'passeport';
                                                    })
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->inscription->getFirstMediaUrl('passeport_premiere_page')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Passeport Page Infos
                                                IconEntry::make('inscription.passeport_page_infos')
                                                    ->label('Passeport page informations')
                                                    ->visible(function ($record) {
                                                        return $record->inscription->type_piece_identite === 'passeport';
                                                    })
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->inscription->getFirstMediaUrl('passeport_page_infos')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Certificat de nationalité
                                                IconEntry::make('inscription.certificat_nationalite')
                                                    ->label('Certificat de nationalité')
                                                    ->visible(function ($record) {
                                                        return filter_var($record->inscription->citoyen_guineen, FILTER_VALIDATE_BOOLEAN);
                                                    })
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->inscription->getFirstMediaUrl('certificat_nationalite')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Certificat de fin de cycle
                                                IconEntry::make('inscription.certificat_fin_cycle')
                                                    ->label('Certificat de fin de cycle')
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->inscription->getFirstMediaUrl('certificat_fin_cycle')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Diplome d'études
                                                IconEntry::make('inscription.diplome')
                                                    ->label('Diplôme')
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->inscription->getFirstMediaUrl('diplome')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Equivalence diplôme d'études
                                                IconEntry::make('inscription.equivalence_diplome')
                                                    ->label("Attestation d'équivalence")
                                                    ->visible(function ($record) {
                                                        return filter_var($record->diplome_etranger, FILTER_VALIDATE_BOOLEAN);
                                                    })
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->inscription->getFirstMediaUrl('equivalence_diplome')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Extrait de naissance
                                                IconEntry::make('inscription.extrait_naissance')
                                                    ->label('Extrait de naissance')
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->inscription->getFirstMediaUrl('extrait_naissance')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Casier judiciare
                                                IconEntry::make('inscription.casier_judiciaire')
                                                    ->label('Casier judiciaire')
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->inscription->getFirstMediaUrl('casier_judiciaire')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Attestation de moralité
                                                IconEntry::make('inscription.attestation_moralite')
                                                    ->label('Attestation de moralité')
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->inscription->getFirstMediaUrl('attestation_moralite')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Lettre manuscrite
                                                IconEntry::make('inscription.lettre_manuscrite')
                                                    ->label('Lettre manuscrite au Président')
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->inscription->getFirstMediaUrl('lettre_manuscrite')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                                // Attestation d'emploi
                                                IconEntry::make('inscription.attestation_emploi')
                                                    ->label("Attestation d'emploi")
                                                    ->visible(function ($record) {
                                                        return filter_var($record->inscription->salarie, FILTER_VALIDATE_BOOLEAN);
                                                    })
                                                    ->hintAction(
                                                        \Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction::make('viewFile') // Action name
                                                        ->label('Afficher')
                                                            ->icon('heroicon-o-photo') // Use a relevant icon
                                                            ->media(fn($record) => $record->inscription->getFirstMediaUrl('attestation_emploi')) // Media URL for the CIN Recto image
                                                            ->modalHeading('Aperçu du fichier') // Modal heading
                                                    ),
                                            ]),
                                    ]),
                            ]),

                    ])->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->searchable(),
                SpatieMediaLibraryImageColumn::make('inscription.photo_identite')
                    ->collection('photo_identite')
                    ->label('Photo ID')
                    ->circular(),
                TextColumn::make('inscription.nom')
                    ->label('Prénom & Nom')
                    ->searchable()
                    ->formatStateUsing(function ($state, Reinscription $reinscription) {
                        return $reinscription->inscription->nom . ' ' . $reinscription->inscription->prenom;
                    }),
                TextColumn::make('inscription.email')->label('Email')->searchable(),
                TextColumn::make('inscription.telephone_mobile')->label('Telephone')->searchable(),
                CountryColumn::make('inscription.nationalite')->label('Nationalité')->searchable(),

            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->hiddenLabel()
                    ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReinscriptions::route('/'),
            'view' => Pages\ViewReInscription::route('/{record}'),
        ];
    }

    public function approveReinscription(Reinscription $reinscription)
    {
        DB::transaction(function () use ($reinscription) {

            // Update registration status
            $reinscription->inscription->update([
                'statut' => 'approved',
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addYear(),
            ]);

            $reinscription->update([
                'statut' => 'approved',
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addYear(),
            ]);


            // 2. Générer l'attestation PDF avec TCPDF
            $attestationService = app()->make(AttestationPdfService::class);

            $signature_president = public_path('storage/'.app(DocumentSettings::class)->signature_president);
            $attestation_background_url = public_path('storage/'.app(DocumentSettings::class)->attestation_background) ;
            $media_uuid = Str::uuid()->toString();
            $filename = 'attestation_' . $reinscription->inscription->id . '_' . Str::random(8) . '.pdf';

            $data = [
                'presidentNom' => app(DocumentSettings::class)->nom_president,
                'rngpsNumero' => $reinscription->inscription->numero_rngps,
                'ordreNumero' => $reinscription->inscription->numero_ordre,
                'validiteAttesation' => $reinscription->inscription->valid_until,
                'pharmacienNom' => $reinscription->inscription->prenom.' '.$reinscription->inscription->nom,
                'pharmacienProfil' => ucfirst($reinscription->inscription->profil),
                'dateOfValidation' => $reinscription->inscription->valid_from,
                'signatureAttestation' => $signature_president,
                'backgroundAttestation' => $attestation_background_url,
                'verifyAttestation_url' => route('attestation.verify', $media_uuid),
                'fileName' => $filename,
            ];

            $pdfContent = $attestationService->generate($data);

            // 3. Générer un nom de fichier unique
            $media = $reinscription->inscription->addMediaFromString($pdfContent)  // Add the PDF content to media
            ->usingFileName($filename)  // Use the generated filename
            ->withCustomProperties([
                'generated_date' => now()->format('Y-m-d H:i:s'),
                'document_type' => 'attestation',
                'readable_name' => 'Attestation '.date('Y'),
                'reference_number' => $data['rngpsNumero'],
                'valid_until' => $data['validiteAttesation'],
                'uuid' => $media_uuid,
                // Vous pouvez ajouter d'autres métadonnées ici
            ])->toMediaCollection('attestations');  // Save it to the 'attestations' collection

            // 6. Envoyer l'email avec Beautymail
            $beautymail = app()->make(BeautyMail::class);
            $beautymail->send('emails.reinscription-approved', [
                'reinscription' => $reinscription,
                'date' => now()->format('d/m/Y'),
            ], function ($message) use ($reinscription, $media) {
                $message
                    ->from('ousmaneciss1@gmail.com')
                    ->to($reinscription->inscription->email, $reinscription->inscription->prenom . ' ' . $reinscription->inscription->nom)
                    ->subject('Votre réinscription a été approuvée - ONPG')
                    // Attach the file using the file path from Spatie Media Library
                    ->attach($media->getPath(), [
                        'as' => 'attestation.pdf',
                        'mime' => 'application/pdf',
                    ]);
            });


        });

        Notification::make()
            ->title('Réinscription Approuvée')
            ->body("La réinscription a été approuvée et les emails ont été envoyés.")
            ->success()
            ->send();
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('statut','pending')->count();
    }
}
