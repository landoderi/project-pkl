@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Alert sukses / error --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-primary">Edit Produk</h5>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Nama Produk --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama Produk</label>
                            <input type="text"
                                name="name"
                                id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $product->name) }}"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kategori --}}
                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-semibold">Kategori</label>
                            <select name="category_id"
                                id="category_id"
                                class="form-select @error('category_id') is-invalid @enderror"
                                required>
                                <option value="">Pilih Kategori...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Harga --}}
                        <div class="mb-3">
                            <label for="price" class="form-label fw-semibold">Harga (Rp)</label>
                            <input type="number"
                                name="price"
                                id="price"
                                class="form-control @error('price') is-invalid @enderror"
                                value="{{ old('price', $product->price) }}"
                                min="0"
                                required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Stok --}}
                        <div class="mb-3">
                            <label for="stock" class="form-label fw-semibold">Stok</label>
                            <input type="number"
                                name="stock"
                                id="stock"
                                class="form-control @error('stock') is-invalid @enderror"
                                value="{{ old('stock', $product->stock) }}"
                                min="0"
                                required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Berat --}}
                        <div class="mb-3">
                            <label for="weight" class="form-label fw-semibold">Berat (gram)</label>
                            <input type="number"
                                name="weight"
                                id="weight"
                                class="form-control @error('weight') is-invalid @enderror"
                                value="{{ old('weight', $product->weight) }}"
                                min="0"
                                step="0.01"
                                required>
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Gambar Produk (hanya 1 foto) --}}
                        <div class="mb-3">
                            <label for="image" class="form-label fw-semibold">Ganti Foto Produk</label>
                            <input type="file"
                                name="image"
                                id="image"
                                accept="image/*"
                                class="form-control @error('image') is-invalid @enderror">
                            @error('image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            {{-- Preview gambar lama --}}
                            @if($product->primaryImage)
                                <div class="mt-3">
                                    <p class="fw-semibold text-muted mb-2">Foto Sekarang:</p>
                                    <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                        alt="Foto Produk"
                                        width="180"
                                        class="rounded shadow-sm border">
                                </div>
                            @endif
                        </div>

                        {{-- Tombol Simpan --}}
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
