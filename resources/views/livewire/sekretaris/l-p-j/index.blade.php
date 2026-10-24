<div class="siom-admin-page siom-lpj-page">
    <style>
        .siom-lpj-page .siom-admin-table {
            min-width: 0;
            width: 100%;
        }
        .siom-lpj-page .siom-admin-table th,
        .siom-lpj-page .siom-admin-table td {
            white-space: normal;
            vertical-align: top;
        }
        .siom-lpj-page .siom-admin-actions {
            flex-wrap: wrap;
        }
        .siom-lpj-page .siom-step-row {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem;
            margin-top: .35rem;
        }
        .siom-lpj-page .siom-step-pill {
            display: inline-flex;
            align-items: center;
            max-width: 100%;
            padding: .3rem .6rem;
            border: 1.5px solid currentColor;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 800;
            line-height: 1.3;
            white-space: normal;
        }
        .siom-lpj-page .is-approved { background: #dcfce7; color: #166534; }
        .siom-lpj-page .is-pending { background: #fef9c3; color: #854d0e; }
        .siom-lpj-page .is-waiting { background: #f1f5f9; color: #475569; }
        .siom-lpj-page .is-revision { background: #e0f2fe; color: #075985; }
        .siom-lpj-page .is-rejected { background: #fee2e2; color: #991b1b; }
        .siom-lpj-page .siom-lpj-cards {
            display: none;
            gap: .75rem;
            padding: 1rem;
        }
        .siom-lpj-page .siom-lpj-card {
            display: grid;
            gap: .4rem;
            padding: .9rem;
            border: 1.5px solid #0f172a;
            border-radius: 14px;
            background: #f8fbff;
        }
        .siom-lpj-page .siom-lpj-card strong,
        .siom-lpj-page .siom-lpj-card small {
            overflow-wrap: anywhere;
        }
        @media (max-width: 900px) {
            .siom-lpj-page .siom-lpj-table { display: none; }
            .siom-lpj-page .siom-lpj-cards { display: grid; }
            .siom-lpj-page .siom-lpj-cards .siom-admin-btn { width: 100%; }
        }
    </style>

    <div class="siom-admin-heading">
        <div>
            <h1>Laporan Pertanggungjawaban</h1>
            <p>Kelola LPJ kegiatan yang telah disetujui.</p>
        </div>
        <a class="siom-admin-btn siom-admin-btn-primary" href="{{ route('sekretaris.lpj.create') }}">
            <i class="bi bi-plus-lg"></i> Buat LPJ
        </a>
    </div>

    @if (session('success'))
        <div class="siom-admin-alert siom-admin-alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="siom-admin-alert siom-admin-alert-error">{{ session('error') }}</div>
    @endif
    @error('documents')
        <div class="siom-admin-alert siom-admin-alert-error">{{ $message }}</div>
    @enderror

    <div class="siom-admin-card">
        <div class="siom-admin-table-wrap siom-lpj-table">
            <table class="siom-admin-table">
                <thead>
                    <tr>
                        <th>LPJ</th>
                        <th>Kegiatan</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th style="width: 90px;">Dokumen</th>
                        <th style="width: 210px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lpjs as $lpj)
                        <tr wire:key="lpj-{{ $lpj->id }}">
                            <td><strong>{{ $lpj->title }}</strong></td>
                            <td>{{ $lpj->submission?->activity_name }}</td>
                            <td>{{ $lpj->period?->name }}</td>
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $lpj->status)) }}
                                <div class="siom-step-row">
                                    @foreach ($lpj->approvalSteps as $step)
                                        <span class="siom-step-pill is-{{ $step->status }}">
                                            {{ $step->role?->display_name }}: {{ ucfirst(str_replace('_', ' ', $step->status)) }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td>{{ $lpj->documents->count() }}</td>
                            <td>
                                <div class="siom-admin-actions">
                                    @if (in_array($lpj->status, ['draft', 'revision'], true))
                                        <a class="siom-admin-btn siom-admin-btn-sm" href="{{ route('sekretaris.lpj.edit', $lpj) }}">Edit</a>
                                        <button type="button" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-primary" wire:click="submit({{ $lpj->id }})" wire:confirm="Kirim LPJ untuk persetujuan?">Kirim</button>
                                        <button type="button" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-danger" wire:click="delete({{ $lpj->id }})" wire:confirm="Hapus LPJ ini?">Hapus</button>
                                    @elseif ($lpj->status === 'approved')
                                        <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="downloadSigned({{ $lpj->id }})" wire:loading.attr="disabled">Unduh DOCX</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="siom-admin-empty">Belum ada LPJ.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="siom-lpj-cards">
            @forelse ($lpjs as $lpj)
                <article class="siom-lpj-card" wire:key="lpj-card-{{ $lpj->id }}">
                    <strong>{{ $lpj->title }}</strong>
                    <span>{{ $lpj->submission?->activity_name }}</span>
                    <small class="text-muted">{{ $lpj->period?->name }} · {{ $lpj->documents->count() }} dokumen · {{ ucfirst(str_replace('_', ' ', $lpj->status)) }}</small>
                    <div class="siom-step-row">
                        @foreach ($lpj->approvalSteps as $step)
                            <span class="siom-step-pill is-{{ $step->status }}">
                                {{ $step->role?->display_name }}: {{ ucfirst(str_replace('_', ' ', $step->status)) }}
                            </span>
                        @endforeach
                    </div>
                    <div class="siom-admin-actions">
                        @if (in_array($lpj->status, ['draft', 'revision'], true))
                            <a class="siom-admin-btn siom-admin-btn-sm" href="{{ route('sekretaris.lpj.edit', $lpj) }}">Edit</a>
                            <button type="button" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-primary" wire:click="submit({{ $lpj->id }})" wire:confirm="Kirim LPJ untuk persetujuan?">Kirim</button>
                            <button type="button" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-danger" wire:click="delete({{ $lpj->id }})" wire:confirm="Hapus LPJ ini?">Hapus</button>
                        @elseif ($lpj->status === 'approved')
                            <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="downloadSigned({{ $lpj->id }})" wire:loading.attr="disabled">Unduh DOCX</button>
                        @endif
                    </div>
                </article>
            @empty
                <p class="siom-admin-empty">Belum ada LPJ.</p>
            @endforelse
        </div>

        @if ($lpjs->hasPages())
            <div class="siom-admin-pagination p-3">
                {{ $lpjs->links() }}
            </div>
        @endif
    </div>
</div>