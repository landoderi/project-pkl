{{-- resources/views/checkout/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-5">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-2xl fw-bold mb-4">Checkout</h1>

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <div class="row g-4">

                {{-- Form Alamat --}}
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <h2 class="h5 fw-semibold mb-3">Informasi Pengiriman</h2>

                            <div class="mb-3">
                                <label class="form-label">Nama Penerima</label>
                                <input type="text" name="name" value="{{ auth()->user()->name }}"
                                       class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea name="address" rows="3" class="form-control" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ringkasan Pesanan --}}
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 sticky-top">
                        <div class="card-body">
                            <h2 class="h5 fw-semibold mb-3">Ringkasan Pesanan</h2>

                            <div class="mb-3" style="max-height: 200px; overflow-y: auto;">
                                @forelse($cart->items as $item)
                                    <div class="d-flex justify-content-between small mb-2">
                                        <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                                        <span class="fw-medium">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-muted small">Keranjang belanja kosong.</p>
                                @endforelse
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total</span>
                                <span>Rp {{ number_format($cart->items->sum('subtotal'), 0, ',', '.') }}</span>
                            </div>

                            <button type="submit"
                                    class="btn btn-primary w-100 mt-4 fw-semibold py-2">
                                Buat Pesanan
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection
