<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_status',     // ✅ konsisten dengan OrderService
        'shipping_name',
        'shipping_address',
        'shipping_phone',
        'total_amount',       // ✅ kolom utama total
    ];

    /**
     * Auto-generate order_number setiap kali Order dibuat.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(10));
            }
        });
    }

    /**
     * Relasi ke OrderItem
     * Satu Order bisa punya banyak OrderItem.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi ke User (pembuat order)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
