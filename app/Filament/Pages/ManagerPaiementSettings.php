<?php

namespace App\Filament\Pages;

use App\Settings\PaiementSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManagerPaiementSettings extends SettingsPage
{
    use HasPageShield;
    protected static ?string $navigationGroup = 'Paramètres';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Frais Inscription - Réinscription';

    protected static ?string $title = "Frais d'inscription et de réinscription";

    protected static string $settings = PaiementSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make("Inscription")
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('inscription_frais_citoyen')
                                    ->label("Citoyen")
                                    ->numeric()
                                    ->required()
                                    ->minValue(1),  // Montant entier

                                TextInput::make('inscription_frais_resident')
                                    ->label("Résident")
                                    ->numeric()
                                    ->required()
                                    ->minValue(1),  // Montant entier

                                TextInput::make('inscription_frais_citoyen_diplome_etranger')
                                    ->label("Citoyen avec diplôme étranger")
                                    ->numeric()
                                    ->required()
                                    ->minValue(1), // Montant entier

                                TextInput::make('inscription_frais_resident_diplome_etranger')
                                    ->label("Résident avec diplôme étranger")
                                    ->numeric()
                                    ->required()
                                    ->minValue(1), // Montant entier
                            ]),
                    ]),
                Section::make("Réinscription")
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('reinscription_frais_citoyen')
                                    ->label("Citoyen")
                                    ->numeric()
                                    ->required()
                                    ->minValue(1),  // Montant entier

                                TextInput::make('reinscription_frais_resident')
                                    ->label("Résident")
                                    ->numeric()
                                    ->required()
                                    ->minValue(1),  // Montant entier

                                TextInput::make('reinscription_frais_citoyen_diplome_etranger')
                                    ->label("Citoyen avec diplôme étranger")
                                    ->numeric()
                                    ->required()
                                    ->minValue(1), // Montant entier

                                TextInput::make('reinscription_frais_resident_diplome_etranger')
                                    ->label("Résident avec diplôme étranger")
                                    ->numeric()
                                    ->required()
                                    ->minValue(1), // Montant entier
                            ]),


                    ])
            ]);
    }
}
