<?php
// ========================================
// FILE: routes/web.php
// FUNGSI: Mendefinisikan URL routes aplikasi
// ========================================

use Illuminate\Support\Facades\Route;

// Route default (sudah ada)
Route::get('/', function () {
    return view('welcome');
});

// ================================================
// TUGAS: Tambahkan route baru di bawah ini
// ================================================

Route::get('/tentang', function () {
    // ================================================
    // Route::get() = Tangani HTTP GET request
    // '/tentang'   = URL yang akan dihandle
    // function     = Kode yang dijalankan saat URL diakses
    // ================================================

    return view('tentang');
    // ↑ return view('tentang') = Tampilkan file tentang.blade.php
    // ↑ Laravel akan mencari di: resources/views/tentang.blade.php
});
// ================================================
// ROUTE DENGAN PARAMETER DINAMIS
// ================================================
// {nama} adalah parameter yang akan diisi dari URL
// ================================================

Route::get('/sapa/{nama}', function ($nama) {
    // ↑ '/sapa/{nama}' = URL pattern
    // ↑ {nama}         = Parameter dinamis, nilainya dari URL
    // ↑ function($nama) = Parameter diterima di function

    return "Halo, $nama! Selamat datang di Toko Online.";
    // ↑ "$nama" = Variable interpolation (masukkan nilai $nama ke string)
});

// CARA AKSES:
// http://localhost:8000/sapa/Budi
// Output: "Halo, Budi! Selamat datang di Toko Online."

// http://localhost:8000/sapa/Ani
// Output: "Halo, Ani! Selamat datang di Toko Online."
// ================================================
// PARAMETER OPSIONAL DENGAN NILAI DEFAULT
// ================================================

Route::get('/kategori/{nama?}', function ($nama = 'Semua') {
    // ↑ {nama?} = Tanda ? berarti parameter OPSIONAL
    // ↑ $nama = 'Semua' = Nilai default jika parameter tidak diberikan

    return "Menampilkan kategori: $nama";
});

// CARA AKSES:
// http://localhost:8000/kategori
// Output: "Menampilkan kategori: Semua" (menggunakan default)

// http://localhost:8000/kategori/Elektronik
// Output: "Menampilkan kategori: Elektronik"
// ================================================
// ROUTE DENGAN NAMA (NAMED ROUTE)
// ================================================

Route::get('/produk/{id}', function ($id) {
    return "Detail produk #$id";
})->name('produk.detail');
// ↑ ->name('produk.detail') = Memberi nama pada route
// ↑ Nama ini bisa digunakan untuk generate URL di view

// KEGUNAAN DI VIEW (Blade):
// <a href="{{ route('produk.detail', ['id' => 1]) }}">Lihat Produk</a>
// ↑ route() = Helper function untuk generate URL dari nama route
// ↑ ['id' => 1] = Parameter yang dikirim ke route
// ↑ Hasilnya: <a href="/produk/1">Lihat Produk</a>
// ================================================
// ROUTE DENGAN NAMA (NAMED ROUTE)
// ================================================

Route::get('/produk/{id}', function ($id) {
    return "Detail produk #$id";
})->name('produk.detail');
// ↑ ->name('produk.detail') = Memberi nama pada route
// ↑ Nama ini bisa digunakan untuk generate URL di view

// KEGUNAAN DI VIEW (Blade):
// <a href="{{ route('produk.detail', ['id' => 1]) }}">Lihat Produk</a>
// ↑ route() = Helper function untuk generate URL dari nama route
// ↑ ['id' => 1] = Parameter yang dikirim ke route
// ↑ Hasilnya: <a href="/produk/1">Lihat Produk</a>