<?php

namespace App\Filament\Pages;

use App\Settings\IdentificationSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageIdentificationSettings extends SettingsPage
{
    use HasPageShield;
    protected static ?string $navigationGroup = 'Paramètres';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Numéro RNGPS';

    protected static ?string $title = "Paramètrage numéro RNGPS";

    protected static string $settings = IdentificationSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Paramètres Généraux ')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('debut_identifiant')
                                    ->label("Numéro de début de l'identifiant")
                                    ->numeric()
                                    ->required(),
                                TextInput::make('dernier_identifiant')
                                    ->label("Dernier numéro d'identifiant actuel")
                                    ->numeric()
                                    ->disabled(),
                            ]),

                    ])
            ]);
    }
}
