<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'code',
        'transaction_date',
        'status',
        'status_description',
        'error_message',
        'payment_method',
        'payment_description',
        'payment_amount',
        'payment_reference',
        'merchant_name',
        'token',
        'inscription_id',
    ];

    // Relation: Un paiement appartient à une inscription
    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

}
