<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InscriptionResource\Pages;
use App\Filament\Resources\InscriptionResource\RelationManagers;
use App\Models\Inscription;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;  // Import the DB facade
use Parfaitementweb\FilamentCountryField\Infolists\Components\CountryEntry;
use Parfaitementweb\FilamentCountryField\Tables\Columns\CountryColumn;
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
                Section::make('Informations Personnelles')
                    ->description('Identité du pharmacien')
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                Grid::make()
                                    ->columnSpan(2)
                                    ->schema([
                                        SpatieMediaLibraryImageEntry::make('photo_identite')
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
                                        SpatieMediaLibraryImageEntry::make('cin_recto')
                                            ->label('CIN Recto')
                                            ->visible(function ($record) {
                                                return $record->type_piece_identite === 'cin';
                                            }),
                                        SpatieMediaLibraryImageEntry::make('cin_verso')
                                            ->label('CIN Verso')
                                            ->visible(function ($record) {
                                                return $record->type_piece_identite === 'cin';
                                            }),
                                        SpatieMediaLibraryImageEntry::make('passeport_premiere_page')
                                            ->label('Passeport première page')
                                            ->visible(function ($record) {
                                                return $record->type_piece_identite === 'passeport';
                                            }),
                                        SpatieMediaLibraryImageEntry::make('passeport_page_infos')
                                            ->label('Passeport page informations')
                                            ->visible(function ($record) {
                                                return $record->type_piece_identite === 'passeport';
                                            })
                                    ])
                            ]),
                    ])
                    ->collapsed(false),

                Section::make('Coordonnées')
                    ->description('Adresse et contact')
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

                                    ])
                            ]),
                    ])
                    ->collapsed(false),

                Section::make("Documents d'état civil")
                    ->description('Etat civil, antécédents judiciaires et vérification de moralité')
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                Grid::make()
                                    ->columnSpan(12)
                                    ->schema([
                                        TextEntry::make('citoyen_guineen')
                                            ->label("Etes-vous citoyen(ne) guinéen(ne) ?")
                                            ->formatStateUsing(function ($state) {
                                                // Vérifier la valeur et afficher le texte correspondant
                                                return $state === true ? 'Oui' : ($state === false ? 'Non' : '');  // Valeur par défaut si non trouvé
                                            }),

                                        // Champs conditionnels
                                        SpatieMediaLibraryImageEntry::make('certificat_nationalite')
                                            ->label('Certificat de nationalité')
                                            ->visible(function ($record) {
                                                return $record->citoyen_guineen === true;
                                            }),

                                        SpatieMediaLibraryImageEntry::make('extrait_naissance')
                                            ->label('Extrait de naissance'),
                                        SpatieMediaLibraryImageEntry::make('casier_judiciaire')
                                            ->label('Casier judiciaire'),
                                        SpatieMediaLibraryImageEntry::make('attestation_moralite')
                                            ->label('Attestation de moralité'),
                                        SpatieMediaLibraryImageEntry::make('lettre_manuscrite')
                                            ->label('Lettre manuscrite au Président'),

                                    ])
                            ]),
                    ])
                    ->collapsed(false),

                Section::make("Informations académiques")
                    ->description('Diplôme et parcours')
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                Grid::make()
                                    ->columnSpan(12)
                                    ->schema([
                                        TextEntry::make('annee_obtention_diplome')
                                            ->label("Année d'obtention du diplôme")
                                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                                        SpatieMediaLibraryImageEntry::make('diplome')
                                            ->label('Diplôme'),
                                        SpatieMediaLibraryImageEntry::make('certificat_fin_cycle')
                                            ->label('Certificat de fin de cycle'),

                                        TextEntry::make('diplome_etranger')
                                            ->label("Votre diplome a t'il été délivré hors de la Guinée ?")
                                            ->formatStateUsing(function ($state) {
                                                // Vérifier la valeur et afficher le texte correspondant
                                                return $state === true ? 'Oui' : ($state === false ? 'Non' : '');  // Valeur par défaut si non trouvé
                                            }),
                                        // Champs conditionnels
                                        SpatieMediaLibraryImageEntry::make('equivalence_diplome')
                                            ->label("Attestation d'équivalence")
                                            ->visible(function ($record) {
                                                return $record->diplome_etranger === true;
                                            }),

                                    ])
                            ]),
                    ])
                    ->collapsed(false),

                Section::make("Situation professionnelle")
                    ->description('Profil et emploi')
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                Grid::make()
                                    ->columnSpan(12)
                                    ->schema([
                                        TextEntry::make('profil')
                                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                                        TextEntry::make('section')
                                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                                        TextEntry::make('salarie')
                                            ->label("Etes-vous salarié ?")
                                            ->formatStateUsing(function ($state) {
                                                // Vérifier la valeur et afficher le texte correspondant
                                                return $state === true ? 'Oui' : ($state === false ? 'Non' : '');  // Valeur par défaut si non trouvé
                                            }),

                                        // Champs conditionnels
                                        SpatieMediaLibraryImageEntry::make('attestation_emploi')
                                            ->label('CIN Recto')
                                            ->visible(function ($record) {
                                                return $record->salarie === true;
                                            }),

                                    ])
                            ]),
                    ])
                    ->collapsed(false)

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
                TextColumn::make('nationalite')->label('Nationalité')->searchable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'approved' => 'Approuvée',
                        'rejected' => 'Rejetée',
                        'pending' => 'En attente d\'approbation',
                        default => uc($state),
                    })
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'approved',
                        'heroicon-o-x-circle' => 'rejected',
                        'heroicon-o-clock' => 'pending',
                    ]),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->hiddenLabel()
                    ->color('primary'),
                Action::make('delete')
                    ->requiresConfirmation()
                    ->hiddenLabel()
                    ->action(fn (Inscription $record) => $record->delete())
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
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
}
