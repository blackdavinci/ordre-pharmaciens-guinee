<?php

namespace App\Http\Controllers;

use App\Exceptions\PaycardException;
use App\Mail\PaiementInscriptionEchec;
use App\Mail\PaiementInscriptionEchecBeautymail;
use App\Models\Paiement;
use App\Settings\PaiementSettings;
use Illuminate\Http\Request;
use App\Models\Inscription;
use App\Services\PaycardService;
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

        if (!$paiement || $paiement->statut === 'échoué') {
            $frais = $this->calculateFees($inscription);

            // Créer un nouveau paiement
            $reference = 'REG-' . Str::upper(Str::random(10));

            $paiement = Paiement::create([
                'inscription_id' => $inscription->id,
                'user_id' => null,
                'type' => 'inscription',
                'payment_amount' => $frais,
                'payment_reference' => $reference,
                'status' => 'pending',
                'inscription_token' => $inscription->inscription_token,
                'payment_date' => now(),
            ]);

            // Associer le paiement à l'inscription
            $inscription->update(['paiement_id' => $paiement->id]);
        }

        return $paiement;
    }

    // Méthode pour gérer le paiement
    protected function handlePayment(Paiement $paiement)
    {

        $paymentData = [
            'amount' => $paiement->payment_amount,
            'description' => "Paiement frais d'inscription à l'Ordre National des Pharmaciens de Guinée",
            'reference' => $paiement->payment_reference,
            'callback_url' => route('payment.callback'),
            'auto_redirect' => true,
            'redirect_with_get' => true
        ];

        try {

            $numeroInscription = $paiement->inscription->numero_inscription;

            return redirect()->route('inscription.success', ['numero' => $numeroInscription]);

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
            $paiement->update([
                'status' => $status['status'] === 'success' ? 'completed' : 'failed'
            ]);

            $message = $status['status'] === 'success' ? 'Paiement effectué avec succès' : 'Le paiement a échoué';
            return redirect()->route('orders.show', $paiement)->with('success', $message);
        } catch (\Exception $e) {
            $paiement->update(['status' => 'failed']);
            return redirect()->route('orders.show', $paiement)->with('error', 'Une erreur est survenue lors du traitement du paiement');
        }
    }

    public function genererRecuPaiement($paiementId)
    {
        // Récupération du paiement spécifique
        $paiement = Paiement::with('inscription')->findOrFail($paiementId);

        $inscription = $paiement->inscription;

        if (!$inscription) {
            abort(404, 'Aucune inscription liée à ce paiement.');
        }

        $data = [
            'numero_inscription' => $inscription->numero_inscription,
            'prenom' => $inscription->prenom,
            'nom' => $inscription->nom,
            'montant' => $paiement->montant,
            'date_paiement' => $paiement->created_at->format('d/m/Y'),
            'status' => $paiement->statut,
            'type' => $paiement->type,
        ];

        $pdf = PDF::loadView('pharmaciens.recu_paiement', $data);

        return $pdf->download("recu_{$paiement->type}_{$inscription->numero_inscription}.pdf");
    }




}
