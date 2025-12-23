<?php
// ================================================
// FILE: app/Http/Controllers/HomeController.php
// FUNGSI: Menangani halaman utama website
// ================================================

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman beranda.
     */
    public function index()
    {
        // ================================================
        // AMBIL DATA KATEGORI
        // ================================================
$categories = Category::query()
    ->active()
    ->withCount(['activeProducts' => function($q) {
        $q->where('is_active', true)
          ->where('stock', '>', 0);
    }])
    ->orderByDesc('active_products_count') // kategori dengan produk di atas
    ->orderBy('name')
    ->take(6)
    ->get();


        // ================================================
        // PRODUK UNGGULAN
        // ================================================
        $featuredProducts = Product::query()
            ->with(['category', 'primaryImage'])
            ->active()
            ->inStock()
            ->featured()
            ->latest()
            ->take(8)
            ->get();

        // ================================================
        // PRODUK TERBARU
        // ================================================
        $latestProducts = Product::query()
            ->with(['category', 'primaryImage'])
            ->active()
            ->inStock()
            ->latest()
            ->take(8)
            ->get();

        // ================================================
        // KIRIM DATA KE VIEW
        // ================================================
        return view('home', compact(
            'categories',
            'featuredProducts',
            'latestProducts'
        ));
    }
}
