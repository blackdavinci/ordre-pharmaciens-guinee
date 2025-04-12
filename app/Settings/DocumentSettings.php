<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class DocumentSettings extends Settings
{
    public ?string $nom_president;
    public ?string $signature_president;
    public ?string $attestation_background;

    public static function group(): string
    {
        return 'document';
    }
}
