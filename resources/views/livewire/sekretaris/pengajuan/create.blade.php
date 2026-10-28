<div class="siom-proposal">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        .siom-proposal {
            --proposal-bg: #eff6ff;
            --proposal-card: #fff;
            --proposal-ink: #0f172a;
            --proposal-muted: #475569;
            --proposal-blue: #2563eb;
            --proposal-blue-dark: #1d4ed8;
            --proposal-blue-royal: #1e40af;
            --proposal-blue-sky: #38bdf8;
            --proposal-blue-ice: #e0f2fe;
            --proposal-blue-light: #f0f7ff;
            --proposal-border: 2.5px solid var(--proposal-ink);
            --proposal-shadow: 4px 4px 0 var(--proposal-ink);
            position: relative;
            isolation: isolate;
            width: 100%;
            min-height: 100%;
            padding: clamp(1rem, 3vw, 2.5rem);
            overflow: hidden;
            background:
                radial-gradient(circle at 92% 3%, rgba(56, 189, 248, .15), transparent 24rem),
                var(--proposal-bg);
            color: var(--proposal-ink);
            font-family: 'Plus Jakarta Sans', sans-serif;
            line-height: 1.55;
        }

        .siom-proposal::before {
            position: absolute;
            z-index: -1;
            top: 0;
            right: 0;
            left: 0;
            height: 10px;
            background: linear-gradient(
                to bottom,
                #1e3a8a 0 25%,
                #2563eb 25% 50%,
                #38bdf8 50% 75%,
                #93c5fd 75% 100%
            );
            content: '';
        }

        .siom-proposal *,
        .siom-proposal *::before,
        .siom-proposal *::after { box-sizing: border-box; }

        .siom-proposal .container {
            width: 100%;
            max-width: none;
            margin: 0 auto;
            padding: 1rem 0;
        }

        .siom-proposal h3,
        .siom-proposal h5,
        .siom-proposal h6 {
            margin-top: 0;
            color: var(--proposal-ink);
            font-family: Fredoka, 'Plus Jakarta Sans', sans-serif;
            letter-spacing: .01em;
        }

        .siom-proposal h3 { font-size: clamp(1.8rem, 3vw, 2.5rem); line-height: 1.15; }
        .siom-proposal h5 { font-size: clamp(1.15rem, 2vw, 1.45rem); line-height: 1.25; }
        .siom-proposal h6 { font-size: 1.05rem; }
        .siom-proposal p { margin-top: 0; }
        .siom-proposal .fw-bold,
        .siom-proposal .fw-extrabold { font-weight: 800; }
        .siom-proposal .fw-semibold { font-weight: 700; }
        .siom-proposal .text-dark { color: var(--proposal-ink) !important; }
        .siom-proposal .text-muted { color: var(--proposal-muted) !important; }
        .siom-proposal .text-secondary { color: #64748b !important; }
        .siom-proposal .text-primary { color: var(--proposal-blue) !important; }
        .siom-proposal .text-success { color: #15803d !important; }
        .siom-proposal .text-danger { color: #dc2626 !important; }
        .siom-proposal .text-white { color: #fff !important; }
        .siom-proposal .small,
        .siom-proposal small { font-size: .84rem; }

        .siom-proposal .d-flex { display: flex; }
        .siom-proposal .flex-wrap { flex-wrap: wrap; }
        .siom-proposal .flex-column { flex-direction: column; }
        .siom-proposal .justify-content-between { justify-content: space-between; }
        .siom-proposal .align-items-center { align-items: center; }
        .siom-proposal .text-start { text-align: left; }
        .siom-proposal .text-end { text-align: right; }
        .siom-proposal .text-center { text-align: center; }
        .siom-proposal .d-block { display: block; }
        .siom-proposal .d-none { display: none; }
        .siom-proposal .w-100 { width: 100%; }
        .siom-proposal .mb-0 { margin-bottom: 0; }
        .siom-proposal .mb-1 { margin-bottom: .25rem; }
        .siom-proposal .mb-2 { margin-bottom: .5rem; }
        .siom-proposal .mb-3 { margin-bottom: 1rem; }
        .siom-proposal .mb-4 { margin-bottom: 1.5rem; }
        .siom-proposal .me-1 { margin-right: .25rem; }
        .siom-proposal .me-2 { margin-right: .5rem; }
        .siom-proposal .me-3 { margin-right: 1rem; }
        .siom-proposal .mt-3 { margin-top: 1rem; }
        .siom-proposal .mt-5 { margin-top: 3rem; }
        .siom-proposal .p-2 { padding: .5rem; }
        .siom-proposal .p-3 { padding: 1rem; }
        .siom-proposal .p-4 { padding: 1.5rem; }
        .siom-proposal .ps-3 { padding-left: 1rem; }
        .siom-proposal .ps-4 { padding-left: 1.5rem; }
        .siom-proposal .pe-3 { padding-right: 1rem; }
        .siom-proposal .py-3 { padding-top: 1rem; padding-bottom: 1rem; }
        .siom-proposal .py-4 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
        .siom-proposal .px-2 { padding-right: .5rem; padding-left: .5rem; }
        .siom-proposal .px-3 { padding-right: 1rem; padding-left: 1rem; }
        .siom-proposal .px-4 { padding-right: 1.5rem; padding-left: 1.5rem; }
        .siom-proposal .gap-2 { gap: .5rem; }
        .siom-proposal .fs-1 { font-size: 2.5rem; }
        .siom-proposal .fs-4 { font-size: 1.5rem; }
        .siom-proposal .fs-5 { font-size: 1.25rem; }
        .siom-proposal .rounded-circle { border-radius: 50%; }
        .siom-proposal .rounded-3 { border-radius: 16px; }
        .siom-proposal .badge.rounded-circle {
            width: 2rem;
            height: 2rem;
            flex: 0 0 2rem;
            justify-content: center;
            padding: 0;
        }
        .siom-proposal .border { border: 1px solid #cbd5e1 !important; }
        .siom-proposal .border-top { border-top: 1px solid #dbeafe !important; }
        .siom-proposal .border-bottom { border-bottom: 0 !important; }
        .siom-proposal .shadow-sm,
        .siom-proposal .shadow-lg { box-shadow: var(--proposal-shadow) !important; }
        .siom-proposal .bg-light { background: var(--proposal-blue-light) !important; }
        .siom-proposal .bg-white { background: #fff !important; }
        .siom-proposal .bg-primary-subtle { background: var(--proposal-blue-ice) !important; }
        .siom-proposal .bg-success-subtle { background: #dcfce7 !important; }
        .siom-proposal .bg-secondary-subtle { background: #f1f5f9 !important; }
        .siom-proposal .bg-primary { background: var(--proposal-blue) !important; }
        .siom-proposal .bg-success { background: #16a34a !important; }
        .siom-proposal .bg-secondary { background: #64748b !important; }

        .siom-proposal > .container > .d-flex:first-child {
            gap: 1.25rem;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1.4rem 1.6rem;
            border: var(--proposal-border);
            border-radius: 20px;
            background: #fff;
            box-shadow: 5px 5px 0 var(--proposal-ink);
        }

        .siom-proposal .badge {
            display: inline-flex;
            align-items: center;
            padding: .42rem .85rem;
            border: 1.5px solid var(--proposal-ink);
            border-radius: 9999px;
            font-size: .78rem;
            font-weight: 800;
        }

        .siom-proposal .card {
            overflow: hidden;
            border: var(--proposal-border) !important;
            border-radius: 18px !important;
            background: var(--proposal-card);
            box-shadow: var(--proposal-shadow) !important;
        }

        .siom-proposal .card-body { padding: 1.35rem; }
        .siom-proposal .card-body.p-3 { padding: 1rem; }
        .siom-proposal .card-body.p-4 { padding: 1.5rem; }
        .siom-proposal .card-body.p-md-4 { padding: 1.5rem; }
        .siom-proposal .card.border-0 { border: var(--proposal-border) !important; }
        .siom-proposal .card-body.border-0 { border: 0 !important; }
        .siom-proposal .card-body.shadow-sm { box-shadow: 3px 3px 0 var(--proposal-ink) !important; }

        .siom-proposal .row {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 1rem;
            margin: 0;
        }

        .siom-proposal .row.g-2 { gap: .75rem; }
        .siom-proposal .row.g-3 { gap: 1rem; }
        .siom-proposal .col-12 { grid-column: 1 / -1; }
        .siom-proposal .col-6 { grid-column: span 6; }
        .siom-proposal .col-md-3 { grid-column: span 3; }
        .siom-proposal .col-md-4 { grid-column: span 4; }
        .siom-proposal .col-md-6 { grid-column: span 6; }
        .siom-proposal .col-md-8 { grid-column: span 8; }
        .siom-proposal .col-md-1 { grid-column: span 1; }
        .siom-proposal .col-md-2 { grid-column: span 2; }
        .siom-proposal .col-md-5 { grid-column: span 5; }

        .siom-proposal .btn {
            display: inline-flex;
            min-height: 2.7rem;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            padding: .62rem 1.2rem;
            border: var(--proposal-border);
            border-radius: 9999px;
            background: #fff;
            color: var(--proposal-ink);
            font-family: Fredoka, 'Plus Jakarta Sans', sans-serif;
            font-size: .9rem;
            font-weight: 700;
            line-height: 1.2;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 2px 2px 0 var(--proposal-ink);
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .siom-proposal .btn:hover {
            transform: translate(-1px, -1px);
            box-shadow: 3px 3px 0 var(--proposal-ink);
        }

        .siom-proposal .btn-primary { background: var(--proposal-blue); color: #fff; }
        .siom-proposal .btn-primary:hover { background: var(--proposal-blue-dark); color: #fff; }
        .siom-proposal .btn-success { background: #15803d; color: #fff; }
        .siom-proposal .btn-outline-primary { border-color: var(--proposal-blue); color: var(--proposal-blue); }
        .siom-proposal .btn-outline-secondary { background: #f8fafc; }
        .siom-proposal .btn-outline-danger { border-color: #dc2626; color: #dc2626; }
        .siom-proposal .btn-link { min-height: 2rem; border: 0; background: transparent; box-shadow: none; }
        .siom-proposal .btn-sm { min-height: 2.35rem; padding: .45rem .85rem; font-size: .82rem; }
        .siom-proposal .btn.w-100 { width: 100%; }
        .siom-proposal .btn.rounded-3 { border-radius: 14px; }
        .siom-proposal .btn.border-0 { border: 0; }
        .siom-proposal .btn:disabled { cursor: wait; opacity: .65; }

        .siom-proposal .form-label {
            display: block;
            margin-bottom: .45rem;
            color: var(--proposal-ink);
            font-size: .88rem;
            font-weight: 700;
        }

        .siom-proposal .form-control,
        .siom-proposal .form-select {
            display: block;
            width: 100%;
            min-height: 2.8rem;
            padding: .68rem .85rem;
            border: 1.5px solid #94a3b8;
            border-radius: 10px;
            background: #f8fafc;
            color: var(--proposal-ink);
            font: 500 .9rem 'Plus Jakarta Sans', sans-serif;
        }

        .siom-proposal textarea.form-control { min-height: 6rem; resize: vertical; }
        .siom-proposal .form-control:focus,
        .siom-proposal .form-select:focus {
            border-color: var(--proposal-blue);
            outline: 0;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .16);
        }

        .siom-proposal .alert {
            margin-bottom: 1rem;
            padding: 1rem 1.2rem;
            border: var(--proposal-border) !important;
            border-radius: 14px;
            background: #fff;
            box-shadow: 3px 3px 0 var(--proposal-ink) !important;
            font-weight: 600;
        }

        .siom-proposal .alert-success { border-left: 8px solid #16a34a !important; }
        .siom-proposal .alert-danger { border-left: 8px solid #dc2626 !important; }
        .siom-proposal .alert ul { margin-top: .5rem; }

        .siom-proposal .table-responsive {
            width: 100%;
            overflow-x: auto;
            border: var(--proposal-border);
            border-radius: 14px;
            background: #fff;
        }

        .siom-proposal .table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
            color: var(--proposal-ink);
            font-size: .88rem;
        }

        .siom-proposal .table th {
            padding: .9rem .7rem;
            border-bottom: var(--proposal-border);
            background: var(--proposal-blue-ice);
            font-family: Fredoka, 'Plus Jakarta Sans', sans-serif;
            text-align: left;
        }

        .siom-proposal .table td {
            padding: .7rem;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .siom-proposal .table tbody tr:last-child td { border-bottom: 0; }
        .siom-proposal .table tfoot th { border-top: var(--proposal-border); }
        .siom-proposal .table .form-control { min-width: 5rem; }
        .siom-proposal .space-y-3 > * + * { margin-top: .75rem; }
        .siom-proposal hr { border: 0; border-top: 2px dashed #93c5fd; }
        .siom-proposal .text-truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .siom-proposal .fw-medium { font-weight: 600; }

        .siom-proposal .card .card {
            border: 1.5px solid #bfdbfe !important;
            box-shadow: none !important;
        }

        .siom-proposal .siom-sheet-wrap {
            width: 100%;
            overflow-x: auto;
            padding-bottom: .35rem;
        }

        .siom-proposal .siom-sheet { min-width: 980px; }

        .siom-proposal .siom-source {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem;
        }

        .siom-proposal .siom-source-option {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            min-width: 0;
            margin: 0;
            padding: .9rem 1rem;
            border: 1.5px solid #94a3b8;
            border-radius: 14px;
            background: #fff;
            cursor: pointer;
        }

        .siom-proposal .siom-source-option input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .siom-proposal .siom-source-mark {
            width: 1.15rem;
            height: 1.15rem;
            flex: 0 0 1.15rem;
            margin-top: .15rem;
            border: 2px solid #94a3b8;
            border-radius: 50%;
            background: #fff;
        }

        .siom-proposal .siom-source-option strong,
        .siom-proposal .siom-source-option small {
            display: block;
            overflow-wrap: anywhere;
        }

        .siom-proposal .siom-source-option small {
            margin-top: .15rem;
            color: #475569;
        }

        .siom-proposal .siom-source-option.is-active {
            border-color: #2563eb;
            background: #f0f7ff;
            box-shadow: inset 0 0 0 1px #2563eb;
        }

        .siom-proposal .siom-source-option.is-active .siom-source-mark {
            border-color: #2563eb;
            background: radial-gradient(circle, #2563eb 0 42%, #fff 46% 100%);
        }

        @media (min-width: 768px) {
            .siom-proposal .flex-md-row { flex-direction: row; }
            .siom-proposal .align-items-md-center { align-items: center; }
            .siom-proposal .text-md-end { text-align: right; }
            .siom-proposal .d-md-block { display: block; }
        }

        @media (max-width: 767px) {
            .siom-proposal { padding: 1rem; }
            .siom-proposal .col-md-1,
            .siom-proposal .col-md-2,
            .siom-proposal .col-md-3,
            .siom-proposal .col-md-4,
            .siom-proposal .col-md-5,
            .siom-proposal .col-md-6,
            .siom-proposal .col-md-8 { grid-column: 1 / -1; }
            .siom-proposal .siom-sheet .col-md-1 { grid-column: span 1; }
            .siom-proposal .siom-sheet .col-md-2 { grid-column: span 2; }
            .siom-proposal .siom-sheet .col-md-3 { grid-column: span 3; }
            .siom-proposal .siom-source { grid-template-columns: 1fr; }
            .siom-proposal .card-body.p-4 { padding: 1rem; }
            .siom-proposal > .container > .d-flex:first-child {
                align-items: flex-start;
                flex-direction: column;
                padding: 1rem;
            }
            .siom-proposal .text-md-end { text-align: left; }
            .siom-proposal .d-flex.justify-content-end,
            .siom-proposal .d-flex.justify-content-between.mt-5 {
                align-items: stretch;
                flex-direction: column;
            }
            .siom-proposal .d-flex.justify-content-end .btn,
            .siom-proposal .d-flex.justify-content-between.mt-5 .btn { width: 100%; }
        }
    </style>

    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold text-dark mb-1">Pengajuan Kegiatan Baru</h3>
                <p class="text-muted mb-0">
                    <span class="badge bg-primary-subtle text-primary fw-medium me-1">{{ $ormawa->name }}</span>
                    <span class="text-secondary">· Periode {{ $period->name }}</span>
                </p>
            </div>
            <div class="mt-3 mt-md-0 text-md-end">
                <small class="text-muted d-block">{{ $sourceType === 'upload' ? 'Total Anggaran Proposal' : 'Estimasi Total RAB' }}</small>
                <span class="fs-4 fw-extrabold text-success">
                    Rp {{ number_format($sourceType === 'upload' ? (float) $uploadedTotalAmount : $this->grandTotal, 0, ',', '.') }}
                </span>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-4" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Periksa kembali inputan Anda:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3 p-md-4">
                <h5 class="fw-bold mb-3">Sumber Proposal</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="siom-source-option {{ $sourceType === 'form' ? 'is-active' : '' }}">
                            <input type="radio" value="form" wire:model.live="sourceType">
                            <span class="siom-source-mark"></span>
                            <span>
                                <strong>Isi formulir SIOM</strong><small class="d-block text-muted">Susun proposal dengan wizard dan RAB di sistem.</small>
                            </span>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="siom-source-option {{ $sourceType === 'upload' ? 'is-active' : '' }}">
                            <input type="radio" value="upload" wire:model.live="sourceType">
                            <span class="siom-source-mark"></span>
                            <span>
                                <strong>Unggah proposal DOCX</strong>
                                <small class="d-block text-muted">Gunakan dokumen proposal yang sudah disiapkan.</small>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        @if ($sourceType === 'upload')
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Proposal Siap Diproses</h5>
                    <p class="text-muted mb-4">
                        Masukkan metadata kegiatan dan anggaran untuk pencatatan serta pemeriksaan pagu. File dibatasi DOCX, maksimal 20 MB.
                        Agar hasil akhir dapat diberi tanda tangan visual, dokumen harus memuat placeholder
                        <code>${signature_pembina_ormawa}</code>, <code>${signature_kaprodi}</code>,
                        <code>${signature_kabag_kemahasiswaan}</code>, dan <code>${signature_warek}</code>.
                    </p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="activity_name" placeholder="Contoh: Seminar Nasional Kesehatan 2026">
                            @error('activity_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Pelaksanaan Kegiatan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" wire:model="event_date" min="{{ now()->addDays(30)->toDateString() }}">
                            <small class="text-muted d-block mt-1"><i class="bi bi-shield-check text-primary"></i> Wajib minimal 30 hari (1 bulan) dari hari ini.</small>
                            @error('event_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Anggaran (Rp) <span class="text-danger">*</span></label>
                            <input type="number" min="1" step="0.01" class="form-control" wire:model="uploadedTotalAmount" placeholder="Contoh: 5000000">
                            @error('uploadedTotalAmount') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">File Proposal DOCX {{ $submission?->documents->contains('type', 'proposal') ? '(opsional jika tidak diganti)' : '*' }}</label>
                            <input type="file" class="form-control" wire:model="proposalUpload" accept=".docx">
                            @if ($submission?->documents->firstWhere('type', 'proposal'))
                                <small class="d-block text-muted mt-1">File tersimpan: {{ $submission->documents->firstWhere('type', 'proposal')->file_name }}</small>
                            @endif
                            @error('proposalUpload') <small class="text-danger">{{ $message }}</small> @enderror
                            <div wire:loading wire:target="proposalUpload" class="small text-muted mt-1">Mengunggah file…</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between gap-2 mt-4 pt-3 border-top">
                        <a class="btn btn-outline-secondary" href="{{ route('sekretaris.pengajuan') }}">Batal</a>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary" wire:click="saveDraft" wire:loading.attr="disabled">
                                {{ $submission ? 'Simpan Perubahan' : 'Simpan Draft' }}
                            </button>
                            <button type="button" class="btn btn-success fw-bold" wire:click="submit" wire:loading.attr="disabled">
                                <i class="bi bi-send me-1"></i> {{ $submission ? 'Kirim Ulang Revisi' : 'Kirim Pengajuan' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3 p-md-4">
                    <div class="row g-2">
                        @foreach ([
                            1 => 'Detail Kegiatan',
                            2 => 'Konten Proposal',
                            3 => 'Panitia & Rundown',
                            4 => 'RAB Kegiatan',
                        ] as $number => $title)
                            <div class="col-6 col-md-3">
                                <button type="button" wire:click="goToStep({{ $number }})" class="btn w-100 text-start rounded-3 border-0 {{ $step === $number ? 'btn-primary text-white' : ($step > $number ? 'bg-success-subtle text-success fw-semibold' : 'bg-light text-muted') }}">
                                    <span class="badge rounded-circle me-2 {{ $step === $number ? 'bg-white text-primary' : ($step > $number ? 'bg-success text-white' : 'bg-secondary text-white') }}">
                                        {{ $step > $number ? '✓' : $number }}
                                    </span>
                                    <span class="small fw-bold">{{ $title }}</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    @if ($step === 1)
                        <h5 class="fw-bold mb-1">1. Detail Kegiatan & Dasar Pengajuan</h5>
                        <small class="text-muted d-block mb-4">Informasi dasar kegiatan dan acuan Nota Dinas</small>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nama Kegiatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="activity_name" placeholder="Contoh: WORKSHOP KEUANGAN ORMAWA">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tema Kegiatan</label>
                                <input type="text" class="form-control" wire:model="activity_theme" placeholder="Tema/sub-judul kegiatan">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Divisi Penyelenggara</label>
                                <input type="text" class="form-control" wire:model="divisi_name" placeholder="Contoh: Divisi Kominfo">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Rapat Koordinasi</label>
                                <input type="date" class="form-control" wire:model="meeting_date">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Surat Edaran Acuan</label>
                                <input type="text" class="form-control" wire:model="circular_letter" placeholder="Contoh: SE/001/BKA/2026">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Kode Mata Anggaran</label>
                                <input type="text" class="form-control" wire:model="budget_code" placeholder="Contoh: 5051702">
                            </div>
                        </div>
                    @endif

                    @if ($step === 2)
                        <h5 class="fw-bold mb-1">2. Konten Proposal & Pelaksanaan</h5>
                        <small class="text-muted d-block mb-4">Isi naskah proposal dan estimasi jadwal kerja</small>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Latar Belakang <span class="text-danger">*</span></label>
                            <textarea class="form-control" rows="4" wire:model="background" placeholder="Deskripsikan latar belakang pentingnya kegiatan ini..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tujuan Kegiatan</label>
                            @foreach ($objectives as $index => $objective)
                                <div class="d-flex gap-2 mb-2" wire:key="objective-{{ $index }}">
                                    <input type="text" class="form-control" wire:model="objectives.{{ $index }}" placeholder="Masukkan tujuan kegiatan">
                                    <button type="button" class="btn btn-outline-danger" wire:click="removeObjective({{ $index }})" @disabled(count($objectives) <= 1)>Hapus</button>
                                </div>
                            @endforeach
                            <button type="button" class="btn btn-outline-primary btn-sm" wire:click="addObjective">+ Tambah Tujuan</button>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Manfaat Kegiatan</label>
                            @foreach ($benefits as $index => $benefit)
                                <div class="d-flex gap-2 mb-2" wire:key="benefit-{{ $index }}">
                                    <input type="text" class="form-control" wire:model="benefits.{{ $index }}" placeholder="Masukkan manfaat kegiatan">
                                    <button type="button" class="btn btn-outline-danger" wire:click="removeBenefit({{ $index }})" @disabled(count($benefits) <= 1)>Hapus</button>
                                </div>
                            @endforeach
                            <button type="button" class="btn btn-outline-primary btn-sm" wire:click="addBenefit">+ Tambah Manfaat</button>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Sasaran Peserta <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="target_participants" placeholder="Contoh: 100 Mahasiswa">
                        </div>
                        <div class="p-3 bg-light rounded-3 mb-4 border">
                            <h6 class="fw-bold mb-3">Waktu & Tempat Pelaksanaan</h6>
                            <div class="row g-3">
                                <div class="col-md-3"><label class="form-label small">Hari</label><input type="text" class="form-control" wire:model="event_day" placeholder="Sabtu"></div>
                                <div class="col-md-3"><label class="form-label small">Tanggal <span class="text-danger">* (Min. H-30)</span></label><input type="date" class="form-control" wire:model="event_date" min="{{ now()->addDays(30)->format('Y-m-d') }}"></div>
                                <div class="col-md-3"><label class="form-label small">Waktu Mulai <span class="text-danger">*</span></label><input type="time" class="form-control" wire:model="event_start_time"></div>
                                <div class="col-md-3"><label class="form-label small">Waktu Selesai <span class="text-danger">*</span></label><input type="time" class="form-control" wire:model="event_end_time"></div>
                                <div class="col-12"><label class="form-label small">Lokasi <span class="text-danger">*</span></label><input type="text" class="form-control" wire:model="event_location" placeholder="Gedung Aula Kampus"></div>
                            </div>
                        </div>
                        <div class="p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold mb-3">Persiapan Rencana Kegiatan</h6>
                            @foreach ([
                                ['1. Tahap Perencanaan', 'plan_start', 'plan_end', 'plan_context', 'Rapat penyusunan konsep dan pembentukan panitia'],
                                ['2. Tahap Persiapan', 'prepare_start', 'prepare_end', 'prepare_context', 'Pengurusan izin, tempat, publikasi, dan perlengkapan'],
                                ['3. Tahap Pelaksanaan', 'action_start', 'action_end', 'action_context', 'Pelaksanaan kegiatan utama di lokasi'],
                                ['4. Tahap Penutupan', 'closing_start', 'closing_end', 'closing_context', 'Evaluasi kegiatan dan penyusunan LPJ'],
                            ] as [$label, $start, $end, $context, $hint])
                                <div class="card card-body mb-3 p-3 bg-white">
                                    <span class="fw-bold text-primary mb-2">{{ $label }}</span>
                                    <div class="row g-2">
                                        <div class="col-md-3"><label class="form-label small">Mulai</label><input type="date" class="form-control" wire:model="{{ $start }}"></div>
                                        <div class="col-md-3"><label class="form-label small">Selesai</label><input type="date" class="form-control" wire:model="{{ $end }}"></div>
                                        <div class="col-md-6"><label class="form-label small">Konteks</label><input type="text" class="form-control" wire:model="{{ $context }}.0" placeholder="{{ $hint }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($step === 3)
                        <h5 class="fw-bold mb-1">3. Susunan Kepanitiaan & Rundown</h5>
                        <small class="text-muted d-block mb-4">Geser ke samping pada layar kecil.</small>
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-1">Data Penandatangan</h6>
                                <p class="small text-muted mb-3">Isi identitas yang akan dicetak pada dokumen.</p>
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label" for="chairman_name">Nama Ketua Pelaksana</label><input id="chairman_name" type="text" class="form-control" wire:model="chairman_name">@error('chairman_name') <small class="text-danger">{{ $message }}</small> @enderror</div>
                                    <div class="col-md-6"><label class="form-label" for="chairman_nim">NIM Ketua Pelaksana</label><input id="chairman_nim" type="text" class="form-control" wire:model="chairman_nim">@error('chairman_nim') <small class="text-danger">{{ $message }}</small> @enderror</div>
                                    <div class="col-md-6"><label class="form-label" for="pembina_name">Nama Pembina</label><input id="pembina_name" type="text" class="form-control" wire:model="pembina_name">@error('pembina_name') <small class="text-danger">{{ $message }}</small> @enderror</div>
                                    <div class="col-md-6"><label class="form-label" for="pembina_nuptk">NUPTK Pembina</label><input id="pembina_nuptk" type="text" class="form-control" wire:model="pembina_nuptk">@error('pembina_nuptk') <small class="text-danger">{{ $message }}</small> @enderror</div>
                                    <div class="col-md-6"><label class="form-label" for="kaprodi_name">Nama Kaprodi</label><input id="kaprodi_name" type="text" class="form-control" wire:model="kaprodi_name">@error('kaprodi_name') <small class="text-danger">{{ $message }}</small> @enderror</div>
                                    <div class="col-md-6"><label class="form-label" for="kaprodi_nuptk">NUPTK Kaprodi</label><input id="kaprodi_nuptk" type="text" class="form-control" wire:model="kaprodi_nuptk">@error('kaprodi_nuptk') <small class="text-danger">{{ $message }}</small> @enderror</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Susunan Kepanitiaan</h6>
                            <button type="button" class="btn btn-sm btn-primary" wire:click="addCommittee">+ Tambah Panitia</button>
                        </div>
                        <div class="siom-sheet-wrap mb-4">
                            @foreach ($committees as $index => $committee)
                                <div class="row g-2 mb-2 siom-sheet" wire:key="committee-item-{{ $index }}">
                                    <div class="col-md-3"><label class="form-label small">Nama Panitia</label><input type="text" class="form-control" placeholder="Nama lengkap" wire:model="committees.{{ $index }}.name">@error('committees.'.$index.'.name') <small class="text-danger">{{ $message }}</small> @enderror</div>
                                    <div class="col-md-2"><label class="form-label small">Status Struktural</label><input type="text" class="form-control" placeholder="Mahasiswa" wire:model="committees.{{ $index }}.structural_role"></div>
                                    <div class="col-md-3"><label class="form-label small">Jabatan Panitia</label><input type="text" class="form-control" placeholder="Ketua Pelaksana" wire:model="committees.{{ $index }}.committee_role"></div>
                                    <div class="col-md-3"><label class="form-label small">Keterangan</label><input type="text" class="form-control" placeholder="Keterangan" wire:model="committees.{{ $index }}.description"></div>
                                    <div class="col-md-1"><label class="form-label small d-none d-md-block">&nbsp;</label><button type="button" class="btn btn-outline-danger w-100" wire:click="removeCommittee({{ $index }})">✕</button></div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Rundown / Susunan Acara</h6>
                            <button type="button" class="btn btn-sm btn-primary" wire:click="addRundown">+ Tambah Acara</button>
                        </div>
                        <div class="siom-sheet-wrap">
                            @foreach ($rundowns as $index => $rundown)
                                <div class="row g-2 mb-2 siom-sheet" wire:key="rundown-item-{{ $index }}">
                                    <div class="col-md-2"><label class="form-label small">Hari / Tanggal</label><input type="text" class="form-control" wire:model="rundowns.{{ $index }}.day_date"></div>
                                    <div class="col-md-2"><label class="form-label small">Waktu</label><input type="text" class="form-control" wire:model="rundowns.{{ $index }}.time_slot"></div>
                                    <div class="col-md-3"><label class="form-label small">Nama Agenda</label><input type="text" class="form-control" wire:model="rundowns.{{ $index }}.event_name"></div>
                                    <div class="col-md-2"><label class="form-label small">PIC</label><input type="text" class="form-control" wire:model="rundowns.{{ $index }}.pic"></div>
                                    <div class="col-md-2"><label class="form-label small">Catatan</label><input type="text" class="form-control" wire:model="rundowns.{{ $index }}.note"></div>
                                    <div class="col-md-1"><label class="form-label small d-none d-md-block">&nbsp;</label><button type="button" class="btn btn-outline-danger w-100" wire:click="removeRundown({{ $index }})">✕</button></div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($step === 4)
                        <h5 class="fw-bold mb-1">4. Rencana Anggaran Biaya (RAB)</h5>
                        <small class="text-muted d-block mb-4">Geser tabel ke samping pada layar kecil.</small>
                        <div class="table-responsive mb-3">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Rincian Kebutuhan</th>
                                        <th>Qty</th>
                                        <th>Satuan</th>
                                        <th>Harga Satuan</th>
                                        <th class="text-end">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $index => $item)
                                        <tr wire:key="rab-item-{{ $index }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td><input type="text" class="form-control" wire:model="items.{{ $index }}.item_name" placeholder="Spanduk / Nasi Kotak"></td>
                                            <td><input type="number" min="0.01" step="0.01" class="form-control" wire:model.live="items.{{ $index }}.qty"></td>
                                            <td><input type="text" class="form-control" wire:model="items.{{ $index }}.unit" placeholder="pcs"></td>
                                            <td><input type="number" min="0" step="1000" class="form-control" wire:model.live="items.{{ $index }}.price"></td>
                                            <td class="text-end fw-bold">Rp {{ number_format($this->itemTotal($index), 0, ',', '.') }}</td>
                                            <td><button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeItem({{ $index }})">✕</button></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-end">TOTAL</th>
                                        <th class="text-end text-success">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline-primary" wire:click="addItem">+ Tambah Baris RAB</button>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                        <div class="d-flex gap-2">
                            <a class="btn btn-outline-secondary" href="{{ route('sekretaris.pengajuan') }}">Batal</a>
                            @if ($step > 1)
                                <button type="button" class="btn btn-outline-secondary" wire:click="previousStep">← Kembali</button>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            @if ($step < 4)
                                <button type="button" class="btn btn-primary" wire:click="nextStep">Lanjut →</button>
                            @else
                                <button type="button" class="btn btn-outline-secondary" wire:click="saveDraft" wire:loading.attr="disabled">
                                    {{ $submission ? 'Simpan Perubahan' : 'Simpan Draft' }}
                                </button>
                                <button type="button" class="btn btn-success fw-bold" wire:click="submit" wire:loading.attr="disabled">
                                    <i class="bi bi-send me-1"></i> {{ $submission ? 'Kirim Ulang Revisi' : 'Kirim Pengajuan' }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>