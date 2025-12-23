<?php
// app/Http/Controllers/Admin/CategoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori.
     */
    public function index()
    {
        // Mengambil data kategori dengan pagination.
        // withCount('products'): Menghitung jumlah produk di setiap kategori.
        // Teknik ini jauh lebih efisien daripada memanggil $category->products->count() di view (N+1 Problem).
        $categories = Category::withCount('products')
            ->latest() // Urutkan dari yang terbaru (created_at desc)
            ->paginate(10); // Batasi 10 item per halaman

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Menyimpan kategori baru ke database.
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:100|unique:categories',
        'description' => 'nullable|string|max:500',
        'image' => 'nullable|image|max:1024',
        'is_active' => 'nullable|boolean',
    ]);

    // Upload gambar jika ada
    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('categories', 'public');
    }

    // Generate slug
    $validated['slug'] = Str::slug($validated['name']);

    // Set default aktif = false jika checkbox tidak dicentang
    $validated['is_active'] = $request->has('is_active') ? 1 : 0;

    Category::create($validated);

    return back()->with('success', 'Kategori berhasil ditambahkan!');
}


    /**
     * Memperbarui data kategori.
     */
public function update(Request $request, Category $category)
{
    $validated = $request->validate([
        'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
        'description' => 'nullable|string|max:500',
        'image' => 'nullable|image|max:1024',
        'is_active' => 'nullable|boolean',
    ]);

    if ($request->hasFile('image')) {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        $validated['image'] = $request->file('image')->store('categories', 'public');
    }

    $validated['slug'] = Str::slug($validated['name']);

    // Pastikan aktif atau tidak tetap tersimpan dengan benar
    $validated['is_active'] = $request->has('is_active') ? 1 : 0;

    $category->update($validated);

    return back()->with('success', 'Kategori berhasil diperbarui!');
}


    /**
     * Menghapus kategori.
     */
    public function destroy(Category $category)
    {
        // 1. Safeguard (Pencegahan)
        // Jangan hapus kategori jika masih ada produk di dalamnya.
        // Ini mencegah produk menjadi "yatim piatu" (orphan data) yang tidak punya kategori.
        if ($category->products()->exists()) {
            return back()->with('error',
                'Kategori tidak dapat dihapus karena masih memiliki produk. Silahkan pindahkan atau hapus produk terlebih dahulu.');
        }

        // 2. Hapus file gambar fisik dari storage
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        // 3. Hapus record dari database
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus!');
    }
}