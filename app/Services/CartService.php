<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Ambil atau buat keranjang aktif.
     */
    public function getCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }

        $sessionId = Session::getId();
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    /**
     * Tambahkan produk ke keranjang.
     */
    public function addProduct(Product $product, int $quantity = 1): void
    {
        $cart = $this->getCart();

        $existingItem = $cart->items()->where('product_id', $product->id)->first();

        // Pastikan tidak melebihi stok
        if ($quantity > $product->stock) {
            throw new \Exception("Stok tidak mencukupi. Maksimal: {$product->stock}");
        }

        if ($existingItem) {
            // Produk sudah ada di keranjang → update jumlah dan subtotal
            $newQuantity = $existingItem->quantity + $quantity;

            if ($newQuantity > $product->stock) {
                throw new \Exception("Stok tidak mencukupi. Maksimal: {$product->stock}");
            }

            $existingItem->update([
                'quantity' => $newQuantity,
                'price' => $product->price, // pastikan harga tersimpan
                'subtotal' => $product->price * $newQuantity,
            ]);
        } else {
            // Produk baru → buat item baru
            $cart->items()->create([
                'product_id' => $product->id,
                'price' => $product->price, // wajib!
                'quantity' => $quantity,
                'subtotal' => $product->price * $quantity,
            ]);
        }

        // Update waktu terakhir aktif
        $cart->touch();
    }

    /**
     * Update jumlah item di keranjang.
     */
    public function updateQuantity(int $itemId, int $quantity): void
    {
        $item = CartItem::findOrFail($itemId);
        $product = $item->product;

        $this->verifyCartOwnership($item->cart);

        if ($quantity <= 0) {
            $item->delete();
            return;
        }

        if ($quantity > $product->stock) {
            throw new \Exception("Stok tidak mencukupi. Tersisa: {$product->stock}");
        }

        // update jumlah + subtotal
        $item->update([
            'quantity' => $quantity,
            'price' => $product->price, // pastikan update harga
            'subtotal' => $product->price * $quantity,
        ]);
    }

    /**
     * Hapus item dari keranjang.
     */
    public function removeItem(int $itemId): void
    {
        $item = CartItem::findOrFail($itemId);
        $this->verifyCartOwnership($item->cart);
        $item->delete();
    }

    /**
     * Gabungkan cart tamu ke user saat login.
     */
    public function mergeCartOnLogin(): void
    {
        $sessionId = Session::getId();
        $guestCart = Cart::where('session_id', $sessionId)->with('items')->first();
        if (!$guestCart) return;

        $userCart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        foreach ($guestCart->items as $item) {
            $existing = $userCart->items()->where('product_id', $item->product_id)->first();

            if ($existing) {
                $existing->increment('quantity', $item->quantity);
                $existing->update([
                    'subtotal' => $existing->price * $existing->quantity,
                ]);
                $item->delete();
            } else {
                $item->update(['cart_id' => $userCart->id]);
            }
        }

        $guestCart->delete();
    }

    /**
     * Pastikan user/guest yang benar mengakses cart.
     */
    private function verifyCartOwnership(Cart $cart): void
    {
        $currentCart = $this->getCart();
        if ($cart->id !== $currentCart->id) {
            abort(403, 'Akses ditolak. Ini bukan keranjang Anda.');
        }
    }
}
