@extends('beautymail::templates.minty')

@section('content')

    @include('beautymail::templates.minty.contentStart')
    <tr>
        <td class="title" style="color: #2ab27b; font-size: 18px; font-weight: bold;">
            Félicitations, {{ $inscription->prenom }} !
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
            <table style="width: 100%;">
                <tr>
                    <td style="width: 120px;"><strong>Code d'enregistrement :</strong></td>
                    <td>{{ $inscription->code_registre }}</td>
                </tr>
                <tr>
                    <td><strong>Nom complet :</strong></td>
                    <td>{{ $inscription->prenom }} {{ $inscription->nom }}</td>
                </tr>
                <tr>
                    <td><strong>Date d'approbation :</strong></td>
                    <td>{{ now()->format('d/m/Y') }}</td>
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
