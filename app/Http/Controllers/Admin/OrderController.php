<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan (untuk admin).
     * Dapat difilter berdasarkan status (?status=pending).
     */
    public function index(Request $request)
    {
        $orders = Order::with('user') // Eager loading user
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail satu pesanan.
     */
    public function show(Order $order)
    {
        // Eager load relasi untuk efisiensi
        $order->load(['items.product', 'user']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update status pesanan.
     * Jika status menjadi cancelled, stok produk akan otomatis dikembalikan.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        // 🔹 Kembalikan stok jika dibatalkan
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }

        // 🔹 Perbarui status pesanan
        $order->update(['status' => $newStatus]);

        return back()->with('success', "Status pesanan #{$order->order_number} telah diperbarui menjadi {$newStatus}.");
    }
}
