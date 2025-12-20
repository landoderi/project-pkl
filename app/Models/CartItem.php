<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    // Tambahkan product_id di sini 👇
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
    ];

    // (Opsional) Jika relasi digunakan
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
