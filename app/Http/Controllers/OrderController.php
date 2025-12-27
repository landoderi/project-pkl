<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\MidtransService;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan milik user.
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['items.product'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail satu pesanan.
     */
    public function show(Order $order)
    {
        // Pastikan hanya pemilik order yang bisa melihatnya
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        // Load relasi produk + gambar utama
        $order->load(['items.product', 'items.product.primaryImage']);

        // Siapkan Snap Token Midtrans (kalau ada)
        $snapToken = $order->snap_token;

        // Jika belum ada token, buat baru
        if (!$snapToken) {
            try {
    $midtrans = new MidtransService();
    $snapToken = $midtrans->createSnapToken($order);

    dd($snapToken); // <--- tambahkan ini sementara untuk tes
    $order->update(['snap_token' => $snapToken]);
} catch (\Exception $e) {
    return view('orders.show', [
        'order' => $order,
        'snapToken' => null,
        'error' => 'Gagal membuat Snap Token: ' . $e->getMessage(),
    ]);
}

        }

        // Pastikan $snapToken SELALU dikirim ke view
        return view('orders.show', [
            'order' => $order,
            'snapToken' => $snapToken,
        ]);
    }
}
