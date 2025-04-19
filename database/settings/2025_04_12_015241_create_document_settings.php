<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('document.nom_president', null);
        $this->migrator->add('document.signature_president', null);
        $this->migrator->add('document.attestation_background', null);
        $this->migrator->add('document.receipt_background', null);
        $this->migrator->add('document.adresse', 'Kaloum, Conakry, Rép. de Guinée');
        $this->migrator->add('document.mention_fiscale', 'Ordre National des Pharmaciens de Guinée - RCCM 122222 - NIF 445533');
    }
};
