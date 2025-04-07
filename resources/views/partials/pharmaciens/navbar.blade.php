<!-- Navbar Area Start -->
<div class="navbar-area style-one bg-ash" id="navbar">
    <div class="container style-one">
        <div class="navbar-wrapper d-flex justify-content-between align-items-center p-0">
            <a href="{{route('pharmaciens.home')}}" class="logo d-lg-none">
                <img src="{{asset('assets/img/logo.png')}}" alt="Logo" class="logo-light">
                <img src="{{asset('assets/img/logo-white.png')}}" alt="Logo" class="logo-dark">
            </a>
            <div class="menu-area me-auto">
                <div class="overlay"></div>
                <nav class="menu">
                    <div class="menu-mobile-header">
                        <button type="button" class="menu-mobile-arrow bg-transparent border-0"><i class="ri-arrow-left-s-line"></i></button>
                        <button type="button" class="menu-mobile-close bg-transparent border-0"><i class="ri-close-line"></i></button>
                    </div>
                    <ul class="menu-section p-0 mb-0">
                        <li><a href="{{route('pharmaciens.home')}}">Accueil</a></li>
                        <li><a href="{{route('pharmaciens.about')}}">A propos</a></li>

                    </ul>
                </nav>
            </div>
            <div class="other-options d-flex flex-wrap align-items-center justify-content-end">
                <div class="option-item d-lg-none">
                    <div class="contact-card-btn">
                        <button class="dropdown-toggle d-flex align-items-center position-relative bg-transparent border-0 ms-auto transition" type="button" data-bs-toggle="dropdown" aria-expanded="true">
                            <img src="{{asset('assets/img/dashboard/icons/dot.svg')}}" alt="Dot Icon" class="action-btn">
                        </button>
                        <div class="dropdown-menu top-1 border-0 round-5">
                            <div class="contact-card-wrap style-two d-flex flex-wrap">
                                <div class="contact-card d-flex flex-wrap align-items-center">
                                    <div class="contact-icon d-flex flex-column align-items-center justify-content-center rounded-circle bg-ash transition">
                                        <img src="{{asset('assets/img/icons/phone-blue.svg')}}" alt="Icon" class="transition">
                                    </div>
                                    <div class="contact-info">
                                        <span class="d-block fs-15 fs-xx-14">Support Téléphone</span>
                                        <a href="tel:{{$settings->support_phone}}" class="d-inline-block font-secondary fw-semibold text-title fs-xx-14 hover-text-secondary">{{$settings->support_phone}}</a>
                                    </div>
                                </div>
                                <div class="contact-card d-flex flex-wrap align-items-center">
                                    <div class="contact-icon d-flex flex-column align-items-center justify-content-center rounded-circle bg-ash transition">
                                        <img src="{{asset('assets/img/icons/mail-blue.svg')}}" alt="Icon" class="transition">
                                    </div>
                                    <div class="contact-info">
                                        <span class="d-block fs-15 fs-xx-14">Support E-mail</span>
                                        <a href="" class="d-inline-block font-secondary fw-semibold text-title fs-xx-14 hover-text-secondary">
                                            <span class="__cf_email__">{{$settings->support_email}}</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="contact-card d-flex flex-wrap align-items-center d-none">
                                    <div class="contact-icon d-flex flex-column align-items-center justify-content-center rounded-circle bg-ash transition">
                                        <img src="{{asset('assets/img/icons/pin-blue.svg')}}" alt="Icon" class="transition">
                                    </div>
                                    <div class="contact-info">
                                        <span class="d-block fs-15 fs-xx-14">Visit Us On</span>
                                        <p class="font-secondary fw-semibold text-title fs-xx-14 mb-0">245 14h Street, Torento, Canada</p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{route('pharmaciens.inscription')}}" class="btn style-two font-secondary fw-semibold position-relative z-1 round-10">
                                        <span>S'inscrire à l'ONPG
                                            <img src="{{asset('assets/img/icons/right-arrow-white.svg')}}" alt="Icon" class="transition icon-left">
                                        </span>
                                <img src="{{asset('assets/img/icons/right-arrow-white.svg')}}" alt="Icon" class="transition icon-right">
                            </a>
                        </div>
                    </div>
                </div>

                <div class="option-item d-lg-inline-block">
                    <a href="" class="d-none header-link font-secondary fw-semibold position-relative d-inline-block text-title hover-text-secondary link-hover-secondary transition">
                        <i class="ri-account-circle-line"></i>
                        <span class="d-none d-lg-inline-block">Se connecter</span>
                    </a>
                </div>
                <div class="option-item d-lg-none me-0">
                    <button type="button" class="menu-mobile-trigger">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
                <div class="option-item d-none d-lg-block">
                    <a href="{{route('pharmaciens.inscription')}}" class="btn style-two font-secondary fw-semibold position-relative z-1 round-10">
                                <span>S'inscrire à l'ONPG
                                    <img src="{{asset('assets/img/icons/right-arrow-white.svg')}}" alt="Icon" class="transition icon-left">
                                </span>
                        <img src="{{asset('assets/img/icons/right-arrow-white.svg')}}" alt="Icon" class="transition icon-right">
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Navbar Area End-->
