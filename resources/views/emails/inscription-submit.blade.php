@extends('beautymail::templates.minty')

@section('content')

    @include('beautymail::templates.minty.contentStart')
    <tr>
        <td class="title" style="color: #2ab27b; font-size: 18px; font-weight: bold;">
            Merci pour votre inscription, {{ $inscription->prenom }} !
        </td>
    </tr>
    <tr>
        <td width="100%" height="15"></td>
    </tr>
    <tr>
        <td class="paragraph">
            Nous avons bien reçu votre inscription et elle est actuellement <strong>en cours de validation</strong> par le président de l'Ordre des Pharmaciens.
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
                    <td><strong>Numéro d'inscription :</strong></td>
                    <td>{{ $inscription->numero_inscription }}</td>
                </tr>
                <tr>
                    <td><strong>Nom complet :</strong></td>
                    <td>{{ $inscription->prenom }} {{ $inscription->nom }}</td>
                </tr>
                <tr>
                    <td><strong>Date de soumission :</strong></td>
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
            Vous trouverez ci-joint votre <strong>reçu de paiement</strong>. Ce document est important, nous vous recommandons de le conserver précieusement.
        </td>
    </tr>
    <tr>
        <td width="100%" height="20"></td>
    </tr>
    <tr>
        <td class="paragraph">
            <h2>Prochaines étapes :</h2>
            <ul>
                <li>Votre inscription sera vérifiée par le président de l'Ordre des Pharmaciens.</li>
                <li>Une fois validée, vous recevrez un e-mail de confirmation avec vos accès.</li>
                <li>Si vous avez des questions, n'hésitez pas à nous contacter à
                    <a href="mailto:{{$settings->support_email}}">{{$settings->support_email}}</a> ou en appelant le
                    <a href="tel:{{$settings->support_phone}}" class="d-inline-block font-secondary fw-semibold text-title fs-xx-14 hover-text-secondary">{{$settings->support_phone}}</a>.
                </li>
            </ul>
        </td>
    </tr>
    <tr>
        <td width="100%" height="25"></td>
    </tr>
    <tr>
        <td class="paragraph" style="font-size: 12px; color: #777;">
            <em>Si vous n'avez pas effectué cette demande, veuillez nous contacter immédiatement à l'adresse <a href="mailto:{{$settings->support_email}}">{{$settings->support_email}}</a>.</em>
        </td>
    </tr>
    @include('beautymail::templates.minty.contentEnd')

@stop
