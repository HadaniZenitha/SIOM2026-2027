<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIOM 2026 – Sistem Informasi Organisasi Mahasiswa | ITSK RS dr. Soepraoen Malang</title>

  <!-- Google Fonts: Fredoka & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

  <style>
    :root {
      --bg-retro: #EFF6FF;
      --bg-card: #FFFFFF;
      --border-dark: #0F172A;
      --border-w: 2.5px;
      --blue-deep: #0F1E36;
      --blue-primary: #2563EB;
      --blue-dark: #1D4ED8;
      --blue-royal: #1E40AF;
      --blue-sky: #38BDF8;
      --blue-ice: #E0F2FE;
      --blue-light: #F0F7FF;
      --text-main: #0F172A;
      --text-muted: #475569;
      --font-display: 'Fredoka', 'Plus Jakarta Sans', sans-serif;
      --font-body: 'Plus Jakarta Sans', sans-serif;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body {
      background-color: var(--bg-retro);
      color: var(--text-main);
      font-family: var(--font-body);
      overflow-x: hidden;
      line-height: 1.5;
    }
    body.siom-dark-theme {
      --bg-retro: #0f172a;
      --bg-card: #1e293b;
      --border-dark: #94a3b8;
      --blue-ice: #1e3a5f;
      --blue-light: #172554;
      --text-main: #e2e8f0;
      --text-muted: #cbd5e1;
    }
    body.siom-dark-theme .siom-nav,
    body.siom-dark-theme .siom-mobile-toggle,
    body.siom-dark-theme .siom-chunky-card,
    body.siom-dark-theme .siom-faq-item {
      background-color: var(--bg-card);
      color: var(--text-main);
    }
    body.siom-dark-theme .siom-nav-links a,
    body.siom-dark-theme .siom-nav-title,
    body.siom-dark-theme .siom-card-title,
    body.siom-dark-theme .siom-faq-question {
      color: var(--text-main);
    }
    body.siom-dark-theme .siom-footer {
      background-color: #0b1220;
      color: var(--text-main);
    }

    /* ===== NAVBAR ===== */
    .siom-nav {
      background: #FFFFFF;
      border-bottom: var(--border-w) solid var(--border-dark);
      padding: 0.85rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 1000;
      flex-wrap: wrap;
    }
    .siom-nav-brand {
      display: inline-flex;
      align-items: center;
      gap: 0.75rem;
      border: var(--border-w) solid var(--border-dark);
      border-radius: 12px;
      padding: 0.4rem 0.85rem;
      background: var(--blue-ice);
      text-decoration: none;
      box-shadow: 2px 2px 0px var(--border-dark);
      transition: all 0.15s ease;
    }
    .siom-nav-brand:hover {
      transform: translate(-1px, -1px);
      box-shadow: 3px 3px 0px var(--border-dark);
    }
    .siom-nav-logo-icon {
      width: 32px; height: 32px;
      background: var(--blue-primary);
      border: 2px solid var(--border-dark);
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-size: 1rem;
    }
    .siom-nav-brand-text { display: flex; flex-direction: column; }
    .siom-nav-title {
      font-family: var(--font-display);
      font-size: 0.95rem; font-weight: 800; color: var(--border-dark); line-height: 1.1;
      letter-spacing: 0.02em;
    }
    .siom-nav-sub {
      font-size: 0.65rem; font-weight: 700; color: var(--blue-dark); text-transform: uppercase; letter-spacing: 0.05em;
    }
    
    .siom-mobile-toggle {
      display: none;
      background: #FFFFFF;
      border: var(--border-w) solid var(--border-dark);
      border-radius: 8px;
      padding: 0.4rem 0.75rem;
      font-size: 1.25rem;
      cursor: pointer;
      box-shadow: 2px 2px 0px var(--border-dark);
    }

    .siom-nav-menu-wrapper {
      display: flex;
      align-items: center;
      gap: 2rem;
    }
    .siom-nav-links {
      display: flex; align-items: center; gap: 1.5rem;
    }
    .siom-nav-links a {
      font-family: var(--font-body);
      font-size: 0.875rem; font-weight: 700; color: var(--border-dark);
      text-decoration: none; transition: color 0.15s ease;
    }
    .siom-nav-links a:hover { color: var(--blue-primary); }
    .siom-nav-actions {
      display: flex; align-items: center; gap: 0.75rem;
    }

    .siom-pill-btn {
      font-family: var(--font-display);
      font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;
      padding: 0.5rem 1.35rem; border-radius: 9999px;
      border: var(--border-w) solid var(--border-dark);
      text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;
      cursor: pointer; transition: all 0.15s ease;
    }
    .siom-pill-white {
      background: #FFFFFF; color: var(--border-dark);
      box-shadow: 2px 2px 0px var(--border-dark);
    }
    .siom-pill-white:hover {
      background: var(--blue-ice); transform: translate(-1px, -1px); box-shadow: 3px 3px 0px var(--border-dark);
    }
    .siom-pill-blue {
      background: var(--blue-primary); color: #FFFFFF;
      box-shadow: 2px 2px 0px var(--border-dark);
    }
    .siom-pill-blue:hover {
      background: var(--blue-dark); transform: translate(-1px, -1px); box-shadow: 3px 3px 0px var(--border-dark);
      color: #fff;
    }

    /* ===== HERO SECTION ===== */
    .siom-hero {
      position: relative;
      padding: 3.5rem 1.5rem 4.5rem;
      text-align: center;
      background: radial-gradient(circle at 50% 60%, rgba(186, 230, 253, 0.4) 0%, rgba(239, 246, 255, 0.9) 65%);
      overflow: hidden;
      border-bottom: var(--border-w) solid var(--border-dark);
    }
    .siom-sunburst {
      position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
      background: repeating-conic-gradient(
        from 0deg,
        rgba(191, 219, 254, 0.25) 0deg 15deg,
        rgba(239, 246, 255, 0) 15deg 30deg
      );
      pointer-events: none; z-index: 0;
      animation: spin 90s linear infinite;
    }
    @keyframes spin { 100% { transform: rotate(360deg); } }

    .siom-vinyl {
      position: absolute; left: 4%; top: 22%; width: 140px; height: 140px;
      border-radius: 50%; background: #18181B;
      border: 4px solid var(--border-dark);
      box-shadow: 4px 4px 0px rgba(0,0,0,0.3);
      display: flex; align-items: center; justify-content: center;
      z-index: 1; transform: rotate(-15deg);
      animation: float 6s ease-in-out infinite alternate;
    }
    .siom-vinyl-center {
      width: 48px; height: 48px; border-radius: 50%;
      background: linear-gradient(135deg, var(--blue-primary), var(--blue-sky));
      border: 3px solid var(--border-dark);
      display: flex; align-items: center; justify-content: center;
      color: white; font-size: 0.75rem; font-weight: 800;
    }
    @keyframes float { 0% { transform: rotate(-15deg) translateY(0); } 100% { transform: rotate(5deg) translateY(-12px); } }

    .siom-hero-content {
      position: relative; z-index: 2; max-width: 900px; margin: 0 auto;
    }
    .siom-hero-badge {
      display: inline-block;
      background: var(--blue-royal);
      color: #FFFFFF;
      font-family: var(--font-display);
      font-size: clamp(0.75rem, 2vw, 0.85rem); font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em;
      padding: 0.45rem 1.25rem; border-radius: 9999px;
      border: var(--border-w) solid var(--border-dark);
      box-shadow: 3px 3px 0px var(--border-dark);
      margin-bottom: 1.5rem;
    }
    .siom-hero-mascot {
      width: clamp(100px, 15vw, 140px);
      height: clamp(100px, 15vw, 140px);
      margin: 0 auto 1rem;
      background: linear-gradient(135deg, var(--blue-primary), var(--blue-sky));
      border: 3px solid var(--border-dark);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: clamp(3rem, 6vw, 4.5rem); color: #FFFFFF;
      box-shadow: 4px 4px 0px var(--border-dark);
    }
    .siom-hero-title-sub {
      font-family: var(--font-display);
      font-size: clamp(1.1rem, 3vw, 1.5rem); font-weight: 800;
      color: #F59E0B;
      text-transform: uppercase; letter-spacing: 0.05em;
      text-shadow: 
        2px 2px 0px var(--border-dark),
        -1px -1px 0px var(--border-dark),
        1px -1px 0px var(--border-dark),
        -1px 1px 0px var(--border-dark);
      margin-bottom: 0.25rem;
    }
    .siom-3d-title {
      font-family: var(--font-display);
      font-size: clamp(2rem, 6vw, 3.5rem); font-weight: 900;
      color: #FFFFFF;
      text-transform: uppercase; letter-spacing: 0.02em; line-height: 1.1;
      text-shadow: 
        4px 4px 0px var(--border-dark),
        -2px -2px 0px var(--border-dark),
        2px -2px 0px var(--border-dark),
        -2px 2px 0px var(--border-dark),
        0px 4px 0px var(--border-dark);
      margin-bottom: 0.5rem;
    }
    .siom-title-banner {
      display: inline-block;
      background: var(--blue-primary);
      color: #FFFFFF;
      font-family: var(--font-display);
      font-size: clamp(1.2rem, 4vw, 2.25rem); font-weight: 800;
      padding: 0.35rem 1.25rem;
      border: var(--border-w) solid var(--border-dark);
      border-radius: 14px;
      box-shadow: 4px 4px 0px var(--border-dark);
      transform: rotate(-1.5deg);
      margin-bottom: 1.5rem;
    }
    .siom-hero-desc {
      font-size: clamp(0.9rem, 2vw, 1.05rem); font-weight: 600; color: var(--text-muted);
      max-width: 680px; margin: 0 auto 2rem; line-height: 1.6;
    }

    .siom-hero-buttons {
      display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;
    }

    /* ===== SECTION COMMON ===== */
    .siom-section {
      padding: 4rem 1.5rem;
      max-width: 950px;
      margin: 0 auto;
    }
    .siom-section-header {
      text-align: center;
      margin-bottom: 3rem;
    }
    .siom-section-subtitle {
      font-family: var(--font-display);
      font-size: 0.85rem; font-weight: 800; color: var(--blue-primary);
      text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.25rem;
    }
    .siom-section-title {
      font-family: var(--font-display);
      font-size: clamp(1.75rem, 5vw, 2.5rem); font-weight: 900; color: var(--border-dark);
      text-transform: uppercase; letter-spacing: 0.02em;
    }
    .siom-section-desc {
      font-size: 0.95rem; color: var(--text-muted); font-weight: 500; margin-top: 0.25rem;
    }

    /* TIMELINE */
    .siom-timeline-container {
      position: relative;
      padding-left: 2.5rem;
    }
    .siom-timeline-line {
      position: absolute;
      left: 12px; top: 20px; bottom: 20px;
      width: 4px;
      border-left: 3px dashed #F59E0B;
    }
    .siom-timeline-item {
      position: relative;
      margin-bottom: 1.5rem;
    }
    .siom-timeline-node {
      position: absolute;
      left: -2.5rem; top: 18px;
      width: 26px; height: 26px;
      background: #FFFFFF;
      border: 3px solid #F59E0B;
      border-radius: 50%;
      box-shadow: 2px 2px 0px var(--border-dark);
      z-index: 2;
    }
    .siom-chunky-card {
      background: #FFFFFF;
      border: var(--border-w) solid var(--border-dark);
      border-radius: 18px;
      padding: 1.25rem 1.75rem;
      box-shadow: 4px 4px 0px var(--border-dark);
      display: flex;
      align-items: center;
      gap: 1.25rem;
      transition: all 0.18s ease;
    }
    .siom-chunky-card:hover {
      transform: translate(-2px, -2px);
      box-shadow: 6px 6px 0px var(--border-dark);
    }
    .siom-card-pill {
      background: var(--blue-deep);
      color: #FFFFFF;
      font-family: var(--font-display);
      font-size: 0.8rem; font-weight: 800;
      padding: 0.4rem 1.1rem;
      border-radius: 9999px;
      border: 2px solid var(--border-dark);
      white-space: nowrap; flex-shrink: 0;
    }
    .siom-card-content { flex: 1; }
    .siom-card-title {
      font-family: var(--font-display);
      font-size: 1.05rem; font-weight: 800; color: var(--border-dark);
      text-transform: uppercase; line-height: 1.25; margin-bottom: 0.2rem;
    }
    .siom-card-sub {
      font-size: 0.8125rem; font-weight: 600; color: var(--text-muted);
    }

    /* FAQ */
    .siom-faq-item {
      background: #FFFFFF;
      border: var(--border-w) solid var(--border-dark);
      border-radius: 16px;
      padding: 1rem 1.5rem;
      box-shadow: 4px 4px 0px var(--border-dark);
      margin-bottom: 1.25rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
      gap: 1rem;
      transition: all 0.18s ease;
    }
    .siom-faq-item:hover {
      background: var(--blue-light);
      transform: translate(-1px, -1px);
    }
    .siom-faq-question {
      font-family: var(--font-body);
      font-size: 0.95rem; font-weight: 700; color: var(--border-dark);
    }
    .siom-faq-icon {
      width: 28px; height: 28px;
      border: 2px solid var(--border-dark);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 0.9rem; font-weight: 800; color: var(--border-dark);
      background: #FFFFFF; flex-shrink: 0;
    }

    /* RETRO BLUE STRIPES */
    .siom-stripes {
      width: 100%;
      height: 24px;
      display: flex; flex-direction: column;
      margin: 3rem 0;
      border-top: var(--border-w) solid var(--border-dark);
      border-bottom: var(--border-w) solid var(--border-dark);
      box-shadow: 0px 4px 0px var(--border-dark);
    }
    .siom-stripe-1 { height: 6px; background: #1E3A8A; }
    .siom-stripe-2 { height: 6px; background: #2563EB; }
    .siom-stripe-3 { height: 6px; background: #38BDF8; }
    .siom-stripe-4 { height: 6px; background: #93C5FD; }

    /* CONTACT CARD */
    .siom-contact-card {
      background: var(--blue-deep);
      border: var(--border-w) solid var(--border-dark);
      border-radius: 24px;
      padding: 2.25rem 2.5rem;
      color: #FFFFFF;
      box-shadow: 6px 6px 0px var(--border-dark);
      display: grid;
      grid-template-columns: 1.2fr 1fr auto;
      gap: 2rem;
      align-items: center;
      margin-bottom: 4rem;
    }
    .siom-contact-label {
      font-family: var(--font-display);
      font-size: 0.75rem; font-weight: 800; color: #F59E0B;
      text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.35rem;
    }
    .siom-contact-title {
      font-family: var(--font-display);
      font-size: clamp(1.5rem, 4vw, 2rem); font-weight: 900;
      color: #FFFFFF;
      text-transform: uppercase; line-height: 1.15; margin-bottom: 0.75rem;
    }
    .siom-contact-desc {
      font-size: 0.85rem; color: #94A3B8; line-height: 1.5;
    }
    .siom-contact-info-block { margin-bottom: 0.75rem; }
    .siom-contact-info-title {
      font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: #38BDF8; letter-spacing: 0.06em;
    }
    .siom-contact-info-text {
      font-size: 0.85rem; font-weight: 600; color: #F1F5F9;
    }
    .siom-mascot-graphic {
      width: 100px; height: 100px;
      background: radial-gradient(circle, var(--blue-sky) 0%, var(--blue-primary) 100%);
      border: 3px solid var(--border-dark);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 3.5rem; color: #fff;
      box-shadow: 3px 3px 0px #000;
    }

    /* FOOTER */
    .siom-footer {
      background: var(--blue-deep);
      border-top: var(--border-w) solid var(--border-dark);
      padding: 1.5rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      font-size: 0.8rem;
      color: #94A3B8;
      font-weight: 600;
      flex-wrap: wrap;
    }
    .siom-footer strong { color: #F59E0B; }

    /* ===== MEDIA QUERIES UNTUK RESPONSIVITAS ===== */
    @media (max-width: 900px) {
      .siom-nav { padding: 0.75rem 1.25rem; }
      .siom-mobile-toggle { display: block; }
      
      .siom-nav-menu-wrapper {
        display: none;
        width: 100%;
        flex-direction: column;
        align-items: stretch;
        gap: 1.25rem;
        padding-top: 1rem;
        margin-top: 0.5rem;
        border-top: 2px dashed #CBD5E1;
      }
      .siom-nav-menu-wrapper.active { display: flex; }

      .siom-nav-links {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.85rem;
      }
      .siom-nav-actions {
        width: 100%;
        justify-content: flex-start;
      }

      .siom-vinyl { display: none; }
      .siom-contact-card {
        grid-template-columns: 1fr;
        text-align: center;
        padding: 1.75rem 1.25rem;
      }
      .siom-mascot-graphic { margin: 0 auto; }
    }

    @media (max-width: 600px) {
      .siom-section { padding: 2.5rem 1rem; }
      .siom-timeline-container { padding-left: 1.75rem; }
      .siom-timeline-node { left: -1.75rem; width: 20px; height: 20px; top: 14px; }
      
      .siom-chunky-card {
        flex-direction: column;
        align-items: flex-start;
        padding: 1rem 1.25rem;
        gap: 0.75rem;
      }
      .siom-hero-buttons { flex-direction: column; width: 100%; }
      .siom-hero-buttons .siom-pill-btn { width: 100%; }
      
      .siom-footer {
        flex-direction: column;
        text-align: center;
      }
    }
  </style>
</head>
<body>

  <!-- 1. Top Navbar -->
  <nav class="siom-nav">
    <a href="/" class="siom-nav-brand">
      <div class="siom-nav-logo-icon">
        <i class="bi bi-mortarboard-fill"></i>
      </div>
      <div class="siom-nav-brand-text">
        <div class="siom-nav-title">SOEPRAOEN MUDA</div>
        <div class="siom-nav-sub">SIOM ITSK 2026</div>
      </div>
    </a>

    <!-- Tombol Hamburger Mobile -->
    <button type="button" class="siom-mobile-toggle" onclick="toggleMobileMenu()" aria-label="Menu">
      <i class="bi bi-list"></i>
    </button>

    <div class="siom-nav-menu-wrapper" id="navMenu">
      <div class="siom-nav-links">
        <a href="#filosofi">Filosofi</a>
        <a href="#agenda">Agenda</a>
        <a href="#fakultas">Fakultas & Prodi</a>
        <a href="#faq">FAQ</a>
        <a href="#kontak">Kontak</a>
      </div>

      <div class="siom-nav-actions">
        <button type="button" class="siom-pill-btn siom-pill-white siom-theme-toggle" onclick="toggleTheme()" title="Ubah Mode Gelap / Terang">
          <i class="bi bi-moon-stars-fill theme-icon"></i>
          <span class="theme-text">TEMA</span>
        </button>
        <a href="{{ route('login') }}" class="siom-pill-btn siom-pill-blue">
          <i class="bi bi-box-arrow-in-right"></i> MASUK
        </a>
      </div>
    </div>
  </nav>

  <!-- 2. Hero Section -->
  <section class="siom-hero">
    <div class="siom-sunburst"></div>

    <div class="siom-vinyl">
      <div class="siom-vinyl-center">SIOM</div>
    </div>

    <div class="siom-hero-content">
      <div class="siom-hero-badge">
        <i class="bi bi-stars"></i> SIOM 2026 • KELOLA ORGANISASI. TUMBUH BERSAMA.
      </div>

      <div class="siom-hero-mascot">
        🦉
      </div>

      <div class="siom-hero-title-sub">SELAMAT DATANG DI PORTAL</div>
      <h1 class="siom-3d-title">SISTEM INFORMASI</h1>
      <div class="siom-title-banner">ORGANISASI MAHASISWA</div>

      <p class="siom-hero-desc">
        Pusat digitalisasi administrasi, pengajuan proposal, approval berjenjang, tanda tangan elektronik, transparansi anggaran, hingga pelaporan LPJ kegiatan di ITSK RS dr. Soepraoen Malang.
      </p>

      <div class="siom-hero-buttons">
        <a href="{{ route('login') }}" class="siom-pill-btn siom-pill-blue" style="font-size:1rem; padding:0.75rem 2rem;">
          <i class="bi bi-door-open-fill"></i> Masuk ke Sistem SIOM
        </a>
        <a href="#agenda" class="siom-pill-btn siom-pill-white" style="font-size:1rem; padding:0.75rem 2rem;">
          <i class="bi bi-calendar-event"></i> Lihat Alur & Agenda
        </a>
      </div>
    </div>
  </section>

  <!-- 3. Agenda & Alur Kegiatan Section -->
  <section id="agenda" class="siom-section">
    <div class="siom-section-header">
      <div class="siom-section-subtitle">Rangkaian Alur Administrasi</div>
      <h2 class="siom-section-title">AGENDA & WORKFLOW 2026</h2>
      <p class="siom-section-desc">Tahapan resmi pengajuan proposal, verifikasi, dan pelaporan kegiatan ormawa.</p>
    </div>

    <div class="siom-timeline-container">
      <div class="siom-timeline-line"></div>

      <!-- Item 1 -->
      <div class="siom-timeline-item">
        <div class="siom-timeline-node"></div>
        <div class="siom-chunky-card">
          <div class="siom-card-pill">TAHAP 1</div>
          <div class="siom-card-content">
            <div class="siom-card-title">PENYUSUNAN DRAFT PROPOSAL & RAB OLEH PENGURUS ORMAWA</div>
            <div class="siom-card-sub"><i class="bi bi-calendar3 me-1"></i> Mulai Pengajuan Awal Tahun Akademik 2025/2026</div>
          </div>
        </div>
      </div>

      <!-- Item 2 -->
      <div class="siom-timeline-item">
        <div class="siom-timeline-node"></div>
        <div class="siom-chunky-card">
          <div class="siom-card-pill">TAHAP 2</div>
          <div class="siom-card-content">
            <div class="siom-card-title">REVIEW & VERIFIKASI PEMBINA ORMAWA SERTA KAPRODI</div>
            <div class="siom-card-sub"><i class="bi bi-check2-circle me-1"></i> Sinkronisasi dengan kalender akademik & visi pembinaan karakter</div>
          </div>
        </div>
      </div>

      <!-- Item 3 -->
      <div class="siom-timeline-item">
        <div class="siom-timeline-node"></div>
        <div class="siom-chunky-card">
          <div class="siom-card-pill">TAHAP 3</div>
          <div class="siom-card-content">
            <div class="siom-card-title">VALIDASI ANGGARAN & PAGU OLEH BIRO KEMAHASISWAAN (BKA)</div>
            <div class="siom-card-sub"><i class="bi bi-shield-check me-1"></i> Pemeriksaan ketersediaan pagu dana kampus se-ITSK RS dr. Soepraoen</div>
          </div>
        </div>
      </div>

      <!-- Item 4 -->
      <div class="siom-timeline-item">
        <div class="siom-timeline-node"></div>
        <div class="siom-chunky-card">
          <div class="siom-card-pill">TAHAP 4</div>
          <div class="siom-card-content">
            <div class="siom-card-title">PERSETUJUAN AKHIR & TTD ELEKTRONIK WAKIL REKTOR III</div>
            <div class="siom-card-sub"><i class="bi bi-pen-fill me-1"></i> Otorisasi final (ACC Final) & pencairan dana kegiatan</div>
          </div>
        </div>
      </div>

      <!-- Item 5 -->
      <div class="siom-timeline-item">
        <div class="siom-timeline-node"></div>
        <div class="siom-chunky-card" style="border-color: var(--blue-primary); background: var(--blue-light);">
          <div class="siom-card-pill" style="background: var(--blue-primary);">TAHAP 5</div>
          <div class="siom-card-content">
            <div class="siom-card-title">PELAKSANAAN KEGIATAN & UNGGAH LAPORAN LPJ TERPADU</div>
            <div class="siom-card-sub"><i class="bi bi-journal-check me-1"></i> Pelaporan pertanggungjawaban dana, nota transaksi, & dokumentasi</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 4. Fakultas & 18 Program Studi -->
  <section id="fakultas" class="siom-section" style="padding-top:1rem;">
    <div class="siom-section-header">
      <div class="siom-section-subtitle">Struktur Institusional</div>
      <h2 class="siom-section-title">4 FAKULTAS & 18 PROGRAM STUDI</h2>
      <p class="siom-section-desc">Mendukung seluruh organisasi kemahasiswaan di lingkungan ITSK RS dr. Soepraoen Malang.</p>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:1.5rem;">
      <div class="siom-chunky-card" style="flex-direction:column; align-items:flex-start; background:#fff;">
        <div style="display:flex; align-items:center; justify-content:space-between; width:100%; margin-bottom:0.5rem;">
          <span class="siom-card-pill" style="background:var(--blue-royal);">FAKULTAS 1</span>
          <span style="font-size:0.75rem; font-weight:800; color:var(--blue-primary);">4 PRODI</span>
        </div>
        <div class="siom-card-title">FAKULTAS SAINS & TEKNOLOGI</div>
        <ul style="font-size:0.85rem; font-weight:600; color:var(--text-muted); padding-left:1.2rem; margin-top:0.5rem; line-height:1.6;">
          <li>D3 Akupunktur</li>
          <li>D3 Rekam Medis & Info Kes.</li>
          <li>D4 Keperawatan Anestesiologi</li>
          <li>S1 Informatika</li>
        </ul>
      </div>

      <div class="siom-chunky-card" style="flex-direction:column; align-items:flex-start; background:#fff;">
        <div style="display:flex; align-items:center; justify-content:space-between; width:100%; margin-bottom:0.5rem;">
          <span class="siom-card-pill" style="background:var(--blue-dark);">FAKULTAS 2</span>
          <span style="font-size:0.75rem; font-weight:800; color:var(--blue-primary);">3 PRODI</span>
        </div>
        <div class="siom-card-title">FAKULTAS FARMASI</div>
        <ul style="font-size:0.85rem; font-weight:600; color:var(--text-muted); padding-left:1.2rem; margin-top:0.5rem; line-height:1.6;">
          <li>D3 Farmasi</li>
          <li>S1 Farmasi Klinis & Komunitas</li>
          <li>Pendidikan Profesi Apoteker</li>
        </ul>
      </div>

      <div class="siom-chunky-card" style="flex-direction:column; align-items:flex-start; background:#fff;">
        <div style="display:flex; align-items:center; justify-content:space-between; width:100%; margin-bottom:0.5rem;">
          <span class="siom-card-pill" style="background:var(--blue-primary);">FAKULTAS 3</span>
          <span style="font-size:0.75rem; font-weight:800; color:var(--blue-primary);">9 PRODI</span>
        </div>
        <div class="siom-card-title">FAKULTAS ILMU KESEHATAN</div>
        <ul style="font-size:0.85rem; font-weight:600; color:var(--text-muted); padding-left:1.2rem; margin-top:0.5rem; line-height:1.6;">
          <li>D3 & S1 Keperawatan, D4 & S1 Kebidanan</li>
          <li>S1 Fisioterapi</li>
          <li>Profesi Ners, Bidan, Fisioterapis</li>
        </ul>
      </div>

      <div class="siom-chunky-card" style="flex-direction:column; align-items:flex-start; background:#fff;">
        <div style="display:flex; align-items:center; justify-content:space-between; width:100%; margin-bottom:0.5rem;">
          <span class="siom-card-pill" style="background:var(--blue-deep);">FAKULTAS 4</span>
          <span style="font-size:0.75rem; font-weight:800; color:var(--blue-primary);">2 PRODI</span>
        </div>
        <div class="siom-card-title">FAKULTAS KEDOKTERAN GIGI</div>
        <ul style="font-size:0.85rem; font-weight:600; color:var(--text-muted); padding-left:1.2rem; margin-top:0.5rem; line-height:1.6;">
          <li>S1 Kedokteran Gigi</li>
          <li>Pendidikan Profesi Dokter Gigi</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- 5. FAQ Section -->
  <section id="faq" class="siom-section">
    <div class="siom-section-header">
      <div class="siom-section-subtitle">Tanya Jawab</div>
      <h2 class="siom-section-title">PERTANYAAN UMUM (FAQ)</h2>
      <p class="siom-section-desc">Informasi seputar penggunaan sistem dan tata cara administrasi.</p>
    </div>

    <div class="siom-faq-item" onclick="toggleFaq(this)">
      <div class="siom-faq-question">Bagaimana cara login bagi pengurus ormawa dan dosen pembina?</div>
      <div class="siom-faq-icon">+</div>
    </div>
    <div class="siom-faq-item" onclick="toggleFaq(this)">
      <div class="siom-faq-question">Berapa alokasi pagu dana awal untuk setiap HIMA/Ormawa?</div>
      <div class="siom-faq-icon">+</div>
    </div>
    <div class="siom-faq-item" onclick="toggleFaq(this)">
      <div class="siom-faq-question">Bagaimana urutan tahapan verifikasi proposal hingga ACC Final?</div>
      <div class="siom-faq-icon">+</div>
    </div>
    <div class="siom-faq-item" onclick="toggleFaq(this)">
      <div class="siom-faq-question">Kapan berkas LPJ kegiatan wajib diunggah ke sistem?</div>
      <div class="siom-faq-icon">+</div>
    </div>
  </section>

  <!-- 6. Retro Blue Stripes Banner -->
  <div class="siom-stripes">
    <div class="siom-stripe-1"></div>
    <div class="siom-stripe-2"></div>
    <div class="siom-stripe-3"></div>
    <div class="siom-stripe-4"></div>
  </div>

  <!-- 7. Contact Card -->
  <section id="kontak" class="siom-section" style="padding-top:0;">
    <div class="siom-contact-card">
      <div>
        <div class="siom-contact-label">BUTUH BANTUAN?</div>
        <div class="siom-contact-title">HUBUNGI BIRO BKA</div>
        <p class="siom-contact-desc">
          Tim Biro Kemahasiswaan & Alumni (BKA) siap membantu kendala teknis, pengajuan proposal, maupun verifikasi administrasi kegiatan organisasi mahasiswa.
        </p>
      </div>

      <div>
        <div class="siom-contact-info-block">
          <div class="siom-contact-info-title">SEKRETARIAT</div>
          <div class="siom-contact-info-text">Kampus ITSK RS dr. Soepraoen Malang</div>
        </div>
        <div class="siom-contact-info-block">
          <div class="siom-contact-info-title">INSTAGRAM</div>
          <div class="siom-contact-info-text">@soepraoen_muda &nbsp;|&nbsp; @itsk_soepraoen</div>
        </div>
        <div class="siom-contact-info-block">
          <div class="siom-contact-info-title">EMAIL RESMI</div>
          <div class="siom-contact-info-text">bka@itsk-soepraoen.ac.id</div>
        </div>
      </div>

      <div class="siom-mascot-graphic">
        🦉
      </div>
    </div>
  </section>

  <!-- 8. Footer -->
  <footer class="siom-footer">
    <div>
      © 2026 Powered by RnD UPTI & BKA - ITSK RS dr. Soepraoen Malang
    </div>
    <div>
      SIOM <strong>Soepraoen Muda 2026</strong> • Sistem Informasi Organisasi Mahasiswa
    </div>
  </footer>

  <script>
    function toggleTheme() {
      const isDark = document.body.classList.toggle('siom-dark-theme');
      localStorage.setItem('siom-theme', isDark ? 'dark' : 'light');
    }

    function toggleMobileMenu() {
      const menu = document.getElementById('navMenu');
      menu.classList.toggle('active');
    }

    function toggleFaq(el) {
      const icon = el.querySelector('.siom-faq-icon');
      if (icon.innerText === '+') {
        icon.innerText = '−';
        icon.style.background = '#2563EB';
        icon.style.color = '#FFF';
      } else {
        icon.innerText = '+';
        icon.style.background = '#FFF';
        icon.style.color = '#0F172A';
      }
    }

    document.body.classList.toggle('siom-dark-theme', localStorage.getItem('siom-theme') === 'dark');
  </script>

</body>
</html>