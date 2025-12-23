{{-- ================================================
     FILE: resources/views/partials/navbar.blade.php
     FUNGSI: Navigation bar untuk customer (sticky atas tanpa bagian menu ikut scroll)
     ================================================ --}}

<style>
    /* Bagian navbar utama tetap menempel di atas saat scroll */
    .navbar-sticky {
        position: sticky;
        top: 0;
        z-index: 1030;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
    }

    /* Saat di-scroll, beri sedikit efek mengecil */
    .navbar-shrink {
        padding-top: 0.25rem !important;
        padding-bottom: 0.25rem !important;
        background-color: #2d2018 !important;
        transition: all 0.3s ease;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark navbar-sticky" style="background-color: #3b2b1f;">
    <div class="container">
<a class="navbar-brand d-flex align-items-center fw-bold text-uppercase" href="{{ route('home') }}" style="color: #f4d9a0;">
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


        {{-- Mobile Toggle --}}
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Navbar Content --}}
        <div class="collapse navbar-collapse" id="navbarMain">
            {{-- Search Bar (panjang penuh) --}}
            <form class="d-flex mx-auto flex-grow-1 px-3" style="max-width: 100%;" action="{{ route('catalog.index') }}" method="GET">
                <div class="input-group w-100">
                    <input type="text" name="q"
                           class="form-control bg-transparent text-white border-light"
                           placeholder="Search" value="{{ request('q') }}">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            {{-- Right Icons --}}
            <ul class="navbar-nav ms-auto align-items-center">
                {{-- Gear --}}


                {{-- Wishlist --}}
                @auth
                    <li class="nav-item position-relative me-3">
                        <a href="{{ route('wishlist.index') }}" class="nav-link text-light position-relative">
                            <i class="bi bi-heart"></i>
                            @if(auth()->user()->wishlists()->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                      style="font-size: 0.6rem;">
                                    {{ auth()->user()->wishlists()->count() }}
                                </span>
                            @endif
                        </a>
                    </li>
                @else
                    <li class="nav-item position-relative me-3">
                        <a href="{{ route('login') }}" class="nav-link text-light">
                            <i class="bi bi-heart"></i>
                        </a>
                    </li>
                @endauth

                {{-- Cart --}}
                @auth
                    @php
                        $cartCount = auth()->user()->cart?->items()->count() ?? 0;
                    @endphp
                    <li class="nav-item position-relative me-3">
                        <a href="{{ route('cart.index') }}" class="nav-link text-light position-relative">
                            <i class="bi bi-bag"></i>
                            @if($cartCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                      style="font-size: 0.6rem;">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                @else
                    <li class="nav-item position-relative me-3">
                        <a href="{{ route('login') }}" class="nav-link text-light">
                            <i class="bi bi-bag"></i>
                        </a>
                    </li>
                @endauth

                {{-- Profil User --}}
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center text-white"
                           href="#" id="userDropdown" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->avatar_url }}"
                                 class="rounded-circle me-2"
                                 width="32" height="32"
                                 alt="{{ auth()->user()->name }}">
                            <span class="d-none d-lg-inline">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i> Profil Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('orders.index') }}">
                                    <i class="bi bi-bag me-2"></i> Pesanan Saya
                                </a>
                            </li>
                            @if(auth()->user()->isAdmin())
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-primary" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i> Admin Panel
                                    </a>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    {{-- Jika belum login --}}
                    <li class="nav-item">
                        <a class="nav-link text-light" href="{{ route('login') }}">Masuk</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm ms-2" href="{{ route('register') }}">Daftar</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- Lapisan bawah: menu kategori (tidak ikut scroll) --}}
<div style="background-color: #ffffff;">
    <div class="container">
        <ul class="nav justify-content-center text-uppercase fw-semibold py-2">
            <li class="nav-item mx-3"><a class="nav-link text-dark" href="#">Cappuccino</a></li>
            <li class="nav-item mx-3"><a class="nav-link text-dark" href="#">Cafe Mocha</a></li>
            <li class="nav-item mx-3"><a class="nav-link text-dark" href="#">Iced Coffee</a></li>
            <li class="nav-item mx-3"><a class="nav-link text-dark" href="#">Macchiato</a></li>
            <li class="nav-item mx-3"><a class="nav-link text-dark" href="#">Cortado</a></li>
            <li class="nav-item mx-3"><a class="nav-link text-dark" href="#">Cafe au Lait</a></li>
            <li class="nav-item mx-3"><a class="nav-link text-dark" href="#">Blogs</a></li>
        </ul>
    </div>
</div>

{{-- JS untuk efek mengecil saat scroll --}}
<script>
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar-sticky');
        if (window.scrollY > 30) {
            navbar.classList.add('navbar-shrink');
        } else {
            navbar.classList.remove('navbar-shrink');
        }
    });
</script>
