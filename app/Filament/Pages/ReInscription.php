<?php

namespace App\Filament\Pages;

use App\Models\Inscription;
use App\Settings\FormulaireSettings;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use Snowfire\Beautymail\Beautymail;
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

    public static function canAccess(): bool
    {
        $user = auth()->user();

        // Vérifie le rôle
        if (!$user->hasRole('membre')) {
            return false;
        }

        // Vérifie la présence des dates nécessaires
        if (!$user->inscription->valid_until) {
            return false;
        }

        // Vérifie la période de réinscription (2 mois avant valid_until)
        $reinscriptionStart = $user->inscription->valid_until->subMonths(2);
        $isInReinscriptionPeriod = now()->between(
            $reinscriptionStart,
            $user->inscription->valid_until
        );

        // Vérifie que l'utilisateur ne s'est pas déjà réinscrit cette année
        $hasNotReinscribedThisYear = $user->reinscription == null;

        return $isInReinscriptionPeriod && $hasNotReinscribedThisYear;


    }

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

    public function save()
    {
        try {
            // Récupérer les données du formulaire
            $data = $this->form->getState();
            $rawData = $this->form->getRawState();
            $token = Str::uuid();
            $data['inscription_token'] = $token;

            DB::beginTransaction();

            // Vérifier si le record existe déjà (ce qui est le cas pour une réinscription)
            if ($this->inscription) {
                // Pour les champs uniques, on vérifie s'ils ont changé avant de mettre à jour
                $uniqueFields = ['email', 'telephone_mobile']; // Ajoutez ici tous vos champs uniques

                // Préparation des données à mettre à jour
                $updateData = $data;

                // Pour chaque champ unique, vérifier s'il a changé
                foreach ($uniqueFields as $field) {
                    if (isset($data[$field]) && $data[$field] == $this->inscription->$field) {
                        // Si le champ n'a pas changé, on le retire des données à mettre à jour
                        // pour éviter les erreurs de contrainte d'unicité
                        unset($updateData[$field]);
                    }
                }

                // Mettre à jour l'inscription avec les données modifiées
                $this->inscription->update($updateData);

                // Traitement des fichiers multimédias
                foreach ($rawData as $field => $files) {
                    if (is_array($files)) {
                        foreach ($files as $file) {
                            if ($file instanceof TemporaryUploadedFile) {
                                // Supprimer les anciens médias de cette collection si nécessaire
                                // (optionnel, selon votre logique métier)
                                // $this->inscription->clearMediaCollection($field);

                                // Ajouter le nouveau média
                                $this->inscription->addMedia($file->getRealPath())
                                    ->toMediaCollection($field);
                            }
                        }
                    }
                }

                DB::commit();


                $reinscription = \App\Models\Reinscription::create([
                    'user_id' => auth()->user()->id,
                    'inscription_id' => $this->inscription->id,
                    'date_reinscription' => now(),
                    'statut' => 'pending',
                ]);

                return redirect()->route('payment.initiate', [
                    'token' => $token,
                ]);


                // 6. Envoyer l'email avec Beautymail
                $beautymail = app()->make(BeautyMail::class);
                $beautymail->send('emails.reinscription-submit', [
                    'inscription' => $inscription,
                    'date' => now()->format('d/m/Y'),
                ], function ($message) use ($inscription, $media) {
                    $message
                        ->from('ousmaneciss1@gmail.com')
                        ->to($inscription->email, $inscription->prenom . ' ' . $inscription->nom)
                        ->subject('Votre réinscription a été approuvée - ONPG')
                        // Attach the file using the file path from Spatie Media Library
                        ->attach($media->getPath(), [
                            'as' => 'attestation.pdf',
                            'mime' => 'application/pdf',
                        ]);
                });

                // Notification de succès
                Notification::make()
                    ->success()
                    ->title('Réinscription réussie')
                    ->body('Votre réinscription a été enregistrée avec succès.')
                    ->send();

                // Si vous souhaitez rediriger vers une page de paiement comme dans votre exemple
                // Décommentez ces lignes et ajustez selon vos besoins
                // return redirect()->route('payment.initiate', [
                //     'token' => $this->inscription->inscription_token,
                // ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->danger()
                ->title('Erreur')
                ->body('Une erreur est survenue lors de la réinscription: ' . $e->getMessage())
                ->send();
        }

    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Customize the save behavior if needed
        return $data;
    }
}
