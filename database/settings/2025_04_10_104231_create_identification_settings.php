<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Définir la valeur par défaut pour 'code_etablissement'
        $etablissement = [
            [
                'nom' => 'Université Gamal Abdel Nasser de Conakry',
                'code' => 'A'
            ],
            [
                'nom' => 'Université de Sonfonia',
                'code' => 'B'
            ],
            [
                'nom' => "Université de N'Zérékoré",
                'code' => 'C'
            ],
            [
                'nom' => "École de Santé Publique de Guinée (ESPG)",
                'code' => 'D'
            ],
            [
                'nom' => "Institut Supérieur de Technologie de Guinée (ISTG)",
                'code' => 'E'
            ],
            [
                'nom' => "Université Koffi Annan de Guinée (UKAG)",
                'code' => 'F'
            ],
            [
                'nom' => 'Autre',
                'code' => 'Z'
            ]
        ];

        // Ajouter les paramètres dans la base de données
        $this->migrator->add('identification.debut_identifiant', 2000);
        $this->migrator->add('identification.dernier_identifiant', null);
        $this->migrator->add('identification.code_etablissement', $etablissement);  // Utilisation de $etablissement
    }
};
