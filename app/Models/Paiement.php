<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'inscription_id',
        'user_id',
        'type',               // 'inscription' ou 'reinscription'
        'amount',
        'payment_method',     // Orange Money, Playcard, etc.
        'reference',
        'status',             // en attente, effectué, échoué
        'inscription_token'
    ];

    public function inscription()
    {
        return $this->hasOne(Inscription::class);
    }

}
