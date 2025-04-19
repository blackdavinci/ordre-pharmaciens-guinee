<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Ajouter les paramètres dans la base de données
        $this->migrator->add('identification.debut_identifiant', 17);
        $this->migrator->add('identification.dernier_identifiant', null);

    }
};
