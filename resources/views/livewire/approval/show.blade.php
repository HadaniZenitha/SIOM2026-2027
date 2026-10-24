<div class="siom-admin-page siom-approval-show">
    <style>
        .siom-approval-show .siom-back {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            margin-bottom: .7rem;
            color: #1d4ed8;
            font-size: .85rem;
            font-weight: 700;
            text-decoration: none;
        }
        .siom-approval-show .siom-meta {
            color: #64748b;
            font-size: .9rem;
            margin: 0;
        }
        .siom-approval-show .siom-status-badge {
            display: inline-block;
            padding: .35rem .7rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 700;
            white-space: nowrap;
        }
        .siom-approval-show .siom-status-approved { background: #dcfce7; color: #166534; }
        .siom-approval-show .siom-status-rejected { background: #fee2e2; color: #991b1b; }
        .siom-approval-show .siom-status-revision { background: #fef9c3; color: #854d0e; }
        .siom-approval-show .siom-status-pending,
        .siom-approval-show .siom-status-default { background: #dbeafe; color: #1e40af; }
        .siom-approval-show .siom-dl {
            display: grid;
            gap: .65rem 1rem;
        }
        .siom-approval-show .siom-dl-row {
            display: grid;
            grid-template-columns: minmax(120px, 180px) 1fr;
            gap: .35rem .75rem;
            align-items: start;
        }
        .siom-approval-show .siom-dl-row dt {
            margin: 0;
            font-weight: 600;
            color: #475569;
            font-size: .88rem;
        }
        .siom-approval-show .siom-dl-row dd {
            margin: 0;
            overflow-wrap: anywhere;
        }
        .siom-approval-show .siom-list {
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }
        .siom-approval-show .siom-list-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .75rem;
            padding: .65rem .75rem;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #fff;
        }
        .siom-approval-show .siom-list-item small { color: #64748b; }
        .siom-approval-show .siom-step-cols {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: .45rem;
        }
        .siom-approval-show .siom-step-col {
            display: grid;
            grid-template-columns: 2rem minmax(140px, 1fr) 140px minmax(0, 1.4fr);
            gap: .35rem .75rem;
            align-items: center;
            width: min(100%, 760px);
            padding: .6rem .75rem;
            border: 1.5px solid currentColor;
            border-radius: 12px;
        }
        .siom-approval-show .siom-step-col strong,
        .siom-approval-show .siom-step-col small,
        .siom-approval-show .siom-step-col span { overflow-wrap: anywhere; }
        .siom-approval-show .siom-step-col small { color: inherit; font-size: .78rem; opacity: .85; }
        .siom-approval-show .is-approved { background: #dcfce7; color: #166534; }
        .siom-approval-show .is-pending { background: #fef9c3; color: #854d0e; }
        .siom-approval-show .is-waiting { background: #f1f5f9; color: #475569; }
        .siom-approval-show .is-revision { background: #e0f2fe; color: #075985; }
        .siom-approval-show .is-rejected { background: #fee2e2; color: #991b1b; }
        .siom-approval-show .siom-table-wrap { overflow-x: auto; }
        .siom-approval-show .siom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .9rem;
        }
        .siom-approval-show .siom-table th,
        .siom-approval-show .siom-table td {
            padding: .55rem .75rem;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            vertical-align: middle;
        }
        .siom-approval-show .siom-table th { font-weight: 600; color: #475569; background: #f8fafc; }
        .siom-approval-show .siom-table .text-end { text-align: right; }
        .siom-approval-show .siom-table tfoot th { border-bottom: none; background: transparent; }
        .siom-approval-show .siom-admin-actions { flex-wrap: wrap; justify-content: flex-end; }
        .siom-approval-show .siom-log-item {
            padding: .75rem 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .siom-approval-show .siom-log-item:last-child { border-bottom: none; }
        @media (max-width: 760px) {
            .siom-approval-show .siom-dl-row { grid-template-columns: 1fr; }
            .siom-approval-show .siom-step-col { grid-template-columns: 2rem 1fr; width: 100%; }
            .siom-approval-show .siom-step-col small { grid-column: 2; }
            .siom-approval-show .siom-admin-actions { align-items: stretch; flex-direction: column; }
            .siom-approval-show .siom-admin-actions .siom-admin-btn { width: 100%; }
            .siom-approval-show .siom-list-item { flex-direction: column; gap: .25rem; }
        }
    </style>

    <a class="siom-back" href="{{ route('approval.index') }}">
        <i class="bi bi-arrow-left"></i> Kembali ke daftar
    </a>

    <div class="siom-admin-heading">
        <div>
            <h1>{{ $submission->activity_name }}</h1>
            <p class="siom-meta">{{ $submission->submission_number }}</p>
        </div>
        @php
            $submissionStatus = is_object($submission->status) && $submission->status instanceof \BackedEnum
                ? $submission->status->value
                : (string) $submission->status;
            $statusClass = match ($submissionStatus) {
                'approved' => 'siom-status-approved',
                'rejected' => 'siom-status-rejected',
                'revision' => 'siom-status-revision',
                'pending' => 'siom-status-pending',
                default => 'siom-status-default',
            };
        @endphp
        <span class="siom-status-badge {{ $statusClass }}">
            {{ ucfirst(str_replace('_', ' ', $submissionStatus)) }}
        </span>
    </div>

    @if (session('success'))
        <div class="siom-admin-alert siom-admin-alert-success">{{ session('success') }}</div>
    @endif

    @error('approval')
        <div class="siom-admin-alert siom-admin-alert-error">{{ $message }}</div>
    @enderror

    <section class="siom-admin-card">
        <div class="siom-admin-card-body">
            <h2 class="h5 mb-3">Informasi Pengajuan</h2>
            <div class="siom-dl">
                <div class="siom-dl-row">
                    <dt>Pemohon</dt>
                    <dd>{{ $submission->submitter?->name ?? '-' }}</dd>
                </div>
                <div class="siom-dl-row">
                    <dt>ORMAWA</dt>
                    <dd>{{ $submission->ormawa?->name ?? '-' }}</dd>
                </div>
                <div class="siom-dl-row">
                    <dt>Periode</dt>
                    <dd>{{ $submission->period?->name ?? '-' }}</dd>
                </div>
                <div class="siom-dl-row">
                    <dt>Tanggal kegiatan</dt>
                    <dd>{{ $submission->event_date?->format('d/m/Y') ?? '-' }}</dd>
                </div>
                <div class="siom-dl-row">
                    <dt>Waktu & tempat</dt>
                    <dd>
                        {{ $submission->event_start_time?->format('H:i') ?? '-' }}–{{ $submission->event_end_time?->format('H:i') ?? '-' }},
                        {{ $submission->event_location ?? '-' }}
                    </dd>
                </div>
                <div class="siom-dl-row">
                    <dt>Latar belakang</dt>
                    <dd>{{ $submission->background ?? $submission->description ?? '-' }}</dd>
                </div>
                @if ($submission->objectives)
                    <div class="siom-dl-row">
                        <dt>Tujuan</dt>
                        <dd>
                            <ul class="mb-0 ps-3">
                                @foreach ($submission->objectives as $objective)
                                    <li>{{ $objective }}</li>
                                @endforeach
                            </ul>
                        </dd>
                    </div>
                @endif
                @if ($submission->benefits)
                    <div class="siom-dl-row">
                        <dt>Manfaat</dt>
                        <dd>
                            <ul class="mb-0 ps-3">
                                @foreach ($submission->benefits as $benefit)
                                    <li>{{ $benefit }}</li>
                                @endforeach
                            </ul>
                        </dd>
                    </div>
                @endif
                <div class="siom-dl-row">
                    <dt>Sasaran peserta</dt>
                    <dd>{{ $submission->target_participants ?? '-' }}</dd>
                </div>
            </div>
        </div>
    </section>

    <section class="siom-admin-card">
        <div class="siom-admin-card-body">
            <h2 class="h5 mb-3">Data Penandatangan Dokumen</h2>
            <div class="siom-dl">
                <div class="siom-dl-row">
                    <dt>Ketua Pelaksana</dt>
                    <dd>{{ ($submission->chairman_name ?: $submission->submitter?->name) ?? '-' }}</dd>
                </div>
                <div class="siom-dl-row">
                    <dt>NIM Ketua Pelaksana</dt>
                    <dd>{{ $submission->chairman_nim ?: '-' }}</dd>
                </div>
                <div class="siom-dl-row">
                    <dt>Pembina</dt>
                    <dd>{{ $submission->pembina_name ?: '-' }}</dd>
                </div>
                <div class="siom-dl-row">
                    <dt>NUPTK Pembina</dt>
                    <dd>{{ $submission->pembina_nuptk ?: '-' }}</dd>
                </div>
                <div class="siom-dl-row">
                    <dt>Kaprodi</dt>
                    <dd>{{ $submission->kaprodi_name ?: '-' }}</dd>
                </div>
                <div class="siom-dl-row">
                    <dt>NUPTK Kaprodi</dt>
                    <dd>{{ $submission->kaprodi_nuptk ?: '-' }}</dd>
                </div>
            </div>
        </div>
    </section>

    <section class="siom-admin-card">
        <div class="siom-admin-card-body">
            <h2 class="h5 mb-3">Timeline Kegiatan</h2>
            @forelse ($submission->timelines as $timeline)
                <div class="mb-3" wire:key="approval-timeline-{{ $timeline->id }}">
                    <div class="fw-semibold">{{ ucfirst($timeline->stage) }}</div>
                    <small class="text-muted d-block mb-1">
                        {{ $timeline->start_date?->format('d/m/Y') ?? '-' }}
                        – {{ $timeline->end_date?->format('d/m/Y') ?? '-' }}
                    </small>
                    @if ($timeline->description)
                        <ul class="mb-0 ps-3">
                            @foreach ($timeline->description as $context)
                                <li>{{ $context }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada timeline kegiatan.</p>
            @endforelse
        </div>
    </section>

    <section class="siom-admin-card">
        <div class="siom-admin-card-body">
            <h2 class="h5 mb-3">Panitia</h2>
            <div class="siom-list">
                @forelse ($submission->committees as $committee)
                    <div class="siom-list-item" wire:key="approval-committee-{{ $committee->id }}">
                        <span>{{ $committee->name }}</span>
                        <small>{{ $committee->committee_role }}</small>
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada data panitia.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="siom-admin-card">
        <div class="siom-admin-card-body">
            <h2 class="h5 mb-3">Rundown</h2>
            <div class="siom-list">
                @forelse ($submission->rundowns as $rundown)
                    <div class="siom-list-item" wire:key="approval-rundown-{{ $rundown->id }}">
                        <div>
                            <span class="fw-semibold">{{ $rundown->time_slot }}</span>
                            · {{ $rundown->event_name }}
                            @if ($rundown->pic)
                                <small class="d-block">({{ $rundown->pic }})</small>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada rundown.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="siom-admin-card">
        <div class="siom-admin-card-body">
            <h2 class="h5 mb-3">Rencana Anggaran</h2>
            <div class="siom-table-wrap">
                <table class="siom-table">
                    <thead>
                        <tr>
                            <th>Uraian</th>
                            <th>Jumlah</th>
                            <th>Harga satuan</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($submission->items as $item)
                            <tr wire:key="approval-item-{{ $item->id }}">
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->qty }} {{ $item->unit }}</td>
                                <td>Rp {{ number_format((float) $item->price, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format((float) $item->total_price, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Tidak ada rincian anggaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total pengajuan</th>
                            <th class="text-end">Rp {{ number_format((float) $submission->total_amount, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>

    <section class="siom-admin-card">
        <div class="siom-admin-card-body">
            <h2 class="h5 mb-3">Tahapan Persetujuan</h2>
            <div class="siom-step-cols">
                @foreach ($submission->approvalSteps as $step)
                    @php
                        $stepStatus = $step->status instanceof \BackedEnum
                            ? $step->status->value
                            : (string) $step->status;
                    @endphp
                    <div class="siom-step-col is-{{ $stepStatus }}" wire:key="approval-step-{{ $step->id }}">
                        <strong>{{ $step->sequence }}.</strong>
                        <span>
                            {{ $step->role?->display_name ?? 'Tahap '.$step->sequence }}
                            @if ($step->approver)
                                <small class="d-block">{{ $step->approver->name }} · {{ $step->approved_at?->format('d/m/Y H:i') }}</small>
                            @endif
                        </span>
                        <strong>{{ ucfirst(str_replace('_', ' ', $stepStatus)) }}</strong>
                        <small>{{ $step->note }}</small>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="siom-admin-card">
        <div class="siom-admin-card-body">
            <h2 class="h5 mb-3">Riwayat Keputusan</h2>
            @forelse ($submission->approvalLogs as $log)
                <div class="siom-log-item" wire:key="approval-log-{{ $log->id }}">
                    <div class="d-flex justify-content-between gap-3 flex-wrap">
                        <span class="fw-semibold">{{ $log->user?->name ?? 'Pengguna' }}</span>
                        <small class="text-muted">{{ $log->created_at?->format('d/m/Y H:i') }}</small>
                    </div>
                    <div>
                        {{ match ($log->action) {
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                            'revision' => 'Diminta revisi',
                            'resubmitted' => 'Dikirim ulang',
                            default => ucfirst($log->action),
                        } }}
                    </div>
                    @if ($log->note)
                        <p class="text-muted mb-0 mt-1">{{ $log->note }}</p>
                    @endif
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada keputusan.</p>
            @endforelse
        </div>
    </section>

    @if ($canDecide)
        <form class="siom-admin-card" wire:submit.prevent>
            <div class="siom-admin-card-body">
                <div class="siom-admin-field">
                    <label for="approval-note">Catatan keputusan</label>
                    <textarea
                        id="approval-note"
                        rows="4"
                        wire:model="note"
                        maxlength="2000"
                        placeholder="Catatan persetujuan atau alasan penolakan/revisi"
                    ></textarea>
                    @error('note')
                        <span class="siom-admin-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="siom-admin-actions">
                    <button type="button" class="siom-admin-btn siom-admin-btn-danger" wire:click="reject" wire:loading.attr="disabled" wire:confirm="Tolak pengajuan ini?">
                        Tolak
                    </button>
                    <button type="button" class="siom-admin-btn" wire:click="requestRevision" wire:loading.attr="disabled">
                        Minta Revisi
                    </button>
                    <button type="button" class="siom-admin-btn siom-admin-btn-primary" wire:click="approve" wire:loading.attr="disabled">
                        Setujui
                    </button>
                </div>
            </div>
        </form>
    @elseif ($submission->status === 'revision')
        <div class="siom-admin-alert siom-admin-alert-warning">
            Pengajuan menunggu perbaikan dari sekretaris ORMAWA.
        </div>
    @endif
</div>