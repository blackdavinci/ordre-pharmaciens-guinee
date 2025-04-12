<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Settings\DocumentSettings;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use App\Services\AttestationPdfService;
use Illuminate\Http\Response;


class AttestationController extends Controller
{
    protected $pdfService;

    public function __construct(AttestationPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function generatePDF(Request $request)
    {
        // Vous pouvez adapter cela pour récupérer les données depuis un formulaire ou une base de données
        $inscription = Inscription::first();
        $signature_president = public_path('storage/'.app(DocumentSettings::class)->signature_president);
        $attestation_background_url = public_path('storage/'.app(DocumentSettings::class)->attestation_background);
        $logo = public_path('storage/'.app(GeneralSettings::class)->logo) ;

        $data = [
            'presidentNom' => app(DocumentSettings::class)->nom_president,
            'rngpsNumero' => $inscription->numero_rngps,
            'medecinNumero' => $inscription->numero_medecin,
            'validiteAttesation' => $inscription->expiration_at,
            'pharmacienNom' => $inscription->prenom.' '.$inscription->nom,
            'pharmacienProfil' => ucfirst($inscription->profil),
            'dateOfValidation' => $inscription->date_validation,
            'signatureAttestation' => $signature_president,
            'backgroundAttestation' => $attestation_background_url,
            'logo' => $logo

        ];

        $pdf = $this->pdfService->generate($data);

        // Pour afficher dans le navigateur (au lieu de télécharger)
        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="attestation.pdf"'
        ]);

        // Pour télécharger directement le PDF
        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="attestation.pdf"',
        ]);



        // OU pour afficher le PDF dans le navigateur
        /*
        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="attestation.pdf"',
        ]);
        */
    }
}
