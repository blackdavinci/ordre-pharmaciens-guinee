<?php

namespace App\Filament\Pages;

use App\Settings\DocumentSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManagerDocumentSettings extends SettingsPage
{
    use HasPageShield;
    protected static ?string $navigationGroup = 'Paramètres';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Document';

    protected static ?string $title = "Paramètres documents";

    protected static string $settings = DocumentSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Paramètres Documents ')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nom_president')
                                    ->label('Nom du président')
                                    ->columnSpanFull()
                                    ->required(),
                                FileUpload::make('signature_president')
                                    ->preserveFilenames()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->image(),
                                FileUpload::make('attestation_background')
                                    ->label('Image de fond attesation')
                                    ->preserveFilenames()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->image(),
                            ]),

                    ])
            ]);
    }
}
