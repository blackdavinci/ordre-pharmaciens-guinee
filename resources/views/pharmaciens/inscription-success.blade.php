@extends('layouts.pharmaciens')

@section('title', 'Paiement réussi - '.$settings->site_name)

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
            <h6>Merci de vous être inscrit auprès de l'Ordre National des Pharmaciens de Guinée.
                <br>Nous avons bien reçu vos informations et elles sont actuellement en cours de validation par le président.
            </h6>
            <!-- Affichage du numéro d'inscription -->
            <p><strong>Numéro d'inscription :</strong> <span style="color: #004ca3;">{{ $inscription->numero_inscription }}</span></p>

            <div class="instructions">
            <div class="content">
                <div class="instructions">
                    <h2>Prochaines étapes :</h2>
                    <ul>
                        <li>Votre inscription sera vérifiée par le président de l'Ordre des Pharmaciens.</li>
                        <li>Une fois validée, vous recevrez un e-mail de confirmation avec vos accès.</li>
                        <li>En attendant, vous pouvez consulter la page de <a href="#">suivi de votre inscription</a> pour vérifier l'état de votre demande.</li>
                        <li>Si vous avez des questions, n'hésitez pas à nous contacter à
                            <a href="mailto:{{$settings->support_phone}}">{{$settings->support_email}}</a> ou en appelant le
                            <a href="tel:{{$settings->support_phone}}" class="d-inline-block font-secondary fw-semibold text-title fs-xx-14 hover-text-secondary">{{$settings->support_phone}}</a>.
                        </li>
                    </ul>
                </div>
                <!-- Lien pour télécharger le reçu de paiement -->
                <p>Vous pouvez télécharger votre <a href="{{ route('recu.paiement', ['id' => $paiementID]) }}" class="download-link">reçu de paiement</a>.</p>
            </div>

        </div>
    </div>
    </div>





@endsection

@section('scripts')

@endsection
