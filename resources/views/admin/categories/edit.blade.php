@extends('layouts.admin')

@section('page-title', 'Edit Kategori')

@section('content')
<div class="container my-4">
    <h1 class="mb-4">Edit Kategori</h1>

    {{-- Notifikasi sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Notifikasi error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Nama kategori --}}
        <div class="mb-3">
            <label for="name" class="form-label">Nama Kategori</label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $category->name) }}"
                   required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Gambar --}}
        <div class="mb-3">
            <label for="image" class="form-label">Gambar Kategori (Opsional)</label>
            <input type="file"
                   name="image"
                   id="image"
                   class="form-control @error('image') is-invalid @enderror"
                   accept="image/*">
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if($category->image)
                <div class="mt-3">
                    <p class="mb-1 text-muted small">Gambar saat ini:</p>
                    <img src="{{ asset('storage/'.$category->image) }}" alt="Gambar Kategori" width="120" class="rounded border">
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
