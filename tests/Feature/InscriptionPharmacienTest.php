<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InscriptionPharmacienTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        Storage::fake('public');

        Livewire::test(InscriptionPharmacienForm::class)
            ->set('data.prenom', 'Mohamed')
            ->set('data.nom', 'Camara')
            ->set('data.genre', 'masculin')
            ->set('data.date_naissance', '1990-01-01')
            ->set('data.pays_naissance', 'GN')
            ->set('data.lieu_naissance', 'Conakry')
            ->set('data.nationalite', 'GN')
            ->set('data.type_piece_identite', 'cin')
            ->set('data.email', 'test@example.com')
            ->set('data.telephone_mobile', '+224620000000')
            ->set('data.pays_residence', 'GN')
            ->set('data.ville_residence', 'Conakry')
            ->set('data.adresse_personnelle', 'Matoto')
            ->set('data.citoyen_guineen', true)
            ->set('data.extrait_naissance', [UploadedFile::fake()->image('naissance.jpg')])
            ->set('data.casier_judiciaire', [UploadedFile::fake()->create('casier.pdf')])
            ->set('data.attestation_moralite', [UploadedFile::fake()->create('moralite.pdf')])
            ->set('data.lettre_manuscrite', [UploadedFile::fake()->create('lettre.pdf')])
            ->set('data.diplome', [UploadedFile::fake()->create('diplome.pdf')])
            ->set('data.certificat_fin_cycle', [UploadedFile::fake()->create('cycle.pdf')])
            ->set('data.annee_obtention_diplome', '2020')
            ->set('data.diplome_etranger', false)
            ->set('data.profil', 'pharmacien assistant')
            ->set('data.section', 'section a')
            ->set('data.salarie', false)
            ->set('data.payment_method', 'PAYCARD')
            ->call('create')
            ->assertRedirect(); // ou assertStatus(302) selon le comportement

        // Assert que les fichiers ont été stockés
        Storage::disk('public')->assertExists('naissance.jpg');

        $response->assertStatus(200);
    }
}
