<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Reinscription extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $dates = ['valid_from', 'valid_until'];
    protected $casts = [
        'valid_until' => 'datetime',
        'valid_from' => 'datetime'// This will automatically convert the 'valid_until' field to a Carbon instance
    ];
    protected $fillable = [
        'user_id',
        'inscription_id',
        'date_reinscription',
        'statut',
        'motif_rejet',
        'valid_from',
        'valid_until',
    ];

    public function registerMediaCollections(): void
    {

        $this->addMediaCollection('attestations');
        $this->addMediaCollection('receipt');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }
}
