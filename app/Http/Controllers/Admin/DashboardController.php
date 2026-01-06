<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ===== Statistik Utama =====
        $stats = [
            'total_revenue'  => Order::whereIn('status', ['processing','completed'])
                                     ->sum('total_amount'),
            'total_orders'   => Order::count(),
            'pending_orders' => Order::where('status','pending')
                                     ->where('payment_status','paid')
                                     ->count(),
            'low_stock'      => Product::where('stock','<=',5)->count(), // Stok Hampir Menipis
        ];

        // ===== Recent Orders =====
        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        // ===== Chart Penjualan per Bulan =====
        $sales = Order::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->where('payment_status','paid')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total','month');

        $chartLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $chartData   = [];
        foreach(range(1,12) as $m){
            $chartData[] = $sales[$m] ?? 0;
        }

        return view('admin.dashboard', compact('stats','recentOrders','chartLabels','chartData'));
    }
}
