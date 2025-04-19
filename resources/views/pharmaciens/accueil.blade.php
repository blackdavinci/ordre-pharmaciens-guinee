<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{$settings->site_name}}</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">

    <!-- External Css -->
    <link rel="stylesheet" href="{{asset('assets/css/line-awesome.min.css')}}">

    <!-- Custom Css -->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/main.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/covid.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/custom.css')}}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" href="{{asset('storage/'.$settings->favicon)}}">
    <link rel="apple-touch-icon" href="{{asset('storage/'.$settings->favicon)}}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{asset('storage/'.$settings->favicon)}}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{asset('storage/'.$settings->favicon)}}">

</head>
<body>

<div class="ugf-covid covid-bg">
    <div class="container">
        <div class="row">
            <div class="col">
                <nav class="navbar navbar-expand-md anfra-nav">
                    <a class="navbar-brand" href="{{route('pharmaciens.home')}}">
                        <img src="{{asset('storage/'.$settings->logo_alt)}}" class="main-logo" alt="" width="25%">
                        <img src="{{asset('storage/'.$settings->logo_mobile)}}" class="logo-2" alt="" width="25%">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="las la-bars"></i>
                    </button>

                    <div class="collapse navbar-collapse justify-end col-6" id="navbarSupportedContent">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item d-none">
                                <a class="nav-link" href="#">A propos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-button col-12" href="{{url('/admin')}}">Se connecter</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
    <div class="pt20">
        <div class="container-md">
            <div class="row">
                <div class="col-xl-6 col-lg-6 relative">
                    <div class="covid-tips">
                        <h3>Procédure d'inscription à l'ONPG</h3>
                        <br>
                        <p>Avant de procéder à cette demande, veuillez photographier votre photo d'identité, vos diplômes et attestations.</p>
                        <br>
                        <div class="tips">
                            <div class="icon">
                                <img src="assets/images/icon/docs.png" class="img-fluid" alt="">
                            </div>
                            <div class="content">
                                <h4>Voici la liste des documents à numériser :</h4>
                                <ul class="list">
                                    <li>Une Pièce d'identité (Carte d'identité Nationale ou Passeport)</li>
                                    <li>Une Photo d'identité</li>
                                    <li>Une Lettre manuscrite adressée au président de la section</li>
                                    <li>Le Diplôme de pharmacie</li>
                                    <li>Extrait de Casier judiciaire</li>
                                    <li>Extrait d'acte de naissance</li>
                                    <li>Attestation de moralité de l'université</li>
                                </ul>
                            </div>
                        </div>
                        <div class="tips">
                            <div class="icon">
                                <img src="assets/images/icon/add-task.png" class="img-fluid" alt="">
                            </div>
                            <div class="content">
                                <h4>Documents supplémentaires</h4>
                                <ul class="list">
                                    <li>Certificat de nationalité (si vous êtes guinéen)</li>
                                    <li>Équivalence de diplôme de pharmacie (si vous avez un diplôme étranger)</li>
                                    <li>Attestation de moralité de l'employeur (si vous êtes salarié(e))</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 offset-xl- col-lg-5 offset-lg- p-sm-0">
                    <div class="covid-wrap">
                        <div class="ugf-covid ugf-contact">
                            <div class="pt70 pb70">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg- offset-lg-2 p-sm-0">
                                            <div class="contact-form-wrap">
                                                <h4>Saisissez votre e-mail pour commencer.</h4>
                                                <p class="d-none">We always want to hear from you! Let us know how we can best help you and we'll do our very best.</p>
                                                <!-- Affichage du message d'erreur pour l'email -->
                                                @if ($errors->has('pending'))
                                                    <div class="alert alert-warning">
                                                        {!! $errors->first('pending') !!}
                                                    </div>
                                                @endif
                                                @if ($errors->has('approved'))
                                                    <div class="alert alert-success">
                                                        {!! $errors->first('approved') !!}
                                                    </div>
                                                @endif
                                                @if ($errors->has('rejected'))
                                                    <div class="alert alert-danger">
                                                        {!! $errors->first('rejected') !!}
                                                    </div>
                                                @endif
                                                <form action="{{ route('inscription.check') }}" method="POST" id="emailVerificationForm">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-md-12 p-sm-0">
                                                            <div class="form-group">
                                                                <input type="email" name="email" class="form-control" placeholder="monemail@email.com" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button class="btn" type="submit">S'inscrire</button>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="footer">
                    <div class="footer-social d-none">
                        <a href="#"><i class="lab la-facebook-f"></i></a>
                        <a href="#"><i class="lab la-twitter"></i></a>
                        <a href="#"><i class="lab la-linkedin-in"></i></a>
                        <a href="#"><i class="lab la-youtube"></i></a>
                        <a href="#"><i class="lab la-instagram"></i></a>
                    </div>
                    <div class="copyright-text">
                        <p>Copyright © {{date('Y')}} Ordre National des Pharmaciens de Guinée, Tous les droits réservés</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script src="{{asset('assets/js/popper.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap.min.js')}}"></script>

<script src="{{asset('assets/js/map.js')}}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCo_UiZM19FOm6-Vpl42HXNDrpYwGHCzPo"></script>

<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/custom.js')}}"></script>
<script>
    document.getElementById('emailVerificationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const button = this.querySelector('button[type="submit"]');
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Vérification...';

        this.submit();
    });
</script>
</body>
</html>
