@extends('layouts.app')

@section('title', 'Daftar Pesanan')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4 text-center">🧾 Daftar Pesanan Saya</h2>

    @if ($orders->isEmpty())
        <div class="text-center py-5">
            <p class="text-muted mb-3">Kamu belum memiliki pesanan.</p>
            <a href="{{ route('catalog.index') }}" class="btn btn-primary">
                <i class="bi bi-bag"></i> Mulai Belanja
            </a>
        </div>
    @else
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Kode Pesanan</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                        <th>Total</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $order->order_number }}</strong></td>

                            {{-- Status pengiriman --}}
                            <td>
                                @php
                                    $statusColor = match($order->status) {
                                        'pending' => 'warning',
                                        'processing' => 'primary',
                                        'shipped' => 'info',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">{{ ucfirst($order->status) }}</span>
                            </td>

                            {{-- Status pembayaran --}}
                            <td>
                                @php
                                    $payColor = match($order->payment_status) {
                                        'paid' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        'unpaid' => 'secondary',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $payColor }}">{{ ucfirst($order->payment_status) }}</span>
                            </td>

                            <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>

                            <td>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
