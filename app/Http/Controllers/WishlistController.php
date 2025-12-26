<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Menampilkan halaman daftar wishlist user.
     */
    public function index()
    {
        $products = auth()->user()->wishlists()
            ->with(['category', 'primaryImage'])
            ->latest('wishlists.created_at')
            ->paginate(12);

        return view('wishlist.index', compact('products'));
    }

    /**
     * Toggle wishlist (AJAX handler).
     * Bisa juga diakses langsung (akan redirect balik, bukan JSON mentah).
     */
    public function toggle(Product $product)
    {
        $user = auth()->user();

        // Cek apakah produk ini ada di daftar wishlist user
        if ($user->hasInWishlist($product)) {
            $user->wishlists()->detach($product->id);
            $added = false;
            $message = 'Produk dihapus dari wishlist.';
        } else {
            $user->wishlists()->attach($product->id);
            $added = true;
            $message = 'Produk ditambahkan ke wishlist!';
        }

        // 🔹 Jika bukan request AJAX (misalnya user akses via browser langsung)
        if (!request()->ajax()) {
            return redirect()->back()->with('success', $message);
        }

        // 🔹 Jika request dari AJAX (fetch/axios), kirim JSON
        return response()->json([
            'status' => 'success',
            'added' => $added,
            'message' => $message,
            'count' => $user->wishlists()->count(),
        ]);
    }
}
