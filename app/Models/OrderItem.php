<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name', // ✅ tambahkan untuk snapshot nama produk
        'quantity',
        'price',
        'subtotal',
    ];

    /**
     * Relasi ke model Order.
     * Satu OrderItem dimiliki oleh satu Order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke model Product.
     * OrderItem terhubung ke Product asli (opsional).
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
