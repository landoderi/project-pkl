{{-- ================================================
     FILE: resources/views/home.blade.php
     FUNGSI: Halaman utama website (versi modern & premium)
     ================================================ --}}

@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<style>
    body {
        background-color: #e7d3b0; /* coklat lembut modern */
        font-family: 'Poppins', sans-serif;
    }

    h1, h2, h3, h4, h5 {
        font-weight: 700;
        color: #ff6f00;
    }

    .hero-section {
        background: linear-gradient(135deg, #3b2b1f 60%, #7b4f28);
        color: #fff;
        border-radius: 0 0 60px 60px;
        overflow: hidden;
    }

    .btn-premium {
        background-color: #ffae21;
        border: none;
        color: #fff;
        transition: 0.3s;
    }

    .btn-premium:hover {
        background-color: #b67a4d;
        color: #fff;
    }

    .category-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 20px;
    }

    .category-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.2);
    }

    .product-section {
        background-color: #ffeb7a;
        border-radius: 40px;
        padding: 60px 30px;
    }

    .section-title {
        position: relative;
        display: inline-block;
    }

    .section-title::after {
        content: '';
        display: block;
        width: 60%;
        height: 4px;
        background-color: #b67a4d;
        margin: 8px auto 0;
        border-radius: 2px;
    }

    .promo-card {
        border-radius: 25px;
        transition: all 0.3s ease;
    }

    .promo-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    video {
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }
</style>

{{-- Hero Section --}}
<section class="hero-section py-5 mb-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h1 class="display fw-bold mb-3">
                    Nikmati Pengalaman kopi premium dari<br>
                    <span style="color:#f4d9a0;">Mas Ironi</span> dari Rumah
                </h1>
                <p class="mb-4 text-light">
                    Temukan berbagai produk kopi terbaik dan perlengkapan pilihan.
                    Gratis ongkir untuk pembelian pertama kamu!
                </p>
                <a href="{{ route('catalog.index') }}" class="btn btn-premium btn-lg shadow text-dark">
                    <i class="bi bi-bag me-2"></i>Mulai Belanja
                </a>
            </div>
            <div class="col-lg-6 text-center">
                <video autoplay loop muted playsinline src="images/iron.mp4" type="video/mp4" width="50%"></video>
            </div>
        </div>
    </div>
</section>

{{-- Kategori --}}
<section class="py-5">
    <div class="container text-center">
        <h2 class="section-title mb-4">Kategori Populer</h2>
        <div class="row g-4">
            @foreach($categories as $category)
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('catalog.index', ['category' => $category->slug]) }}"
                       class="text-decoration-none text-dark">
                        <div class="card category-card border-0 shadow-sm text-center h-100 bg-white">
                            <div class="card-body">
                                <img src="{{ $category->image_url }}"
                                     alt="{{ $category->name }}"
                                     class="rounded-circle mb-3 shadow-sm"
                                     width="80" height="80"
                                     style="object-fit: cover;">
                                <h6 class="card-title mb-0 fw-semibold">{{ $category->name }}</h6>
                                <small class="text-muted">{{ $category->products_count }} produk</small>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Produk Unggulan --}}
<section class="product-section my-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title mb-0">Produk Unggulan</h2>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('catalog.index') }}" class="btn btn-outline-dark rounded-pill">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($featuredProducts as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    @include('partials.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Promo Banner --}}
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card promo-card bg-warning text-dark border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <h3 class="fw-bold mb-2">
                            <i class="bi bi-lightning-charge-fill me-2"></i>Flash Sale!
                        </h3>
                        <p>Diskon hingga <strong>50%</strong> untuk produk pilihan terbatas.</p>
                        <a href="#" class="btn btn-dark rounded-pill px-4 mt-2">
                            Lihat Promo
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card promo-card bg-info text-white border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <h3 class="fw-bold mb-2">
                            <i class="bi bi-gift-fill me-2"></i>Member Baru?
                        </h3>
                        <p>Dapatkan <strong>voucher Rp 50.000</strong> untuk pembelian pertama kamu.</p>
                        <a href="{{ route('register') }}" class="btn btn-light rounded-pill px-4 mt-2">
                            Daftar Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Produk Terbaru --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title mb-0">Produk Terbaru</h2>
        </div>

        <div class="row g-4">
            @foreach($latestProducts as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    @include('partials.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
