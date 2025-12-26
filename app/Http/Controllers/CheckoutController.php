<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class CheckoutController extends Controller
{
    /**
     * Tampilkan halaman checkout.
     */
    public function index()
    {
        $cart = Auth::user()->cart;

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang kosong.');
        }

        return view('checkout.index', compact('cart'));
    }

    /**
     * Proses checkout dan buat order baru.
     */
    public function store(Request $request, OrderService $orderService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        try {
            // ✅ Ambil user yang login
            $user = Auth::user();

            // ✅ Panggil service untuk membuat order
            $order = $orderService->createOrder($user, [
                'shipping_name' => $request->name,
                'shipping_phone' => $request->phone,
                'shipping_address' => $request->address,
            ]);

            return redirect()->route('orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
