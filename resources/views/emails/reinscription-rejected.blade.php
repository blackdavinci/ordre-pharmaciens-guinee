@extends('beautymail::templates.minty')

@section('content')

    @include('beautymail::templates.minty.contentStart')
    <tr>
        <td class="title" style="color: #e74c3c; font-size: 18px; font-weight: bold;">
            Bonjour, {{ $inscription->prenom.' '.$inscription->nom }}.
        </td>
    </tr>
    <tr>
        <td width="100%" height="15"></td>
    </tr>
    <tr>
        <td class="paragraph">
            Après examen attentif de votre dossier par notre commission, nous sommes au regret de vous informer que votre demande de réinscription <br/>
            à l'Ordre National des Pharmaciens de Guinée n'a pas été approuvée.
        </td>
    </tr>
    <tr>
        <td width="100%" height="15"></td>
    </tr>
    <tr>
        <td class="paragraph">
            <strong>Motif du refus :</strong>
        </td>
        <td class="paragraph">
            {!! $inscription->motif_rejet !!}}
        </td>
    </tr>
    <tr>
        <td width="100%" height="10"></td>
    </tr>
    <tr>
        <td class="paragraph">
            <strong>Détails de votre réinscription :</strong>
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
            Si vous avez des questions concernant cette décision ou si vous pensez qu'une erreur a été commise, nous vous invitons à nous contacter dès que possible à l'adresse suivante : <a href="mailto:support@votredomaine.com">support@votredomaine.com</a>.
        </td>
    </tr>
    <tr>
        <td width="100%" height="25"></td>
    </tr>
    <tr>
        <td class="paragraph" style="font-size: 12px; color: #777;">
            <em>Nous vous remercions pour votre compréhension et restons à votre disposition pour toute question.</em>
        </td>
    </tr>
    @include('beautymail::templates.minty.contentEnd')

@stop
