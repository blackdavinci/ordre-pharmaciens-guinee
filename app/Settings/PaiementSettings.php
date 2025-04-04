<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PaiementSettings extends Settings
{
    public int $inscription_frais_citoyen;
    public int $inscription_frais_resident;
    public int $inscription_frais_citoyen_diplome_etranger;
    public int $inscription_frais_resident_diplome_etranger;

    public int $reinscription_frais_citoyen;
    public int $reinscription_frais_resident;
    public int $reinscription_frais_citoyen_diplome_etranger;
    public int $reinscription_frais_resident_diplome_etranger;

    public static function group(): string
    {
        return 'paiement';
    }
}
