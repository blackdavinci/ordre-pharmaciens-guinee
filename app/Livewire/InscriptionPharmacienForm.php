<?php

namespace App\Livewire;

use App\Models\Inscription;
use App\Models\Paiement;
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

    public function mount(Inscription $inscription): void
    {
        $this->inscription = $inscription;

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
                    Wizard\Step::make('Informations personnelles')
                        ->description('Identité du pharmacien')
                        ->columns(3)
                        ->schema([
                            Grid::make(12) // This creates a 2-column layout for the next group of fields
                            ->schema([
                                Grid::make()
                                    ->columnSpan(12)
                                    ->schema([
                                        TextInput::make('prenom')->required(),
                                        TextInput::make('nom')->required(),
                                        Select::make('genre')->options(['masculin' => 'Masculin', 'féminin' => 'Féminin'])->required(),
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
                                    ]),

                            ]),

                        ]),

                    Wizard\Step::make('Coordonnées')
                        ->description('Adresse et contact')
                        ->columns(2)
                        ->schema([
                            TextInput::make('email')->email()->required(),
                            PhoneInput::make('telephone_mobile')
                                ->defaultCountry('GN')
                                ->validateFor(
                                    lenient: true, // default: false
                                )
                                ->required(),
                            Country::make('pays_residence')->label('Pays de résidence')->searchable()->required(),
                            TextInput::make('ville_residence')->label('Ville de résidence')->required(),
                            TextInput::make('adresse_personnelle')->columnSpanFull()->required(),
                        ]),

                    Wizard\Step::make("Documents d'état civil")
                        ->description('Vérification de nationalité et moralité')
                        ->columns(2)
                        ->schema([
                            Select::make('citoyen_guineen')
                                ->label('Etes-vous citoyen(ne) guinéen(ne) ?')
                                ->options([
                                    true => 'Oui',
                                    false => 'Non',
                                ])
                                ->live()
                                ->columnSpanFull()
                                ->required(),
                            SpatieMediaLibraryFileUpload::make('certificat_nationalite')
                                ->collection('certificat_nationalite')
                                ->label("Certificat de nationalité")
                                ->columnSpanFull()
                                ->visible(fn ($get) => $get('citoyen_guineen') == true)
                                ->required(),
                            SpatieMediaLibraryFileUpload::make('extrait_naissance')->collection('extrait_naissance')->required(),
                            SpatieMediaLibraryFileUpload::make('casier_judiciaire')->collection('casier_judiciaire')->required(),
                            SpatieMediaLibraryFileUpload::make('attestation_moralite')->collection('attestation_moralite')->required(),
                            SpatieMediaLibraryFileUpload::make('lettre_manuscrite')
                                ->label('Lettre manuscrite au Président')
                                ->collection('lettre_manuscrite')
                                ->required(),
                        ]),

                    Wizard\Step::make('Informations académiques')
                        ->description('Diplôme et parcours')
                        ->columns(2)
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('diplome')
                                ->collection('diplome')
                                ->required(),
                            SpatieMediaLibraryFileUpload::make('certificat_fin_cycle')
                                ->label('Certificat de fin de cycle')
                                ->collection('certificat_fin_cycle')
                                ->required(),
                            Select::make('annee_obtention_diplome')
                                ->options(range((int) date('Y'), 1900, -1))  // Génère les années de l'année actuelle à 1900 de manière décroissante
                                ->required()  // Le champ est requis
                                ->label("Année d'obtention du diplôme"),  // Le label du champ

                            // Diplôme étranger
                            Select::make('diplome_etranger')
                                ->label("Votre diplome a t'il été délivré hors de la Guinée ?")
                                ->options([
                                    true => 'Oui',
                                    false => 'Non',
                                ])
                                ->live()
                                ->required(),

                            // Attestation d'équivalence (visible si diplôme étranger sélectionné)
                            SpatieMediaLibraryFileUpload::make('equivalence_diplome')
                                ->collection('equivalence_diplome')
                                ->label("Veuillez fournir l'attestation d'équivalence")
                                ->columnSpanFull()
                                ->visible(fn (Get $get) => $get('diplome_etranger') == true)
                                ->required(),

                        ]),
                    Wizard\Step::make('Situation professionnelle')
                        ->description('Profil et emploi')
                        ->columns(2)
                        ->schema([
                            Select::make('profil')->options([
                                'pharmacien assistant' => 'Pharmacien assistant',
                                'pharmacien biologiste' => 'Pharmacien biologiste',
                                'pharmacien delegue medical' => 'Délégué médical',
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

                    Wizard\Step::make('Paiement')
                        ->description("Sélection du moyen de paiement")
                        ->schema([
                            Select::make('payment_method')
                                ->label('Méthode de paiement')
                                ->options([
                                    'ORANGE_MONEY' => 'Orange Money',
                                    'MOMO' => 'MTN MoMo',
                                    'CREDIT_CARD' => 'Carte bancaire',
                                    'PAYCARD' => 'Paycard',
                                ])
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

            $inscription = Inscription::create(array_merge($data, [
                'token' => $token,
                'statut' => 'pending',
                'expiration_at' => now()->addHours(48),
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

            return redirect()->route('payment.initiate', ['token' => $token, 'payment_method' => $data['payment_method']]);

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
