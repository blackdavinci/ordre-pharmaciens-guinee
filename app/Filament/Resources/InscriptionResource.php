<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InscriptionResource\Pages;
use App\Filament\Resources\InscriptionResource\RelationManagers;
use App\Models\Inscription;
use App\Models\User;
use App\Services\AttestationPdfService;
use App\Settings\DocumentSettings;
use App\Settings\IdentificationSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;  // Import the DB facade
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Parfaitementweb\FilamentCountryField\Infolists\Components\CountryEntry;
use Parfaitementweb\FilamentCountryField\Tables\Columns\CountryColumn;
use Snowfire\Beautymail\Beautymail;
use Spatie\Permission\Models\Role;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Carbon\Carbon;

class InscriptionResource extends Resource
{
    protected static ?string $model = Inscription::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

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
                                                Infolists\Components\SpatieMediaLibraryImageEntry::make('photo_identite')
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->searchable(),
                SpatieMediaLibraryImageColumn::make('photo_identite')
                    ->collection('photo_identite')
                    ->label('Photo ID')
                    ->circular(),
                TextColumn::make('nom')
                    ->label('Prénom & Nom')
                    ->searchable()
                    ->formatStateUsing(function ($state, Inscription $inscription) {
                        return $inscription->nom . ' ' . $inscription->prenom;
                    }),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('telephone_mobile')->label('Telephone')->searchable(),
                CountryColumn::make('nationalite')->label('Nationalité')->searchable(),

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
            'index' => Pages\ListInscriptions::route('/'),
            'create' => Pages\CreateInscription::route('/create'),
            'view' => Pages\ViewInscription::route('/{record}'),
            'edit' => Pages\EditInscription::route('/{record}/edit'),
        ];
    }

    public function approveInscription(Inscription $inscription)
    {
        DB::transaction(function () use ($inscription) {
            // Generate unique registration code
            $rngps = $inscription->generateRngps();

            // Generate random password
            $password = Str::random(12);

            // Create user account
            $user = User::create([
                'name' => User::makeUniqueName($inscription->nom, $inscription->prenom),
                'prenom' => $inscription->prenom,
                'nom' => $inscription->nom,
                'email' => $inscription->email,
                'telephone' => $inscription->telephone_mobile,
                'password' => Hash::make($password),
                'statut' => true,
            ]);

            // Update registration status
            $inscription->update([
                'statut' => 'approved',
                'numero_rngps' => $rngps,
                'user_id' => $user->id,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addYear(),
            ]);

            // Assign role
            $role = Role::findByName('membre');
            if($role==null){
                Role::create(['name' => 'membre']);
            }
            $user->assignRole($role);

            // 2. Générer l'attestation PDF avec TCPDF
            $attestationService = app()->make(AttestationPdfService::class);

            $signature_president = public_path('storage/'.app(DocumentSettings::class)->signature_president);
            $attestation_background_url = public_path('storage/'.app(DocumentSettings::class)->attestation_background) ;

            $data = [
                'presidentNom' => app(DocumentSettings::class)->nom_president,
                'rngpsNumero' => $inscription->numero_rngps,
                'medecinNumero' => $inscription->numero_medecin,
                'validiteAttesation' => $inscription->valid_until,
                'pharmacienNom' => $inscription->prenom.' '.$inscription->nom,
                'pharmacienProfil' => ucfirst($inscription->profil),
                'dateOfValidation' => $inscription->valid_from,
                'signatureAttestation' => $signature_president,
                'backgroundAttestation' => $attestation_background_url,
            ];

            $pdfContent = $attestationService->generate($data);

            // 3. Générer un nom de fichier unique
            $filename = 'attestation_' . $inscription->id . '_' . Str::random(8) . '.pdf';

            // Sauvegarder le PDF dans la bibliothèque de médias Spatie
            $media = $inscription->addMediaFromString($pdfContent)  // Add the PDF content to media
            ->usingFileName($filename)  // Use the generated filename
            ->withCustomProperties([
                'generated_date' => now()->format('Y-m-d H:i:s'),
                // Vous pouvez ajouter d'autres métadonnées ici
            ])
            ->toMediaCollection('attestations');  // Save it to the 'attestations' collection

            // 6. Envoyer l'email avec Beautymail
            $beautymail = app()->make(BeautyMail::class);
            $beautymail->send('emails.inscription-approved', [
                'inscription' => $inscription,
                'date' => now()->format('d/m/Y'),
            ], function ($message) use ($inscription, $media) {
                $message
                    ->from('ousmaneciss1@gmail.com')
                    ->to($inscription->email, $inscription->prenom . ' ' . $inscription->nom)
                    ->subject('Votre inscription a été approuvée - ONPG')
                    // Attach the file using the file path from Spatie Media Library
                    ->attach($media->getPath(), [
                        'as' => 'attestation.pdf',
                        'mime' => 'application/pdf',
                    ]);
            });


            // Envoi des informations de compte utilisateur
            $beautymail->send('emails.user-account', ['user' => $user, 'password' => $password], function ($message) use ($user) {
                $message
                    ->from('ousmaneciss1@gmail.com')
                    ->to($user->email, $user->prenom.' '.$user->nom)
                    ->subject('Votre compte a été créé - ONPG');
            });
        });

        Notification::make()
            ->title('Inscription Approuvée')
            ->body("L'inscription a été approuvée et les emails ont été envoyés.")
            ->success()
            ->send();
    }

}
