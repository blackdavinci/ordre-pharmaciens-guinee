<!-- Navbar Center Start -->
<div class="navbar-center style-one bg-ash d-none d-lg-block">
    <div class="container style-one">
        <div class="row align-items-center">
            <div class="col-xl-3 col-lg-2 col-5">
                <a href="{{route('pharmaciens.home')}}" class="logo">
                    <img src="{{asset('storage/'.$settings->logo)}}" alt="Logo" class="logo-light" width="60%"}}>
                </a>
            </div>
            <div class="col-xl-9 col-lg-10 col-7">
                <div class="contact-card-wrap style-two d-none d-lg-flex flex-wrap align-items-center justify-content-lg-end ">
                    <div class="contact-card d-flex flex-wrap align-items-center">
                        <div class="contact-icon d-flex flex-column align-items-center justify-content-center rounded-circle bg-white transition">
                            <img src="{{asset('assets/img/icons/phone-blue.svg')}}" alt="Icon" class="transition">
                        </div>
                        <div class="contact-info">
                            <span class="d-block fs-15 fs-xx-14">Support Téléphone</span>
                            <a href="tel:{{$settings->support_phone}}" class="d-inline-block font-secondary fw-semibold text-title fs-xx-14 hover-text-secondary">
                                {{$settings->support_phone}}
                            </a>
                        </div>
                    </div>
                    <div class="contact-card d-flex flex-wrap align-items-center">
                        <div class="contact-icon d-flex flex-column align-items-center justify-content-center rounded-circle bg-white transition">
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
                        <div class="contact-icon d-flex flex-column align-items-center justify-content-center rounded-circle bg-white transition">
                            <img src="assets/img/icons/pin-blue.svg" alt="Icon" class="transition">
                        </div>
                        <div class="contact-info">
                            <span class="d-block fs-15 fs-xx-14">Visit Us On</span>
                            <p class="font-secondary fw-semibold text-title fs-xx-14 mb-0">245 14h Street, Torento, Canada</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Navbar Center End -->
