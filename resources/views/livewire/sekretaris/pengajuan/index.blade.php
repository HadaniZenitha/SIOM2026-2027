<div class="siom-admin-page siom-submit-page">
    <style>
        .siom-submit-page .siom-admin-table { min-width: 0; width: 100%; }
        .siom-submit-page .siom-admin-table th,
        .siom-submit-page .siom-admin-table td { white-space: normal; vertical-align: top; }
        .siom-submit-page .siom-amount { white-space: nowrap; }
        .siom-submit-page .siom-admin-actions { flex-direction: column; align-items: stretch; }
        .siom-submit-page .siom-step-row {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: .35rem;
            margin-top: .45rem;
        }
        .siom-submit-page .siom-step-pill {
            display: block;
            width: fit-content;
            max-width: 100%;
            padding: .3rem .6rem;
            border: 1.5px solid currentColor;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 800;
            line-height: 1.3;
            white-space: normal;
        }
        .siom-submit-page .is-approved,
        .siom-submit-page .is-completed { background: #dcfce7; color: #166534; }
        .siom-submit-page .is-pending,
        .siom-submit-page .is-draft,
        .siom-submit-page .is-submitted,
        .siom-submit-page .is-waiting_approval { background: #fef9c3; color: #854d0e; }
        .siom-submit-page .is-waiting { background: #f1f5f9; color: #475569; }
        .siom-submit-page .is-revision { background: #e0f2fe; color: #075985; }
        .siom-submit-page .is-rejected { background: #fee2e2; color: #991b1b; }
        .siom-submit-page .siom-submit-cards { display: none; gap: .75rem; padding: 1rem; }
        .siom-submit-page .siom-submit-card {
            display: grid;
            gap: .4rem;
            padding: .9rem;
            border: 1.5px solid #0f172a;
            border-radius: 14px;
            background: #f8fbff;
        }
        @media (max-width: 900px) {
            .siom-submit-page .siom-submit-table { display: none; }
            .siom-submit-page .siom-submit-cards { display: grid; }
            .siom-submit-page .siom-admin-toolbar { align-items: stretch; flex-direction: column; }
            .siom-submit-page .siom-admin-toolbar .siom-admin-field { width: 100% !important; }
        }
    </style>

    <div class="siom-admin-heading">
        <div>
            <h1>Pengajuan Dana</h1>
            <p>Kelola pengajuan dana ORMAWA.</p>
        </div>
        <a class="siom-admin-btn siom-admin-btn-primary" href="{{ route('sekretaris.pengajuan.create') }}">
            <i class="bi bi-plus-lg"></i> Buat Pengajuan
        </a>
    </div>

    @if (session('success'))
        <div class="siom-admin-alert siom-admin-alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="siom-admin-alert siom-admin-alert-error">{{ session('error') }}</div>
    @endif

    <div class="siom-admin-card">
        <div class="siom-admin-card-body">
            <div class="siom-admin-toolbar mb-3">
                <h2 class="h6 fw-semibold mb-0">Daftar Pengajuan</h2>
                <div class="siom-admin-field mb-0" style="width: min(100%, 320px);">
                    <input type="search" placeholder="Cari nomor atau nama kegiatan" wire:model.live.debounce.400ms="search">
                </div>
            </div>

            <div class="siom-admin-table-wrap siom-submit-table">
                <table class="siom-admin-table">
                    <thead>
                        <tr>
                            <th>No. Pengajuan</th>
                            <th>Kegiatan</th>
                            <th>Periode</th>
                            <th class="siom-amount">Total Anggaran</th>
                            <th>Status</th>
                            <th style="width: 190px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($submissions as $submission)
                            @php
                                $activeStep = $submission->approvalSteps->firstWhere('status', 'pending');
                                $statusLabel = match ($submission->status) {
                                    'draft' => 'Draft',
                                    'submitted', 'waiting_approval' => $activeStep
                                        ? 'Ditinjau '.($activeStep->role?->display_name ?? 'pihak terkait')
                                        : 'Menunggu persetujuan',
                                    'revision' => 'Perlu revisi',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    'completed' => 'Selesai',
                                    default => ucfirst(str_replace('_', ' ', $submission->status)),
                                };
                            @endphp
                            <tr wire:key="submission-{{ $submission->id }}">
                                <td><strong>{{ $submission->submission_number }}</strong></td>
                                <td>{{ $submission->activity_name }}</td>
                                <td>{{ $submission->period?->name ?? '-' }}</td>
                                <td class="siom-amount">Rp {{ number_format($submission->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    <span class="siom-step-pill is-{{ $submission->status }}">{{ $statusLabel }}</span>
                                    @if ($submission->approvalSteps->isNotEmpty())
                                        <div class="siom-step-row">
                                            @foreach ($submission->approvalSteps as $step)
                                                <span class="siom-step-pill is-{{ $step->status }}">
                                                    {{ $step->role?->display_name ?? 'Tahap' }}: {{ ucfirst(str_replace('_', ' ', $step->status)) }}
                                                    @if ($step->approver) · {{ $step->approver->name }} @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="siom-admin-actions">
                                        @if (in_array($submission->status, ['draft', 'revision'], true))
                                            <a class="siom-admin-btn siom-admin-btn-sm" href="{{ $submission->status === 'revision' ? route('sekretaris.pengajuan.revisi', $submission) : route('sekretaris.pengajuan.edit', $submission) }}">
                                                {{ $submission->status === 'revision' ? 'Perbaiki' : 'Edit' }}
                                            </a>
                                            @if ($submission->status === 'draft')
                                                <button type="button" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-danger" wire:click="deleteDraft({{ $submission->id }})" wire:confirm="Hapus draft pengajuan ini?">Hapus</button>
                                            @endif
                                        @endif
                                        @if (! in_array($submission->status, ['draft'], true))
                                            @php
                                                $isBemOrDpm = $submission->ormawa && ($submission->ormawa->isBem() || $submission->ormawa->isDpm());
                                            @endphp
                                            @if ($isBemOrDpm)
                                                <button type="button" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-primary" wire:click="downloadProposal({{ $submission->id }})" wire:loading.attr="disabled" title="Unduh Format Proposal BEM">
                                                    Unduh Proposal
                                                </button>
                                                <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="downloadNotaDinas({{ $submission->id }})" wire:loading.attr="disabled" title="Unduh Format Nota Dinas BEM">
                                                    Unduh Nota Dinas
                                                </button>
                                            @else
                                                <button type="button" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-primary" wire:click="downloadProposal({{ $submission->id }})" wire:loading.attr="disabled" title="Unduh Format Nota Dinas & Proposal Ormawa">
                                                    Unduh Proposal & Nota Dinas
                                                </button>
                                            @endif
                                        @endif
                                        @if ($submission->status === 'approved')
                                            <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="downloadSignedProposal({{ $submission->id }})" wire:loading.attr="disabled">
                                                Bertanda tangan
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="siom-admin-empty">Belum ada pengajuan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="siom-submit-cards">
                @forelse ($submissions as $submission)
                    @php
                        $status = $submission->status instanceof \BackedEnum ? $submission->status->value : (string) $submission ->status;
                        $activeStep = $submission->approvalSteps->firstWhere('status', 'pending');
                        $statusLabel = match ($submission->status) {
                            'draft' => 'Draft',
                            'submitted', 'waiting_approval' => $activeStep
                                ? 'Ditinjau '.($activeStep->role?->display_name ?? 'pihak terkait')
                                : 'Menunggu persetujuan',
                            'revision' => 'Perlu revisi',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                            'completed' => 'Selesai',
                            default => ucfirst(str_replace('_', ' ', $submission->status)),
                        };
                    @endphp
                    <article class="siom-submit-card" wire:key="submission-card-{{ $submission->id }}">
                        <strong>{{ $submission->activity_name }}</strong>
                        <small class="text-muted">{{ $submission->submission_number }} · {{ $submission->period?->name ?? '-' }}</small>
                        <span class="siom-amount">Total Anggaran Rp {{ number_format($submission->total_amount, 0, ',', '.') }}</span>
                        <span class="siom-step-pill is-{{ $submission->status }}">{{ $statusLabel }}</span>
                        @if ($submission->approvalSteps->isNotEmpty())
                            <div class="siom-step-row">
                                @foreach ($submission->approvalSteps as $step)
                                    <span class="siom-step-pill is-{{ $step->status }}">
                                        {{ $step->role?->display_name ?? 'Tahap' }}: {{ ucfirst(str_replace('_', ' ', $step->status)) }}
                                        @if ($step->approver) · {{ $step->approver->name }} @endif
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        <div class="siom-admin-actions">
                            @if (in_array($status, ['draft', 'revision'], true))
                                <a class="siom-admin-btn siom-admin-btn-sm" href="{{ $status === 'revision' ? route('sekretaris.pengajuan.revisi', $submission) : route('sekretaris.pengajuan.edit', $submission) }}">
                                    {{ $status === 'revision' ? 'Perbaiki' : 'Edit' }}
                                </a>
                                @if ($status === 'draft')
                                    <button type="button" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-danger" wire:click="deleteDraft({{ $submission->id }})" wire:confirm="Hapus draft pengajuan ini?">Hapus</button>
                                @endif
                            @endif
                            @if (! in_array($status, ['draft'], true))
                                @php
                                    $isBemOrDpm = $submission->ormawa && ($submission->ormawa->isBem() || $submission->ormawa->isDpm());
                                @endphp
                                @if ($isBemOrDpm)
                                    <button type="button" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-primary" wire:click="downloadProposal({{ $submission->id }})" wire:loading.attr="disabled">Unduh Proposal</button>
                                    <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="downloadNotaDinas({{ $submission->id }})" wire:loading.attr="disabled">Unduh Nota Dinas</button>
                                @else
                                    <button type="button" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-primary" wire:click="downloadProposal({{ $submission->id }})" wire:loading.attr="disabled">Unduh Proposal & Nota Dinas</button>
                                @endif
                            @endif
                            @if ($status === 'approved')
                                <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="downloadSignedProposal({{ $submission->id }})" wire:loading.attr="disabled">Bertanda tangan</button>
                            @endif
                        </div>
                    </article>
                @empty
                    <p class="siom-admin-empty">Belum ada pengajuan.</p>
                @endforelse
            </div>

            @if ($submissions->hasPages())
                <div class="siom-admin-pagination mt-3">{{ $submissions->links() }}</div>
            @endif
        </div>
    </div>
</div>