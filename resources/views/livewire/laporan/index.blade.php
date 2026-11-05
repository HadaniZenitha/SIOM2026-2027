<div class="siom-report">
    <style>
        .siom-report .siom-report-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }
        .siom-report .siom-report-head h1 { margin: 0; }
        .siom-report .siom-report-head p { margin: .35rem 0 0; }
        .siom-report .siom-report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }
        .siom-report .siom-report-stat {
            padding: 1rem;
            border: 2px solid #0f172a;
            border-radius: 14px;
            background: #fff;
            box-shadow: 3px 3px 0 #0f172a;
        }
        .siom-report .siom-report-stat span,
        .siom-report .siom-report-stat strong { display: block; }
        .siom-report .siom-report-stat span { color: #475569; font-size: .82rem; font-weight: 700; }
        .siom-report .siom-report-stat strong { margin-top: .35rem; font-size: 1.1rem; }
        .siom-report .siom-report-card {
            margin-top: 1rem;
            padding: 1.15rem 1.25rem;
            border: 2px solid #0f172a;
            border-radius: 16px;
            background: #fff;
            box-shadow: 3px 3px 0 #0f172a;
        }
        .siom-report .siom-report-card h2 { margin: 0 0 .85rem; font-size: 1.05rem; }
        .siom-report .siom-report-table-wrap { overflow-x: auto; }
        .siom-report .siom-report-table { width: 100%; border-collapse: collapse; }
        .siom-report .siom-report-table th,
        .siom-report .siom-report-table td {
            padding: .65rem;
            border-bottom: 1px solid #cbd5e1;
            text-align: left;
            vertical-align: top;
        }
        .siom-report .siom-report-table th { font-size: .78rem; }
        .siom-report .siom-report-muted { color: #475569; }
        .siom-report .siom-report-empty { margin: 0; color: #475569; }
        .siom-report .siom-activity-row {
            padding: .85rem 0;
            border-bottom: 1px solid #cbd5e1;
        }
        .siom-report .siom-activity-row:last-child { border-bottom: 0; }
        .siom-report .siom-activity-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: .7rem;
        }
        .siom-report .siom-activity-heading strong { display: block; }
        .siom-report .siom-activity-heading small { color: #475569; }
        .siom-report .siom-activity-badge {
            display: inline-flex;
            padding: .25rem .55rem;
            border-radius: 999px;
            background: #fee2e2;
            color: #991b1b;
            font-size: .72rem;
            font-weight: 800;
            white-space: nowrap;
        }
        .siom-report .siom-activity-badge.is-active { background: #dcfce7; color: #166534; }
        .siom-report .siom-chart-item {
            display: grid;
            grid-template-columns: 8.5rem minmax(0, 1fr) 2.5rem;
            align-items: center;
            gap: .65rem;
            margin-top: .4rem;
            font-size: .78rem;
        }
        .siom-report .siom-chart-track {
            height: .7rem;
            overflow: hidden;
            border-radius: 999px;
            background: #e2e8f0;
        }
        .siom-report .siom-chart-fill { height: 100%; border-radius: inherit; }
        .siom-report .siom-chart-fill.is-proposal { background: #2563eb; }
        .siom-report .siom-chart-fill.is-lpj { background: #16a34a; }
        .siom-report .siom-readonly {
            display: inline-flex;
            padding: .35rem .7rem;
            border: 1.5px solid #0f172a;
            border-radius: 999px;
            background: #f1f5f9;
            font-size: .75rem;
            font-weight: 800;
        }
        @media (max-width: 640px) {
            .siom-report .siom-report-head { flex-direction: column; }
            .siom-report .siom-chart-item { grid-template-columns: 6rem minmax(0, 1fr) 2rem; }
        }
    </style>

    <header class="siom-report-head">
        <div>
            <h1 class="h3">Laporan</h1>
            <p class="siom-report-muted">
                {{ $activePeriod ? 'Periode '.$activePeriod->name : 'Belum ada periode aktif.' }}
                @if ($user->ormawa)
                    · {{ $user->ormawa->name }}
                @elseif ($user->programStudi)
                    · {{ $user->programStudi->name }}
                @endif
            </p>
            @if ($activePeriod && $reportType !== 'superadmin')
                <p class="siom-report-muted">Tabel menampilkan maksimal 50 data terbaru per bagian; ringkasan menghitung seluruh data periode aktif.</p>
            @elseif ($activePeriod)
                <p class="siom-report-muted">Grafik dan ringkasan menghitung seluruh pengajuan serta LPJ di periode aktif.</p>
            @endif
        </div>
        @if ($user->hasAnyRole(['pembina_ormawa', 'kaprodi']))
            <span class="siom-readonly">Hanya dapat melihat</span>
        @endif
    </header>

    @if (! $activePeriod)
        <section class="siom-report-card">
            <p class="siom-report-empty">Laporan akan tersedia setelah periode aktif ditetapkan.</p>
        </section>
    @elseif ($reportType === 'superadmin')
        <section class="siom-report-grid" aria-label="Ringkasan aktivitas seluruh ORMAWA">
            <div class="siom-report-stat">
                <span>ORMAWA dengan Aktivitas</span>
                <strong>{{ $activeOrmawaCount }} / {{ $ormawaActivity->count() }}</strong>
            </div>
            <div class="siom-report-stat">
                <span>ORMAWA tanpa Aktivitas</span>
                <strong>{{ $ormawaActivity->count() - $activeOrmawaCount }}</strong>
            </div>
            <div class="siom-report-stat">
                <span>Total Pengajuan Dana</span>
                <strong>{{ $totalProposalCount }}</strong>
            </div>
            <div class="siom-report-stat">
                <span>Total Laporan Pertanggungjawaban</span>
                <strong>{{ $totalLpjCount }}</strong>
            </div>
        </section>

        <section class="siom-report-card">
            <h2>Grafik Aktivitas ORMAWA</h2>
            <p class="siom-report-muted">Perbandingan jumlah pengajuan dana dan LPJ pada periode aktif. ORMAWA dinilai aktif apabila memiliki minimal satu pengajuan atau LPJ.</p>
            @if ($ormawaActivity->isNotEmpty())
                @foreach ($ormawaActivity as $activity)
                    <div class="siom-activity-row" wire:key="superadmin-activity-{{ $activity['id'] }}">
                        <div class="siom-activity-heading">
                            <div>
                                <strong>{{ $activity['name'] }}</strong>
                                <small>{{ $activity['code'] }}</small>
                            </div>
                            <span class="siom-activity-badge {{ $activity['is_active'] ? 'is-active' : '' }}">
                                {{ $activity['is_active'] ? 'Aktif' : 'Belum ada aktivitas' }}
                            </span>
                        </div>
                        <div class="siom-chart-item">
                            <span>Pengajuan Dana</span>
                            <div class="siom-chart-track" role="img" aria-label="{{ $activity['proposals'] }} pengajuan dana">
                                <div class="siom-chart-fill is-proposal" style="width: {{ ($activity['proposals'] / $maximumProposalCount) * 100 }}%"></div>
                            </div>
                            <strong>{{ $activity['proposals'] }}</strong>
                        </div>
                        <div class="siom-chart-item">
                            <span>Laporan LPJ</span>
                            <div class="siom-chart-track" role="img" aria-label="{{ $activity['lpjs'] }} laporan LPJ">
                                <div class="siom-chart-fill is-lpj" style="width: {{ ($activity['lpjs'] / $maximumLpjCount) * 100 }}%"></div>
                            </div>
                            <strong>{{ $activity['lpjs'] }}</strong>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="siom-report-empty">Belum ada data ORMAWA untuk ditampilkan.</p>
            @endif
        </section>
    @elseif ($reportType === 'budget')
        <section class="siom-report-grid" aria-label="Ringkasan pagu">
            <div class="siom-report-stat">
                <span>Total Pagu</span>
                <strong>{{ $budget ? 'Rp '.number_format((float) $budget->budget_amount, 0, ',', '.') : 'Belum ditetapkan' }}</strong>
            </div>
            <div class="siom-report-stat">
                <span>Pagu Terpakai / Dialokasikan</span>
                <strong>{{ $budget ? 'Rp '.number_format((float) $budget->allocated_amount, 0, ',', '.') : 'Belum ditetapkan' }}</strong>
            </div>
            <div class="siom-report-stat">
                <span>Sisa Pagu</span>
                <strong>{{ $budget ? 'Rp '.number_format((float) $budget->remaining_amount, 0, ',', '.') : 'Belum ditetapkan' }}</strong>
            </div>
            <div class="siom-report-stat">
                <span>Realisasi LPJ Disetujui</span>
                <strong>Rp {{ number_format($realizedAmount, 0, ',', '.') }}</strong>
            </div>
        </section>

        <section class="siom-report-card">
            <h2>Ringkasan Laporan Pertanggungjawaban</h2>
            @if ($lpjStatusCounts->isNotEmpty())
                <div class="siom-report-grid">
                    <div class="siom-report-stat">
                        <span>Total LPJ</span>
                        <strong>{{ $lpjStatusCounts->sum() }}</strong>
                    </div>
                    @foreach ($lpjStatusCounts as $status => $total)
                        <div class="siom-report-stat" wire:key="report-lpj-status-{{ $status }}">
                            <span>{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                            <strong>{{ $total }}</strong>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="siom-report-empty">Belum ada LPJ pada periode ini.</p>
            @endif

            @if ($lpjs->isNotEmpty())
                <div class="siom-report-table-wrap">
                    <table class="siom-report-table">
                        <thead>
                            <tr>
                                <th>LPJ</th>
                                <th>Kegiatan</th>
                                <th>Status</th>
                                <th>Realisasi Disetujui</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lpjs as $lpj)
                                <tr wire:key="report-lpj-{{ $lpj->id }}">
                                    <td>{{ $lpj->title }}</td>
                                    <td>{{ $lpj->submission?->activity_name ?? '-' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $lpj->status)) }}</td>
                                    <td>
                                        @if ($lpj->status === 'approved')
                                            Rp {{ number_format((float) $lpj->items->sum('total_price'), 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="siom-report-card">
            <h2>Penggunaan Pagu per Kegiatan</h2>
            @if ($budgetActivities->isNotEmpty())
                <div class="siom-report-table-wrap">
                    <table class="siom-report-table">
                        <thead>
                            <tr>
                                <th>Kegiatan</th>
                                <th>Status LPJ</th>
                                <th>Dana Disetujui</th>
                                <th>Realisasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($budgetActivities as $activity)
                                <tr wire:key="report-budget-activity-{{ $activity->id }}">
                                    <td>{{ $activity->activity_name }}</td>
                                    <td>{{ $activity->lpj ? ucfirst(str_replace('_', ' ', $activity->lpj->status)) : 'Belum dibuat' }}</td>
                                    <td>Rp {{ number_format((float) $activity->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($activity->lpj?->status === 'approved')
                                            Rp {{ number_format((float) $activity->lpj->items->sum('total_price'), 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="siom-report-empty">Belum ada penggunaan pagu untuk proposal yang disetujui pada periode ini.</p>
            @endif
        </section>
    @elseif ($reportType === 'kaprodi')
        <section class="siom-report-grid" aria-label="Ringkasan aktivitas HIMA">
            <div class="siom-report-stat">
                <span>Total Proposal</span>
                <strong>{{ $proposalStatusCounts->sum() }}</strong>
            </div>
            <div class="siom-report-stat">
                <span>Total LPJ</span>
                <strong>{{ $lpjStatusCounts->sum() }}</strong>
            </div>
        </section>

        <section class="siom-report-card">
            <h2>Aktivitas Pengajuan Proposal HIMA</h2>
            @if ($proposals->isNotEmpty())
                <div class="siom-report-table-wrap">
                    <table class="siom-report-table">
                        <thead>
                            <tr>
                                <th>Kegiatan</th>
                                <th>HIMA</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($proposals as $proposal)
                                <tr wire:key="report-proposal-{{ $proposal->id }}">
                                    <td>{{ $proposal->activity_name }}</td>
                                    <td>{{ $proposal->ormawa?->name ?? '-' }}</td>
                                    <td>{{ $proposal->submitted_at?->format('d/m/Y') ?? '-' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $proposal->status)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="siom-report-empty">Belum ada pengajuan proposal HIMA pada periode ini.</p>
            @endif
        </section>

        <section class="siom-report-card">
            <h2>Aktivitas Laporan Pertanggungjawaban HIMA</h2>
            @if ($lpjs->isNotEmpty())
                <div class="siom-report-table-wrap">
                    <table class="siom-report-table">
                        <thead>
                            <tr>
                                <th>LPJ</th>
                                <th>Kegiatan</th>
                                <th>HIMA</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lpjs as $lpj)
                                <tr wire:key="report-prodi-lpj-{{ $lpj->id }}">
                                    <td>{{ $lpj->title }}</td>
                                    <td>{{ $lpj->submission?->activity_name ?? '-' }}</td>
                                    <td>{{ $lpj->ormawa?->name ?? '-' }}</td>
                                    <td>{{ $lpj->submitted_at?->format('d/m/Y') ?? '-' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $lpj->status)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="siom-report-empty">Belum ada LPJ HIMA pada periode ini.</p>
            @endif
        </section>
    @else
        <section class="siom-report-card">
            <p class="siom-report-empty">Laporan ringkasan saat ini tersedia untuk Sekretaris ORMAWA, Pembina ORMAWA, dan Kaprodi.</p>
        </section>
    @endif
</div>
