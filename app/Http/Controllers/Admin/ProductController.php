<?php
// app/Http/Controllers/Admin/ProductController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk dengan fitur pagination dan filtering.
     */
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['category', 'primaryImage'])
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->when($request->category, fn($q, $cat) => $q->where('category_id', $cat))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::active()->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Form tambah produk baru.
     */
    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Simpan produk baru ke database.
     * Otomatis aktif dan tampil di halaman utama.
     */
public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'name'        => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'price'       => 'required|numeric|min:0',
        'stock'       => 'required|integer|min:0',
        'weight'      => 'required|numeric|min:0',
        'images.*'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    // ✅ Generate slug unik
    $baseSlug = \Illuminate\Support\Str::slug($validated['name']);
    $slug = $baseSlug;
    $counter = 1;
    while (\App\Models\Product::where('slug', $slug)->exists()) {
        $slug = $baseSlug . '-' . $counter++;
    }

    $validated['slug'] = $slug;
    $validated['is_active'] = true;
    $validated['is_featured'] = true;

    DB::beginTransaction();
    try {
        $product = \App\Models\Product::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        DB::commit();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan dan tampil di halaman utama!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', 'Gagal menyimpan produk: ' . $e->getMessage());
    }
}


    /**
     * Detail produk.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'images', 'orderItems']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Form edit produk.
     */
    public function edit(Product $product): View
    {
        $categories = Category::active()->orderBy('name')->get();
        $product->load('images');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update data produk.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $product->update($request->validated());

            if ($request->hasFile('images')) {
                $this->uploadImages($request->file('images'), $product);
            }

            if ($request->has('delete_images')) {
                $this->deleteImages($request->delete_images);
            }

            if ($request->has('primary_image')) {
                $this->setPrimaryImage($product, $request->primary_image);
            }

            DB::commit();
            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Hapus produk beserta gambar.
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            $product->delete();
            return redirect()->route('admin.products.index')
                ->with('success', 'Produk dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    // ============================================================
    // ====================== HELPER METHODS ======================
    // ============================================================

    protected function uploadImages(array $files, Product $product): void
    {
        $isFirst = $product->images()->count() === 0;

        foreach ($files as $index => $file) {
            $filename = 'product-' . $product->id . '-' . time() . '-' . $index . '.' . $file->extension();
            $path = $file->storeAs('products', $filename, 'public');

            $product->images()->create([
                'image_path' => $path,
                'is_primary' => $isFirst && $index === 0,
                'sort_order' => $product->images()->count() + $index,
            ]);
        }
    }

    protected function deleteImages(array $imageIds): void
    {
        $images = ProductImage::whereIn('id', $imageIds)->get();
        foreach ($images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
    }

    protected function setPrimaryImage(Product $product, int $imageId): void
    {
        $product->images()->update(['is_primary' => false]);
        $product->images()->where('id', $imageId)->update(['is_primary' => true]);
    }
}
