<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class DocumentSettings extends Settings
{
    public ?string $nom_president;
    public ?string $mention_fiscale = null;

    public ?string $adresse = null;
    public ?string $signature_president;
    public ?string $attestation_background;

    public ?string $receipt_background;

    public static function group(): string
    {
        return 'document';
    }
}
