<?php
// ========================================
// FILE: routes/web.php
// FUNGSI: Mendefinisikan URL routes aplikasi
// ========================================

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;

// ================================================
// GOOGLE OAUTH ROUTES
// ================================================
// Route ini diakses oleh browser, tidak perlu middleware auth
// ================================================

Route::controller(GoogleController::class)->group(function () {
    // ================================================
    // ROUTE 1: REDIRECT KE GOOGLE
    // ================================================
    // URL: /auth/google
    // Dipanggil saat user klik tombol "Login dengan Google"
    // ================================================
    Route::get('/auth/google', 'redirect')
        ->name('auth.google');

    // ================================================
    // ROUTE 2: CALLBACK DARI GOOGLE
    // ================================================
    // URL: /auth/google/callback
    // Dipanggil oleh Google setelah user klik "Allow"
    // URL ini HARUS sama dengan yang didaftarkan di Google Console!
    // ================================================
    Route::get('/auth/google/callback', 'callback')
        ->name('auth.google.callback');
});

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
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// ========================================
// FILE: routes/web.php
// FUNGSI: Mendefinisikan semua URL route aplikasi
// ========================================
// ================================================
// ROUTE PUBLIK (Bisa diakses siapa saja)
// ================================================
Route::get('/', function () {
    return view('welcome');
});
// ↑ Halaman utama, tidak perlu login

// ================================================
// AUTH ROUTES
// ================================================
// Auth::routes() adalah "shortcut" yang membuat banyak route sekaligus:
// - GET  /login           → Tampilkan form login
// - POST /login           → Proses login
// - POST /logout          → Proses logout
// - GET  /register        → Tampilkan form register
// - POST /register        → Proses register
// - GET  /password/reset  → Tampilkan form lupa password
// - POST /password/email  → Kirim email reset password
// - dll...
// ================================================
Auth::routes();

// ================================================
// ROUTE YANG MEMERLUKAN LOGIN
// ================================================
// middleware('auth') = Harus login dulu untuk akses
// Jika belum login, otomatis redirect ke /login
// ================================================
Route::middleware('auth')->group(function () {
    // Semua route di dalam group ini HARUS LOGIN

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
        ->name('home');
    // ↑ ->name('home') = Memberi nama route
    // Kegunaan: route('home') akan menghasilkan URL /home

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
});

