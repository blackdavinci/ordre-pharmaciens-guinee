<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
// Définir la valeur par défaut pour 'code_etablissement'
        $etablissement = [
            ['nom' => "Université Gamal Abdel Nasser de Conakry"],
            ['nom' => "Université de Sonfonia"],
            ['nom' => "Université de N'Zérékoré"],
            ['nom' => "École de Santé Publique de Guinée (ESPG)"],
            ['nom' => "Institut Supérieur de Technologie de Guinée (ISTG)"],
            ['nom' => "Université Koffi Annan de Guinée (UKAG)"],
            ['nom' => "Autre"],
        ];

        $profil = [
            ['nom' => 'Pharmacien assistant'],
            ['nom' => 'Pharmacien biologiste'],
            ['nom' => 'Pharmacien délégué médical'],
            ['nom' => 'Pharmacien grossiste'],
            ['nom' => 'Pharmacien inspecteur de santé'],
            ['nom' => 'Pharmacien d\'industrie'],
            ['nom' => 'Pharmacien sans affectation'],
            ['nom' => 'Pharmacien titulaire d\'officine'],
        ];

        $section = [
            ['nom' => 'Section A'],
            ['nom' => 'Section B'],
        ];



        $this->migrator->add('formulaire.liste_section', $section);
        $this->migrator->add('formulaire.liste_profil_professionnel', $profil);
        $this->migrator->add('formulaire.liste_etablissement', $etablissement);  // Utilisation de $etablissement
    }
};
