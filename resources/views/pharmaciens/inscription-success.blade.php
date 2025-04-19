@extends('layouts.pharmaciens')

@section('title', 'Inscription réussie - '.$settings->site_name)

@section('content')

    <!-- Breadcrumb Section Start -->
    <div class="breadcrumb-area bg-ash d-none">
        <div class="breadcrumb-wrap bg-f bg-2 position-relative z-1 round-20">
            <img src="{{asset('assets/img/breadcrumb/br-shape-1.png')}}" alt="Shape" class="br-shape-one position-absolute z-1 bounce">
            <img src="{{asset('assets/img/breadcrumb/br-shape-2.png')}}" alt="Shape" class="br-shape-two position-absolute z-1 rotate">
            <div class="container">
                <h2 class="section-title fw-bold text-white text-center mb-13">Inscription réussie</h2>
                <ul class="br-menu text-center list-unstyled mb-0">
                    <li class="position-relative fs-xx-14 d-inline-block"><a href="{{route('pharmaciens.home')}}">Accueil</a></li>
                    <li class="position-relative fs-xx-14 d-inline-block">Inscription réussie</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Breadcrumb Section End -->


    <!-- Online Payment Section Start -->
    <div class="container style-one ptb-120">
        <div class="form-box style-one round-20">
            <h2 class="fs-36 text-title fw-extrabold mb-35">Inscription réussie à l'Ordre National des Pharmaciens de Guinée</h2>
            <h4 class="fs-xx-14 mb-28 aos-init aos-animate">Félicitations ! Votre inscription a été réussie.</h4>
            <h6>
                Merci pour votre inscription auprès de l'Ordre National des Pharmaciens de Guinée.
                <br><br>
                Nous avons bien reçu vos informations, et celles-ci sont actuellement en cours de validation par le président.
                <br><br>
                Un e-mail contenant les détails de votre inscription ainsi que les prochaines étapes a été envoyé à l'adresse e-mail suivante : <strong>{{$email}}</strong>.
            </h6>
            <!-- Affichage du numéro d'inscription -->
        </div>
    </div>

@endsection

@section('scripts')

@endsection
