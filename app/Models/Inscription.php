<?php

namespace App\Models;

use App\Settings\IdentificationSettings;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Inscription extends Model implements HasMedia
{
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'numero_inscription',
        'numero_rngps',
        'numero_medecin',
        'prenom',
        'nom',
        'genre',
        'date_naissance',
        'lieu_naissance',
        'pays_naissance',
        'nationalite',
        'citoyen_guineen',
        'pays_residence',
        'ville_residence',
        'adresse_residence',
        'telephone_mobile',
        'email',
        'type_piece_identite',
        'profil',
        'section',
        'annee_obtention_diplome',
        'numero_diplome',
        'code_etablissement',
        'diplome_etranger',
        'equivalence_diplome',
        'salarie',
        'statut',
        'motif_rejet',
        'frais_paiement',
        'date_validation',
        'user_id',
        'statut',
        'inscription_token',
        'expiration_at'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation: Une inscription peut avoir plusieurs paiements
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    // Ajoutez une méthode pour les réinscriptions
    public function paiementsReinscription()
    {
        return $this->hasMany(Paiement::class)->where('type', 'reinscription');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo_identite');
        $this->addMediaCollection('extrait_naissance');
        $this->addMediaCollection('piece_identite'); // plusieurs fichiers (recto/verso ou pages passeport)
        $this->addMediaCollection('casier_judiciaire');
        $this->addMediaCollection('certificat_fin_cycle');
        $this->addMediaCollection('diplome');
        $this->addMediaCollection('attestation_moralite');
        $this->addMediaCollection('attestation_emploi');
        $this->addMediaCollection('lettre_manuscrite');
        $this->addMediaCollection('fiche_inscription');
        $this->addMediaCollection('equivalence_diplome');
        $this->addMediaCollection('attestations');
    }

    // Méthode pour générer automatiquement le numéro rngps
    public function generateRngps()
    {
        $identification = app(IdentificationSettings::class); // Récupérer les paramètres

        // Utiliser numero_debut pour le premier pharmacien
        if (is_null($identification->dernier_identifiant)) {
            // Si c'est le premier pharmacien, on utilise numero_debut
            $id = $identification->debut_identifiant;
        } else {
            // Sinon, on incrémente le dernier numéro généré
            $id = $identification->dernier_identifiant + 1;
        }

        // S'assurer que code_etablissement existe
        $code_etablissement = $this->code_etablissement ?? 'DEFAULT';
        $annee = date('Y');

        // Mettre à jour le dernier numéro généré dans les paramètres
        $identification->dernier_identifiant = $id;
        $identification->save();

        return "{$id}/{$code_etablissement}/{$annee}";
    }


}
