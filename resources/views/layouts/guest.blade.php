<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIOM') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="siom-guest-body antialiased">
        <div class="siom-guest-layout">
            <a class="siom-nav-brand siom-guest-brand" href="/" wire:navigate>
                <span class="siom-nav-logo-icon"><i class="bi bi-music-note-beamed"></i></span>
                <span class="siom-nav-brand-text">
                    <span class="siom-nav-title">SIOM</span>
                    <span class="siom-nav-sub">Organisasi Mahasiswa</span>
                </span>
            </a>

            <div class="siom-guest-card">
                <div class="siom-guest-card-heading">
                    <h1>Selamat datang</h1>
                    <p>Masuk untuk melanjutkan ke SIOM.</p>
                </div>
                {{ $slot }}
            </div>
            <div class="siom-guest-footer">Sistem Informasi Organisasi Mahasiswa</div>
        </div>
    </body>
</html>
