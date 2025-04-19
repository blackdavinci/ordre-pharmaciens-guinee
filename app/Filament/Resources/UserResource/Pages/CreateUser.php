<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Notifications\SendLoginCredentials;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Auth\Events\Registered;
use Snowfire\Beautymail\Beautymail;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate a unique name using the User model method
        $data['name'] = User::makeUniqueName($data['nom'], $data['prenom']);

        // Generate a random password with 8 characters
        $password = $this->generateRandomPassword(8);
        $data['password'] = $password;
        $data['statut'] = 1;
        $data['email_verified_at'] = now();


        // Store the plain password to send it in the email
        $this->plainPassword = $password;

        return $data;
    }

    protected function beforeCreate(): void
    {
        $authUser = auth()->user();

        $selectedRoles = $this->data['roles'] ?? []; // Retrieve selected roles from form data

        $adminRole = Role::where('name', 'super_admin')->first();

        if ( $adminRole->id ==  $selectedRoles && !$authUser->hasRole('super_admin')) {
            abort(403, 'Vous n’êtes pas autorisé à effectuer cette action.');
        }

    }
    protected function afterCreate(): void
    {
        // Send email notification with the login credentials
        $record = $this->record;
        $password = $this->plainPassword;

        // Envoi des informations de compte utilisateur
        $beautymail = app()->make(Beautymail::class);
        $beautymail->send('emails.user-account', ['user' => $record, 'password' => $password], function ($message) use ($record) {
            $message
                ->from('ousmaneciss1@gmail.com')
                ->to($record->email, $record->prenom.' '.$record->nom)
                ->subject('Votre compte a été créé - ONPG');
        });
    }

    private function generateRandomPassword($length = 8): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
        $password = substr(str_shuffle(str_repeat($characters, ceil($length / strlen($characters)))), 1, $length);

        return $password;
    }
}
