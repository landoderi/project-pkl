@extends('layouts.admin')

@section('page-title', 'Daftar Produk')

@section('content')
<div class="container my-4">
    <h1 class="mb-4">Daftar Produk</h1>

    <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Tambah Produk</a>

    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Harga</th>
                    <th scope="col">Stok</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product->id) }}"
                            class="btn btn-sm btn-warning">Edit</a>

                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                            style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($products->isEmpty())
    <p class="text-muted">Belum ada produk tersedia.</p>
    @endif
</div>
@endsection