<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'SIOM 2026 – Masuk Sistem Informasi Organisasi Mahasiswa | ITSK RS dr. Soepraoen Malang' }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="siom-guest-body antialiased">
        <div class="siom-guest-container">
            <!-- Header Navbar -->
            <header class="siom-guest-header">
                <a class="siom-nav-brand siom-guest-brand" href="/" wire:navigate>
                    <span class="siom-nav-logo-icon">
                        <i class="bi bi-mortarboard-fill"></i>
                    </span>
                    <span class="siom-nav-brand-text">
                        <span class="siom-nav-title">SOEPRAOEN MUDA</span>
                        <span class="siom-nav-sub">SIOM ITSK 2026</span>
                    </span>
                </a>

                <div class="siom-guest-header-actions">
                    <a href="/" class="siom-guest-btn-back" wire:navigate>
                        <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                    </a>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="siom-guest-main">
                {{ $slot }}
            </main>

            <!-- Footer Bar -->
            <footer class="siom-guest-footer-bar">
                <div>© 2026 Powered by RnD UPTI & BKA - ITSK RS dr. Soepraoen Malang</div>
                <div>SIOM <strong>Soepraoen Muda 2026</strong> • Sistem Informasi Organisasi Mahasiswa</div>
            </footer>
        </div>
    </body>
</html>
