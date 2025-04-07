<!DOCTYPE html>
<html lang="zxx">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Link of CSS files -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/swiper-bundle.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/aos.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/remixicon.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/flaticon_hinton.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/header.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/footer.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/responsive.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/dark-theme.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}">

    <title>Hinton - Doctors & Hospital Directory HTML Template</title>
    <link rel="icon" type="image/png" href="{{asset('assets/img/favicon.png')}}">

    @yield('head')
</head>
<body>

<!--  Preloader Start -->
{{--@include('partials.pharmaciens.preloader')--}}
<!--  Preloader End -->

<!-- Navbaar Top Start -->
{{--    @include('partials.pharmaciens.top-navbar')--}}
<!-- Navbaar Top End -->

<!-- Navbar Center Start -->
    @include('partials.pharmaciens.center-navbar')
<!-- Navbar Center End -->

<!-- Navbar Area Start -->
    @include('partials.pharmaciens.navbar')
<!-- Navbar Area End-->

@yield('content')

<!-- Footer Start -->
    @include('partials.pharmaciens.footer')
<!-- Footer End -->

<!-- Back to Top -->
<div id="progress-wrap" class="progress-wrap style-one">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path id="progress-path" d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/>
    </svg>
</div>

<!-- Link of JS files -->
<script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/js/megamenu.js')}}"></script>
<script src="{{asset('assets/js/swiper-bundle.min.js')}}"></script>
<script src="{{asset('assets/js/fslightbox.js')}}"></script>
<script src="{{asset('assets/js/gsap.min.js')}}"></script>
<script src="{{asset('assets/js/scrollTrigger.min.js')}}"></script>
<script src="{{asset('assets/js/SplitText.min.js')}}"></script>
<script src="{{asset('assets/js/customEase.js')}}"></script>
<script src="{{asset('assets/js/aos.js')}}"></script>
<script src="{{asset('assets/js/main.js')}}"></script>
<link rel="stylesheet" href="{{asset('assets/js/custom.js')}}">

<!-- Custom JS Add-->
@yield('scripts')
</body>
</html>
