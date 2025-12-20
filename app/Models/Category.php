<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    // $fillable: Menentukan kolom mana saja yang BOLEH diisi secara massal
    // (Mass Assignment). Ini fitur keamanan Laravel untuk mencegah
    // user jahat mengisi kolom sensitive.
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
    ];

    // $casts: Mengubah tipe data dari database ke tipe PHP native.
    // Database: TINYINT(1) (0 atau 1)
    // Laravel: boolean (false atau true)
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Relasi One-to-Many: Satu Kategori memiliki BANYAK Produk.
     *
     * - Parameter 1: Model tujuan (Product::class)
     * - Parameter 2 (opsional): Foreign key di tabel products ('category_id')
     * - Parameter 3 (opsional): Local key di tabel categories ('id')
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relasi dengan filter tambahan.
     * Hanya mengambil produk yang aktif dan stok > 0.
     *
     * Contoh penggunaan:
     * $category->activeProducts; // Return Collection of Products
     */
    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class)
                    ->where('is_active', true)
                    ->where('stock', '>', 0);
    }

    // ==================== SCOPES ====================

    /**
     * Local Scope: Helper untuk filter query yang sering dipakai.
     *
     * Cara Pakai: Category::active()->get();
     * $query otomatis dideviasikan oleh Laravel sebagai parameter pertama.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Hanya kategori yang memiliki produk aktif di dalamnya.
     * Menggunakan whereHas() untuk mengecek relasi.
     */
    public function scopeWithProducts($query)
    {
        return $query->whereHas('products', function ($q) {
            $q->where('is_active', true); // Di dalam relasi products
        });
    }

    // ==================== ACCESSORS ====================

    /**
     * Accessor: Membuat "Virtual Attribute" baru.
     * Nama attribute di code: $category->image_url
     * (Konversi dari getImageUrlAttribute -> image_url)
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            // Jika ada gambar, generate full URL ke storage
            return asset('storage/' . $this->image);
        }
        // Jika tidak, tampilkan placeholder
        return asset('images/images.jpg');
    }

    /**
     * Accessor: Menghitung jumlah produk aktif.
     * $category->products_count
     */
    public function getProductsCountAttribute(): int
    {
        // Tips: Untuk performa, sebaiknya gunakan withCount() di controller
        // daripada menghitung manual di sini jika datanya banyak.
        return $this->activeProducts()->count();
    }

    // ==================== BOOT (MODEL EVENTS) ====================

    protected static function boot()
    {
        parent::boot();

        // Event: creating (Sebelum data disimpan ke DB)
        // Kita gunakan untuk auto-generate slug dari name.
        static::creating(function ($category) {
            if (empty($category->slug)) {
                // Contoh: "Elektronik & Gadget" -> "elektronik-gadget"
                $category->slug = Str::slug($category->name);
            }
        });

        // Event: updating (Sebelum data yang diedit disimpan)
        // Cek jika nama berubah, update juga slug-nya.
        static::updating(function ($category) {
            if ($category->isDirty('name')) { // isDirty() = apakah nilai berubah?
                $category->slug = Str::slug($category->name);
            }
        });
    }
}