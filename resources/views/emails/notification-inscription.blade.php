@extends('beautymail::templates.minty')

@section('content')

    @include('beautymail::templates.minty.contentStart')
    <tr>
        <td class="title" style="color: #2ab27b; font-size: 18px; font-weight: bold;">
            Nouvelle inscription sur la plateforme
        </td>
    </tr>
    <tr>
        <td width="100%" height="15"></td>
    </tr>
    <tr>
        <td class="paragraph">
            Bonjour {{ $president->name }},
        </td>
    </tr>
    <tr>
        <td width="100%" height="15"></td>
    </tr>
    <tr>
        <td class="paragraph">
            Nous vous notifions d'une nouvelle inscription sur la plateforme. Voici les informations de l'utilisateur inscrit :
        </td>
    </tr>
    <tr>
        <td width="100%" height="20"></td>
    </tr>
    <tr>
        <td>
            <table style="width: 100%; background: #f5f5f5; padding: 15px; border-radius: 5px;">
                <tr>
                    <td style="width: 120px; font-weight: bold;">Nom :</td>
                    <td>{{ $user->prenom.' '.$user->nom }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Email :</td>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Téléphone :</td>
                    <td>{{ $user->telephone }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Date de l'inscription :</td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td width="100%" height="20"></td>
    </tr>
    <tr>
        <td width="100%" height="10"></td>
    </tr>
    <tr>
        <td width="100%" height="25"></td>
    </tr>
    <tr>
        <td>
            @include('beautymail::templates.minty.button', [
                'text' => 'Voir les inscriptions',
                'link' => route('admin.inscriptions'), // Lien vers la page des inscriptions
                'color' => '#2ab27b'
            ])
        </td>
    </tr>
    <tr>
        <td width="100%" height="25"></td>
    </tr>
    <tr>
        <td width="100%" height="15"></td>
    </tr>

    @include('beautymail::templates.minty.contentEnd')

@stop
