@extends('layouts.pharmaciens')

@section('title', 'Accueil - Ordre National des Pharmaciens de Guinée')

@section('content')

    <!-- Account Section Start -->
    <div class="account-wrap position-relative z-1 pt-120 pb-90">
        <div class="container style-one">
            <div class="row">

                <div class="col-lg-6">
                    <div class="account-box round-20 mb-30">
                        <h4 class="fs-20 fw-extrabold text-title mb-20">Procédure de demande d'inscription</h4>
                        <p>Avant de procéder à cette demande, veuillez photographier votre photo d'identité, vos diplômes et attestations.</p>
                        <div class="section">
                            <h5>Voici la liste des documents à photographier:</h5>
                            <ul class="list">
                                <li>Lettre manuscrite adressée au président de la section</li>
                                <li>Diplôme de pharmacie (diplômes Guinéens)</li>
                                <li>Équivalence de diplôme de pharmacie (si diplôme étranger délivré par le ministère de l'enseignement supérieur)</li>
                                <li>Extrait de Casier judiciaire</li>
                                <li>Certificat de nationalité</li>
                                <li>Extrait d'acte de naissance</li>
                                <li>Attestation de moralité de l'employeur (si vous êtes salarié(e))</li>
                                <li>Attestation de moralité de l'université (si diplôme Guinéen)</li>
                            </ul>
                        </div>

                        <div class="section">
                            <h5>Documents supplémentaires :</h5>
                            <ul class="list">
                                <li>Pièce d'identité recto/verso (PAS OBLIGATOIRE)</li>
                                <li>Photo d'identité</li>
                            </ul>
                        </div>
                        <a href="{{route('pharmaciens.inscription')}}" type="submit" class="btn style-one w-100 d-block font-secondary fw-bold position-relative z-1 round-10">
                                    <span>S'inscrire à l'Ordre
                                        <img src="{{asset('assets/img/icons/right-arrow-white.svg')}}" alt="Icon" class="transition icon-left">
                                    </span>
                            <img src="{{asset('assets/img/icons/right-arrow-white.svg')}}" alt="Icon" class="transition icon-right">
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Account Section End -->

@endsection

@section('scripts')


@endsection
