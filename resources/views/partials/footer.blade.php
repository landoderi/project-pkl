{{-- ================================================
     FILE: resources/views/partials/footer.blade.php
     FUNGSI: Footer website
     ================================================ --}}

<footer class="bg-secondry text-light pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            {{-- Brand & Description --}}
            <div class="col-lg-4 col-md-6">
                <h5 class="text-white mb-3">
                    <a class="navbar-brand d-flex align-items-center fw-bold text-uppercase"  style="color: #f4d9a0;">
    {{-- SVG Logo Kopi --}}
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="45" height="45" fill="none" stroke="#f4d9a0" stroke-width="2" class="me-2">
        <path d="M16 24h32v14a12 12 0 0 1-12 12H28a12 12 0 0 1-12-12V24z" fill="#d2b48c"/>
        <path d="M48 24h4a6 6 0 0 1 0 12h-4" fill="none" stroke="#f4d9a0"/>
        <path d="M20 10c0 3 4 3 4 6s-4 3-4 6" stroke="#f4d9a0" stroke-linecap="round"/>
        <path d="M32 10c0 3 4 3 4 6s-4 3-4 6" stroke="#f4d9a0" stroke-linecap="round"/>
        <path d="M44 10c0 3 4 3 4 6s-4 3-4 6" stroke="#f4d9a0" stroke-linecap="round"/>
    </svg>

    {{-- Teks Brand --}}
    <div class="d-flex flex-column lh-1">
        <span style="font-size: 0.9rem; letter-spacing: 1px;">KOPI SUSU</span>
        <span style="font-size: 1.3rem; font-weight: 700; color: #ffae21;">KEKINIAN</span>
    </div>
</a>
                </h5>
                <p class="text-secondary">
                    Toko online terpercaya dengan berbagai produk berkualitas.
                    Belanja mudah, aman, dan nyaman.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-secondary fs-5"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-secondary fs-5"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-secondary fs-5"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-secondary fs-5"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="col-lg-2 col-md-6">
                <h6 class="text-white mb-3">Menu</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ route('catalog.index') }}" class="text-secondary text-decoration-none">
                            Katalog Produk
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-secondary text-decoration-none">Tentang Kami</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-secondary text-decoration-none">Kontak</a>
                    </li>
                </ul>
            </div>

            {{-- Help --}}
            <div class="col-lg-2 col-md-6">
                <h6 class="text-white mb-3">Bantuan</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="#" class="text-secondary text-decoration-none">FAQ</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-secondary text-decoration-none">Cara Belanja</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-secondary text-decoration-none">Kebijakan Privasi</a>
                    </li>
                </ul>
            </div>

            {{-- Contact --}}
            <div class="col-lg-4 col-md-6">
                <h6 class="text-white mb-3">Hubungi Kami</h6>
                <ul class="list-unstyled text-secondary">
                    <li class="mb-2">
                        <i class="bi bi-geo-alt me-2"></i>
                        Jl. ai No. 123, Bandung
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-telephone me-2"></i>
                        (022) 123-4567
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-envelope me-2"></i>
                        info@tokoonline.com
                    </li>
                </ul>
            </div>
        </div>

        <hr class="my-4 border-secondary">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-secondary mb-0 small">
                    &copy; {{ date('Y') }} TokoOnline. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                <img src="{{ asset('images/4.png') }}" alt="Payment Methods" height="100">
                <img src="{{ asset('images/7.png') }}" alt="Payment Methods" height="100">
                <img src="{{ asset('images/6.png') }}" alt="Payment Methods" height="100">
            </div>
        </div>
    </div>
</footer>