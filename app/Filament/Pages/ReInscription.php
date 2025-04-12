<?php

namespace App\Filament\Pages;

use App\Models\Inscription;
use App\Settings\IdentificationSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class ReInscription extends Page
{

    use InteractsWithForms, HasPageShield;

    public ?array $data = [];
    protected static ?string $navigationLabel = 'Réinscription';

    protected static ?string $title = "Réinscription";
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.re-inscription';

    public $record;

    public ?Inscription $inscription = null; // Ajoutez cette ligne

    public function mount()
    {
        // Vérifier si l'utilisateur est connecté
        if (!auth()->check()) {
            abort(403, 'Vous devez être connecté pour accéder à cette page.');
        }

        // Récupérer l'inscription de l'utilisateur connecté
        $this->inscription = Inscription::where('user_id', auth()->id())->first();
        $this->record = $this->inscription; // Assurez-vous que $record pointe vers le même objet

        // Vérifier si l'inscription existe
        if (!$this->inscription) {
            abort(404, 'Inscription non trouvée.');
        }

        // Remplir le tableau $data avec les données du modèle
        $this->form->fill($this->inscription->toArray());

    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Identité personnelle & Coordonnées')
                        ->columns(3)
                        ->schema([
                            Grid::make(12) // This creates a 2-column layout for the next group of fields
                            ->schema([
                                Grid::make()
                                    ->columnSpan(3)
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('photo_identite')
                                            ->collection('photo_identite')
                                            ->label("Photo d'identité")
                                            ->avatar()
                                            ->responsiveImages()
                                            ->image()
                                            ->required(),
                                    ]),
                                Grid::make()
                                    ->columnSpan(9)
                                    ->schema([
                                        TextInput::make('prenom')->required(),
                                        TextInput::make('nom')->required(),
                                        Select::make('genre')->options(['homme' => 'Homme', 'femme' => 'Femme'])->required(),
                                        DatePicker::make('date_naissance')->required(),
                                        Country::make('pays_naissance')->searchable()->required(),
                                        TextInput::make('lieu_naissance')->required(),
                                        Country::make('nationalite')->searchable()->required(),
                                        Select::make('type_piece_identite')
                                            ->label('Type de pièce d\'identité')
                                            ->options([
                                                'cin' => 'Carte d\'identité nationale',
                                                'passeport' => 'Passeport',
                                            ])
                                            ->reactive()
                                            ->required(),

                                        SpatieMediaLibraryFileUpload::make('cin_recto')
                                            ->collection('cin_recto')
                                            ->label('CIN - Recto')
                                            ->visible(fn ($get) => $get('type_piece_identite') === 'cin')
                                            ->required(fn ($get) => $get('type_piece_identite') === 'cin'),

                                        SpatieMediaLibraryFileUpload::make('cin_verso')
                                            ->collection('cin_verso')
                                            ->label('CIN - Verso')
                                            ->visible(fn ($get) => $get('type_piece_identite') === 'cin')
                                            ->required(fn ($get) => $get('type_piece_identite') === 'cin'),

                                        SpatieMediaLibraryFileUpload::make('passeport_page_infos')
                                            ->collection('passeport_page_infos')
                                            ->label('Passeport - Page d\'information')
                                            ->visible(fn ($get) => $get('type_piece_identite') === 'passeport')
                                            ->required(fn ($get) => $get('type_piece_identite') === 'passeport'),

                                        SpatieMediaLibraryFileUpload::make('passeport_premiere_page')
                                            ->collection('passeport_premiere_page')
                                            ->label('Passeport - Première page')
                                            ->visible(fn ($get) => $get('type_piece_identite') === 'passeport')
                                            ->required(fn ($get) => $get('type_piece_identite') === 'passeport'),

                                        TextInput::make('email')->email()->required(),
                                        PhoneInput::make('telephone_mobile')
                                            ->defaultCountry('GN')
                                            ->validateFor(
                                                lenient: true, // default: false
                                            )
                                            ->required(),
                                        Country::make('pays_residence')->label('Pays de résidence')->searchable()->required(),
                                        TextInput::make('ville_residence')->label('Ville de résidence')->required(),
                                        TextInput::make('adresse_residence')->columnSpanFull()->required(),
                                    ]),

                            ]),

                        ]),


                    Wizard\Step::make("Documents d'état civil & académiques")
                        ->columns(2)
                        ->schema([
                            Select::make('citoyen_guineen')
                                ->label('Etes-vous citoyen(ne) guinéen(ne) ?')
                                ->options([
                                    true => 'Oui',
                                    false => 'Non',
                                ])
                                ->live()
                                ->required(),
                            // Diplôme étranger
                            Select::make('diplome_etranger')
                                ->label("Votre diplome a t'il été délivré hors de la Guinée ?")
                                ->options([
                                    true => 'Oui',
                                    false => 'Non',
                                ])
                                ->live()
                                ->required(),
                            Select::make('annee_obtention_diplome')
                                ->options(range((int) date('Y'), 1900, -1))  // Génère les années de l'année actuelle à 1900 de manière décroissante
                                ->required()  // Le champ est requis
                                ->label("Année d'obtention du diplôme"),  // Le label du champ
                            Select::make('code_etablissement')
                                ->options(collect(app(IdentificationSettings::class)->code_etablissement)->pluck('nom', 'code')->toArray())  // Génère les années de l'année actuelle à 1900 de manière décroissante
                                ->required()  // Le champ est requis
                                ->label("Etablissement d'obtention du diplôme"),  // Le label du champ
                            SpatieMediaLibraryFileUpload::make('extrait_naissance')
                                ->label('Extrait de naissance')
                                ->collection('extrait_naissance')
                                ->required(),

                            SpatieMediaLibraryFileUpload::make('certificat_nationalite')
                                ->collection('certificat_nationalite')
                                ->label("Certificat de nationalité")
                                ->visible(fn ($get) => $get('citoyen_guineen') == true)
                                ->required(),

                            SpatieMediaLibraryFileUpload::make('casier_judiciaire')
                                ->collection('casier_judiciaire')
                                ->required(),
                            SpatieMediaLibraryFileUpload::make('attestation_moralite')
                                ->collection('attestation_moralite')
                                ->required(),
                            SpatieMediaLibraryFileUpload::make('lettre_manuscrite')
                                ->label('Lettre manuscrite au Président')
                                ->collection('lettre_manuscrite')
                                ->required(),

                            SpatieMediaLibraryFileUpload::make('diplome')
                                ->collection('diplome')
                                ->required(),
                            SpatieMediaLibraryFileUpload::make('certificat_fin_cycle')
                                ->label('Certificat de fin de cycle')
                                ->collection('certificat_fin_cycle')
                                ->required(),

                            // Attestation d'équivalence (visible si diplôme étranger sélectionné)
                            SpatieMediaLibraryFileUpload::make('equivalence_diplome')
                                ->collection('equivalence_diplome')
                                ->label("Veuillez fournir l'attestation d'équivalence")
                                ->visible(fn (Get $get) => $get('diplome_etranger') == true)
                                ->required(),

                        ]),


                    Wizard\Step::make('Situation professionnelle')
                        ->columns(2)
                        ->schema([
                            Select::make('profil')->options([
                                'pharmacien assistant' => 'Pharmacien assistant',
                                'pharmacien biologiste' => 'Pharmacien biologiste',
                                'pharmacien délégué médical' => 'Pharmacien délégué médical',
                                'pharmacien grossiste' => 'Pharmacien grossiste',
                                'pharmacien inspecteur de santé' => 'Pharmacien inspecteur de santé',
                                'pharmacien d\'industrie' => 'Pharmacien d\'industrie',
                                'pharmacien sans affectation' => 'Pharmacien sans affectation',
                                'pharmacien titulaire d\'officine' => 'Pharmacien titulaire d\'officine',
                            ])->required(),
                            Select::make('section')
                                ->options(['section a' => 'Section A', 'section b' => 'Section B'])
                                ->required(),
                            Select::make('salarie')
                                ->label('Etes-vous salarié ?')
                                ->options([
                                    true => 'Oui',
                                    false => 'Non',
                                ])
                                ->live()
                                ->columnSpanFull()
                                ->required(),

                            SpatieMediaLibraryFileUpload::make('attestation_emploi')
                                ->collection('attestation_emploi')
                                ->label("Attestation d'emploi")
                                ->columnSpanFull()
                                ->visible(fn ($get) => $get('salarie') == true)
                                ->required(),
                        ]),


                ])->persistStepInQueryString('wizard-step')
            ])
            ->statePath('data')
            ->model($this->inscription);
    }


    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->requiresConfirmation()
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            // Get the form data
            $data = $this->form->getState();

            // Check if a Setting record exists
            $inscription = Inscription::first();

            if (!$inscription) {
                // Send a notification if no record exists
                Inscription::create($data);
            }else{

                // Save the data (create or update)
                $inscription->update($data);
                $inscription->save();

                // Send a success notification after saving
                Notification::make()
                    ->success()
                    ->title('Paramètres sauvegardés')
                    ->body('Les paramètres ont été enregistrés avec succès.')
                    ->send();
            }
        } catch (\Exception $e) {
            // Handle errors and send an error notification
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('There was an error saving the settings: ' . $e->getMessage())
                ->send();
        }

    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Customize the save behavior if needed
        return $data;
    }
}
