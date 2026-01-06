@extends('layouts.app')

@section('title', 'Menunggu Pembayaran')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body py-5">
                    <div class="display-1 mb-3 text-warning">⏳</div>

                    <h4 class="fw-bold mb-3">
                        Pembayaran Belum Selesai
                    </h4>

                    <p class="text-muted mb-4">
                        Pembayaran untuk pesanan
                        <strong>#{{ $order->order_number }}</strong><br>
                        masih menunggu konfirmasi dari sistem pembayaran.
                    </p>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary px-4">
                            🔍 Lihat Detail Pesanan
                        </a>
                        <a href="{{ route('catalog.index') }}" class="btn btn-light border px-4">
                            🏠 Kembali ke Katalog
                        </a>
                    </div>
                </div>
            </div>

            <p class="text-muted small mt-3">
                Pastikan kamu menyelesaikan pembayaran sebelum batas waktu habis.
            </p>

        </div>
    </div>
</div>
@endsection
