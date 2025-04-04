<?php

use App\Http\Controllers\PaymentController;
use App\Livewire\PharmaciensList;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Livewire\InscriptionPharmacienForm;

Route::get('/', PharmaciensList::class)->name('pharmaciens.index');

Route::get('/pharmacien/inscription', InscriptionPharmacienForm::class)->name('inscription');

// PayCard Online Payment Routes
Route::get('/paiement/initiate/{token}', [PaymentController::class, 'initiate'])->name('payment.initiate');
Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

