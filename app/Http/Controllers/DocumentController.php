<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\Reinscription;
use App\Services\AttestationPdfService;
use App\Services\ReceiptPdfService;
use App\Settings\DocumentSettings;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DocumentController extends Controller
{
    protected $attestation;
    protected $receipt;

    public function __construct(AttestationPdfService $attestation, ReceiptPdfService $receipt)
    {
        $this->attestation = $attestation;
        $this->receipt = $receipt;
    }

    public function generateAttestationPDF(Request $request)
    {
        // Vous pouvez adapter cela pour récupérer les données depuis un formulaire ou une base de données
        $inscription = Inscription::first();
        $signature_president = public_path('storage/'.app(DocumentSettings::class)->signature_president);
        $attestation_background_url = public_path('storage/'.app(DocumentSettings::class)->attestation_background);
        $logo = public_path('storage/'.app(GeneralSettings::class)->logo) ;

        $data = [
            'presidentNom' => app(DocumentSettings::class)->nom_president,
            'rngpsNumero' => $inscription->numero_rngps,
            'ordreNumero' => $inscription->numero_ordre,
            'validiteAttesation' => $inscription->expiration_at,
            'pharmacienNom' => $inscription->prenom.' '.$inscription->nom,
            'pharmacienProfil' => ucfirst($inscription->profil),
            'dateOfValidation' => $inscription->date_validation,
            'signatureAttestation' => $signature_president,
            'backgroundAttestation' => $attestation_background_url,
            'logo' => $logo

        ];

        $pdf = $this->attestation->generate($data);

        // Pour afficher dans le navigateur (au lieu de télécharger)
        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="attestation.pdf"'
        ]);
    }

    public function generateReceiptPDF(Request $request)
    {
        // Vous pouvez adapter cela pour récupérer les données depuis un formulaire ou une base de données
        $inscription = Inscription::first();
        $receipt_background_url = public_path('storage/'.app(DocumentSettings::class)->receipt_background);
        $logo = public_path('storage/'.app(GeneralSettings::class)->logo) ;

        $data = [
            'numero_inscription' => 'INS-200-2025',
            'payment_reference' => 'REF-200-2025',
            'payment_method' => 'Orange Money',
            'payment_amount' => '100000',
            'payment_date' => '13/04/2025',
            'customer_nom' => 'Ousmane CISSE',
            'customer_adresse' => '1905 Boulevard Jacques-Cartier E',
            'customer_ville' => 'Longueuil',
            'customer_pays' => 'Canada',
            'customer_telephone' => '600 00 00 00',
            'customer_email' => 'ousmane@gmail.com',
            'merchant_name' => app(GeneralSettings::class)->site_name,
            'merchant_email' => app(GeneralSettings::class)->support_email,
            'merchant_phone' => app(GeneralSettings::class)->support_phone,
            'merchant_adresse' => app(DocumentSettings::class)->adresse,
            'merchant_mention_fiscale'=> app(DocumentSettings::class)->mention_fiscale,
            'backgroundReceipt' => $receipt_background_url,
            'logo' => $logo
        ];

        $pdf = $this->receipt->generate($data);

        // Pour afficher dans le navigateur (au lieu de télécharger)
        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="receipt.pdf"'
        ]);
    }

    public function verify($uuid)
    {
        // Filter media where custom_properties['uuid'] matches the provided $uuid
        $pdf = Media::query()
            ->where('collection_name', 'attestations')
            ->whereJsonContains('custom_properties->uuid', $uuid)
            ->first();

        // Pour afficher dans le navigateur (au lieu de télécharger)
        return new Response(file_get_contents($pdf->getPath()), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $pdf->file_name . '"'
        ]);

    }
}
