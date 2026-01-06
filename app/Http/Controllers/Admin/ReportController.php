<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\SalesReportExport;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Halaman laporan penjualan
     */
    public function sales(Request $request)
    {
        // 1. Filter tanggal default: bulan ini
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to ?? now()->toDateString();

        // 2. Tabel transaksi detail (paginate)
        $orders = Order::with(['items', 'user'])
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('payment_status', 'paid')
            ->latest()
            ->paginate(20);

        // 3. Summary total orders & revenue
        $summary = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('payment_status', 'paid')
            ->selectRaw('COUNT(*) as total_orders, SUM(total_amount) as total_revenue')
            ->first();

        // 4. Analitik: penjualan per kategori
        $byCategory = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->whereDate('orders.created_at', '>=', $dateFrom)
            ->whereDate('orders.created_at', '<=', $dateTo)
            ->where('orders.payment_status', 'paid')
            ->groupBy('categories.id', 'categories.name')
            ->select('categories.name', DB::raw('SUM(order_items.subtotal) as total'))
            ->orderByDesc('total')
            ->get();

        // 5. Chart: total penjualan per bulan (12 bulan)
        $currentYear = now()->year;

        $monthlySales = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->where('payment_status', 'paid')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month'); // kunci berdasarkan bulan

        $chartLabels = [];
        $chartData   = [];

        // Loop 1-12 bulan
        for ($m = 1; $m <= 12; $m++) {
            $chartLabels[] = date('F', mktime(0, 0, 0, $m, 1)); // Januari, Februari, ...
            $chartData[] = $monthlySales[$m]->total_sales ?? 0;
        }

        return view('admin.reports.sales', compact(
            'orders', 'summary', 'byCategory', 'dateFrom', 'dateTo', 'chartLabels', 'chartData'
        ));
    }

    /**
     * Export Excel
     */
    public function exportSales(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to ?? now()->toDateString();

        return Excel::download(
            new SalesReportExport($dateFrom, $dateTo),
            "laporan-penjualan-{$dateFrom}-sd-{$dateTo}.xlsx"
        );
    }
}
