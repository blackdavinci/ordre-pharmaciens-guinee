<?php

namespace App\Models;

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


}
