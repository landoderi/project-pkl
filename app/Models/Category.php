<?php
// ================================================
// FILE: app/Models/Category.php
// FUNGSI: Model untuk tabel 'categories'
// ================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi massal
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
    ];

    // Casting tipe data
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Relasi One-to-Many: 1 kategori memiliki banyak produk
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relasi dengan filter produk aktif & tersedia
     */
    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class)
            ->where('is_active', true)
            ->where('stock', '>', 0);
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Hanya kategori aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Kategori yang memiliki produk aktif
     */
    public function scopeWithProducts($query)
    {
        return $query->whereHas('activeProducts');
    }

    // ==================== ACCESSORS ====================

    /**
     * Accessor: URL gambar kategori
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && Storage::exists($this->image)) {
            return Storage::url($this->image);
        }

        // Placeholder default
        return asset('images/default-category.jpg');
    }

    /**
     * Accessor: Jumlah produk aktif (untuk tampilan cepat)
     */
    public function getProductsCountAttribute(): int
    {
        return $this->activeProducts()->count();
    }

    // ==================== EVENTS ====================

    protected static function boot()
    {
        parent::boot();

        // Auto generate slug saat membuat kategori
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $baseSlug = Str::slug($category->name);
                $slug = $baseSlug;
                $counter = 1;

                // Pastikan slug unik
                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                $category->slug = $slug;
            }
        });

        // Auto update slug jika nama diubah
        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $baseSlug = Str::slug($category->name);
                $slug = $baseSlug;
                $counter = 1;

                while (static::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                $category->slug = $slug;
            }
        });
    }
}
