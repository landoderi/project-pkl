<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori dengan caching dan pagination.
     */
    public function index(Request $request)
    {
        // Ambil data kategori dari cache
        $allCategories = Cache::remember('global_categories_data', 3600, function () {
            return Category::select('id', 'name', 'slug', 'is_active', 'image', 'created_at')
                ->withCount('products')
                ->latest()
                ->get(); // pakai get() agar bisa manual pagination
        });

        // Buat paginator manual
        $perPage = 10;
        $page = $request->get('page', 1);
        $categories = new LengthAwarePaginator(
            $allCategories->forPage($page, $perPage),
            $allCategories->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Menyimpan kategori baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'image' => 'nullable|image|max:1024',
            'is_active' => 'boolean',
        ]);

        // Upload gambar jika ada
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        // Generate slug otomatis
        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);

        // Hapus cache lama agar data terbaru muncul
        Cache::forget('global_categories_data');

        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Memperbarui data kategori (hanya nama & gambar).
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'image' => 'nullable|image|max:1024',
        ]);

        // Handle upload gambar baru (jika ada)
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $validated['image'] = $request->file('image')->store('categories', 'public');
        } else {
            // Jika tidak ada upload gambar baru, pakai gambar lama
            $validated['image'] = $category->image;
        }

        // Update slug berdasarkan nama baru
        $validated['slug'] = Str::slug($validated['name']);

        // Update hanya nama & gambar
        $category->update([
            'name'  => $validated['name'],
            'slug'  => $validated['slug'],
            'image' => $validated['image'],
        ]);

        // Hapus cache lama agar data terbaru muncul
        Cache::forget('global_categories_data');

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Menghapus kategori.
     */
    public function destroy(Category $category)
    {
        // Cegah hapus kategori jika masih ada produk
        if ($category->products()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk.');
        }

        // Hapus file gambar jika ada
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        // Hapus dari database
        $category->delete();

        // Hapus cache agar daftar kategori terbaru muncul
        Cache::forget('global_categories_data');

        return back()->with('success', 'Kategori berhasil dihapus!');
    }
}
