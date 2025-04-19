<?php

namespace App\Livewire;

use App\Models\Inscription;
use App\Models\Paiement;
use App\Settings\FormulaireSettings;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class InscriptionPharmacienForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount($token = null): void
    {
        // Cas 1: Token fourni - charger l'inscription existante
        if ($token) {
            $existingInscription = Inscription::where('inscription_token', $token)->first();

            if ($existingInscription) {
                $this->inscription = $existingInscription;
                $this->form->fill($existingInscription->toArray());

                session()->put('id',$existingInscription->id);

                Notification::make()
                    ->title('Inscription récupérée')
                    ->body('Nous avons récupéré votre inscription. Vous pouvez continuer votre processus d\'inscription.')
                    ->info()
                    ->send();

                return;
            }
        }

        // Cas 2: Email en session temporaire - initialiser avec cet email
        if (session()->has('temp_email')) {
            $this->inscription = new Inscription();
            $this->inscription->email = session()->pull('temp_email'); // Récupère et supprime
            $this->form->fill(['email' => $this->inscription->email]);
            return;
        }

        // Cas 3: Email en session flash (from "with")
        if (session()->has('email')) {
            $this->inscription = new Inscription();
            $this->inscription->email = session('email');
            $this->form->fill(['email' => $this->inscription->email]);
            return;
        }

        // Cas 4: Nouvelle inscription vide
        $this->inscription = new Inscription();

        // Chargement des données sauvegardées en session si disponibles
        if ($savedData = session()->get('saved_data')) {
            $this->form->fill($savedData);
        } else {
            $formData = $this->inscription->toArray();
            $this->form->fill($formData);
        }

    }


    public Inscription $inscription;

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
                                            ->maxSize(2048)
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Dimensions max : (35 mm x 45 mm, 413 px x 531 px)')
                                            ->avatar()
                                            ->responsiveImages()
                                            ->image()
                                            ->required()
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
                                            ->maxSize(2048)
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Taille max : 2 Mo')
                                            ->visible(fn ($get) => $get('type_piece_identite') === 'cin')
                                            ->required(fn ($get) => $get('type_piece_identite') === 'cin'),

                                        SpatieMediaLibraryFileUpload::make('cin_verso')
                                            ->collection('cin_verso')
                                            ->label('CIN - Verso')
                                            ->maxSize(2048)
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Taille max : 2 Mo')
                                            ->visible(fn ($get) => $get('type_piece_identite') === 'cin')
                                            ->required(fn ($get) => $get('type_piece_identite') === 'cin'),

                                        SpatieMediaLibraryFileUpload::make('passeport_page_infos')
                                            ->collection('passeport_page_infos')
                                            ->label('Passeport - Page d\'information')
                                            ->maxSize(2048)
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Taille max : 2 Mo')
                                            ->visible(fn ($get) => $get('type_piece_identite') === 'passeport')
                                            ->required(fn ($get) => $get('type_piece_identite') === 'passeport'),

                                        SpatieMediaLibraryFileUpload::make('passeport_premiere_page')
                                            ->collection('passeport_premiere_page')
                                            ->label('Passeport - Première page')
                                            ->maxSize(2048)
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Taille max : 2 Mo')
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
                            DatePicker::make('date_obtention_diplome')
                                ->label("Date d'obtention du diplôme")
                                ->required(),
                            Select::make('etablissement_etude')
                                ->options(collect(app(FormulaireSettings::class)->liste_etablissement)->pluck('nom','nom')->toArray())  // Génère les années de l'année actuelle à 1900 de manière décroissante
                                ->required()  // Le champ est requis
                                ->label("Etablissement d'obtention du diplôme"),  // Le label du champ
                            SpatieMediaLibraryFileUpload::make('extrait_naissance')
                                ->label('Extrait de naissance')
                                ->maxSize(2048)
                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Taille max : 2 Mo')
                                ->collection('extrait_naissance')
                                ->required(),

                            SpatieMediaLibraryFileUpload::make('certificat_nationalite')
                                ->collection('certificat_nationalite')
                                ->label("Certificat de nationalité")
                                ->maxSize(2048)
                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Taille max : 2 Mo')
                                ->visible(fn ($get) => $get('citoyen_guineen') == true)
                                ->required(),

                            SpatieMediaLibraryFileUpload::make('casier_judiciaire')
                                ->collection('casier_judiciaire')
                                ->maxSize(2048)
                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Taille max : 2 Mo')
                                ->required(),
                            SpatieMediaLibraryFileUpload::make('attestation_moralite')
                                ->collection('attestation_moralite')
                                ->maxSize(2048)
                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Taille max : 2 Mo')
                                ->required(),
                            SpatieMediaLibraryFileUpload::make('lettre_manuscrite')
                                ->label('Lettre manuscrite au Président')
                                ->maxSize(2048)
                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Taille max : 2 Mo')
                                ->collection('lettre_manuscrite')
                                ->required(),

                            SpatieMediaLibraryFileUpload::make('diplome')
                                ->maxSize(2048)
                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Taille max : 2 Mo')
                                ->collection('diplome')
                                ->required(),
                            SpatieMediaLibraryFileUpload::make('certificat_fin_cycle')
                                ->label('Certificat de fin de cycle')
                                ->maxSize(2048)
                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Taille max : 2 Mo')
                                ->collection('certificat_fin_cycle')
                                ->required(),

                            // Attestation d'équivalence (visible si diplôme étranger sélectionné)
                            SpatieMediaLibraryFileUpload::make('equivalence_diplome')
                                ->collection('equivalence_diplome')
                                ->label("Veuillez fournir l'attestation d'équivalence")
                                ->maxSize(2048)
                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Taille max : 2 Mo')
                                ->visible(fn (Get $get) => $get('diplome_etranger') == true)
                                ->required(),

                        ]),


                    Wizard\Step::make('Situation professionnelle')
                        ->columns(2)
                        ->schema([
                            Select::make('profil')
                                ->options(collect(app(FormulaireSettings::class)->liste_profil_professionnel)->pluck('nom','nom')->toArray())
                                ->required(),
                            Select::make('section')
                                ->options(collect(app(FormulaireSettings::class)->liste_section)->pluck('nom','nom')->toArray())
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
                                ->maxSize(2048)
                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Taille max : 2 Mo')
                                ->columnSpanFull()
                                ->visible(fn ($get) => $get('salarie') == true)
                                ->required(),
                        ]),


                ])->persistStepInQueryString('wizard-step')
            ])
            ->statePath('data')
            ->model($this->inscription);
    }

    public function saveLocally(): void
    {
        // Get raw form data without validation
    $rawData = $this->form->getRawState();

    // Prepare data for session storage
    $sessionData = [];

    foreach ($rawData as $key => $value) {
        // Skip file upload objects
        if ($value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            continue;
        }

        // Handle collections of files
        if (is_array($value)) {
            $sessionData[$key] = array_filter($value, function ($item) {
                return !($item instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile);
            });
        } else {
            $sessionData[$key] = $value;
        }
    }

    // Save to session
    session()->put('saved_data', $sessionData);

        // Afficher une notification avec Filament
        Notification::make()
            ->title('Données enregistrées')
            ->body('Les informations ont été enregistrées localement.')
            ->success() // Vous pouvez aussi utiliser ->warning() ou ->error() selon le contexte
            ->send();

        // Facultatif : Afficher un message de succès via la session
        session()->flash('message', 'Données enregistrées localement');
    }

    public function create()
    {
        $data = $this->form->getState();
        $rawData = $this->form->getRawState();
        $token = Str::uuid();

        DB::beginTransaction();

        try {

            if(session()->has('id')){

                $inscription = Inscription::findOrFail(session('id'));

                // Pour les champs uniques, on vérifie s'ils ont changé avant de mettre à jour
                $uniqueFields = ['email', 'telephone_mobile']; // Ajoutez ici tous vos champs uniques

                // Préparation des données à mettre à jour
                $updateData = $data;

                // Pour chaque champ unique, vérifier s'il a changé
                foreach ($uniqueFields as $field) {
                    if (isset($data[$field]) && $data[$field] == $inscription->$field) {
                        // Si le champ n'a pas changé, on le retire des données à mettre à jour
                        // pour éviter les erreurs de contrainte d'unicité
                        unset($updateData[$field]);
                    }
                }

                // Mettre à jour l'inscription avec les données modifiées
                $inscription->update($updateData);

                // Traitement des fichiers multimédias
                foreach ($rawData as $field => $files) {
                    if (is_array($files)) {
                        foreach ($files as $file) {
                            if ($file instanceof TemporaryUploadedFile) {
                                // Supprimer les anciens médias de cette collection si nécessaire
                                // (optionnel, selon votre logique métier)
                                 $inscription->clearMediaCollection($field);

                                // Ajouter le nouveau média
                                $inscription->addMedia($file->getRealPath())
                                    ->toMediaCollection($field);
                            }
                        }
                    }
                }


                DB::commit();

                session()->forget('saved_data');

                return redirect()->route('payment.initiate', [
                    'token' => $inscription->inscription_token,
                ]);

            }else{
                $inscription = Inscription::create(array_merge($data, [
                    'inscription_token' => $token,
                    'numero_inscription' => $this->generateInscriptionNumber(),
                    'statut' => 'pending',
                    'inscription_draft_expiration_at' => now()->addHours(48),
                ]));

                foreach ($rawData as $field => $files) {
                    if (is_array($files)) {
                        foreach ($files as $file) {
                            if ($file instanceof TemporaryUploadedFile) {
                                $inscription->addMedia($file->getRealPath())
                                    ->toMediaCollection($field);
                            }
                        }
                    }
                }

                DB::commit();

                session()->forget('saved_data');

                return redirect()->route('payment.initiate', [
                    'token' => $token,
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Erreur')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        return null;
    }

    public function render()
    {
        return view('livewire.inscription-pharmacien');
    }
}
