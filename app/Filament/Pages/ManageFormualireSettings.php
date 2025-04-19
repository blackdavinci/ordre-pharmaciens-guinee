<?php

namespace App\Filament\Pages;

use App\Settings\FormulaireSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageFormualireSettings extends SettingsPage
{
    use HasPageShield;
    protected static ?string $navigationGroup = 'Paramètres';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = "Formulaire d'inscription";

    protected static ?string $title = 'Paramètres formulaire';

    protected static string $settings = FormulaireSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Liste établissements ')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Repeater::make('liste_etablissement')
                                    ->label('Liste des établissement')
                                    ->schema([
                                        TextInput::make('nom')->label("Nom de l'établissement")->required(),
                                    ])
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ]),
                Section::make('Liste profil professionnel ')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Repeater::make('liste_profil_professionnel')
                                    ->label('Liste des profils')
                                    ->schema([
                                        TextInput::make('nom')->label("Nom du profil")->required(),
                                    ])
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ])->collapsed(),
                Section::make('Liste section professionnelle ')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Repeater::make('liste_section')
                                    ->label('Liste des sections')
                                    ->schema([
                                        TextInput::make('nom')->label("Nom section")->required(),
                                    ])
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ])->collapsed()
            ]);
    }
}
