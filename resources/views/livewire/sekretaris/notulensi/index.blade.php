<div class="siom-admin-page siom-notes-page">
    <style>
        .siom-notes-page .siom-admin-table {
            min-width: 0;
            width: 100%;
        }
        .siom-notes-page .siom-admin-table th,
        .siom-notes-page .siom-admin-table td {
            white-space: normal;
            vertical-align: top;
        }
        .siom-notes-page .siom-admin-actions {
            flex-wrap: wrap;
        }
        .siom-notes-page .siom-notes-cards {
            display: none;
            gap: .75rem;
            padding: 1rem;
        }
        .siom-notes-page .siom-notes-card {
            display: grid;
            gap: .35rem;
            padding: .9rem;
            border: 1.5px solid #0f172a;
            border-radius: 14px;
            background: #f8fbff;
        }
        .siom-notes-page .siom-notes-card strong,
        .siom-notes-page .siom-notes-card small {
            overflow-wrap: anywhere;
        }
        @media (max-width: 900px) {
            .siom-notes-page .siom-notes-table { display: none; }
            .siom-notes-page .siom-notes-cards { display: grid; }
            .siom-notes-page .siom-admin-toolbar { align-items: stretch; flex-direction: column; }
            .siom-notes-page .siom-admin-toolbar .siom-admin-field { width: 100% !important; }
        }
    </style>

    <div class="siom-admin-heading">
        <div>
            <h1>Notulensi Rapat</h1>
            <p>Catatan dan absensi rapat ORMAWA.</p>
        </div>
        <a class="siom-admin-btn siom-admin-btn-primary" href="{{ route('sekretaris.notulensi.create') }}">
            <i class="bi bi-plus-lg"></i> Buat Notulensi
        </a>
    </div>

    @if (session('success'))
        <div class="siom-admin-alert siom-admin-alert-success">{{ session('success') }}</div>
    @endif

    <div class="siom-admin-card">
        <div class="siom-admin-card-body">
            <div class="siom-admin-toolbar mb-3">
                <h2 class="h6 fw-semibold mb-0">Daftar Notulensi</h2>
                <div class="siom-admin-field mb-0" style="width: min(100%, 280px);">
                    <input type="search" placeholder="Cari rapat" wire:model.live.debounce.300ms="search">
                </div>
            </div>

            <div class="siom-admin-table-wrap siom-notes-table">
                <table class="siom-admin-table">
                    <thead>
                        <tr>
                            <th>Rapat</th>
                            <th style="width: 120px;">Tanggal</th>
                            <th>Lokasi</th>
                            <th>Dibuat oleh</th>
                            <th style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($meetings as $meeting)
                            <tr wire:key="meeting-{{ $meeting->id }}">
                                <td><strong>{{ $meeting->title }}</strong></td>
                                <td>{{ $meeting->meeting_date->format('d/m/Y') }}</td>
                                <td>{{ $meeting->location ?? '-' }}</td>
                                <td>{{ $meeting->creator?->name }}</td>
                                <td>
                                    <div class="siom-admin-actions">
                                        <a class="siom-admin-btn siom-admin-btn-sm" href="{{ route('sekretaris.notulensi.edit', $meeting) }}">Edit</a>
                                        <button type="button" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-danger" wire:click="delete({{ $meeting->id }})" wire:confirm="Hapus notulensi ini?">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="siom-admin-empty">Belum ada notulensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="siom-notes-cards">
                @forelse ($meetings as $meeting)
                    <article class="siom-notes-card" wire:key="meeting-card-{{ $meeting->id }}">
                        <strong>{{ $meeting->title }}</strong>
                        <small class="text-muted">{{ $meeting->meeting_date->format('d/m/Y') }} · {{ $meeting->location ?? '-' }}</small>
                        <span>Dibuat oleh {{ $meeting->creator?->name }}</span>
                        <div class="siom-admin-actions">
                            <a class="siom-admin-btn siom-admin-btn-sm" href="{{ route('sekretaris.notulensi.edit', $meeting) }}">Edit</a>
                            <button type="button" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-danger" wire:click="delete({{ $meeting->id }})" wire:confirm="Hapus notulensi ini?">Hapus</button>
                        </div>
                    </article>
                @empty
                    <p class="siom-admin-empty">Belum ada notulensi.</p>
                @endforelse
            </div>

            @if ($meetings->hasPages())
                <div class="siom-admin-pagination mt-3">
                    {{ $meetings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>