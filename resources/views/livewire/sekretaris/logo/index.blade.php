<div class="siom-admin-page siom-logo-studio">
    <style>
        .siom-logo-studio {
            font-family: 'Plus Jakarta Sans', sans-serif;
            max-width: 1320px;
            margin: 0 auto;
            padding: 1.5rem 1rem 3rem;
        }

        .siom-logo-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .siom-logo-title {
            font-family: 'Fredoka', 'Plus Jakarta Sans', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0.25rem 0 0.25rem;
            letter-spacing: -0.5px;
        }

        /* 2-Column Responsive Layout */
        .siom-logo-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr);
            gap: 1.75rem;
            align-items: start;
        }

        @media (max-width: 992px) {
            .siom-logo-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Neo-Brutalist Studio Card */
        .siom-studio-card {
            background: #ffffff;
            border: 2.5px solid #0f172a;
            border-radius: 16px;
            box-shadow: 5px 5px 0 #0f172a;
            padding: 1.5rem;
        }

        .siom-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 0.85rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .siom-card-title {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }

        /* Dropzone */
        .siom-logo-dropzone {
            padding: 1.25rem 1rem;
            text-align: center;
            border: 2px dashed #0f172a;
            border-radius: 12px;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .siom-logo-dropzone:hover,
        .siom-logo-dropzone.is-dragging {
            background: #eff6ff;
            border-color: #2563eb;
            transform: translateY(-2px);
        }

        /* Canvas Viewport */
        .siom-viewport-container {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 380px;
            background: radial-gradient(#334155 15%, #0f172a 80%);
            border: 2px solid #0f172a;
            border-radius: 14px;
            overflow: hidden;
            user-select: none;
            touch-action: none;
        }

        /* Control Slider Group */
        .siom-control-box {
            background: #f8fafc;
            border: 2px solid #0f172a;
            border-radius: 12px;
            padding: 1.25rem;
            margin-top: 1.25rem;
        }

        .siom-control-row {
            margin-bottom: 1rem;
        }

        .siom-control-row:last-child {
            margin-bottom: 0;
        }

        .siom-control-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.4rem;
            font-size: 0.85rem;
            font-weight: 700;
            color: #0f172a;
        }

        .siom-control-range {
            width: 100%;
            height: 8px;
            border-radius: 4px;
            background: #cbd5e1;
            outline: none;
            cursor: pointer;
            accent-color: #2563eb;
        }

        /* Button Presets */
        .siom-btn-presets {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1.5px dashed #cbd5e1;
            align-items: center;
            justify-content: space-between;
        }

        .siom-pill-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 0.85rem;
            font-size: 0.82rem;
            font-weight: 700;
            color: #0f172a;
            background: #ffffff;
            border: 2px solid #0f172a;
            border-radius: 8px;
            box-shadow: 2px 2px 0 #0f172a;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .siom-pill-btn:hover {
            background: #f1f5f9;
            transform: translate(-1px, -1px);
            box-shadow: 3px 3px 0 #0f172a;
        }

        .siom-pill-btn:active {
            transform: translate(1px, 1px);
            box-shadow: 1px 1px 0 #0f172a;
        }

        /* Simulator Sheet (A4 Proportion) */
        .siom-sheet-simulator {
            background: #ffffff;
            border: 2.5px solid #0f172a;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08), 4px 4px 0 #0f172a;
            padding: 2.5rem 1.75rem;
            text-align: center;
            min-height: 540px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .siom-cover-title {
            font-family: 'Fredoka', 'Plus Jakarta Sans', sans-serif;
            font-size: 0.92rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #0f172a;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
        }

        .siom-cover-subtitle {
            font-size: 0.78rem;
            font-weight: 600;
            color: #64748b;
            margin: 0;
        }

        /* Center Circular Target Frame */
        .siom-cover-center-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 2.25rem auto;
            width: 100%;
        }

        .siom-cover-target-circle {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            border: 3.5px solid #dc2626;
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08), 0 0 0 5px rgba(220, 38, 38, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            margin: 0 auto;
            transition: all 0.2s ease;
        }

        .siom-cover-target-circle img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            margin: auto;
            pointer-events: none;
        }

        .siom-circle-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #dc2626;
            background: #fef2f2;
            border: 1.5px solid #fecaca;
            border-radius: 9999px;
            padding: 0.2rem 0.75rem;
        }

        .siom-cover-disusun {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .siom-cover-ormawa {
            font-family: 'Fredoka', 'Plus Jakarta Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0;
        }

        .siom-cover-campus-footer {
            margin-top: 2.5rem;
            padding-top: 1rem;
            border-top: 1.5px solid #e2e8f0;
        }

        .siom-cover-campus-name {
            font-size: 0.72rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.35;
            margin: 0;
        }

        .siom-cover-campus-addr {
            font-size: 0.65rem;
            color: #64748b;
            margin: 0.2rem 0 0;
        }
    </style>

    <!-- Header -->
    <div class="siom-logo-header">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="siom-doc-badge" style="background: #e0f2fe; color: #0369a1; border: 1.5px solid #0284c7; padding: 0.2rem 0.65rem; border-radius: 9999px; font-weight: 700; font-size: 0.75rem;">
                    IDENTITAS RESMI ORMAWA
                </span>
                <span style="background: #2563eb; color: #ffffff; padding: 0.2rem 0.65rem; border-radius: 9999px; font-weight: 800; font-size: 0.75rem; letter-spacing: 0.5px;">
                    {{ $ormawa?->code ?? 'ORMAWA' }}
                </span>
            </div>
            <h1 class="siom-logo-title">
                Pengaturan Logo Presisi: {{ $ormawa?->name ?? 'ORMAWA' }}
            </h1>
            <p style="color: #64748b; font-size: 0.88rem; margin: 0;">
                Atur posisi, perbesaran (zoom), dan penempatan logo secara presisi agar tampil tepat di tengah lingkaran cover dokumen resmi.
            </p>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('sekretaris.pengajuan') }}" class="siom-admin-btn siom-admin-btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.5rem 1rem; border: 2px solid #0f172a; border-radius: 8px; background: #fff; font-weight: 700; text-decoration: none; color: #0f172a; box-shadow: 2px 2px 0 #0f172a;">
                <i class="bi bi-arrow-left"></i> Kembali ke Pengajuan
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if (session()->has('success'))
        <div style="background: #dcfce7; color: #166534; border: 2px solid #16a34a; border-radius: 12px; padding: 0.85rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; font-weight: 600; box-shadow: 3px 3px 0 #16a34a;">
            <i class="bi bi-check-circle-fill" style="font-size: 1.25rem;"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if (session()->has('error'))
        <div style="background: #fee2e2; color: #991b1b; border: 2px solid #dc2626; border-radius: 12px; padding: 0.85rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; font-weight: 600; box-shadow: 3px 3px 0 #dc2626;">
            <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.25rem;"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="siom-logo-layout" x-data="ormawaLogoEditor({
        initialLogoUrl: '{{ $ormawa?->logoUrl() }}',
        hasCustomLogo: {{ $ormawa?->hasCustomLogo() ? 'true' : 'false' }},
        savedConfig: @js($savedConfig)
    })">
        <!-- Kolom Kiri: Studio Kalibrasi Logo -->
        <div>
            <div class="siom-studio-card">
                <div class="siom-card-header">
                    <h2 class="siom-card-title">
                        <i class="bi bi-sliders" style="color: #2563eb;"></i> Studio Kalibrasi Logo
                    </h2>
                    <span style="background: #f1f5f9; color: #475569; border: 1.5px solid #94a3b8; padding: 0.2rem 0.65rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">
                        Resolusi Output 600 x 600 px
                    </span>
                </div>

                <!-- Dropzone / File Input -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.4rem;">
                        Unggah Berkas Logo Baru
                    </label>
                    <div 
                        class="siom-logo-dropzone"
                        :class="{ 'is-dragging': isDragging }"
                        x-on:click="$refs.fileInput.click()"
                        x-on:dragover.prevent="isDragging = true"
                        x-on:dragleave.prevent="isDragging = false"
                        x-on:drop.prevent="handleDrop($event)"
                    >
                        <input 
                            type="file" 
                            x-ref="fileInput" 
                            style="display: none;"
                            accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml"
                            x-on:change="handleFileSelect($event)"
                        >
                        <i class="bi bi-cloud-arrow-up" style="font-size: 2rem; color: #2563eb; display: block; margin-bottom: 0.25rem;"></i>
                        <span style="display: block; font-weight: 700; color: #0f172a; font-size: 0.9rem;">
                            Pilih File Logo atau Tarik & Lepas di Sini
                        </span>
                        <span style="color: #64748b; font-size: 0.75rem;">
                            Mendukung PNG transparan, JPG, WebP, SVG (Maks. 5 MB)
                        </span>
                    </div>
                </div>

                <!-- Canvas Viewport dengan Circular Guide -->
                <div style="margin-bottom: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                        <span style="font-size: 0.78rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">
                            Area Pengaturan & Panduan Lingkaran
                        </span>
                        <span style="background: #e2e8f0; color: #334155; font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 4px;">
                            <i class="bi bi-arrows-move"></i> Klik & Geser untuk mengatur posisi
                        </span>
                    </div>

                    <div 
                        class="siom-viewport-container"
                        x-ref="viewport"
                        x-on:mousedown="startPan($event)"
                        x-on:mousemove="pan($event)"
                        x-on:mouseup="endPan()"
                        x-on:mouseleave="endPan()"
                        x-on:wheel.prevent="handleWheel($event)"
                        x-on:touchstart="startTouch($event)"
                        x-on:touchmove="touchMove($event)"
                        x-on:touchend="endTouch()"
                    >
                        <!-- Main Canvas (Hidden in DOM, updated live) -->
                        <canvas x-ref="renderCanvas" width="600" height="600" style="display: none;"></canvas>

                        <!-- Display Canvas -->
                        <canvas x-ref="displayCanvas" width="340" height="340" style="max-width: 100%; max-height: 100%; cursor: grab;"></canvas>

                        <!-- Circular Guide Mask Overlay -->
                        <div style="position: absolute; inset: 0; pointer-events: none; display: flex; justify-content: center; align-items: center;">
                            <div style="width: 280px; height: 280px; border-radius: 50%; border: 3px dashed #38bdf8; box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.65); pointer-events: none; position: relative;">
                                <!-- Crosshair center guide -->
                                <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: rgba(56, 189, 248, 0.45); transform: translateY(-50%);"></div>
                                <div style="position: absolute; left: 50%; top: 0; bottom: 0; width: 1px; background: rgba(56, 189, 248, 0.45); transform: translateX(-50%);"></div>
                            </div>
                        </div>

                        <!-- Guide Label -->
                        <div style="position: absolute; bottom: 0.5rem; left: 0; right: 0; text-align: center; color: rgba(255,255,255,0.7); font-size: 0.72rem; pointer-events: none;">
                            Garis putus-putus biru = Area lingkaran cover dokumen resmi
                        </div>
                    </div>
                </div>

                <!-- Precision Sliders -->
                <div class="siom-control-box">
                    <!-- Zoom / Scale -->
                    <div class="siom-control-row">
                        <div class="siom-control-header">
                            <span><i class="bi bi-zoom-in" style="color: #2563eb;"></i> Perbesaran (Zoom / Skala)</span>
                            <div style="display: flex; align-items: center; gap: 0.35rem;">
                                <button type="button" class="siom-pill-btn" style="padding: 0.15rem 0.55rem; font-size: 0.75rem;" x-on:click="adjustZoom(-0.05)">-</button>
                                <span style="background: #0f172a; color: #fff; font-family: monospace; font-size: 0.8rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 6px; min-width: 52px; text-align: center;" x-text="Math.round(zoom * 100) + '%'">100%</span>
                                <button type="button" class="siom-pill-btn" style="padding: 0.15rem 0.55rem; font-size: 0.75rem;" x-on:click="adjustZoom(0.05)">+</button>
                            </div>
                        </div>
                        <input type="range" class="siom-control-range" min="0.3" max="2.5" step="0.01" x-model.number="zoom" x-on:input="draw()">
                    </div>

                    <!-- Offset X -->
                    <div class="siom-control-row">
                        <div class="siom-control-header">
                            <span><i class="bi bi-arrows-expand-vertical" style="color: #2563eb; transform: rotate(90deg); display: inline-block;"></i> Geser Horisontal (Kiri - Kanan)</span>
                            <div style="display: flex; align-items: center; gap: 0.35rem;">
                                <span style="background: #e2e8f0; color: #0f172a; font-family: monospace; font-size: 0.8rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 6px;" x-text="offsetX + ' px'">0 px</span>
                                <button type="button" class="siom-pill-btn" style="padding: 0.15rem 0.45rem; font-size: 0.75rem;" title="Pusatkan Horisontal" x-on:click="offsetX = 0; draw();">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </div>
                        </div>
                        <input type="range" class="siom-control-range" min="-180" max="180" step="1" x-model.number="offsetX" x-on:input="draw()">
                    </div>

                    <!-- Offset Y -->
                    <div class="siom-control-row">
                        <div class="siom-control-header">
                            <span><i class="bi bi-arrows-expand-vertical" style="color: #2563eb;"></i> Geser Vertikal (Atas - Bawah)</span>
                            <div style="display: flex; align-items: center; gap: 0.35rem;">
                                <span style="background: #e2e8f0; color: #0f172a; font-family: monospace; font-size: 0.8rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 6px;" x-text="offsetY + ' px'">0 px</span>
                                <button type="button" class="siom-pill-btn" style="padding: 0.15rem 0.45rem; font-size: 0.75rem;" title="Pusatkan Vertikal" x-on:click="offsetY = 0; draw();">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </div>
                        </div>
                        <input type="range" class="siom-control-range" min="-180" max="180" step="1" x-model.number="offsetY" x-on:input="draw()">
                    </div>

                    <!-- Quick Preset Buttons & Background Mode -->
                    <div class="siom-btn-presets">
                        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                            <button type="button" class="siom-pill-btn" x-on:click="centerImage()" title="Pusatkan di tengah">
                                🎯 Pusatkan
                            </button>
                            <button type="button" class="siom-pill-btn" x-on:click="fitImage()" title="Paskan ukuran agar muat dalam lingkaran">
                                🔍 Paskan Lingkaran
                            </button>
                            <button type="button" class="siom-pill-btn" x-on:click="fillCircle()" title="Perbesar agar memenuhi lingkaran">
                                ⭕ Penuhi Lingkaran
                            </button>
                            <button type="button" class="siom-pill-btn" x-on:click="rotate(90)" title="Putar 90 derajat">
                                <i class="bi bi-arrow-clockwise"></i> Putar
                            </button>
                        </div>

                        <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; font-weight: 700; color: #0f172a; cursor: pointer; user-select: none;">
                            <input type="checkbox" id="bgWhiteSwitch" style="width: 1.1rem; height: 1.1rem; accent-color: #2563eb; cursor: pointer;" x-model="bgWhite" x-on:change="draw()">
                            Latar Putih Solid
                        </label>
                    </div>
                </div>

                <!-- Action Save / Reset -->
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 2px solid #e2e8f0;">
                    <div>
                        @if ($ormawa?->hasCustomLogo())
                            <button 
                                type="button" 
                                class="siom-pill-btn"
                                style="color: #dc2626; border-color: #dc2626; box-shadow: 2px 2px 0 #dc2626;"
                                wire:click="deleteLogo"
                                wire:confirm="Kembalikan logo ke logo default sistem?"
                            >
                                <i class="bi bi-trash"></i> Hapus & Pakai Default
                            </button>
                        @endif
                    </div>

                    <div style="display: flex; gap: 0.6rem;">
                        <button type="button" class="siom-pill-btn" x-on:click="resetAll()">
                            Reset Kalibrasi
                        </button>
                        <button 
                            type="button" 
                            class="siom-pill-btn"
                            style="background: #2563eb; color: #ffffff; border-color: #0f172a; box-shadow: 3px 3px 0 #0f172a; padding: 0.55rem 1.35rem; font-size: 0.9rem;"
                            x-on:click="saveLogo()"
                            :disabled="saving"
                        >
                            <span x-show="!saving"><i class="bi bi-check2-circle"></i> Simpan Logo Presisi</span>
                            <span x-show="saving" style="display: none;">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Simulasi Real-Time Cover Dokumen Resmi -->
        <div>
            <div class="siom-studio-card">
                <div class="siom-card-header">
                    <h2 class="siom-card-title">
                        <i class="bi bi-file-earmark-text" style="color: #2563eb;"></i> Simulasi Cover Dokumen
                    </h2>
                    <span style="background: #dcfce7; color: #166534; border: 1.5px solid #16a34a; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 800;">
                        Live Real-Time
                    </span>
                </div>

                <p style="color: #64748b; font-size: 0.85rem; margin: 0 0 1.25rem;">
                    Pratinjau langsung bagaimana logo akan muncul tepat di tengah lingkaran pada lembar pertama dokumen Word & PDF resmi:
                </p>

                <!-- Mini Sheet Simulator Card -->
                <div class="siom-sheet-simulator">
                    <!-- Title Section -->
                    <div>
                        <h3 class="siom-cover-title">
                            LAPORAN PERTANGGUNG JAWABAN KEGIATAN
                        </h3>
                        <p class="siom-cover-subtitle">
                            Contoh Kegiatan: Seminar & Workshop Mahasiswa
                        </p>
                    </div>

                    <!-- CENTER LOGO TARGET CIRCLE (Di Tengah-Tengah Pas) -->
                    <div class="siom-cover-center-wrapper">
                        <div class="siom-cover-target-circle">
                            <img 
                                x-ref="simPreviewImg" 
                                src="{{ $ormawa?->logoUrl() }}" 
                                alt="Pratinjau Logo"
                            >
                        </div>
                        <span class="siom-circle-badge">
                            <i class="bi bi-bullseye"></i> Posisi Logo Tengah Cover
                        </span>
                    </div>

                    <!-- Subtitle Disusun Oleh -->
                    <div>
                        <p class="siom-cover-disusun">Disusun oleh:</p>
                        <h4 class="siom-cover-ormawa">
                            {{ mb_strtoupper($ormawa?->name ?? 'HIMA S1 INFORMATIKA') }}
                        </h4>
                    </div>

                    <!-- Campus Footer -->
                    <div class="siom-cover-campus-footer">
                        <p class="siom-cover-campus-name">
                            INSTITUT TEKNOLOGI, SAINS, DAN KESEHATAN<br>
                            RS dr. SOEPRAOEN KESDAM V/BRW MALANG
                        </p>
                        <p class="siom-cover-campus-addr">
                            Jalan Sudanco Supriadi Nomor 22 Sukun, Kota Malang 65147
                        </p>
                    </div>
                </div>

                <!-- Info Box -->
                <div style="margin-top: 1rem; padding: 0.85rem 1rem; background: #f8fafc; border: 2px solid #0f172a; border-radius: 10px; display: flex; gap: 0.65rem; align-items: flex-start;">
                    <i class="bi bi-info-circle-fill" style="color: #2563eb; font-size: 1.15rem; flex-shrink: 0; margin-top: 0.1rem;"></i>
                    <div style="font-size: 0.78rem; line-height: 1.45; color: #334155;">
                        <strong>Posisi Presisi:</strong> Logo yang Anda atur otomatis tersimpan simetris di tengah cover dokumen Word dan PDF resmi saat diunduh maupun dipratinjau.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
