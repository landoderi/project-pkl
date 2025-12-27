<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Toko Online') - {{ config('app.name') }}</title>

    {{-- Vite CSS & JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Stack untuk style tambahan dari child --}}
    @stack('styles')
</head>

<body>
    {{-- Navbar --}}
    @include('partials.navbar')

    {{-- Flash Message --}}
    <div class="container mt-3">
        @include('partials.flash-messages')
    </div>

    {{-- Konten Utama --}}
    <main class="min-vh-100">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- Stack Script Tambahan (Midtrans, dll) --}}
    @stack('scripts')

    {{-- Script Wishlist --}}
    <script>
      async function toggleWishlist(productId) {
        try {
          const token = document.querySelector('meta[name="csrf-token"]').content;
          const response = await fetch(`/wishlist/toggle/${productId}`, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": token,
            },
          });

          if (response.status === 401) {
            window.location.href = "/login";
            return;
          }

          const data = await response.json();

          if (data.status === "success") {
            updateWishlistUI(productId, data.added);
            updateWishlistCounter(data.count);
            showToast(data.message);
          }
        } catch (error) {
          console.error("Error:", error);
          showToast("Terjadi kesalahan sistem.", "error");
        }
      }

      function updateWishlistUI(productId, isAdded) {
        const buttons = document.querySelectorAll(`.wishlist-btn-${productId}`);
        buttons.forEach((btn) => {
          const icon = btn.querySelector("i");
          if (isAdded) {
            icon.classList.remove("bi-heart", "text-secondary");
            icon.classList.add("bi-heart-fill", "text-danger");
          } else {
            icon.classList.remove("bi-heart-fill", "text-danger");
            icon.classList.add("bi-heart", "text-secondary");
          }
        });
      }

      function updateWishlistCounter(count) {
        const badge = document.getElementById("wishlist-count");
        if (badge) {
          badge.innerText = count;
          badge.style.display = count > 0 ? "inline-block" : "none";
        }
      }
    </script>
</body>
</html>
