<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk.
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

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Form tambah produk.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Simpan produk baru (1 foto saja).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'weight'      => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Buat slug unik
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $validated['slug'] = $slug;
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = true;

        DB::beginTransaction();

        try {
            // Simpan produk utama
            $product = Product::create($validated);

            // Simpan 1 foto utama jika ada
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');

                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan produk: ' . $e->getMessage());
        }
    }

    /**
     * Form edit produk.
     */
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        $product->load('primaryImage');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update produk (1 foto utama).
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'weight'      => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Update data utama
            $product->update($validated);

            // Jika upload foto baru, hapus lama lalu ganti
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');

                // Hapus gambar lama (jika ada)
                if ($product->primaryImage) {
                    Storage::disk('public')->delete($product->primaryImage->image_path);
                    $product->primaryImage->delete();
                }

                // Simpan gambar baru
                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update produk: ' . $e->getMessage());
        }
    }

    /**
     * Hapus produk & gambar.
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
            if ($product->primaryImage) {
                Storage::disk('public')->delete($product->primaryImage->image_path);
                $product->primaryImage->delete();
            }

            $product->delete();

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}
