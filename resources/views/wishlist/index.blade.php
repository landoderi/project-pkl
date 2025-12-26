{{-- resources/views/wishlist/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Wishlist Saya')

@section('content')
<div class="container py-5">
    <h1 class="h3 fw-bold mb-4 text-dark">Wishlist Saya</h1>

    @if ($products->count() > 0)
        <div class="row row-cols-2 row-cols-md-4 g-4">
            @foreach ($products as $product)
                <div class="col">
                    {{-- Komponen kartu produk --}}
                    <x-product-card :product="$product" />
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if (method_exists($products, 'links'))
            <div class="mt-4 d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        @endif
    @else
        {{-- Tampilan jika wishlist kosong --}}
        <div class="text-center py-5 bg-light rounded-3 shadow-sm">
            <div class="mb-3">
                <i class="bi bi-heart text-secondary" style="font-size: 4rem;"></i>
            </div>
            <h3 class="h5 fw-medium text-dark mb-2">Wishlist Kosong</h3>
            <p class="text-muted mb-3">Simpan produk yang kamu suka di sini.</p>
            <a href="{{ route('catalog.index') }}" class="btn btn-primary px-4">
                Mulai Belanja
            </a>
        </div>
    @endif
</div>
@endsection
