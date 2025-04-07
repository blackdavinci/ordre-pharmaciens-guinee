<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\Paiement;
use Illuminate\Http\Request;


class PageController extends Controller
{
    public function home(){
        return view('pharmaciens.home');
    }

    public function about(){
        return view('pharmaciens.about');
    }

    public function liste(){
        return view('pharmaciens.liste-pharmacien');
    }

    public function inscriptionSuccess(string $numero){

        $inscription = Inscription::where('numero_inscription', $numero)->firstOrFail();

        // Récupérer uniquement le paiement d’inscription initial
        $paiement = Paiement::where('inscription_id', $inscription->id)
            ->where('type', 'inscription')
            ->firstOrFail();

        return view('pharmaciens.inscription-success', [
            'inscription' => $inscription,
            'paiementID' => $paiement->id,
        ]);
    }
}
