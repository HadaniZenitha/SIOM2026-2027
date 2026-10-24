<div class="siom-admin-page siom-approval-page">
    <style>
        .siom-approval-page .siom-admin-table { min-width: 0; width: 100%; }
        .siom-approval-page .siom-admin-table th,
        .siom-approval-page .siom-admin-table td { white-space: normal; vertical-align: top; }
        .siom-approval-page .siom-nowrap { white-space: nowrap; }
        .siom-approval-page .siom-step-cols { display: flex; flex-direction: column; align-items: flex-start; gap: .35rem; }
        .siom-approval-page .siom-step-col {
            display: grid;
            grid-template-columns: minmax(110px, 1fr) auto;
            gap: .35rem .6rem;
            width: 100%;
            max-width: 280px;
            padding: .4rem .6rem;
            border: 1.5px solid currentColor;
            border-radius: 10px;
            font-size: .75rem;
            font-weight: 800;
        }
        .siom-approval-page .is-approved, .siom-approval-page .is-completed { background: #dcfce7; color: #166534; }
        .siom-approval-page .is-pending, .siom-approval-page .is-submitted, .siom-approval-page .is-waiting_approval { background: #fef9c3; color: #854d0e; }
        .siom-approval-page .is-waiting, .siom-approval-page .is-draft { background: #f1f5f9; color: #475569; }
        .siom-approval-page .is-revision { background: #e0f2fe; color: #075985; }
        .siom-approval-page .is-rejected { background: #fee2e2; color: #991b1b; }
        .siom-approval-page .siom-approval-cards { display: none; gap: .75rem; }
        .siom-approval-page .siom-approval-card {
            display: grid;
            gap: .35rem;
            padding: .9rem;
            border: 1.5px solid #0f172a;
            border-radius: 14px;
            background: #f8fbff;
        }
        .siom-approval-page .siom-approval-card strong,
        .siom-approval-page .siom-approval-card small { overflow-wrap: anywhere; }
        @media (max-width: 900px) {
            .siom-approval-page .siom-approval-table { display: none; }
            .siom-approval-page .siom-approval-cards { display: grid; }
            .siom-approval-page .siom-approval-cards .siom-admin-btn { width: 100%; }
            .siom-approval-page .siom-step-col { max-width: none; }
        }
    </style>

    <div class="siom-admin-heading">
        <div>
            <h1>Persetujuan Pengajuan</h1>
            <p>Pengajuan yang sedang menunggu keputusan Anda.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="siom-admin-alert siom-admin-alert-success">{{ session('success') }}</div>
    @endif

    <div class="siom-admin-card">
        <div class="siom-admin-card-body">
            <div class="siom-admin-table-wrap siom-approval-table">
                <table class="siom-admin-table">
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Kegiatan</th>
                            <th>ORMAWA</th>
                            <th>Pemohon</th>
                            <th>Tahap</th>
                            <th class="siom-nowrap">Total Anggaran</th>
                            <th class="siom-nowrap">Dikirim</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($submissions as $submission)
                            @php
                                $activeStep = $submission->approvalSteps->first(function ($step) {
                                    $status = $step->status instanceof \BackedEnum ? $step->status->value : (string) $step->status;
                                    return $status === 'pending';
                                });
                            @endphp
                            <tr wire:key="approval-submission-{{ $submission->id }}">
                                <td><strong>{{ $submission->submission_number }}</strong></td>
                                <td>{{ $submission->activity_name }}</td>
                                <td>{{ $submission->ormawa?->name ?? '-' }}</td>
                                <td>{{ $submission->submitter?->name ?? '-' }}</td>
                                <td>
                                    <div class="siom-step-cols">
                                        @forelse ($submission->approvalSteps as $step)
                                            @php $stepStatus = $step->status instanceof \BackedEnum ? $step->status->value : (string) $step->status; @endphp
                                            <div class="siom-step-col is-{{ $stepStatus }}">
                                                <span>{{ $step->role?->display_name ?? 'Tahap' }}</span>
                                                <strong>{{ ucfirst(str_replace('_', ' ', $stepStatus)) }}</strong>
                                            </div>
                                        @empty
                                            <span>{{ $activeStep?->role?->display_name ?? '-' }}</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="siom-nowrap">Rp {{ number_format((float) $submission->total_amount, 0, ',', '.') }}</td>
                                <td class="siom-nowrap">{{ $submission->submitted_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>
                                    <a class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-primary" href="{{ route('approval.show', $submission) }}">Tinjau</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="siom-admin-empty">Tidak ada pengajuan yang menunggu persetujuan Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="siom-approval-cards">
                @forelse ($submissions as $submission)
                    <article class="siom-approval-card" wire:key="approval-submission-card-{{ $submission->id }}">
                        <strong>{{ $submission->activity_name }}</strong>
                        <small class="text-muted">{{ $submission->submission_number }} · {{ $submission->ormawa?->name ?? '-' }}</small>
                        <span>Pemohon: {{ $submission->submitter?->name ?? '-' }}</span>
                        <span class="siom-nowrap">Total Anggaran Rp {{ number_format((float) $submission->total_amount, 0, ',', '.') }}</span>
                        <small class="text-muted">Dikirim {{ $submission->submitted_at?->format('d/m/Y H:i') ?? '-' }}</small>
                        <div class="siom-step-cols">
                            @foreach ($submission->approvalSteps as $step)
                                @php $stepStatus = $step->status instanceof \BackedEnum ? $step->status->value : (string) $step->status; @endphp
                                <div class="siom-step-col is-{{ $stepStatus }}">
                                    <span>{{ $step->role?->display_name ?? 'Tahap' }}</span>
                                    <strong>{{ ucfirst(str_replace('_', ' ', $stepStatus)) }}</strong>
                                </div>
                            @endforeach
                        </div>
                        <a class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-primary" href="{{ route('approval.show', $submission) }}">Tinjau</a>
                    </article>
                @empty
                    <p class="siom-admin-empty">Tidak ada pengajuan yang menunggu persetujuan Anda.</p>
                @endforelse
            </div>

            @if ($submissions->hasPages())
                <div class="siom-admin-pagination mt-3">{{ $submissions->links() }}</div>
            @endif
        </div>
    </div>

    <div class="siom-admin-heading mt-4">
        <div>
            <h1>Persetujuan LPJ</h1>
            <p>Laporan pertanggungjawaban yang menunggu keputusan Anda.</p>
        </div>
    </div>

    <div class="siom-admin-card">
        <div class="siom-admin-card-body">
            <div class="siom-admin-table-wrap siom-approval-table">
                <table class="siom-admin-table">
                    <thead>
                        <tr>
                            <th>LPJ</th>
                            <th>ORMAWA</th>
                            <th>Pengajuan</th>
                            <th>Tahap</th>
                            <th class="siom-nowrap">Dikirim</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lpjs as $lpj)
                            <tr wire:key="approval-lpj-{{ $lpj->id }}">
                                <td><strong>{{ $lpj->title }}</strong></td>
                                <td>{{ $lpj->ormawa?->name ?? '-' }}</td>
                                <td>{{ $lpj->submission?->submission_number ?? '-' }}</td>
                                <td>
                                    <div class="siom-step-cols">
                                        @forelse ($lpj->approvalSteps as $step)
                                            @php $stepStatus = $step->status instanceof \BackedEnum ? $step->status->value : (string) $step->status; @endphp
                                            <div class="siom-step-col is-{{ $stepStatus }}">
                                                <span>{{ $step->role?->display_name ?? 'Tahap' }}</span>
                                                <strong>{{ ucfirst(str_replace('_', ' ', $stepStatus)) }}</strong>
                                            </div>
                                        @empty
                                            <span>-</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="siom-nowrap">{{ $lpj->submitted_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>
                                    <a class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-primary" href="{{ route('approval.lpj.show', $lpj) }}">Tinjau</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="siom-admin-empty">Tidak ada LPJ yang menunggu persetujuan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="siom-approval-cards">
                @forelse ($lpjs as $lpj)
                    <article class="siom-approval-card" wire:key="approval-lpj-card-{{ $lpj->id }}">
                        <strong>{{ $lpj->title }}</strong>
                        <small class="text-muted">{{ $lpj->ormawa?->name ?? '-' }} · {{ $lpj->submission?->submission_number ?? '-' }}</small>
                        <small class="text-muted">Dikirim {{ $lpj->submitted_at?->format('d/m/Y H:i') ?? '-' }}</small>
                        <div class="siom-step-cols">
                            @foreach ($lpj->approvalSteps as $step)
                                @php $stepStatus = $step->status instanceof \BackedEnum ? $step->status->value : (string) $step->status; @endphp
                                <div class="siom-step-col is-{{ $stepStatus }}">
                                    <span>{{ $step->role?->display_name ?? 'Tahap' }}</span>
                                    <strong>{{ ucfirst(str_replace('_', ' ', $stepStatus)) }}</strong>
                                </div>
                            @endforeach
                        </div>
                        <a class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-primary" href="{{ route('approval.lpj.show', $lpj) }}">Tinjau</a>
                    </article>
                @empty
                    <p class="siom-admin-empty">Tidak ada LPJ yang menunggu persetujuan.</p>
                @endforelse
            </div>

            @if ($lpjs->hasPages())
                <div class="siom-admin-pagination mt-3">{{ $lpjs->links() }}</div>
            @endif
        </div>
    </div>
</div>