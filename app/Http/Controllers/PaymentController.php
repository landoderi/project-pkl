<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Callback jika pembayaran sukses
     */
    public function success(Order $order)
    {
        // Update status pembayaran
        if ($order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);
        }

        // Tampilkan halaman sukses
        return view('orders.success', compact('order'));
    }

    /**
     * Callback jika pembayaran masih pending
     */
    public function pending(Order $order)
{
    if ($order->payment_status !== 'pending') {
        $order->update(['payment_status' => 'pending']);
    }

    return view('orders.pending', compact('order'));
}


    /**
     * Callback jika pembayaran gagal
     */
    public function failed(Order $order)
    {
        $order->update([
            'payment_status' => 'failed',
        ]);

        // Redirect ke halaman detail order
        return redirect()
            ->route('orders.show', $order)
            ->with('error', 'Pembayaran gagal. Silakan coba lagi.');
    }
}
