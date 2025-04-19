@extends('beautymail::templates.minty')

@section('content')

    @include('beautymail::templates.minty.contentStart')
    <tr>
        <td class="title" style="color: #2ab27b; font-size: 18px; font-weight: bold;">
            Félicitations, {{ $inscription->prenom.' '.$inscription->nom }} !
        </td>
    </tr>
    <tr>
        <td width="100%" height="15"></td>
    </tr>
    <tr>
        <td class="paragraph">
            Nous avons le plaisir de vous informer que votre inscription a été <strong>approuvée</strong> avec succès.
        </td>
    </tr>
    <tr>
        <td width="100%" height="15"></td>
    </tr>
    <tr>
        <td class="paragraph">
            <strong>Détails de votre inscription :</strong>
        </td>
    </tr>
    <tr>
        <td width="100%" height="10"></td>
    </tr>
    <tr>
        <td class="paragraph">
            <table style="width: 100%; background: #f5f5f5; border-radius: 5px;">
                <tr>
                    <td style="padding: 8px;"><strong>Nom complet :</strong></td>
                    <td>{{ $inscription->prenom }} {{ $inscription->nom }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px;"><strong>Profil :</strong></td>
                    <td>{{ $inscription->profil }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px;"><strong>Numéro RNGPS :</strong></td>
                    <td>{{ $inscription->numero_rngps }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px;"><strong>Numéro Ordre :</strong></td>
                    <td>{{ $inscription->numero_ordre }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px;"><strong>Date d'approbation :</strong></td>
                    <td>{{ $inscription->valid_from->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px;"><strong>Inscription valide jusqu'au :</strong></td>
                    <td>{{ $inscription->valid_until->format('d/m/Y') }}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td width="100%" height="20"></td>
    </tr>
    <tr>
        <td class="paragraph">
            Vous trouverez ci-joint votre <strong>attestation d'approbation</strong> officielle. Ce document est important, nous vous recommandons de le conserver précieusement.
        </td>
    </tr>
    <tr>
        <td width="100%" height="20"></td>
    </tr>
    <tr>
        <td class="paragraph">
            Un compte utilisateur a été créé pour vous. Vous recevrez sous peu un email séparé contenant vos identifiants de connexion.
        </td>
    </tr>
    <tr>
        <td width="100%" height="25"></td>
    </tr>
    <tr>
        <td>
            @include('beautymail::templates.minty.button', [
                'text' => 'Accéder à votre espace',
                'link' => route('pharmaciens.home'),
                'color' => '#2ab27b'
            ])
        </td>
    </tr>
    <tr>
        <td width="100%" height="25"></td>
    </tr>
    <tr>
        <td class="paragraph" style="font-size: 12px; color: #777;">
            <em>Si vous n'avez pas effectué cette demande, veuillez nous contacter immédiatement à l'adresse <a href="mailto:support@votredomaine.com">support@votredomaine.com</a>.</em>
        </td>
    </tr>
    @include('beautymail::templates.minty.contentEnd')

@stop
