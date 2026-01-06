<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\MidtransService;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan milik user yang sedang login.
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
    public function show(Order $order, MidtransService $midtrans)
    {
        // 🔒 Pastikan hanya pemilik order yang bisa melihat
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        // 🔄 Load relasi produk
        $order->load(['items.product', 'items.product.primaryImage']);

        // 📦 Default SnapToken dari DB
        $snapToken = $order->snap_token;

        // 💳 Jika belum ada SnapToken, buat baru
        if ($order->payment_status === 'unpaid' && !$snapToken) {
            try {
                $snapToken = $midtrans->createSnapToken($order);

                $order->update([
                    'snap_token' => $snapToken,
                ]);
            } catch (\Exception $e) {
                return view('orders.show', [
                    'order' => $order,
                    'snapToken' => null,
                    'error' => 'Gagal membuat Snap Token: ' . $e->getMessage(),
                ]);
            }
        }

        // ✅ Kirim token ke view agar tombol bayar muncul
        return view('orders.show', [
            'order' => $order,
            'snapToken' => $snapToken,
        ]);
    }
    public function success($orderId)
{
    $order = Order::findOrFail($orderId);
    return view('orders.success', compact('order'));
}

public function pending($orderId)
{
    $order = Order::findOrFail($orderId);
    return view('orders.pending', compact('order'));
}

public function cancel(Order $order)
{
    if (!in_array($order->status, ['pending', 'processing'])) {
        return back()->with('error', 'Pesanan tidak dapat dibatalkan.');
    }

    // Update status ke cancelled
    $order->update(['status' => 'cancelled']);

    // (Opsional) kembalikan stok produk
    foreach ($order->items as $item) {
        $item->product->increment('stock', $item->quantity);
    }

    return redirect()->route('orders.show', $order->id)
        ->with('success', 'Pesanan berhasil dibatalkan.');
}

}
