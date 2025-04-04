<?php

namespace App\Filament\Pages;

use App\Settings\PaiementSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManagerPaiementSettings extends SettingsPage
{
    protected static ?string $navigationGroup = 'Paramètres';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Frais inscription - réinscription';

    protected static ?string $title = "Frais d'inscription et de réinscription";

    protected static string $settings = PaiementSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // ...
            ]);
    }
}
