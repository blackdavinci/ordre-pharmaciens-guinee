<?php

namespace App\Http\Controllers;

use App\Exceptions\PaycardException;
use App\Mail\PaiementInscriptionEchec;
use App\Models\Paiement;
use App\Settings\PaiementSettings;
use Illuminate\Http\Request;
use App\Models\Inscription;
use App\Services\PaycardService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private $paycardService;

    public function __construct(PaycardService $paycardService)
    {
        $this->paycardService = $paycardService;
    }

    public function initiate(string $token, string $payment_method)
    {

        $settings = app(PaiementSettings::class);

        $frais = $settings->inscription_frais_citoyen;

        if (($data['citoyen_guineen'] ?? false) === true && ($data['diplome_etranger'] ?? false) === true) {
            $frais = $settings->inscription_frais_citoyen_diplome_etranger;
        } elseif (($data['citoyen_guineen'] ?? false) === false && ($data['diplome_etranger'] ?? false) === false) {
            $frais = $settings->reinscription_frais_resident;
        } elseif (($data['citoyen_guineen'] ?? false) === false && ($data['diplome_etranger'] ?? false) === true) {
            $frais = $settings->reinscription_frais_resident_diplome_etranger;
        }

        $inscription = Inscription::where('token', $token)->firstOrFail();

        // Crée ou récupère un paiement en attente
        $paiement = $inscription->paiement;

        if (!$paiement || $paiement->statut === 'échoué') {
            $reference = 'REG-' . Str::upper(Str::random(10));

            $paiement = Paiement::create([
                'inscription_id' => $inscription->id,
                'user_id' => null,
                'type' => 'inscription',
                'amount' => $frais,
                'reference' => $reference,
                'payment_method' => $payment_method,
                'status' => 'pending',
                'inscription_token' => $token,
            ]);

            $inscription->update(['paiement_id' => $paiement->id]);
        }

        return $this->handlePayment($paiement);

    }

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

            return redirect()->route('orders.show', $paiement)
                ->with('success', $message);
        } catch (\Exception $e) {
            $paiement->update(['status' => 'failed']);
            return redirect()->route('orders.show', $paiement)
                ->with('error', 'Une erreur est survenue lors du traitement du paiement');
        }
    }

    protected function handlePayment(Paiement $paiement)
    {

        $paymentData = [
            'amount' => $paiement->amount,
            'description' => "Paiement frais d'inscription à l'Ordre National des Pharmaciens de Guinée",
            'reference' => $paiement->reference,
            'payment_method' => $paiement->payment_method,
            'callback_url' => route('payment.callback'),
            'auto_redirect' => true,
            'redirect_with_get' => true
        ];

        try {
            $response = $this->paycardService->createPayment($paymentData);
            return redirect($response['payment_url']);

        } catch (PaycardException $e) {
            // Spécifique à Paycard
            $paiement->update(['statut' => 'failed']);

            $inscription = $paiement->inscription;
            if ($inscription && $inscription->email) {
                Mail::to($inscription->email)->send(new PaiementInscriptionEchec($inscription));
            }

            return redirect()->route('inscription.reprendre', ['token' => $inscription->token])
                ->with('error', 'Échec de paiement via Paycard : ' . $e->getMessage());

        } catch (\Exception $e) {
            // Erreur générique (base de données, logique, etc.)
            Log::error('[Paiement] Erreur inattendue : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue, veuillez réessayer plus tard.');
        }
    }

}
