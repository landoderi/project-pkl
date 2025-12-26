{{-- ================================================
     FILE: resources/views/partials/product-card.blade.php
     FUNGSI: Komponen kartu produk (disamakan dengan detail produk)
     ================================================ --}}

<div class="card product-detail-card h-100 border-0 shadow-sm position-relative">
    <style>
        .product-detail-card {
            border-radius: 20px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            background-color: #fff;
        }

        .badge-discount {
            font-size: 0.9rem;
            font-weight: 600;
            background-color: #dc3545;
            color: #fff;
            border-radius: 8px;
            padding: 4px 8px;
        }

        .wishlist-btn {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            transition: all 0.25s ease;
        }

        .wishlist-btn:hover {
            background-color: #ffae21;
            color: #fff;
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
    </style>

    {{-- Gambar Produk --}}
    <div class="position-relative">
        <a href="{{ route('catalog.show', $product->slug) }}">
            <img src="{{ $product->image_url }}"
                 class="card-img-top"
                 alt="{{ $product->name }}"
                 style="height: 230px; object-fit: cover; border-radius: 20px 20px 0 0;">
        </a>

        {{-- Badge Diskon --}}
        @if($product->has_discount)
            <span class="badge-discount position-absolute top-0 start-0 m-3">
                -{{ $product->discount_percentage }}%
            </span>
        @endif

        {{-- Wishlist Button (pojok kanan atas, sama dengan show.blade.php) --}}
        @auth
            <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="wishlist-form">
                @csrf
                <button type="submit" class="wishlist-btn">
                    <i class="bi bi-heart{{ auth()->user()?->hasInWishlist($product) ? '-fill text-danger' : '' }}"
                       style="font-size: 1.3rem; line-height: 0;"></i>
                </button>
            </form>
        @endauth
    </div>

    {{-- Isi Card --}}
    <div class="card-body d-flex flex-column">
        {{-- Kategori --}}
        <small class="text-muted mb-1">{{ $product->category->name }}</small>

        {{-- Nama Produk --}}
        <h6 class="card-title mb-2">
            <a href="{{ route('catalog.show', $product->slug) }}"
               class="text-decoration-none text-dark stretched-link">
                {{ Str::limit($product->name, 40) }}
            </a>
        </h6>

        {{-- Harga --}}
        <div class="mt-auto mb-2">
            @if($product->has_discount)
                <small class="text-muted text-decoration-line-through">
                    {{ $product->formatted_original_price }}
                </small><br>
            @endif
            <span class="fw-bold text-primary">{{ $product->formatted_price }}</span>
        </div>

        {{-- Stok --}}
        @if($product->stock <= 5 && $product->stock > 0)
            <small class="text-warning mt-1 d-block">
                <i class="bi bi-exclamation-triangle"></i> Stok tinggal {{ $product->stock }}
            </small>
        @elseif($product->stock == 0)
            <small class="text-danger mt-1 d-block">
                <i class="bi bi-x-circle"></i> Stok Habis
            </small>
        @endif
    </div>

    {{-- Tombol Tambah ke Keranjang --}}
    <div class="card-footer bg-white border-0 pt-0">
        <form action="{{ route('cart.add') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit"
                    class="btn btn-premium btn-sm w-100 rounded-pill"
                    @if($product->stock == 0) disabled @endif>
                <i class="bi bi-cart-plus me-1"></i>
                @if($product->stock == 0)
                    Stok Habis
                @else
                    Tambah ke Keranjang
                @endif
            </button>
        </form>
    </div>
</div>
