<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'users'           => User::count(),
            'products'        => Product::count(),
            'categories'      => Category::count(),
            'total_orders'    => Order::count(),
            'total_revenue'   => Order::sum('total_amount'),
            'pending_orders'  => Order::where('status', 'pending')->count(),
            'low_stock'       => Product::where('stock', '<', 5)->count(),
        ];

        // Ambil 5 order terbaru
        $recentOrders = Order::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}