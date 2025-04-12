<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('document.nom_president', null);
        $this->migrator->add('document.signature_president', null);
        $this->migrator->add('document.attestation_background', null);
    }
};
