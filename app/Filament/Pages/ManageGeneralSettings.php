<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageGeneralSettings extends SettingsPage
{

    protected static ?string $navigationGroup = 'Paramètres';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Général';

    protected static ?string $title = 'Paramètres généraux';

    protected static string $settings = GeneralSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Paramètres Généraux ')
                    ->schema([
                        TextInput::make('nom_app')
                            ->label('Nom de l\'application')
                            ->required(),
                        Textarea::make('about')
                            ->label('A propos')
                            ->required(),
                        TextInput::make('email')
                            ->email()
                            ->required(),
                        TextInput::make('phone')
                            ->label('Contact')
                            ->required(),
                        TextInput::make('adresse')
                            ->required(),
                        TextInput::make('copyright'),
                        FileUpload::make('logo')
                            ->preserveFilenames()
                            ->disk('public')
                            ->visibility('public')
                            ->image(),
                        TextInput::make('logo_size')
                            ->label('Taille Logo'),
                        FileUpload::make('logo_mobile')
                            ->preserveFilenames()
                            ->disk('public')
                            ->visibility('public')
                            ->image(),
                        TextInput::make('logo_mobile_size')
                            ->label('Taille logo mobile'),
                        FileUpload::make('logo_sidebar')
                            ->preserveFilenames()
                            ->disk('public')
                            ->visibility('public')
                            ->image(),
                        TextInput::make('logo_sidebar_size')
                            ->label('Taille logo sidebar'),
                        FileUpload::make('logo_footer')
                            ->preserveFilenames()
                            ->disk('public')
                            ->visibility('public')
                            ->image(),
                        TextInput::make('logo_footer_size')
                            ->label('Taille logo footer'),
                        FileUpload::make('favicon')
                            ->preserveFilenames()
                            ->disk('public')
                            ->visibility('public')
                            ->image(),
                    ])

            ]);
    }
}
