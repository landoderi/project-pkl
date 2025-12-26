<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Tampilkan halaman keranjang.
     */
    public function index()
    {
        $cart = $this->cartService->getCart();

        // Eager load agar query efisien
        $cart->load(['items.product.primaryImage']);

        return view('cart.index', compact('cart'));
    }

    /**
     * Tambahkan produk ke keranjang.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $product = Product::findOrFail($request->product_id);

            // Pastikan service menyimpan harga & subtotal
            $this->cartService->addProduct($product, $request->quantity);

            return back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    /**
     * Update jumlah item di keranjang.
     */
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        try {
            $this->cartService->updateQuantity($itemId, $request->quantity);
            return back()->with('success', 'Jumlah item berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui keranjang: ' . $e->getMessage());
        }
    }

    /**
     * Hapus item dari keranjang.
     */
    public function remove($itemId)
    {
        try {
            $this->cartService->removeItem($itemId);
            return back()->with('success', 'Item berhasil dihapus dari keranjang.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus item: ' . $e->getMessage());
        }
    }
}
