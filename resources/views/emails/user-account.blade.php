@extends('beautymail::templates.minty')

@section('content')

    @include('beautymail::templates.minty.contentStart')
    <tr>
        <td class="title" style="color: #2ab27b; font-size: 18px; font-weight: bold;">
            Vos accès à la plateforme
        </td>
    </tr>
    <tr>
        <td width="100%" height="15"></td>
    </tr>
    <tr>
        <td class="paragraph">
            Bonjour {{ $user->name }},
        </td>
    </tr>
    <tr>
        <td width="100%" height="15"></td>
    </tr>
    <tr>
        <td class="paragraph">
            Votre compte a été créé avec succès sur notre plateforme. Voici vos informations de connexion :
        </td>
    </tr>
    <tr>
        <td width="100%" height="20"></td>
    </tr>
    <tr>
        <td>
            <table style="width: 100%; background: #f5f5f5; padding: 15px; border-radius: 5px;">
                <tr>
                    <td style="width: 120px; font-weight: bold;">Identifiant :</td>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Mot de passe :</td>
                    <td>{{ $password }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Profil :</td>
                    <td>Membre</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td width="100%" height="20"></td>
    </tr>
    <tr>
        <td class="paragraph" style="color: #d9534f; font-weight: bold;">
            ⚠️ Pour des raisons de sécurité, nous vous recommandons fortement de :
        </td>
    </tr>
    <tr>
        <td width="100%" height="10"></td>
    </tr>
    <tr>
        <td class="paragraph">
            <ul style="margin-top: 0; padding-left: 20px;">
                <li>Changer votre mot de passe dès votre première connexion</li>
                <li>Ne jamais partager vos identifiants</li>
            </ul>
        </td>
    </tr>
    <tr>
        <td width="100%" height="25"></td>
    </tr>
    <tr>
        <td>
            @include('beautymail::templates.minty.button', [
                'text' => 'Se connecter maintenant',
                'link' => route('pharmaciens.home'),
                'color' => '#2ab27b'
            ])
        </td>
    </tr>
    <tr>
        <td width="100%" height="25"></td>
    </tr>
    <tr>
        <td class="paragraph">
            <strong>Besoin d'aide ?</strong><br>
            Notre équipe support est disponible à <a href="mailto:support@votredomaine.com">support@votredomaine.com</a> pour toute assistance.
        </td>
    </tr>
    <tr>
        <td width="100%" height="15"></td>
    </tr>
    <tr>
        <td class="paragraph" style="font-size: 12px; color: #777;">
            Cet email contient des informations sensibles. Si vous n'êtes pas à l'origine de cette demande,
            veuillez nous contacter immédiatement.
        </td>
    </tr>
    @include('beautymail::templates.minty.contentEnd')

@stop
