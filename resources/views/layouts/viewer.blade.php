<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription ?? 'AeroSenseV2 — Sistem Pemantauan Kualitas Udara Universitas Diponegoro berbasis AI' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'AeroSenseV2' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body>

    {{-- Shared Navbar --}}
    <x-navbar />

    {{-- Main Content --}}
    <main id="main-content">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="footer">
        <div class="container">
            <p class="footer__text">
                &copy; {{ date('Y') }} AeroSense &mdash; Universitas Diponegoro. Sistem Pemantauan Kualitas Udara.
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
