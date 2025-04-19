<?php

namespace App\Http\Controllers;

use App\Exceptions\PaycardException;
use App\Mail\PaiementInscriptionEchec;
use App\Mail\PaiementInscriptionEchecBeautymail;
use App\Models\Paiement;
use App\Models\User;
use App\Services\ReceiptPdfService;
use App\Settings\DocumentSettings;
use App\Settings\GeneralSettings;
use App\Settings\PaiementSettings;
use Illuminate\Http\Request;
use App\Models\Inscription;
use App\Services\PaycardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Snowfire\Beautymail\Beautymail;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    private $paycardService;
    private $paiementSettings;

    public function __construct(PaycardService $paycardService, PaiementSettings $paiementSettings)
    {
        $this->paycardService = $paycardService;
        $this->paiementSettings = $paiementSettings;
    }

    // Méthode pour initier un paiement
    public function initiate(string $token)
    {
        $inscription = Inscription::where('inscription_token', $token)->firstOrFail();

        // Créer ou récupérer le paiement
        $paiement = $this->getOrCreatePaiement($inscription);

        return $this->handlePayment($paiement);
    }

    // Méthode pour récupérer ou créer un paiement
    protected function getOrCreatePaiement(Inscription $inscription)
    {
        // Vérifier s'il existe déjà un paiement ou si le paiement a échoué
        $paiement = $inscription->paiement;

        if (!$paiement) {

            $frais = $this->calculateFees($inscription);

            $type = ($inscription->numero_rngps != null) ? 'reinscription' : 'inscription';

            // Créer un nouveau paiement
            $reference = Str::uuid();

            $paiement = Paiement::create([
                'inscription_id' => $inscription->id,
                'user_id' => null,
                'type' => $type,
                'payment_amount' => $frais,
                'payment_reference' => $reference,
                'status' => 'pending',
                'inscription_token' => $inscription->inscription_token,
                'payment_date' => now(),
            ]);

        }

        return $paiement;
    }

    // Méthode pour calculer les frais
    protected function calculateFees(Inscription $inscription): float
    {
        if (($inscription->citoyen_guineen ?? false) && ($inscription->diplome_etranger ?? false)) {
            return $this->paiementSettings->inscription_frais_citoyen_diplome_etranger;
        }

        if (($inscription->citoyen_guineen ?? false) === false && ($inscription->diplome_etranger ?? false) === false) {
            return $this->paiementSettings->reinscription_frais_resident;
        }

        if (($inscription->citoyen_guineen ?? false) === false && ($inscription->diplome_etranger ?? false) === true) {
            return $this->paiementSettings->reinscription_frais_resident_diplome_etranger;
        }

        return $this->paiementSettings->inscription_frais_citoyen;
    }

    // Méthode pour gérer le paiement
    protected function handlePayment(Paiement $paiement)
    {
        $description = ($paiement->type == 'reinscription') ? "Paiement frais de réinscription à l'Ordre National des Pharmaciens de Guinée" : "Paiement frais d'inscription à l'Ordre National des Pharmaciens de Guinée";

        $paymentData = [
            'amount' => $paiement->payment_amount,
            'description' => $description,
            'reference' => $paiement->payment_reference,
            'callback_url' => route('payment.callback'),
            'auto_redirect' => true,
            'redirect_with_get' => true
        ];

        try {

            $numero = $paiement->inscription->numero_inscription;

            $paiement->update([
                'status' => 'success',
                'transaction_date' => now(),
                'payment_description' => $paymentData['description'],
                'merchant_name' => 'ONPG',
            ]);

            if($paiement->type=='inscription'){
                return redirect()->route('inscription.success', ['numero' => $numero]);
            }else{
                return redirect()->route('inscription.success', ['numero' => $numero]);
            }

            $response = $this->paycardService->createPayment($paymentData);

            return redirect($response['payment_url']);

        } catch (PaycardException $e) {
            // Mise à jour du paiement et envoi d'un email en cas d'échec
            $paiement->update(['statut' => 'failed']);
            $inscription = $paiement->inscription;

            if ($inscription && $inscription->email) {
                $beautymail = app()->make(Beautymail::class);
                $beautymail->send('emails.echec-paiement', [], function ($message) use ($inscription) {
                    $message
                        ->from('ousmaneciss1@gmail.com')
                        ->to($inscription->email, $inscription->prenom.' '.$inscription->nom)
                        ->subject('Échec paiement - Inscription ONPG!');
                });
            }

            return redirect()->route('inscription.reprendre', ['token' => $inscription->token])
                ->with('error', 'Échec de paiement via : '. $e->getMessage());
        } catch (\Exception $e) {
            Log::error('[Paiement] Erreur inattendue : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue, veuillez réessayer plus tard.');
        }
    }

    // Méthode callback pour gérer le retour du paiement
    public function callback(Request $request)
    {
        $reference = $request->input('paycard-operation-reference');

        $paiement = Paiement::where('payment_reference', $reference)->firstOrFail();

        try {
            $status = $this->paycardService->getPaymentStatus($reference);
            $date = $this->paycardService->getPaymentTransactionDate($reference);
            $description = $this->paycardService->getPaymentDescription($reference);
            $merchant_name = $this->paycardService->getPaymentMerchantName($reference);

            $paiement->update([
                'status' => $status['status'] === 'success' ? 'success' : 'failed',
                'transaction_date' => $date,
                'payment_description' => $description,
                'merchant_name' => $merchant_name,
            ]);

            if($status === 'success'){

                if($paiement->type=='reinscription'){
                    $paiement->update([
                        'user_id' => $paiement->inscription->user->id,
                        'reinscription_id' => $paiement->inscription->id,
                    ]);
                }
                // 2. Générer l'attestation PDF avec TCPDF
                $receiptService = app()->make(ReceiptPdfService::class);

                $logo = public_path('storage/'.app(GeneralSettings::class)->logo);
                $receipt_background_url = public_path('storage/'.app(DocumentSettings::class)->receipt_background);

                $data = [
                    'numero_inscription' => $paiement->inscription->numero_inscription,
                    'payment_reference' => $paiement->payment_reference,
                    'payment_method' => $paiement->payment_method,
                    'payment_amount' => $paiement->payment_amount,
                    'payment_date' => $paiement->transaction_date,
                    'customer_nom' => $paiement->inscription->prenom.' '.$paiement->inscription->nom,
                    'customer_adresse' => $paiement->inscription->adresse_residence,
                    'customer_ville' =>$paiement->inscription->ville_residence,
                    'customer_pays' => $paiement->inscription->pays_residence,
                    'customer_telephone' => $paiement->inscription->telephone_mobile,
                    'customer_email' => $paiement->inscription->email,
                    'merchant_name' => app(GeneralSettings::class)->site_name,
                    'merchant_email' => app(GeneralSettings::class)->support_email,
                    'merchant_phone' => app(GeneralSettings::class)->support_phone,
                    'merchant_adresse' => app(DocumentSettings::class)->adresse,
                    'merchant_mention_fiscale'=> app(DocumentSettings::class)->mention_fiscale,
                    'backgroundReceipt' => $receipt_background_url,
                    'logo' => $logo
                ];

                $pdfContent = $receiptService->generate($data);

                // 3. Générer un nom de fichier unique
                $filename = 'recu_' . $paiement->inscription->id . '_' . Str::random(8) . '.pdf';

                // Sauvegarder le PDF dans la bibliothèque de médias Spatie
                $media = $paiement->inscription->addMediaFromString($pdfContent)  // Add the PDF content to media
                ->usingFileName($filename)  // Use the generated filename
                ->withCustomProperties([
                    'generated_date' => now()->format('Y-m-d H:i:s'),
                    // Vous pouvez ajouter d'autres métadonnées ici
                ])->toMediaCollection('receipt');  // Save it to the 'attestations' collection

                // 6. Envoyer l'email avec Beautymail
                $beautymail = app()->make(BeautyMail::class);
                $beautymail->send('emails.inscription-submit', [
                    'inscription' => $paiement->inscription,
                    'date' => now()->format('d/m/Y'),
                ], function ($message) use ($inscription, $media) {
                    $message
                        ->from('ousmaneciss1@gmail.com')
                        ->to($inscription->email, $inscription->prenom . ' ' . $inscription->nom)
                        ->subject("Votre inscription à l'Ordre National des Pharmaciens de Guinée")
                        // Attach the file using the file path from Spatie Media Library
                        ->attach($media->getPath(), [
                            'as' => 'recu_paiement_'.$inscription->numero_inscription.'.pdf',
                            'mime' => 'application/pdf',
                        ]);
                });

                if($paiement->type=='inscription'){
                    return redirect()->route('inscription.success', $paiement)->with('success');
                }
            }

        } catch (\Exception $e) {
            $paiement->update(['status' => 'failed']);
            return redirect()->route('orders.show', $paiement)->with('error', 'Une erreur est survenue lors du traitement du paiement');
        }
    }

}
