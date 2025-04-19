<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Notifications\SendResetPassword;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Snowfire\Beautymail\Beautymail;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function viewAny(): bool
    {
        $authUser = auth()->user();     // The currently authenticated user
        $viewedUser = $this->record;    // The user being viewed

        // Allow admins to view all profiles
        if ($authUser->hasRole('super_admin')) {
            return true;
        }

        // Allow moderators to view all profiles except admin profiles
        if ($authUser->hasRole('moderateur') && !$viewedUser->hasRole('super_admin')) {
            return true;
        }

        // Deny access by default
        return false;
    }

    protected function getActions(): array
    {
        return [
            // Tables\Actions\EditAction::make(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Mise a jour mot de passe')
                ->icon('heroicon-o-key') // Key icon to represent password/security
                ->color('success')
                ->form([
                    TextInput::make('old_password')
                        ->password()
                        ->required()
                        ->revealable()
                        ->label('Ancien mot de passe'),
                    TextInput::make('password')
                        ->password()
                        ->required()
                        ->revealable()
                        ->label('Nouveau mot de passe')
                        ->rules('min:8'),
                    TextInput::make('password_confirmation')
                        ->password()
                        ->required()
                        ->revealable()
                        ->label('Confirmer le nouveau mot de passe')
                        ->same('password'),
                ])
                ->action(function (array $data) {
                    $user = auth()->user();

                    if (!Hash::check($data['old_password'], $user->password)) {
                        throw ValidationException::withMessages([
                            'old_password' => 'L\'ancien mot de passe est incorrect.',
                        ]);
                    }

                    $user->password = Hash::make($data['password']);
                    $user->save();

                    Notification::make()
                        ->title('Mot de passe mis à jour')
                        ->success()
                        ->send();
                })->visible(fn () => auth()->check() && auth()->id() === $this->record->id),
            Actions\Action::make('Réinitialiser le mot de passe')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (array $data) {
                    $user = $this->record;
                    // Generate a random password with 8 characters
                    $password = $this->generateRandomPassword(8);
                    $user->update([
                        'password' => $password,
                    ]);
                    $user->save();
                    // Send email notification with the login credentials
                    // Envoi des informations de compte utilisateur
                    $beautymail = app()->make(Beautymail::class);
                    $beautymail->send('emails.password-reset', ['user' => $user, 'password' => $password], function ($message) use ($user) {
                        $message
                            ->from('ousmaneciss1@gmail.com')
                            ->to($user->email, $user->prenom.' '.$user->nom)
                            ->subject('Votre mot de passe a été réinitialisé - ONPG');
                    });

                    Notification::make()
                        ->title('Mot de passe réinitialisé avec succès')
                        ->success()
                        ->send();
                })->hidden(auth()->user()->hasRole('moderateur') && $this->record->hasRole('super_admin') || auth()->user()->id === $this->record->id),
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil')
                ->hidden(auth()->user()->hasRole('moderateur') && $this->record->hasRole('super_admin')),
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->icon('heroicon-o-trash')
                ->hidden(auth()->user()->hasRole('moderateur') && $this->record->hasRole('super_admin') || auth()->user()->id === $this->record->id),

        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [

        ];
    }

    private function generateRandomPassword($length = 8): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
        $password = substr(str_shuffle(str_repeat($characters, ceil($length / strlen($characters)))), 1, $length);

        return $password;
    }
}
