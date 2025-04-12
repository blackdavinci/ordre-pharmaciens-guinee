<?php

namespace App\Settings;


use Spatie\LaravelSettings\Settings;

class IdentificationSettings extends Settings
{
    public ?int $debut_identifiant;
    public ?array $code_etablissement;
    public ?int $dernier_identifiant;

    public static function group(): string
    {
        return 'identification';
    }
}
