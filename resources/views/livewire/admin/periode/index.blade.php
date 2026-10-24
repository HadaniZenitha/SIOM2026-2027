<div class="siom-admin-page">
    <header class="siom-admin-heading">
        <div>
            <h1>Periode Organisasi</h1>
            <p>Atur rentang periode kegiatan, status aktif, dan masa kepengurusan.</p>
        </div>
        <button type="button" class="siom-admin-btn siom-admin-btn-primary" wire:click="create">
            <i class="bi bi-plus-lg"></i> Tambah Periode
        </button>
    </header>

    @if (session('success'))
        <div class="siom-admin-alert siom-admin-alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="siom-admin-alert siom-admin-alert-error" role="alert">{{ session('error') }}</div>
    @endif

    <section class="siom-admin-card">
        <div class="siom-admin-card-body">
            <div class="siom-admin-toolbar">
                <h2>Daftar Periode</h2>
                <div class="siom-admin-field" style="width:min(100%, 360px)">
                    <label for="period-search">Cari periode</label>
                    <input id="period-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Nama atau keterangan...">
                </div>
            </div>
            <div class="siom-admin-table-wrap">
                <table class="siom-admin-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Periode</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($periods as $period)
                            <tr wire:key="period-{{ $period->id }}">
                                <td>{{ $periods->firstItem() + $loop->index }}</td>
                                <td><strong>{{ $period->name }}</strong></td>
                                <td>{{ $period->start_date->format('d M Y') }}</td>
                                <td>{{ $period->end_date->format('d M Y') }}</td>
                                <td>
                                    <span class="siom-admin-badge siom-admin-badge-{{ $period->status }}">
                                        {{ match ($period->status) {
                                            'active' => 'Aktif',
                                            'finished' => 'Selesai',
                                            default => 'Draft',
                                        } }}
                                    </span>
                                </td>
                                <td>{{ $period->description ?: '-' }}</td>
                                <td>
                                    <div class="siom-admin-actions">
                                        <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="edit({{ $period->id }})"><i class="bi bi-pencil"></i> Edit</button>
                                        <button type="button" class="siom-admin-btn siom-admin-btn-danger siom-admin-btn-sm" wire:click="delete({{ $period->id }})" wire:confirm="Hapus periode ini? Periode yang masih memiliki data terkait tidak dapat dihapus."><i class="bi bi-trash"></i> Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="siom-admin-empty">Belum ada periode organisasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($periods->hasPages())
                <div class="siom-admin-pagination">{{ $periods->links() }}</div>
            @endif
        </div>
    </section>

    @if ($showForm)
        <div class="siom-admin-modal" role="dialog" aria-modal="true" aria-labelledby="period-form-title">
            <div class="siom-admin-modal-card">
                <div class="siom-admin-modal-head">
                    <div>
                        <h2 id="period-form-title">{{ $periodId ? 'Edit Periode' : 'Tambah Periode' }}</h2>
                        <p>Periode aktif akan menjadi satu-satunya periode aktif.</p>
                    </div>
                    <button type="button" class="siom-admin-btn siom-admin-btn-sm" aria-label="Tutup" wire:click="resetForm"><i class="bi bi-x-lg"></i></button>
                </div>
                <form wire:submit="save">
                    <div class="siom-admin-modal-body">
                        <div class="siom-admin-grid">
                            <div class="siom-admin-field siom-admin-span-6">
                                <label for="period-name">Nama Periode *</label>
                                <input id="period-name" type="text" wire:model="name" placeholder="Contoh: 2025/2026" class="@error('name') is-invalid @enderror">
                                @error('name') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="siom-admin-field siom-admin-span-6">
                                <label for="period-status">Status *</label>
                                <select id="period-status" wire:model="status" class="@error('status') is-invalid @enderror">
                                    <option value="draft">Draft</option>
                                    <option value="active">Aktif</option>
                                    <option value="finished">Selesai</option>
                                </select>
                                @error('status') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="siom-admin-field siom-admin-span-6">
                                <label for="period-start">Tanggal Mulai *</label>
                                <input id="period-start" type="date" wire:model="start_date" class="@error('start_date') is-invalid @enderror">
                                @error('start_date') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="siom-admin-field siom-admin-span-6">
                                <label for="period-end">Tanggal Selesai *</label>
                                <input id="period-end" type="date" wire:model="end_date" class="@error('end_date') is-invalid @enderror">
                                @error('end_date') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="siom-admin-field siom-admin-span-12">
                                <label for="period-description">Keterangan</label>
                                <textarea id="period-description" wire:model="description"></textarea>
                                @error('description') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="siom-admin-modal-foot">
                        <button type="button" class="siom-admin-btn" wire:click="resetForm">Batal</button>
                        <button type="submit" class="siom-admin-btn siom-admin-btn-primary" wire:loading.attr="disabled"><i class="bi bi-check-lg"></i> Simpan Periode</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
