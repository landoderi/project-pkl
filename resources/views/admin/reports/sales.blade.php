@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Laporan Penjualan</h2>
</div>

{{-- Filter --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row align-items-end g-3">
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
            </div>
            <div class="col-md-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
                <a href="{{ route('admin.reports.export-sales', request()->all()) }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Summary --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm border-start border-4 border-success">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Total Pendapatan</div>
                <div class="h3 fw-bold text-dark mb-0">
                    Rp {{ number_format($summary->total_revenue ?? 0, 0, ',', '.') }}
                </div>
                <small class="text-muted">Periode ini</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm border-start border-4 border-primary">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Total Transaksi</div>
                <div class="h3 fw-bold text-dark mb-0">
                    {{ number_format($summary->total_orders ?? 0) }}
                </div>
                <small class="text-muted">Order paid</small>
            </div>
        </div>
    </div>
</div>

{{-- Chart Penjualan per Bulan --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">📊 Tren Penjualan Per Bulan</h5>
    </div>
    <div class="card-body">
        <canvas id="salesChart" height="100"></canvas>
    </div>
</div>

{{-- By Category --}}
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Performa Kategori</h5>
            </div>
            <div class="card-body">
                @foreach($byCategory as $cat)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-medium">{{ $cat->name }}</span>
                            <span class="fw-bold">Rp {{ number_format($cat->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar"
                                 style="width: {{ ($cat->total / ($summary->total_revenue ?: 1)) * 100 }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Rincian Transaksi</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="fw-bold text-decoration-none">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <div class="fw-bold">{{ $order->user->name }}</div>
                                    <div class="small text-muted">{{ $order->user->email }}</div>
                                </td>
                                <td class="text-end fw-bold">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    Tidak ada data penjualan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                {{ $orders->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'Total Penjualan (Rp)',
            data: {!! json_encode($chartData) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
            borderRadius: 5,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: function(context) {
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
