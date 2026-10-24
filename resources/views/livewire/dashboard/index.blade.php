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
        <section class="card">
            <div class="card-body">
                <div class="siom-panel-head">
                    <div>
                        <h2 class="h5">Tanda Tangan Persetujuan</h2>
                        <p class="text-muted">PNG/JPG, maksimal 2 MB. Tanda tangan visual; snapshot melekat pada keputusan berikutnya.</p>
                    </div>
                    <span class="badge {{ $user->signature_path ? 'bg-success' : 'bg-warning' }}">
                        {{ $user->signature_path ? 'Tanda tangan tersimpan' : 'Belum ada tanda tangan' }}
                    </span>
                </div>
                <form wire:submit="uploadSignature" class="siom-sign-form">
                    <div class="siom-field">
                        <label for="signatureFile">Pilih gambar tanda tangan</label>
                        <input id="signatureFile" type="file" class="form-control" wire:model="signatureFile" accept=".png,.jpg,.jpeg">
                        @error('signatureFile') <small class="text-danger">{{ $message }}</small> @enderror
                        <span wire:loading wire:target="signatureFile" class="text-muted">Mengunggah…</span>
                    </div>
                    <button class="btn btn-primary" type="submit" wire:loading.attr="disabled" wire:target="uploadSignature,signatureFile">Simpan Tanda Tangan</button>
                </form>
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
</div>