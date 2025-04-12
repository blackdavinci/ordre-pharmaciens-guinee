<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageGeneralSettings extends SettingsPage
{
    use HasPageShield;
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
                        Grid::make(2)
                            ->schema([
                                TextInput::make('site_name')
                                    ->label('Nom de l\'application')
                                    ->columnSpanFull()
                                    ->required(),
                                Textarea::make('about')
                                    ->label('A propos')
                                    ->columnSpanFull(),
                                TextInput::make('support_email')
                                    ->label('E-mail')
                                    ->email()
                                    ->required(),
                                TextInput::make('support_phone')
                                    ->label('Téléphone')
                                    ->required(),
                                FileUpload::make('logo')
                                    ->preserveFilenames()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->image(),
                                FileUpload::make('logo_alt')
                                    ->label('Logo alternatif')
                                    ->preserveFilenames()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->image(),
                                TextInput::make('logo_size')
                                    ->label('Taille logo'),
                                TextInput::make('logo_alt_size')
                                    ->label('Taille Logo alternatif'),
                                FileUpload::make('logo_mobile')
                                    ->preserveFilenames()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->image(),
                                FileUpload::make('favicon')
                                    ->preserveFilenames()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->image(),
                                TextInput::make('logo_mobile_size')
                                    ->label('Taille logo mobile'),

                            ]),

                    ])
            ]);
    }
}
