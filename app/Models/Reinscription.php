<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reinscription extends Model
{
    protected $fillable = [
        'user_id',
        'annee',
        'paiement_effectue',
        'statut',
        'motif_rejet',
        'validated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
