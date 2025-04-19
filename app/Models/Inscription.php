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
    protected $dates = ['valid_from', 'valid_until'];

    protected $casts = [
        'valid_until' => 'datetime',
        'valid_from' => 'datetime'// This will automatically convert the 'valid_until' field to a Carbon instance
    ];
    protected $fillable = [
        'numero_inscription',
        'numero_rngps',
        'numero_ordre',
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
        'date_obtention_diplome',
        'numero_diplome',
        'etablissement_etude',
        'diplome_etranger',
        'equivalence_diplome',
        'salarie',
        'statut',
        'motif_rejet',
        'valid_from',
        'valid_until',
        'user_id',
        'statut',
        'inscription_token',
        'expiration_at'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reinscriptions()
    {
        return $this->hasMany(Reinscription::class);
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
        $this->addMediaCollection('receipt');
    }

    // Méthode pour générer automatiquement le numéro rngps

    public function generateRngpsNumber()
    {
        $identification = app(IdentificationSettings::class); // Récupérer les paramètres

        // Utiliser numero_debut pour le premier pharmacien
        if (is_null($identification->dernier_identifiant)) {
            // Si c'est le premier pharmacien, on utilise numero_debut
            $id = $identification->debut_identifiant;
            $compteur = 1; // Commence à 00001
        } else {
            // Sinon, on incrémente le dernier numéro généré
            $id = $identification->dernier_identifiant;
            $compteur = ($id % 100000) + 1; // On récupère les 5 derniers chiffres pour incrémenter le compteur

            if ($compteur > 99999) {
                // Si le compteur atteint 99999, on incrémente l'année et recommence le compteur
                $id = ($id + 100000) - ($id % 100000); // On passe au chiffre suivant
                $compteur = 1; // On recommence à 00001
            }
        }

        $annee = date('Y');

        // Mettre à jour le dernier numéro généré dans les paramètres
        $identification->dernier_identifiant = $id + $compteur;
        $identification->save();

        // Retourner le code RNGPS formaté
        return sprintf("%02d%05d%d", $id, $compteur, $annee); // Exemple: 17140022025
    }


    public function scopeActive($query)
    {
        return $query->where('statut', true)
            ->where('valid_until', '>', now());
    }

    public function activate()
    {
        // Désactive toutes les autres inscriptions
        $this->user->inscriptions()
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Active la nouvelle inscription
        $this->update([
            'is_active' => true,
            'valid_from' => now(),
            'valid_until' => now()->addYear() // 1 an de validité
        ]);
    }

    /**
     * Generate a unique inscription number in the format: 00{MM}{ID}{random(5)}{YY}
     *
     * @return string
     */
    public function generateInscriptionNumber(): string
    {
        // Get current month (MM) and year (YY)
        $month = now()->format('m');
        $year = now()->format('y');

        // Get the ID (will be 0 if the model isn't saved yet)
        $id = $this->id ?? 1;

        // Generate 5 random digits
        $random = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

        // Combine all parts to create the inscription number
        $inscriptionNumber = "00{$month}{$id}{$random}{$year}";

        return $inscriptionNumber;
    }


}
