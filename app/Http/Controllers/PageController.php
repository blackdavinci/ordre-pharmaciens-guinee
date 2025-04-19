<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\User;
use App\Services\AttestationPdfService;
use App\Services\ReceiptPdfService;
use App\Settings\DocumentSettings;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Snowfire\Beautymail\Beautymail;


class PageController extends Controller
{
    public function home(){

        return view('pharmaciens.accueil');
    }

    public function about(){
        return view('pharmaciens.about');
    }

    public function liste(){
        return view('pharmaciens.liste-pharmacien');
    }

    public function checkEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255'
        ]);

        $email = $validated['email'];

        // Vérifier si une inscription complète existe déjà
        $existingInscription = Inscription::where('email', $email)
            ->where('statut', 'approved')
            ->first();

        if ($existingInscription) {
            return redirect()->back()->withErrors([
                'approved' => 'Une inscription validée existe déjà avec cet email. <br> Veuillez vous connecter avec vos identifiants ou <br> contactez nous pour toute assistance.'
            ]);
        }

        // Vérifier les inscriptions est soumise et en attente de validation
        $inscriptionUnderApprobation = Inscription::with('paiements')
            ->where('email', $email)
            ->where('statut', 'pending')
            ->whereHas('paiements', function($query) {
                $query->where('status', 'success');
            })
            ->latest()
            ->first();

        if ($inscriptionUnderApprobation) {
            return redirect()->back()->withErrors([
                'pending' => "Une inscription en attente de validation a déjà été soumise avec l'adresse ".$email.". <br/>Nous procédérons au traitement de votre demande dans les plus brefs délais."
            ]);
        }

        // Vérifier les inscriptions rejetées
        $rejectedInscription = Inscription::where('email', $email)
            ->where('statut', 'rejected')
            ->first();

        if ($rejectedInscription) {
            return redirect()->back()->withErrors([
                'rejected' => "L'adresse e-mail ".$email." a déjà reçu un refus d'inscription. <br> Pour plus d'information veuillez contacter l'administration."
            ]);
        }

        // Vérifier les inscriptions en cours
        $pendingInscription = Inscription::with('paiements')
            ->where('email', $email)
            ->where('statut', 'pending')
            ->whereHas('paiements', function($query) {
                $query->whereIn('status', ['failed','pending']);
            })
            ->latest()
            ->first();

        if ($pendingInscription) {
            // Rediriger avec le token sécurisé
            return redirect()->route('pharmaciens.inscription', ['token' => $pendingInscription->inscription_token]);
        }

        // Aucune inscription existante, stocker l'email en session et rediriger
        session()->put('temp_email', $email);

        return redirect()->route('pharmaciens.inscription');
    }

    public function inscriptionSuccess(string $numero){

        $inscription = Inscription::where('numero_inscription', $numero)->firstOrFail();

        // Récupérer uniquement le paiement d’inscription initial
        $paiement = Paiement::where('inscription_id', $inscription->id)
            ->where('type', 'inscription')
            ->firstOrFail();

        // 2. Générer le reçu de paiement
        $receiptService = app()->make(ReceiptPdfService::class);

        $logo = public_path('storage/'.app(GeneralSettings::class)->logo);
        $receipt_background_url = public_path('storage/'.app(DocumentSettings::class)->receipt_background);

        $data = [
            'numero_inscription' => $inscription->numero_inscription,
            'payment_reference' => $paiement->payment_reference,
            'payment_method' => $paiement->payment_method,
            'payment_amount' => $paiement->payment_amount,
            'payment_date' => $paiement->transaction_date,
            'customer_nom' => $inscription->prenom.' '.$inscription->nom,
            'customer_adresse' => $inscription->adresse_residence,
            'customer_ville' => $inscription->ville_residence,
            'customer_pays' => $inscription->pays_residence,
            'customer_telephone' => $inscription->telephone_mobile,
            'customer_email' => $inscription->email,
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
        $filename = 'recu_' . $inscription->id . '_' . Str::random(8) . '.pdf';

        // Sauvegarder le PDF dans la bibliothèque de médias Spatie
        $media = $inscription->addMediaFromString($pdfContent)  // Add the PDF content to media
        ->usingFileName($filename)  // Use the generated filename
        ->withCustomProperties([
            'generated_date' => now()->format('Y-m-d H:i:s'),
            // Vous pouvez ajouter d'autres métadonnées ici
        ])->toMediaCollection('receipt');  // Save it to the 'attestations' collection

        // 6. Envoyer l'email avec Beautymail
        $beautymail = app()->make(BeautyMail::class);
        $beautymail->send('emails.inscription-submit', [
            'inscription' => $inscription,
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

        return view('pharmaciens.submit-success', [
            'email' => $inscription->email,
        ]);
    }
}
