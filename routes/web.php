<?php

use App\Http\Controllers\AttestationController;
use App\Http\Controllers\PaymentController;
use App\Livewire\PharmaciensList;
use App\Models\Inscription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Livewire\InscriptionPharmacienForm;

Route::get('/test', function()
{
    $beautymail = app()->make(Snowfire\Beautymail\Beautymail::class);
    $beautymail->send('emails.minty', [], function($message)
    {
        $message
            ->from('ousmaneciss1@gmail.com')
            ->to('ousmaneciss1@gmail.com', 'John Smith')
            ->subject('Welcome!');
    });

});

Route::get('/attestation', function(){

    $inscription = Inscription::findOrFail(4);

    $data = [
        'inscription' => $inscription,
        'date' => now()->format('d/m/Y'),
    ];

//    return view('pharmaciens.attestations.pdf-attestation', compact('inscription'));
//
//    // Créer l'instance de PDF avec orientation paysage
//    $pdf = PDF::loadView('pharmaciens.attestations.pdf-attestation', $data)
//        ->setPaper('a4', 'landscape')
//        ->setOptions([
//            'isHtml5ParserEnabled' => true,
//            'isRemoteEnabled' => true,
//            'defaultFont' => 'sans-serif',
//            'dpi' => 150,
//            'debugCss' => false
//        ]);

    // Generate PDF using DOMPDF
    $pdf = PDF::loadView('pharmaciens.attestations.pdf-attestation', $data)->setPaper('a4', 'landscape');
// Télécharger le PDF
//    return $pdf->download('attestation.pdf');
    return $pdf->stream();
});

Route::get('/attestation-pdf', [AttestationController::class, 'generatePDF']);

Route::get('/', [PageController::class, 'home'])->name('pharmaciens.home');

Route::get('/a-propos', [PageController::class, 'about'])->name('pharmaciens.about');

Route::get('/liste-pharmaciens', [PageController::class, 'liste'])->name('pharmaciens.liste');

Route::get('/pharmacien/inscription', InscriptionPharmacienForm::class)->name('pharmaciens.inscription');

Route::get('/inscription/{numero}/success', [PageController::class,'inscriptionSuccess'])->name('inscription.success');

Route::get('recu-paiement/{id}', [PaymentController::class, 'genererRecuPaiement'])->name('recu.paiement');


// PayCard Online Payment Routes
Route::get('/paiement/initiate/{token}', [PaymentController::class, 'initiate'])->name('payment.initiate');
Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/inscription/reprendre/{token}', [PaymentController::class, 'resumePayment'])->name('inscription.reprendre');

