<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class OrderService
{
    /**
     * Membuat Order baru dari Keranjang belanja.
     *
     * Langkah-langkah:
     * 1. Validasi stok & hitung total
     * 2. Buat order (header)
     * 3. Buat order_items dari cart_items
     * 4. Kurangi stok produk
     * 5. Kosongkan keranjang
     */
    public function createOrder(User $user, array $shippingData): Order
    {
        // 🔹 Ambil data keranjang user
        $cart = $user->cart()->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            throw new Exception("Keranjang belanja kosong.");
        }

        // 🔹 Jalankan semua proses dalam transaksi database
        return DB::transaction(function () use ($user, $cart, $shippingData) {

            // === A. VALIDASI STOK & HITUNG TOTAL ===
            $totalAmount = 0;

            foreach ($cart->items as $item) {
                // Pastikan stok mencukupi
                if ($item->quantity > $item->product->stock) {
                    throw new Exception("Stok produk {$item->product->name} tidak mencukupi.");
                }

                // Hitung total (pakai floatval agar aman)
                $totalAmount += floatval($item->product->price) * intval($item->quantity);
            }

            // === B. BUAT HEADER ORDER ===
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'shipping_name' => $shippingData['shipping_name'] ?? $user->name,
                'shipping_address' => $shippingData['shipping_address'] ?? '-',
                'shipping_phone' => $shippingData['shipping_phone'] ?? '-',
                'total_amount' => $totalAmount,
            ]);

            // === C. PINDAHKAN ITEMS KE ORDER ITEMS ===
            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name, // snapshot data
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->product->price * $item->quantity,
                ]);

                // === D. KURANGI STOK PRODUK ===
                $item->product->decrement('stock', $item->quantity);
            }

            // === E. HAPUS ISI KERANJANG ===
            $cart->items()->delete();

            // (Opsional) Reset total keranjang
            // $cart->update(['total' => 0]);

            // === F. Kembalikan Order ===
            return $order->load('items.product'); // muat relasi agar langsung bisa ditampilkan
        });
    }
}
