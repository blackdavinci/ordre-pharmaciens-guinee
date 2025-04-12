<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {

        $this->migrator->add('paiement.inscription_frais_citoyen', 10000);
        $this->migrator->add('paiement.inscription_frais_resident', 10000);
        $this->migrator->add('paiement.inscription_frais_citoyen_diplome_etranger', 10000);
        $this->migrator->add('paiement.inscription_frais_resident_diplome_etranger', 10000);

        $this->migrator->add('paiement.reinscription_frais_citoyen', 10000);
        $this->migrator->add('paiement.reinscription_frais_resident', 10000);
        $this->migrator->add('paiement.reinscription_frais_citoyen_diplome_etranger', 10000);
        $this->migrator->add('paiement.reinscription_frais_resident_diplome_etranger', 10000);

    }
};
