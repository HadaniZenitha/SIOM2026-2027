<div class="siom-dashboard">
    <style>
        .siom-dashboard .card {
            margin-top: 1rem;
            border: 2.5px solid #0f172a;
            border-radius: 18px;
            background: #fff;
            box-shadow: 4px 4px 0 #0f172a;
        }
        .siom-dashboard .card-body { padding: 1.15rem 1.25rem; }
        .siom-dashboard .siom-panel-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .siom-dashboard .siom-panel-head h2 { margin: 0; }
        .siom-dashboard .siom-panel-head p { margin: .2rem 0 0; }
        .siom-dashboard .siom-search,
        .siom-dashboard .form-control {
            width: min(100%, 280px);
            min-height: 2.6rem;
            margin: 0;
            padding: .55rem .8rem;
            border: 1.5px solid #94a3b8;
            border-radius: 10px;
            background: #f8fafc;
            font: 500 .9rem 'Plus Jakarta Sans', sans-serif;
        }
        .siom-dashboard .siom-sign-form { display: flex; align-items: flex-end; gap: .75rem; }
        .siom-dashboard .siom-sign-form .siom-field { flex: 1; min-width: 0; }
        .siom-dashboard .siom-field label { display: block; margin-bottom: .35rem; font-size: .85rem; font-weight: 700; }
        .siom-dashboard .siom-field .form-control { width: 100%; }
        .siom-dashboard .siom-sign-tabs {
            display: flex;
            gap: .5rem;
            margin-bottom: 1.25rem;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: .75rem;
        }
        .siom-dashboard .siom-sign-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .45rem .95rem;
            border: 2px solid transparent;
            border-radius: 999px;
            background: #f1f5f9;
            color: #475569;
            font: 700 .85rem 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: all .15s ease;
        }
        .siom-dashboard .siom-sign-tab-btn.is-active {
            background: #0f172a;
            color: #fff;
            border-color: #0f172a;
            box-shadow: 2px 2px 0 #3b82f6;
        }
        .siom-dashboard .siom-sign-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr);
            gap: 1.5rem;
            align-items: flex-start;
        }
        @media (max-width: 860px) {
            .siom-dashboard .siom-sign-layout {
                grid-template-columns: 1fr;
            }
        }
        .siom-dashboard .siom-canvas-wrapper {
            position: relative;
            background: #ffffff;
            border: 2.5px solid #0f172a;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 3px 3px 0 #0f172a;
            touch-action: none;
        }
        .siom-dashboard .siom-canvas-guide {
            position: absolute;
            bottom: 42px;
            left: 20px;
            right: 20px;
            border-bottom: 1.5px dashed #cbd5e1;
            pointer-events: none;
            display: flex;
            justify-content: space-between;
            color: #94a3b8;
            font-size: .75rem;
            font-weight: 600;
            padding-bottom: 4px;
        }
        .siom-dashboard .siom-sign-canvas {
            display: block;
            width: 100%;
            height: 240px;
            cursor: crosshair;
        }
        .siom-dashboard .siom-canvas-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .5rem;
            margin-top: .75rem;
            flex-wrap: wrap;
        }
        .siom-dashboard .siom-preview-card {
            background: #f8fafc;
            border: 2px dashed #94a3b8;
            border-radius: 14px;
            padding: 1rem;
            text-align: center;
        }
        .siom-dashboard .siom-checkerboard {
            background-color: #ffffff;
            background-image: linear-gradient(45deg, #f1f5f9 25%, transparent 25%),
                              linear-gradient(-45deg, #f1f5f9 25%, transparent 25%),
                              linear-gradient(45deg, transparent 75%, #f1f5f9 75%),
                              linear-gradient(-45deg, transparent 75%, #f1f5f9 75%);
            background-size: 16px 16px;
            background-position: 0 0, 0 8px, 8px -8px, -8px 0px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            min-height: 130px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: .75rem;
        }
        .siom-dashboard .siom-checkerboard img {
            max-height: 120px;
            max-width: 100%;
            object-fit: contain;
        }
        .siom-dashboard .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.45rem;
            padding: .45rem .9rem;
            border: 2px solid #0f172a;
            border-radius: 999px;
            background: #fff;
            color: #0f172a;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 2px 2px 0 #0f172a;
        }
        .siom-dashboard .btn-primary { background: #2563eb; color: #fff; }
        .siom-dashboard .badge {
            display: inline-flex;
            padding: .35rem .7rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 800;
        }
        .siom-dashboard .bg-success { background: #dcfce7; color: #166534; }
        .siom-dashboard .bg-warning { background: #fef9c3; color: #854d0e; }
        .siom-dashboard .alert { margin-top: 1rem; padding: .85rem 1rem; border: 2px solid #0f172a; border-radius: 12px; }
        .siom-dashboard .alert-success { background: #dcfce7; }
        .siom-dashboard .alert-danger { background: #fee2e2; }
        .siom-dashboard .text-danger { color: #dc2626; }
        .siom-dashboard .text-muted { color: #475569; }
        .siom-dashboard .siom-track-list { display: grid; gap: .75rem; }
        .siom-dashboard .siom-track {
            display: flex;
            flex-direction: column;
            gap: .7rem;
            min-width: 0;
            padding: .95rem 1rem;
            border: 1.5px solid #0f172a;
            border-radius: 14px;
            background: #f8fbff;
        }
        .siom-dashboard .siom-track-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem 1rem;
        }
        .siom-dashboard .siom-track-head > div:first-child { min-width: 0; }
        .siom-dashboard .siom-track-head strong,
        .siom-dashboard .siom-track-head small { display: block; overflow-wrap: anywhere; }
        .siom-dashboard .siom-track-head small { margin-top: .15rem; color: #475569; font-size: .8rem; }
        .siom-dashboard .siom-track-status { color: #1e3a8a; font-size: .84rem; font-weight: 800; white-space: nowrap; }
        .siom-dashboard .siom-step-cols { display: flex; flex-direction: column; gap: .4rem; }
        .siom-dashboard .siom-step-col {
            display: grid;
            grid-template-columns: minmax(140px, 1.1fr) 130px minmax(0, 1.6fr);
            gap: .35rem .75rem;
            align-items: center;
            min-width: 0;
            padding: .55rem .75rem;
            border: 1.5px solid currentColor;
            border-radius: 12px;
        }
        .siom-dashboard .siom-step-col span,
        .siom-dashboard .siom-step-col strong,
        .siom-dashboard .siom-step-col small { display: block; overflow-wrap: anywhere; }
        .siom-dashboard .siom-step-col span { font-size: .72rem; font-weight: 800; letter-spacing: .03em; text-transform: uppercase; }
        .siom-dashboard .siom-step-col strong { font-size: .9rem; }
        .siom-dashboard .siom-step-col small { color: inherit; font-size: .75rem; opacity: .85; }
        .siom-dashboard .is-approved, .siom-dashboard .is-completed { background: #dcfce7; color: #166534; }
        .siom-dashboard .is-pending, .siom-dashboard .is-submitted, .siom-dashboard .is-waiting_approval { background: #fef9c3; color: #854d0e; }
        .siom-dashboard .is-waiting, .siom-dashboard .is-draft { background: #f1f5f9; color: #475569; }
        .siom-dashboard .is-revision { background: #e0f2fe; color: #075985; }
        .siom-dashboard .is-rejected { background: #fee2e2; color: #991b1b; }
        .siom-dashboard .siom-empty { margin: 0; padding: 1.25rem .5rem; color: #475569; text-align: center; }
        @media (max-width: 760px) {
            .siom-dashboard .siom-panel-head,
            .siom-dashboard .siom-sign-form,
            .siom-dashboard .siom-track-head { align-items: stretch; flex-direction: column; }
            .siom-dashboard .siom-search,
            .siom-dashboard .siom-sign-form .btn { width: 100%; }
            .siom-dashboard .siom-step-col { grid-template-columns: 1fr; }
            .siom-dashboard .siom-track-status { white-space: normal; }
        }
    </style>

    <header class="siom-dashboard-heading">
        <div>
            <h1>Selamat datang, {{ $user->name }}</h1>
            <p>Akses cepat untuk mengelola kegiatan dan layanan organisasi mahasiswa.</p>
        </div>
        <span class="siom-dashboard-role">
            <i class="bi bi-person-badge"></i>
            {{ $user->role?->display_name ?? 'Pengguna' }}
        </span>
    </header>

    <div class="siom-dashboard-shortcuts">
        @if ($user->hasRole('superadmin'))
            <a class="siom-dashboard-shortcut" href="{{ route('admin.ormawa') }}" wire:navigate><i class="bi bi-diagram-3"></i><strong>ORMAWA dan Pagu</strong><span>Kelola organisasi dan anggaran tiap periode.</span></a>
            <a class="siom-dashboard-shortcut" href="{{ route('admin.program-studi') }}" wire:navigate><i class="bi bi-mortarboard"></i><strong>Program Studi</strong><span>Kelola data program studi dan fakultas.</span></a>
            <a class="siom-dashboard-shortcut" href="{{ route('admin.periode') }}" wire:navigate><i class="bi bi-calendar3"></i><strong>Periode</strong><span>{{ $activePeriod ? 'Periode aktif: '.$activePeriod->name : 'Atur periode organisasi.' }}</span></a>
            <a class="siom-dashboard-shortcut" href="{{ route('admin.pengguna') }}" wire:navigate><i class="bi bi-people"></i><strong>Pengguna</strong><span>Kelola akun, role, dan akses SIOM.</span></a>
            <a class="siom-dashboard-shortcut" href="{{ route('admin.template-dokumen') }}" wire:navigate><i class="bi bi-file-earmark-word"></i><strong>Template Dokumen</strong><span>Kelola template Proposal dan LPJ.</span></a>
        @elseif ($user->hasRole('sekretaris_ormawa'))
            <a class="siom-dashboard-shortcut" href="{{ route('sekretaris.anggota') }}" wire:navigate><i class="bi bi-person-vcard"></i><strong>Anggota</strong><span>Kelola data anggota organisasi.</span></a>
            <a class="siom-dashboard-shortcut" href="{{ route('sekretaris.pengajuan') }}" wire:navigate><i class="bi bi-send"></i><strong>Pengajuan Dana</strong><span>Lihat status atau buat pengajuan kegiatan.</span></a>
            <a class="siom-dashboard-shortcut" href="{{ route('sekretaris.notulensi') }}" wire:navigate><i class="bi bi-journal-text"></i><strong>Notulensi</strong><span>Catat dan kelola rapat organisasi.</span></a>
            <a class="siom-dashboard-shortcut" href="{{ route('sekretaris.kas') }}" wire:navigate><i class="bi bi-wallet2"></i><strong>Kas ORMAWA</strong><span>Kelola transaksi dan catatan kas.</span></a>
            <a class="siom-dashboard-shortcut" href="{{ route('sekretaris.lpj') }}" wire:navigate><i class="bi bi-clipboard-check"></i><strong>Laporan Pertanggungjawaban</strong><span>Kelola LPJ kegiatan.</span></a>
        @elseif ($user->hasAnyRole(['pembina_ormawa', 'kaprodi', 'kabag_kemahasiswaan', 'warek']))
            <a class="siom-dashboard-shortcut" href="{{ route('approval.index') }}" wire:navigate><i class="bi bi-check2-square"></i><strong>Persetujuan Pengajuan</strong><span>Tinjau pengajuan yang menunggu keputusan Anda.</span></a>
        @endif
        <a class="siom-dashboard-shortcut" href="{{ route('laporan') }}" wire:navigate><i class="bi bi-bar-chart"></i><strong>Laporan</strong><span>Akses ringkasan dan laporan kegiatan.</span></a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($isApprover)
        <section class="card" x-data="signatureCanvas()" x-init="initCanvas()">
            <div class="card-body">
                <div class="siom-panel-head">
                    <div>
                        <h2 class="h5">Tanda Tangan Persetujuan</h2>
                        <p class="text-muted">Tanda tangan digital transparan dan seragam untuk pengesahan Proposal & LPJ.</p>
                    </div>
                    <span class="badge {{ $user->signature_path ? 'bg-success' : 'bg-warning' }}">
                        {{ $user->signature_path ? 'Tanda tangan tersimpan' : 'Belum ada tanda tangan' }}
                    </span>
                </div>

                <div class="siom-sign-tabs">
                    <button type="button" class="siom-sign-tab-btn" :class="{ 'is-active': mode === 'draw' }" @click="mode = 'draw'; $nextTick(() => resizeCanvas())">
                        <i class="bi bi-pen"></i> Tanda Tangan di Layar (Direkomendasikan)
                    </button>
                    <button type="button" class="siom-sign-tab-btn" :class="{ 'is-active': mode === 'upload' }" @click="mode = 'upload'">
                        <i class="bi bi-cloud-arrow-up"></i> Unggah Berkas Gambar
                    </button>
                </div>

                <div class="siom-sign-layout">
                    <!-- Area Tanda Tangan / Form -->
                    <div>
                        <!-- Mode Draw: Canvas Pad -->
                        <div x-show="mode === 'draw'" style="display: none;" x-transition>
                            <div class="siom-canvas-wrapper">
                                <div class="siom-canvas-guide">
                                    <span>Garis panduan tanda tangan</span>
                                    <span>Tinta Hitam Pekat</span>
                                </div>
                                <canvas
                                    x-ref="canvas"
                                    class="siom-sign-canvas"
                                    @mousedown="startDrawing($event)"
                                    @mousemove="draw($event)"
                                    @mouseup="stopDrawing()"
                                    @mouseleave="stopDrawing()"
                                    @touchstart="startDrawing($event)"
                                    @touchmove="draw($event)"
                                    @touchend="stopDrawing()"
                                    @touchcancel="stopDrawing()"
                                ></canvas>
                            </div>

                            @error('signatureDrawn') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

                            <div class="siom-canvas-toolbar">
                                <div style="display: flex; gap: .5rem;">
                                    <button type="button" class="btn" style="min-height: 2.2rem; font-size: .8rem; padding: .3rem .7rem;" @click="clear()" :disabled="!hasStrokes">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Bersihkan
                                    </button>
                                    <button type="button" class="btn" style="min-height: 2.2rem; font-size: .8rem; padding: .3rem .7rem;" @click="undo()" :disabled="!hasStrokes">
                                        <i class="bi bi-arrow-left me-1"></i> Undo
                                    </button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary" @click="saveSignature()" :disabled="!hasStrokes || saving" wire:loading.attr="disabled">
                                        <span x-show="!saving"><i class="bi bi-check-lg me-1"></i> Simpan Tanda Tangan Digital</span>
                                        <span x-show="saving" style="display: none;"><i class="bi bi-hourglass-split me-1"></i> Menyimpan…</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Mode Upload: File Input -->
                        <div x-show="mode === 'upload'" style="display: none;" x-transition>
                            <form wire:submit="uploadSignature" class="siom-sign-form">
                                <div class="siom-field">
                                    <label for="signatureFile">Pilih gambar tanda tangan (PNG/JPG, maks 2 MB)</label>
                                    <input id="signatureFile" type="file" class="form-control" wire:model="signatureFile" accept=".png,.jpg,.jpeg">
                                    @error('signatureFile') <small class="text-danger">{{ $message }}</small> @enderror
                                    <span wire:loading wire:target="signatureFile" class="text-muted">Mengunggah…</span>
                                </div>
                                <button class="btn btn-primary" type="submit" wire:loading.attr="disabled" wire:target="uploadSignature,signatureFile">Unggah</button>
                            </form>
                        </div>
                    </div>

                    <!-- Area Preview Tanda Tangan Aktif -->
                    <div>
                        <div class="siom-preview-card">
                            <h3 class="h6 mb-2" style="font-weight: 700;">Pratinjau Tanda Tangan Aktif</h3>
                            @if ($savedSignaturePreview)
                                <div class="siom-checkerboard mb-2">
                                    <img src="{{ $savedSignaturePreview }}" alt="Tanda Tangan {{ $user->name }}">
                                </div>
                                <small class="text-muted d-block">Latar belakang transparan. Dokumen akan menggunakan tanda tangan ini secara seragam.</small>
                            @else
                                <div class="siom-checkerboard mb-2" style="color: #94a3b8; font-size: .85rem;">
                                    Belum ada tanda tangan aktif.
                                </div>
                                <small class="text-muted d-block">Silakan tanda tangani di canvas layar atau unggah gambar di samping.</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="card" x-data="{ q: '' }">
            <div class="card-body">
                <div class="siom-panel-head">
                    <div>
                        <h2 class="h5">Proposal yang Telah Disetujui</h2>
                        <p class="text-muted">Menampilkan 10 proposal terbaru dalam cakupan akun Anda.</p>
                    </div>
                    <input class="siom-search" type="search" placeholder="Cari kegiatan atau ORMAWA..." x-model="q">
                </div>
                <div class="siom-track-list">
                    @forelse ($approvedProposals as $proposal)
                        <article class="siom-track" wire:key="dashboard-approved-proposal-{{ $proposal->id }}" x-show="!q || $el.innerText.toLowerCase().includes(q.toLowerCase())">
                            <div class="siom-track-head">
                                <div>
                                    <strong>{{ $proposal->activity_name }}</strong>
                                    <small>{{ $proposal->ormawa?->name }} · {{ $proposal->period?->name }}</small>
                                </div>
                                <button type="button" class="btn" wire:click="downloadSignedProposal({{ $proposal->id }})" wire:loading.attr="disabled">Unduh DOCX</button>
                            </div>
                            <div class="siom-step-cols">
                                @foreach ($proposal->approvalSteps as $step)
                                    @php $stepStatus = $step->status instanceof \BackedEnum ? $step->status->value : (string) $step->status; @endphp
                                    <div class="siom-step-col is-{{ $stepStatus }}">
                                        <span>{{ $step->role?->display_name ?? 'Tahap' }}</span>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $stepStatus)) }}</strong>
                                        <small>
                                            {{ $step->approver?->name }}
                                            @if ($step->approved_at) · {{ $step->approved_at->format('d/m/Y H:i') }} @endif
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @empty
                        <p class="siom-empty">Belum ada proposal yang disetujui.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="card" x-data="{ q: '' }">
            <div class="card-body">
                <div class="siom-panel-head">
                    <div>
                        <h2 class="h5">Keputusan Persetujuan Saya</h2>
                        <p class="text-muted">Menampilkan 10 keputusan proposal terbaru dari akun Anda.</p>
                    </div>
                    <input class="siom-search" type="search" placeholder="Cari kegiatan..." x-model="q">
                </div>
                <div class="siom-track-list">
                    @php $hasDecision = false; @endphp
                    @foreach ($myDecisions as $decision)
                        @foreach ($decision->approvalSteps as $step)
                            @php
                                $hasDecision = true;
                                $stepStatus = $step->status instanceof \BackedEnum ? $step->status->value : (string) $step->status;
                            @endphp
                            <article class="siom-track" wire:key="dashboard-my-decision-{{ $step->id }}" x-show="!q || $el.innerText.toLowerCase().includes(q.toLowerCase())">
                                <div class="siom-track-head">
                                    <div>
                                        <strong>{{ $decision->activity_name }}</strong>
                                        <small>{{ $decision->ormawa?->name }}</small>
                                    </div>
                                    <span class="siom-step-pill siom-step-col is-{{ $stepStatus }}" style="grid-template-columns: auto auto;">
                                        <span>{{ $step->role?->display_name }}</span>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $stepStatus)) }}@if ($step->approved_at) · {{ $step->approved_at->format('d/m/Y H:i') }} @endif</strong>
                                    </span>
                                </div>
                            </article>
                        @endforeach
                    @endforeach
                    @unless ($hasDecision)
                        <p class="siom-empty">Belum ada keputusan yang tercatat.</p>
                    @endunless
                </div>
            </div>
        </section>

        <section class="card" x-data="{ q: '' }">
            <div class="card-body">
                <div class="siom-panel-head">
                    <div>
                        <h2 class="h5">LPJ yang Telah Disetujui</h2>
                        <p class="text-muted">Menampilkan 10 LPJ terbaru dalam cakupan akun Anda.</p>
                    </div>
                    <input class="siom-search" type="search" placeholder="Cari LPJ atau kegiatan..." x-model="q">
                </div>
                <div class="siom-track-list">
                    @forelse ($approvedLpjs as $lpj)
                        <article class="siom-track" wire:key="dashboard-approved-lpj-{{ $lpj->id }}" x-show="!q || $el.innerText.toLowerCase().includes(q.toLowerCase())">
                            <div class="siom-track-head">
                                <div>
                                    <strong>{{ $lpj->title }}</strong>
                                    <small>{{ $lpj->submission?->activity_name }} · {{ $lpj->ormawa?->name }}</small>
                                </div>
                                <button type="button" class="btn" wire:click="downloadSignedLpj({{ $lpj->id }})" wire:loading.attr="disabled">Unduh DOCX</button>
                            </div>
                            <div class="siom-step-cols">
                                @foreach ($lpj->approvalSteps as $step)
                                    @php $stepStatus = $step->status instanceof \BackedEnum ? $step->status->value : (string) $step->status; @endphp
                                    <div class="siom-step-col is-{{ $stepStatus }}">
                                        <span>{{ $step->role?->display_name ?? 'Tahap' }}</span>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $stepStatus)) }}</strong>
                                        <small>{{ $step->approver?->name }}@if ($step->approved_at) · {{ $step->approved_at->format('d/m/Y H:i') }} @endif</small>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @empty
                        <p class="siom-empty">Belum ada LPJ yang disetujui.</p>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

    @if ($user->hasRole('sekretaris_ormawa') || $user->hasRole('superadmin'))
        <section class="card" x-data="{ q: '' }">
            <div class="card-body">
                <div class="siom-panel-head">
                    <div>
                        <h2 class="h5">Tracking Progres Proposal</h2>
                        <p class="text-muted">Menampilkan maksimal 15 pengajuan terbaru dalam cakupan akun Anda.</p>
                    </div>
                    <input class="siom-search" type="search" placeholder="Cari kegiatan atau ORMAWA..." x-model="q">
                </div>
                <div class="siom-track-list">
                    @forelse ($trackedSubmissions as $proposal)
                        @php $proposalStatus = $proposal->status instanceof \BackedEnum ? $proposal->status->value : (string) $proposal->status; @endphp
                        <article class="siom-track" wire:key="dashboard-tracked-proposal-{{ $proposal->id }}" x-show="!q || $el.innerText.toLowerCase().includes(q.toLowerCase())">
                            <div class="siom-track-head">
                                <div>
                                    <strong>{{ $proposal->activity_name }}</strong>
                                    <small>{{ $proposal->ormawa?->name }} · {{ $proposal->submission_number }}</small>
                                </div>
                                <span class="siom-track-status">{{ ucfirst(str_replace('_', ' ', $proposalStatus)) }}</span>
                                @if ($proposalStatus === 'approved')
                                    <button type="button" class="btn" wire:click="downloadSignedProposal({{ $proposal->id }})">Unduh DOCX</button>
                                @endif
                            </div>
                            <div class="siom-step-cols">
                                @foreach ($proposal->approvalSteps as $step)
                                    @php $stepStatus = $step->status instanceof \BackedEnum ? $step->status->value : (string) $step->status; @endphp
                                    <div class="siom-step-col is-{{ $stepStatus }}">
                                        <span>{{ $step->role?->display_name ?? 'Tahap' }}</span>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $stepStatus)) }}</strong>
                                        <small>{{ $step->approver?->name }}@if ($step->approved_at) · {{ $step->approved_at->format('d/m/Y H:i') }} @endif</small>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @empty
                        <p class="siom-empty">Belum ada proposal.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="card" x-data="{ q: '' }">
            <div class="card-body">
                <div class="siom-panel-head">
                    <div>
                        <h2 class="h5">Tracking Progres LPJ</h2>
                        <p class="text-muted">Menampilkan maksimal 15 LPJ terbaru dalam cakupan akun Anda.</p>
                    </div>
                    <input class="siom-search" type="search" placeholder="Cari LPJ atau kegiatan..." x-model="q">
                </div>
                <div class="siom-track-list">
                    @forelse ($trackedLpjs as $lpj)
                        @php $lpjStatus = $lpj->status instanceof \BackedEnum ? $lpj->status->value : (string) $lpj->status; @endphp
                        <article class="siom-track" wire:key="dashboard-tracked-lpj-{{ $lpj->id }}" x-show="!q || $el.innerText.toLowerCase().includes(q.toLowerCase())">
                            <div class="siom-track-head">
                                <div>
                                    <strong>{{ $lpj->title }}</strong>
                                    <small>{{ $lpj->submission?->activity_name }} · {{ $lpj->ormawa?->name }}</small>
                                </div>
                                <span class="siom-track-status">{{ ucfirst(str_replace('_', ' ', $lpjStatus)) }}</span>
                            </div>
                            <div class="siom-step-cols">
                                @foreach ($lpj->approvalSteps as $step)
                                    @php $stepStatus = $step->status instanceof \BackedEnum ? $step->status->value : (string) $step->status; @endphp
                                    <div class="siom-step-col is-{{ $stepStatus }}">
                                        <span>{{ $step->role?->display_name ?? 'Tahap' }}</span>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $stepStatus)) }}</strong>
                                        <small>{{ $step->approver?->name }}@if ($step->approved_at) · {{ $step->approved_at->format('d/m/Y H:i') }} @endif</small>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @empty
                        <p class="siom-empty">Belum ada LPJ.</p>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

    <script>
        function signatureCanvas() {
            return {
                mode: 'draw',
                penColor: '#0f172a',
                strokes: [],
                currentStroke: [],
                isDrawing: false,
                canvas: null,
                ctx: null,
                hasStrokes: false,
                saving: false,

                initCanvas() {
                    this.$nextTick(() => {
                        this.canvas = this.$refs.canvas;
                        if (!this.canvas) return;
                        this.ctx = this.canvas.getContext('2d');
                        this.resizeCanvas();
                        window.addEventListener('resize', () => this.resizeCanvas());
                    });
                },

                resizeCanvas() {
                    if (!this.canvas) return;
                    const rect = this.canvas.parentElement.getBoundingClientRect();
                    const dpr = window.devicePixelRatio || 1;
                    const displayWidth = Math.max(300, Math.floor(rect.width));
                    const displayHeight = 240;

                    if (this.canvas.width !== displayWidth * dpr || this.canvas.height !== displayHeight * dpr) {
                        this.canvas.width = displayWidth * dpr;
                        this.canvas.height = displayHeight * dpr;
                        this.ctx.scale(dpr, dpr);
                        this.redraw();
                    }
                },

                startDrawing(e) {
                    e.preventDefault();
                    this.isDrawing = true;
                    const pt = this.getPoint(e);
                    this.currentStroke = [pt];
                    this.drawDot(pt);
                },

                draw(e) {
                    if (!this.isDrawing) return;
                    e.preventDefault();
                    const pt = this.getPoint(e);
                    this.currentStroke.push(pt);

                    const pts = this.currentStroke;
                    if (pts.length > 1) {
                        this.ctx.beginPath();
                        this.ctx.lineCap = 'round';
                        this.ctx.lineJoin = 'round';
                        this.ctx.strokeStyle = this.penColor;
                        this.ctx.lineWidth = 3.6;

                        const p1 = pts[pts.length - 2];
                        const p2 = pts[pts.length - 1];
                        this.ctx.moveTo(p1.x, p1.y);
                        this.ctx.lineTo(p2.x, p2.y);
                        this.ctx.stroke();
                    }
                    this.hasStrokes = true;
                },

                stopDrawing() {
                    if (!this.isDrawing) return;
                    this.isDrawing = false;
                    if (this.currentStroke.length > 0) {
                        this.strokes.push({
                            color: this.penColor,
                            points: [...this.currentStroke]
                        });
                        this.currentStroke = [];
                        this.hasStrokes = this.strokes.length > 0;
                    }
                },

                drawDot(pt) {
                    this.ctx.beginPath();
                    this.ctx.fillStyle = this.penColor;
                    this.ctx.arc(pt.x, pt.y, 1.8, 0, Math.PI * 2);
                    this.ctx.fill();
                },

                getPoint(e) {
                    const rect = this.canvas.getBoundingClientRect();
                    const clientX = e.touches && e.touches.length > 0 ? e.touches[0].clientX : e.clientX;
                    const clientY = e.touches && e.touches.length > 0 ? e.touches[0].clientY : e.clientY;
                    return {
                        x: clientX - rect.left,
                        y: clientY - rect.top
                    };
                },

                redraw() {
                    if (!this.ctx || !this.canvas) return;
                    const dpr = window.devicePixelRatio || 1;
                    this.ctx.clearRect(0, 0, this.canvas.width / dpr, this.canvas.height / dpr);

                    for (const stroke of this.strokes) {
                        if (stroke.points.length === 1) {
                            this.ctx.beginPath();
                            this.ctx.fillStyle = stroke.color;
                            this.ctx.arc(stroke.points[0].x, stroke.points[0].y, 1.8, 0, Math.PI * 2);
                            this.ctx.fill();
                            continue;
                        }
                        this.ctx.beginPath();
                        this.ctx.lineCap = 'round';
                        this.ctx.lineJoin = 'round';
                        this.ctx.strokeStyle = stroke.color;
                        this.ctx.lineWidth = 3.6;
                        this.ctx.moveTo(stroke.points[0].x, stroke.points[0].y);
                        for (let i = 1; i < stroke.points.length; i++) {
                            this.ctx.lineTo(stroke.points[i].x, stroke.points[i].y);
                        }
                        this.ctx.stroke();
                    }
                    this.hasStrokes = this.strokes.length > 0;
                },

                clear() {
                    this.strokes = [];
                    this.currentStroke = [];
                    this.hasStrokes = false;
                    this.redraw();
                },

                undo() {
                    if (this.strokes.length > 0) {
                        this.strokes.pop();
                        this.redraw();
                    }
                },

                saveSignature() {
                    if (!this.hasStrokes) return;
                    this.saving = true;
                    const dataUrl = this.exportTrimmedDataUrl();
                    this.$wire.saveDrawnSignature(dataUrl).finally(() => {
                        this.saving = false;
                    });
                },

                exportTrimmedDataUrl() {
                    if (!this.strokes || this.strokes.length === 0) {
                        return this.canvas.toDataURL('image/png');
                    }

                    const dpr = window.devicePixelRatio || 1;
                    const canvasW = this.canvas.width / dpr;
                    const canvasH = this.canvas.height / dpr;

                    // 1. Hitung bounding box sebenarnya dari seluruh goresan tanda tangan
                    let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
                    for (const stroke of this.strokes) {
                        for (const pt of stroke.points) {
                            if (pt.x < minX) minX = pt.x;
                            if (pt.x > maxX) maxX = pt.x;
                            if (pt.y < minY) minY = pt.y;
                            if (pt.y > maxY) maxY = pt.y;
                        }
                    }

                    if (minX === Infinity || maxX === -Infinity) {
                        return this.canvas.toDataURL('image/png');
                    }

                    // 2. Beri margin/padding aman (14px) agar lekukan tepi goresan tidak terpotong
                    const padding = 14;
                    const srcX = Math.max(0, minX - padding);
                    const srcY = Math.max(0, minY - padding);
                    const srcW = Math.min(canvasW - srcX, (maxX - minX) + (padding * 2));
                    const srcH = Math.min(canvasH - srcY, (maxY - minY) + (padding * 2));

                    // 3. Siapkan canvas ekspor berstandar tanda tangan resmi (proporsional & tajam)
                    const exportWidth = 520;
                    const aspect = srcW / Math.max(1, srcH);
                    const exportHeight = Math.round(Math.min(260, Math.max(170, exportWidth / Math.max(1.3, aspect))));

                    const offCanvas = document.createElement('canvas');
                    offCanvas.width = exportWidth;
                    offCanvas.height = exportHeight;
                    const offCtx = offCanvas.getContext('2d');
                    offCtx.clearRect(0, 0, exportWidth, exportHeight);

                    // 4. Skalakan goresan agar mengisi ~88% kanvas ekspor secara proporsional dan terpusat
                    const scale = Math.min((exportWidth * 0.88) / srcW, (exportHeight * 0.86) / srcH);
                    const drawW = srcW * scale;
                    const drawH = srcH * scale;
                    const destX = (exportWidth - drawW) / 2;
                    const destY = (exportHeight - drawH) / 2;

                    offCtx.drawImage(
                        this.canvas,
                        srcX * dpr, srcY * dpr, srcW * dpr, srcH * dpr,
                        destX, destY, drawW, drawH
                    );

                    return offCanvas.toDataURL('image/png');
                }
            };
        }
    </script>
</div>