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
    body.siom-dark-theme .siom-faq-header:hover {
      background: rgba(37, 99, 235, 0.12);
    }
    body.siom-dark-theme .siom-faq-icon {
      background: var(--bg-card);
      color: var(--text-main);
      border-color: var(--border-dark);
    }
    body.siom-dark-theme .siom-faq-item.active .siom-faq-icon {
      background: var(--blue-primary);
      color: #FFFFFF;
      border-color: var(--border-dark);
    }
    body.siom-dark-theme .siom-faq-body {
      color: var(--text-muted);
    }
    body.siom-dark-theme .siom-faq-item.active .siom-faq-body {
      border-top-color: #334155;
    }
    body.siom-dark-theme .siom-faq-note {
      background: #172554;
      border-left-color: #38bdf8;
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
    .siom-hero-stats-strip {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-top: 2.75rem;
      text-align: left;
    }
    .siom-stat-card {
      background: rgba(255, 255, 255, 0.95);
      border: 2px solid var(--border-dark);
      border-radius: 16px;
      padding: 1rem 1.25rem;
      box-shadow: 3px 3px 0 var(--border-dark);
      display: flex;
      align-items: center;
      gap: 0.85rem;
      transition: all 0.2s ease;
    }
    .siom-stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 4px 4px 0 var(--border-dark);
    }
    .siom-stat-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      border: 2px solid var(--border-dark);
      background: var(--blue-ice);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      color: var(--blue-primary);
      flex-shrink: 0;
    }
    .siom-stat-val {
      font-family: var(--font-display);
      font-size: 1.15rem;
      font-weight: 800;
      color: var(--border-dark);
      line-height: 1.1;
    }
    .siom-stat-lbl {
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--text-muted);
      margin-top: 0.15rem;
    }
    body.siom-dark-theme .siom-stat-card {
      background: var(--bg-card);
      border-color: var(--border-dark);
    }
    body.siom-dark-theme .siom-stat-val {
      color: var(--text-main);
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

    /* FAQ ACCORDION */
    .siom-faq-item {
      background: #FFFFFF;
      border: var(--border-w) solid var(--border-dark);
      border-radius: 16px;
      box-shadow: 4px 4px 0px var(--border-dark);
      margin-bottom: 1.25rem;
      transition: all 0.2s ease;
      overflow: hidden;
    }
    .siom-faq-item:hover {
      box-shadow: 6px 6px 0px var(--border-dark);
      transform: translate(-1px, -1px);
    }
    .siom-faq-item.active {
      border-color: var(--blue-primary);
      box-shadow: 5px 5px 0px var(--border-dark);
    }
    .siom-faq-header {
      padding: 1.2rem 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
      gap: 1.25rem;
      user-select: none;
      transition: background 0.15s ease;
    }
    .siom-faq-header:hover {
      background: var(--blue-light);
    }
    .siom-faq-question {
      font-family: var(--font-body);
      font-size: 0.98rem;
      font-weight: 700;
      color: var(--border-dark);
      line-height: 1.4;
      text-align: left;
    }
    .siom-faq-icon {
      width: 32px;
      height: 32px;
      border: 2px solid var(--border-dark);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.95rem;
      font-weight: 800;
      color: var(--border-dark);
      background: #FFFFFF;
      flex-shrink: 0;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .siom-faq-item.active .siom-faq-icon {
      background: var(--blue-primary);
      color: #FFFFFF;
      transform: rotate(180deg);
    }
    .siom-faq-body {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1), padding 0.35s ease, opacity 0.25s ease;
      padding: 0 1.5rem;
      opacity: 0;
      font-size: 0.885rem;
      line-height: 1.65;
      color: var(--text-muted);
    }
    .siom-faq-item.active .siom-faq-body {
      max-height: 650px;
      opacity: 1;
      padding: 0 1.5rem 1.35rem 1.5rem;
      border-top: 1.5px dashed #CBD5E1;
      padding-top: 1rem;
    }
    .siom-faq-note {
      margin-top: 0.75rem;
      padding: 0.65rem 0.95rem;
      background: var(--blue-light);
      border-left: 3px solid var(--blue-primary);
      border-radius: 8px;
      font-size: 0.825rem;
      color: var(--text-main);
      font-weight: 500;
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
      grid-template-columns: 1.25fr 1fr auto;
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
    .siom-contact-info-block { margin-bottom: 0.85rem; }
    .siom-contact-info-block:last-child { margin-bottom: 0; }
    .siom-contact-info-title {
      font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: #38BDF8; letter-spacing: 0.06em;
      display: flex; align-items: center; gap: 0.35rem;
    }
    .siom-contact-info-text {
      font-size: 0.85rem; font-weight: 600; color: #F1F5F9; margin-top: 0.15rem;
    }
    .siom-contact-link {
      color: #F1F5F9;
      text-decoration: none;
      transition: all 0.15s ease;
      display: inline-flex;
      align-items: center;
    }
    .siom-contact-link:hover {
      color: #38BDF8;
      text-decoration: underline;
    }
    
    /* PROFESSIONAL HELPDESK BADGE (Replaces Owl Mascot) */
    .siom-contact-badge-wrapper {
      background: #15294a;
      border: var(--border-w) solid var(--border-dark);
      border-radius: 20px;
      padding: 1.5rem 1.35rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      gap: 0.65rem;
      min-width: 210px;
      box-shadow: 4px 4px 0px rgba(0, 0, 0, 0.4);
      transition: all 0.2s ease;
    }
    .siom-contact-badge-wrapper:hover {
      transform: translateY(-2px);
      box-shadow: 6px 6px 0px rgba(0, 0, 0, 0.5);
    }
    .siom-contact-badge-icon {
      width: 68px;
      height: 68px;
      background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
      border: 2px solid #38BDF8;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.1rem;
      color: #FFFFFF;
      box-shadow: 0 4px 14px rgba(37, 99, 235, 0.45);
    }
    .siom-contact-badge-tag {
      font-family: var(--font-display);
      font-size: 0.75rem;
      font-weight: 800;
      letter-spacing: 0.08em;
      color: #F59E0B;
      text-transform: uppercase;
    }
    .siom-contact-badge-hours {
      font-size: 0.75rem;
      color: #94A3B8;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.3rem;
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
      .siom-contact-badge-wrapper {
        margin: 1.25rem auto 0 auto;
        width: 100%;
        max-width: 320px;
      }
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
        <a href="#ormawa">Ormawa</a>
        <a href="#faq">FAQ</a>
        <a href="#kontak">Kontak</a>
      </div>

      <div class="siom-nav-actions">
        <button type="button" class="siom-pill-btn siom-pill-white siom-theme-toggle" onclick="toggleTheme()" title="Ubah Mode Gelap / Terang">
          <i class="bi bi-moon-stars-fill theme-icon"></i>
          <span class="theme-text">TEMA</span>
        </button>
        @auth
          <a href="{{ route('dashboard') }}" class="siom-pill-btn siom-pill-blue">
            <i class="bi bi-speedometer2"></i> DASHBOARD
          </a>
        @else
          <a href="{{ route('login') }}" class="siom-pill-btn siom-pill-blue">
            <i class="bi bi-box-arrow-in-right"></i> MASUK
          </a>
        @endauth
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

      <div class="siom-hero-mascot" style="position: relative;">
        <i class="bi bi-mortarboard-fill" style="filter: drop-shadow(2px 2px 0px #0f172a);"></i>
      </div>

      <div class="siom-hero-title-sub">SELAMAT DATANG DI PORTAL RESMI</div>
      <h1 class="siom-3d-title">SISTEM INFORMASI</h1>
      <div class="siom-title-banner">ORGANISASI MAHASISWA</div>

      <p class="siom-hero-desc">
        Pusat digitalisasi administrasi ormawa, pengajuan proposal otomatis, proteksi tenggat H-30 hari, alur persetujuan cerdas (HIMA vs UKM/DPM/BEM), tanda tangan digital transparan, hingga pelaporan LPJ kegiatan di <strong>ITSK RS dr. Soepraoen Kesdam V/Brw Malang</strong>.
      </p>

      <div class="siom-hero-buttons">
        @auth
          <a href="{{ route('dashboard') }}" class="siom-pill-btn siom-pill-blue" style="font-size:1rem; padding:0.75rem 2rem;">
            <i class="bi bi-speedometer2"></i> Buka Dashboard SIOM
          </a>
        @else
          <a href="{{ route('login') }}" class="siom-pill-btn siom-pill-blue" style="font-size:1rem; padding:0.75rem 2rem;">
            <i class="bi bi-door-open-fill"></i> Masuk ke Sistem SIOM
          </a>
        @endauth
        <a href="#agenda" class="siom-pill-btn siom-pill-white" style="font-size:1rem; padding:0.75rem 2rem;">
          <i class="bi bi-calendar-event"></i> Lihat Alur & Ketentuan
        </a>
      </div>

      <!-- Quick Stats Metrics Strip -->
      <div class="siom-hero-stats-strip">
        <div class="siom-stat-card">
          <div class="siom-stat-icon"><i class="bi bi-shield-check"></i></div>
          <div>
            <div class="siom-stat-val">H-30 Hari</div>
            <div class="siom-stat-lbl">Proteksi Regulasi Pengajuan</div>
          </div>
        </div>
        <div class="siom-stat-card">
          <div class="siom-stat-icon"><i class="bi bi-diagram-3-fill"></i></div>
          <div>
            <div class="siom-stat-val">4 & 3 Tahap</div>
            <div class="siom-stat-lbl">Approval Dinamis (HIMA/UKM)</div>
          </div>
        </div>
        <div class="siom-stat-card">
          <div class="siom-stat-icon"><i class="bi bi-pen-fill"></i></div>
          <div>
            <div class="siom-stat-val">TTE Canvas</div>
            <div class="siom-stat-lbl">Tanda Tangan Transparan Seragam</div>
          </div>
        </div>
        <div class="siom-stat-card">
          <div class="siom-stat-icon"><i class="bi bi-file-earmark-word-fill"></i></div>
          <div>
            <div class="siom-stat-val">Auto DOCX</div>
            <div class="siom-stat-lbl">8 Template Resmi Kampus</div>
          </div>
        </div>
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
            <div class="siom-card-title">PENYUSUNAN PROPOSAL & RAB (PROTEKSI TENGGAT H-30 HARI)</div>
            <div class="siom-card-sub"><i class="bi bi-clock-history text-warning me-1"></i> <strong>Ketentuan Sistem:</strong> Tanggal kegiatan wajib minimal 30 hari kalender setelah tanggal pengajuan proposal. Dokumen dapat digenerate otomatis atau diunggah mandiri (.docx/.pdf max 20MB).</div>
          </div>
        </div>
      </div>

      <!-- Item 2 -->
      <div class="siom-timeline-item">
        <div class="siom-timeline-node"></div>
        <div class="siom-chunky-card">
          <div class="siom-card-pill">TAHAP 2</div>
          <div class="siom-card-content">
            <div class="siom-card-title">REVIEW & PERSETUJUAN PEMBINA SERTA KAPRODI</div>
            <div class="siom-card-sub"><i class="bi bi-diagram-3 me-1"></i> <strong>Alur Fleksibel:</strong> HIMA (tingkat prodi) diverifikasi Pembina & Kaprodi. Sedangkan UKM & DPM (tingkat universitas) diverifikasi langsung oleh Pembina Unit.</div>
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

  <!-- 4.5 Kategori Ormawa Section -->
  <section id="ormawa" class="siom-section" style="padding-top:1rem;">
    <div class="siom-section-header">
      <div class="siom-section-subtitle">Klasifikasi Organisasi</div>
      <h2 class="siom-section-title">HIMA, UKM, DPM & BEM</h2>
      <p class="siom-section-desc">Masing-masing kategori memiliki alur kerja dan pembinaan spesifik di SIOM.</p>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:1.5rem;">
      <div class="siom-chunky-card" style="flex-direction:column; align-items:flex-start; background:#fff;">
        <div style="display:flex; align-items:center; justify-content:space-between; width:100%; margin-bottom:0.5rem;">
          <span class="siom-card-pill" style="background:#2563EB;">PRODI LEVEL</span>
          <span style="font-size:0.75rem; font-weight:800; color:#2563EB;">4 TAHAP APPROVAL</span>
        </div>
        <div class="siom-card-title">HIMA (HIMPUNAN MAHASISWA)</div>
        <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.6; margin-top:0.4rem;">
          Organisasi kepengurusan mahasiswa tingkat Program Studi. Alur persetujuan proposal wajib melalui <strong>Dosen Pembina Himpunan & Ketua Program Studi (Kaprodi)</strong> sebelum naik ke Bagian Kemahasiswaan dan Wakil Rektor III.
        </p>
      </div>

      <div class="siom-chunky-card" style="flex-direction:column; align-items:flex-start; background:#fff;">
        <div style="display:flex; align-items:center; justify-content:space-between; width:100%; margin-bottom:0.5rem;">
          <span class="siom-card-pill" style="background:#16A34A;">KAMPUS LEVEL</span>
          <span style="font-size:0.75rem; font-weight:800; color:#16A34A;">3 TAHAP APPROVAL</span>
        </div>
        <div class="siom-card-title">UKM (UNIT KEGIATAN MAHASISWA)</div>
        <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.6; margin-top:0.4rem;">
          Wadah pengembangan minat, bakat, seni, dan olahraga tingkat Universitas. Karena bersifat lintas program studi, alur approval diverifikasi langsung oleh <strong>Pembina UKM</strong> langsung ke Bagian Kemahasiswaan & Wakil Rektor III tanpa Kaprodi.
        </p>
      </div>

      <div class="siom-chunky-card" style="flex-direction:column; align-items:flex-start; background:#fff;">
        <div style="display:flex; align-items:center; justify-content:space-between; width:100%; margin-bottom:0.5rem;">
          <span class="siom-card-pill" style="background:#7C3AED;">LEGISLATIF & EKSEKUTIF</span>
          <span style="font-size:0.75rem; font-weight:800; color:#7C3AED;">SENTRAL</span>
        </div>
        <div class="siom-card-title">DPM & BEM KAMPUS</div>
        <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.6; margin-top:0.4rem;">
          Dewan Perwakilan Mahasiswa (legislatif) dan Badan Eksekutif Mahasiswa (eksekutif). Mengoordinasikan seluruh ormawa, menyelenggarakan kongres, serta menjembatani aspirasi mahasiswa dengan pimpinan universitas.
        </p>
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

    <!-- FAQ Item 1 -->
    <div class="siom-faq-item">
      <div class="siom-faq-header" onclick="toggleFaq(this)" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleFaq(this);}" role="button" tabindex="0" aria-expanded="false">
        <div class="siom-faq-question">Bagaimana cara login bagi pengurus ormawa dan dosen pembina?</div>
        <div class="siom-faq-icon"><i class="bi bi-chevron-down"></i></div>
      </div>
      <div class="siom-faq-body">
        <p>
          Akun pengurus ormawa (Ketua, Sekretaris, Bendahara) serta Dosen Pembina, Kaprodi, dan Verifikator didaftarkan secara terpusat oleh Biro Kemahasiswaan & Alumni (BKA) bersama Admin UPTI.
        </p>
        <ul style="margin: 0.6rem 0 0.6rem 1.25rem; line-height: 1.7;">
          <li>Klik tombol <strong>"MASUK"</strong> pada navigasi kanan atas atau tombol utama di halaman beranda.</li>
          <li>Masukkan email resmi / NIDN / NIM serta kata sandi default yang telah diterbitkan oleh pihak BKA.</li>
          <li>Setelah login berhasil, sistem secara otomatis mengarahkan ke dashboard kerja sesuai hak akses peran (role) Anda.</li>
        </ul>
        <div class="siom-faq-note">
          <i class="bi bi-info-circle-fill text-primary me-1"></i> <strong>Lupa kata sandi atau belum terdaftar?</strong> Silakan hubungi Sekretariat BKA atau kirimkan tiket permohonan melalui kontak resmi di bawah.
        </div>
      </div>
    </div>

    <!-- FAQ Item 2 -->
    <div class="siom-faq-item">
      <div class="siom-faq-header" onclick="toggleFaq(this)" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleFaq(this);}" role="button" tabindex="0" aria-expanded="false">
        <div class="siom-faq-question">Berapa alokasi pagu dana awal untuk setiap HIMA/Ormawa?</div>
        <div class="siom-faq-icon"><i class="bi bi-chevron-down"></i></div>
      </div>
      <div class="siom-faq-body">
        <p>
          Besaran pagu dana kegiatan ditentukan setiap awal tahun anggaran akademik oleh Rektorat bersama BKA, dengan klasifikasi terstandarisasi:
        </p>
        <ul style="margin: 0.6rem 0 0.6rem 1.25rem; line-height: 1.7;">
          <li><strong>HIMA (Himpunan Mahasiswa Program Studi):</strong> Alokasi pagu proporsional berdasarkan jumlah mahasiswa dan prodi masing-masing di 4 Fakultas.</li>
          <li><strong>UKM (Unit Kegiatan Mahasiswa):</strong> Alokasi pagu berdasarkan bidang minat, bakat, olahraga, seni, atau penalaran tingkat kampus.</li>
          <li><strong>DPM & BEM:</strong> Alokasi pagu operasional lembaga legislatif dan eksekutif sentral mahasiswa.</li>
        </ul>
        <p style="margin-top: 0.35rem;">
          Setiap kali proposal kegiatan mendapatkan persetujuan final (<strong>ACC Final</strong>) dari Wakil Rektor III, sisa saldo pagu akan langsung terpotong secara otomatis dan dapat dipantau real-time di Dashboard Ormawa.
        </p>
      </div>
    </div>

    <!-- FAQ Item 3 -->
    <div class="siom-faq-item">
      <div class="siom-faq-header" onclick="toggleFaq(this)" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleFaq(this);}" role="button" tabindex="0" aria-expanded="false">
        <div class="siom-faq-question">Bagaimana urutan tahapan verifikasi proposal hingga ACC Final?</div>
        <div class="siom-faq-icon"><i class="bi bi-chevron-down"></i></div>
      </div>
      <div class="siom-faq-body">
        <p>
          Alur verifikasi proposal berlangsung berjenjang dengan proteksi otomatis sistem:
        </p>
        <div style="display: grid; gap: 0.6rem; margin: 0.65rem 0;">
          <div style="background: var(--blue-ice); padding: 0.75rem 0.95rem; border-radius: 8px; border-left: 4px solid var(--blue-primary); font-size: 0.84rem;">
            <strong><i class="bi bi-diagram-3-fill me-1"></i> Alur HIMA (Tingkat Prodi - 4 Tahap Approval):</strong><br>
            Pengajuan Sekretaris Ormawa (Min. H-30 Hari) &rarr; Review & Rekomendasi Dosen Pembina &rarr; Verifikasi Ketua Program Studi (Kaprodi) &rarr; Validasi Kelayakan Anggaran BKA &rarr; Pengesahan & TTE Digital Wakil Rektor III (ACC Final).
          </div>
          <div style="background: var(--blue-ice); padding: 0.75rem 0.95rem; border-radius: 8px; border-left: 4px solid #16A34A; font-size: 0.84rem;">
            <strong><i class="bi bi-diagram-2-fill me-1"></i> Alur UKM & DPM (Tingkat Universitas - 3 Tahap Approval):</strong><br>
            Pengajuan Sekretaris Ormawa (Min. H-30 Hari) &rarr; Review Dosen Pembina Unit &rarr; Validasi Kelayakan Anggaran BKA &rarr; Pengesahan & TTE Digital Wakil Rektor III (ACC Final).
          </div>
        </div>
        <div class="siom-faq-note">
          <i class="bi bi-shield-check text-primary me-1"></i> <strong>Sistem Proteksi H-30 Hari:</strong> Sistem secara otomatis menolak tanggal pelaksanaan kegiatan yang kurang dari 30 hari kalender sejak pengajuan proposal diajukan untuk menjamin waktu review memadai.
        </div>
      </div>
    </div>

    <!-- FAQ Item 4 -->
    <div class="siom-faq-item">
      <div class="siom-faq-header" onclick="toggleFaq(this)" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleFaq(this);}" role="button" tabindex="0" aria-expanded="false">
        <div class="siom-faq-question">Kapan berkas LPJ kegiatan wajib diunggah ke sistem?</div>
        <div class="siom-faq-icon"><i class="bi bi-chevron-down"></i></div>
      </div>
      <div class="siom-faq-body">
        <p>
          Laporan Pertanggungjawaban (LPJ) beserta rekapitulasi realisasi dana, bukti kuitansi/nota sah, dan dokumentasi foto/video kegiatan wajib diunggah melalui menu <strong>Laporan LPJ</strong> paling lambat <strong>14 hari kalender</strong> terhitung setelah tanggal akhir kegiatan selesai.
        </p>
        <ul style="margin: 0.6rem 0 0.6rem 1.25rem; line-height: 1.7;">
          <li>Format berkas yang didukung adalah <strong>.PDF</strong> atau <strong>.DOCX</strong> dengan ukuran maksimal 20 MB.</li>
          <li>Berkas LPJ yang masuk akan langsung diperiksa oleh Verifikator BKA untuk verifikasi realisasi saldo pagu.</li>
          <li>Ormawa yang melewati batas waktu 14 hari akan menerima penangguhan (hold) sistem otomatis dan tidak dapat mengajukan proposal kegiatan baru hingga LPJ tuntas disetujui.</li>
        </ul>
      </div>
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
          Tim Biro Kemahasiswaan & Alumni (BKA) siap membantu kendala teknis, pengajuan proposal, konsultasi pagu anggaran, maupun verifikasi administrasi kegiatan organisasi mahasiswa.
        </p>
      </div>

      <div>
        <div class="siom-contact-info-block">
          <div class="siom-contact-info-title"><i class="bi bi-geo-alt-fill"></i> SEKRETARIAT</div>
          <div class="siom-contact-info-text">Gedung Rektorat Lt. 1, Kampus ITSK RS dr. Soepraoen Malang</div>
        </div>
        <div class="siom-contact-info-block">
          <div class="siom-contact-info-title"><i class="bi bi-instagram"></i> INSTAGRAM</div>
          <div class="siom-contact-info-text">
            <a href="https://instagram.com/soepraoen_muda" target="_blank" rel="noopener noreferrer" class="siom-contact-link">@soepraoen_muda</a>
            <span style="color:#64748B; margin: 0 0.35rem;">|</span>
            <a href="https://instagram.com/itsk_soepraoen" target="_blank" rel="noopener noreferrer" class="siom-contact-link">@itsk_soepraoen</a>
          </div>
        </div>
        <div class="siom-contact-info-block">
          <div class="siom-contact-info-title"><i class="bi bi-envelope-fill"></i> EMAIL RESMI</div>
          <div class="siom-contact-info-text">
            <a href="mailto:bka@itsk-soepraoen.ac.id" class="siom-contact-link">
              bka@itsk-soepraoen.ac.id
            </a>
          </div>
        </div>
      </div>

      <!-- Professional Service Badge (Replaced Owl Mascot) -->
      <div class="siom-contact-badge-wrapper">
        <div class="siom-contact-badge-icon">
          <i class="bi bi-headset"></i>
        </div>
        <div class="siom-contact-badge-tag">LAYANAN BANTUAN BKA</div>
        <div class="siom-contact-badge-hours">
          <i class="bi bi-clock-history text-warning"></i> Senin - Jumat (08.00 - 15.30 WIB)
        </div>
        <a href="mailto:bka@itsk-soepraoen.ac.id?subject=Permohonan%20Bantuan%20SIOM%20ITSK%20Soepraoen" class="siom-pill-btn siom-pill-blue" style="font-size:0.8rem; padding:0.45rem 1rem; width:100%; justify-content:center; text-decoration:none; margin-top:0.25rem;">
          <i class="bi bi-send-fill me-1"></i> Hubungi BKA
        </a>
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

    function toggleFaq(headerEl) {
      const item = headerEl.closest('.siom-faq-item');
      const isExpanded = item.classList.contains('active');

      // Accordion behavior: tutup item FAQ lainnya agar tetap rapi
      document.querySelectorAll('.siom-faq-item').forEach(el => {
        if (el !== item) {
          el.classList.remove('active');
          const h = el.querySelector('.siom-faq-header');
          if (h) h.setAttribute('aria-expanded', 'false');
        }
      });

      if (isExpanded) {
        item.classList.remove('active');
        headerEl.setAttribute('aria-expanded', 'false');
      } else {
        item.classList.add('active');
        headerEl.setAttribute('aria-expanded', 'true');
      }
    }

    document.body.classList.toggle('siom-dark-theme', localStorage.getItem('siom-theme') === 'dark');
  </script>

</body>
</html>