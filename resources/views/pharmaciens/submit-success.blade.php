<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{$settings->site_name}} - Inscription soumise avec succès</title>

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

<div class="ugf-covid ugf-contact">
    <div class="container">
        <div class="row">
            <div class="col">
                <nav class="navbar navbar-expand-md anfra-nav">
                    <a class="navbar-brand" href="{{route('pharmaciens.home')}}">
                        <img src="{{asset('storage/'.$settings->logo)}}" class="logo-2" alt="" width="25%">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="las la-bars"></i>
                    </button>

                    <div class="collapse navbar-collapse justify-end col-6" id="navbarSupportedContent">
                        <ul class="navbar-nav ml-auto">

                            <li class="nav-item">
                                <a class="nav-link nav-button" href="{{url('/admin')}}">Se connecter</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <div class="">
        <div class="container">
            <div class="row">

                <div class="col-lg-8 offset-lg-2 p-sm-0">
                    <div class="contact-form-wrap">
                        <div class="covid-test-wrap test-step thankyou-sec">
                            <div class="test-progress">
                                <img src="{{asset('assets/images/big-green-check.png')}}" class="img-fluid" alt="">
                            </div>
                            <h3>Inscription soumise</h3>
                            <h6 class="fs-xx-14 mb-28 aos-init aos-animate">Félicitations ! Votre inscription a été réussie.</h6>
                            <p>
                                Un e-mail contenant les détails de votre inscription ainsi que les prochaines étapes a été envoyé à l'adresse e-mail suivante : <strong>{{$email}}</strong>
                            </p>

                            <a href="{{route('pharmaciens.home')}}" class="button-reload">Retour à l'accueil</a>
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
                    <div class="footer-social">
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

<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/custom.js')}}"></script>
</body>
</html>
