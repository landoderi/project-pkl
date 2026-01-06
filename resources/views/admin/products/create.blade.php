@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">Tambah Produk Baru</h2>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Nama Produk --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Produk</label>
                        <input type="text" name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori</label>
                        <select name="category_id"
                            class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Pilih Kategori...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Harga, Stok, Berat --}}
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Harga (Rp)</label>
                            <input type="number" name="price"
                                class="form-control @error('price') is-invalid @enderror"
                                value="{{ old('price') }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Stok</label>
                            <input type="number" name="stock"
                                class="form-control @error('stock') is-invalid @enderror"
                                value="{{ old('stock') }}" min="0" required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Berat (gram)</label>
                            <input type="number" name="weight"
                                class="form-control @error('weight') is-invalid @enderror"
                                value="{{ old('weight') }}" step="0.01" required>
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Upload Gambar (Hanya 1) --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Upload Foto Produk</label>
                        <input type="file" name="image"
                            accept="image/*"
                            class="form-control @error('image') is-invalid @enderror">
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status Produk --}}
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label fw-bold" for="is_active">Aktifkan Produk</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        Simpan Produk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