Alpine.data('ormawaLogoEditor', ({ initialLogoUrl, hasCustomLogo, savedConfig }) => ({
    isDragging: false,
    saving: false,
    zoom: savedConfig?.zoom ?? 1.0,
    offsetX: savedConfig?.offsetX ?? 0,
    offsetY: savedConfig?.offsetY ?? 0,
    rotation: savedConfig?.rotation ?? 0,
    bgWhite: savedConfig?.bgWhite ?? false,
    img: null,
    isPanning: false,
    panStartX: 0,
    panStartY: 0,

    init() {
        this.loadImage(initialLogoUrl);
    },

    loadImage(url) {
        if (!url) return;
        const image = new Image();
        image.crossOrigin = 'anonymous';
        image.onload = () => {
            this.img = image;
            this.draw();
        };
        image.src = url;
    },

    handleFileSelect(e) {
        const file = e.target.files[0];
        if (file) this.processFile(file);
    },

    handleDrop(e) {
        this.isDragging = false;
        const file = e.dataTransfer.files[0];
        if (file) this.processFile(file);
    },

    processFile(file) {
        if (!file.type.match('image.*')) {
            alert('Silakan pilih berkas gambar (PNG, JPG, SVG, WebP).');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            const image = new Image();
            image.onload = () => {
                this.img = image;
                this.centerImage();
                this.fitImage();
            };
            image.src = e.target.result;
        };
        reader.readAsDataURL(file);
    },

    centerImage() {
        this.offsetX = 0;
        this.offsetY = 0;
        this.draw();
    },

    fitImage() {
        if (!this.img) return;
        const canvas = this.$refs.renderCanvas;
        const circleDiameter = canvas.width * 0.85; // 85% of canvas for circle
        const scaleX = circleDiameter / this.img.width;
        const scaleY = circleDiameter / this.img.height;
        this.zoom = parseFloat(Math.min(scaleX, scaleY).toFixed(2));
        this.draw();
    },

    fillCircle() {
        if (!this.img) return;
        const canvas = this.$refs.renderCanvas;
        const circleDiameter = canvas.width * 0.95;
        const scaleX = circleDiameter / this.img.width;
        const scaleY = circleDiameter / this.img.height;
        this.zoom = parseFloat(Math.max(scaleX, scaleY).toFixed(2));
        this.draw();
    },

    adjustZoom(delta) {
        this.zoom = Math.max(0.3, Math.min(3.0, parseFloat((this.zoom + delta).toFixed(2))));
        this.draw();
    },

    handleWheel(e) {
        const delta = e.deltaY < 0 ? 0.05 : -0.05;
        this.adjustZoom(delta);
    },

    rotate(deg) {
        this.rotation = (this.rotation + deg) % 360;
        this.draw();
    },

    resetAll() {
        this.zoom = 1.0;
        this.offsetX = 0;
        this.offsetY = 0;
        this.rotation = 0;
        this.bgWhite = false;
        if (this.img) {
            this.fitImage();
        } else {
            this.draw();
        }
    },

    startPan(e) {
        this.isPanning = true;
        this.panStartX = e.clientX - this.offsetX;
        this.panStartY = e.clientY - this.offsetY;
        if (this.$refs.displayCanvas) {
            this.$refs.displayCanvas.style.cursor = 'grabbing';
        }
    },

    pan(e) {
        if (!this.isPanning) return;
        this.offsetX = Math.round(e.clientX - this.panStartX);
        this.offsetY = Math.round(e.clientY - this.panStartY);
        this.draw();
    },

    endPan() {
        this.isPanning = false;
        if (this.$refs.displayCanvas) {
            this.$refs.displayCanvas.style.cursor = 'grab';
        }
    },

    startTouch(e) {
        if (e.touches.length === 1) {
            this.isPanning = true;
            this.panStartX = e.touches[0].clientX - this.offsetX;
            this.panStartY = e.touches[0].clientY - this.offsetY;
        }
    },

    touchMove(e) {
        if (!this.isPanning || e.touches.length !== 1) return;
        this.offsetX = Math.round(e.touches[0].clientX - this.panStartX);
        this.offsetY = Math.round(e.touches[0].clientY - this.panStartY);
        this.draw();
    },

    endTouch() {
        this.isPanning = false;
    },

    draw() {
        const renderCanvas = this.$refs.renderCanvas;
        const displayCanvas = this.$refs.displayCanvas;
        if (!renderCanvas || !displayCanvas) return;

        const rCtx = renderCanvas.getContext('2d');
        const dCtx = displayCanvas.getContext('2d');
        const W = renderCanvas.width;
        const H = renderCanvas.height;

        // Clear render canvas
        rCtx.clearRect(0, 0, W, H);

        if (this.bgWhite) {
            // Draw clean white circular backing
            rCtx.save();
            rCtx.beginPath();
            rCtx.arc(W / 2, H / 2, W / 2, 0, Math.PI * 2);
            rCtx.fillStyle = '#ffffff';
            rCtx.fill();
            rCtx.restore();
        }

        if (this.img) {
            rCtx.save();
            // Move origin to center + user offsets
            rCtx.translate(W / 2 + this.offsetX, H / 2 + this.offsetY);
            if (this.rotation !== 0) {
                rCtx.rotate((this.rotation * Math.PI) / 180);
            }
            // Scale
            rCtx.scale(this.zoom, this.zoom);
            // Draw image centered at origin
            rCtx.drawImage(this.img, -this.img.width / 2, -this.img.height / 2);
            rCtx.restore();
        }

        // Copy rendered content to display canvas
        dCtx.clearRect(0, 0, displayCanvas.width, displayCanvas.height);
        dCtx.drawImage(renderCanvas, 0, 0, displayCanvas.width, displayCanvas.height);

        // Update Simulator Image
        const dataUrl = renderCanvas.toDataURL('image/png');
        if (this.$refs.simPreviewImg) {
            this.$refs.simPreviewImg.src = dataUrl;
        }
    },

    saveLogo() {
        const renderCanvas = this.$refs.renderCanvas;
        if (!renderCanvas || !this.img) {
            alert('Silakan pilih berkas gambar logo terlebih dahulu.');
            return;
        }

        this.saving = true;
        const dataUrl = renderCanvas.toDataURL('image/png');
        const config = {
            zoom: this.zoom,
            offsetX: this.offsetX,
            offsetY: this.offsetY,
            rotation: this.rotation,
            bgWhite: this.bgWhite
        };

        this.$wire.saveCalibratedLogo(dataUrl, config)
            .finally(() => {
                this.saving = false;
            });
    }
}));
</script>
@endscript
