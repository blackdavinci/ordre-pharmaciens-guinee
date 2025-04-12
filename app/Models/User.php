<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nom',
        'prenom',
        'email',
        'telephone',
        'password',
        'statut',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        // Example condition: Allow access only to users with specific email domain and verified email

        // Check if the user has one of the allowed roles
        $allowedRoles = ['super_admin', 'membre', 'president'];

        if ($this->hasAnyRole($allowedRoles) && $this->statut) {
            return true;
        }

        return false;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function inscription()
    {
        return $this->hasOne(Inscription::class);
    }

    protected static function booted()
    {
        static::saving(function ($user) {
            if ($user->prenom && $user->nom) {
                $user->name = strtolower(trim($user->prenom . ' ' . $user->nom));
            }
        });
    }

    public static function makeUniqueName($nom, $prenom, $userId = null)
    {
        // Generate the base name
        $baseName = Str::ascii(strtolower(trim($nom) . trim($prenom)));
        $name = $baseName;

        // Initialize counter for uniqueness
        $counter = 1;

        // Loop to ensure uniqueness
        while (self::where('name', $name)
            ->when($userId, function ($query) use ($userId) {
                return $query->where('id', '!=', $userId); // Exclude current user if editing
            })
            ->exists()) {
            // Append a number to make it unique
            $name = $baseName . $counter;
            $counter++;
        }

        return $name;
    }
}
