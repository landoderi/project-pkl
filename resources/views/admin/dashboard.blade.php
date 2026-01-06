@extends('layouts.admin')

@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')
<div class="row g-4 mb-4">
    {{-- Stat Cards --}}
    <div class="col-md-3">
        <div class="card shadow-sm p-3 border-start border-4 border-success">
            <div class="fw-semibold text-uppercase text-muted small">Total Pendapatan</div>
            <div class="h4 fw-bold">Rp {{ number_format($stats['total_revenue'],0,',','.') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm p-3 border-start border-4 border-primary">
            <div class="fw-semibold text-uppercase text-muted small">Total Pesanan</div>
            <div class="h4 fw-bold">{{ $stats['total_orders'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm p-3 border-start border-4 border-warning">
            <div class="fw-semibold text-uppercase text-muted small">Pesanan Pending</div>
            <div class="h4 fw-bold">{{ $stats['pending_orders'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm p-3 border-start border-4 border-danger">
            <div class="fw-semibold text-uppercase text-muted small">Stok Hampir Menipis</div>
            <div class="h4 fw-bold">{{ $stats['low_stock'] }}</div>
        </div>
    </div>
</div>

{{-- Chart Penjualan --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">📈 Tren Penjualan {{ now()->year }}</h5>
    </div>
    <div class="card-body">
        <canvas id="salesChart" height="100"></canvas>
    </div>
</div>

{{-- Tabel Recent Orders --}}
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Pesanan Terbaru</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-decoration-none fw-semibold">
                                #{{ $order->order_number }}
                            </a>
                        </td>
                        <td>{{ $order->user->name }}</td>
                        <td>Rp {{ number_format($order->total_amount,0,',','.') }}</td>
                       <td>
    @php
        $badgeClass = match($order->status) {
            'pending'    => 'badge bg-warning text-dark',   // Kuning
            'processing' => 'badge bg-primary',             // Biru
            'completed'  => 'badge bg-success',             // Hijau
            'cancel'     => 'badge bg-danger',              // Merah
            default      => 'badge bg-secondary',           // Abu-abu
        };
    @endphp
    <span class="{{ $badgeClass }} px-2 py-1 text-capitalize">
        {{ $order->status }}
    </span>
</td>

                        <td>{{ $order->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            Belum ada pesanan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'Total Penjualan (Rp)',
            data: {!! json_encode($chartData) !!},
            borderColor: 'rgba(54,162,235,1)',
            backgroundColor: 'rgba(54,162,235,0.2)',
            fill: true,
            tension: 0.4,
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context){
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value)
                }
            }
        }
    }
});
</script>
@endpush
