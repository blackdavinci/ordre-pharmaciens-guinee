<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class FormulaireSettings extends Settings
{
    public ?array $liste_etablissement;
    public ?array $liste_profil_professionnel;
    public ?array $liste_section;

    public static function group(): string
    {
        return 'formulaire';
    }
}
