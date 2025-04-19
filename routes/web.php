<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PaymentController;
use App\Livewire\PharmaciensList;
use App\Models\Inscription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Livewire\InscriptionPharmacienForm;

Route::get('/attestation', [DocumentController::class, 'generateAttestationPDF'])->name('generate.attestation');

Route::get('/receipt', [DocumentController::class, 'generateReceiptPDF'])->name('generate.receipt');

Route::get('/attestations/{uuid}/verify', [DocumentController::class, 'verify'])->name('attestation.verify');

Route::get('/', [PageController::class, 'home'])->name('pharmaciens.home');

Route::get('/a-propos', [PageController::class, 'about'])->name('pharmaciens.about');

Route::get('/liste-pharmaciens', [PageController::class, 'liste'])->name('pharmaciens.liste');

Route::get('/pharmacien/inscription/{token?}', InscriptionPharmacienForm::class)->name('pharmaciens.inscription');

Route::post('/inscription/check-email', [PageController::class, 'checkEmail'])->name('inscription.check');

Route::get('/inscription/{numero}/success', [PageController::class,'inscriptionSuccess'])->name('inscription.success');

Route::get('recu-paiement/{id}', [PaymentController::class, 'genererRecuPaiement'])->name('recu.paiement');


// PayCard Online Payment Routes
Route::get('/paiement/initiate/{token}', [PaymentController::class, 'initiate'])->name('payment.initiate');
Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/inscription/reprendre/{token}', [PaymentController::class, 'resumePayment'])->name('inscription.reprendre');

