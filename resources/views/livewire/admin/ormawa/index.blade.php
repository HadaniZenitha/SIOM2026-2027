<div class="siom-admin-page">
    <header class="siom-admin-heading">
        <div>
            <h1>Master ORMAWA</h1>
            <p>Kelola organisasi mahasiswa dan status keaktifannya.</p>
        </div>
        <button type="button" class="siom-admin-btn siom-admin-btn-primary" wire:click="create">
            <i class="bi bi-plus-lg"></i> Tambah ORMAWA
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
                <h2>Daftar Organisasi</h2>
                <div class="siom-admin-field" style="width:min(100%, 360px)">
                    <label for="ormawa-search">Cari ORMAWA</label>
                    <input id="ormawa-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Nama atau kode...">
                </div>
            </div>

            <div class="siom-admin-budget-filter">
                <div>
                    <strong>Pengaturan Pagu</strong>
                    <p>Pilih periode untuk melihat dan mengatur pagu tiap ORMAWA.</p>
                </div>
                <div class="siom-admin-field">
                    <label for="ormawa-period">Periode anggaran</label>
                    <select id="ormawa-period" wire:model.live="selectedPeriodId">
                        <option value="">Pilih periode</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}{{ $period->status === 'active' ? ' (Aktif)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @if ($periods->isEmpty())
                <div class="siom-admin-alert siom-admin-alert-error" role="status">
                    Tambahkan periode terlebih dahulu sebelum mengatur pagu ORMAWA.
                </div>
            @endif

            <div class="siom-admin-table-wrap">
                <table class="siom-admin-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kode</th>
                            <th>Nama Organisasi</th>
                            <th>Deskripsi</th>
                            <th>Pagu</th>
                            <th>Terpakai</th>
                            <th>Sisa Pagu</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ormawas as $ormawa)
                            <tr wire:key="ormawa-{{ $ormawa->id }}">
                                <td>{{ $ormawas->firstItem() + $loop->index }}</td>
                                <td><strong>{{ $ormawa->code }}</strong></td>
                                <td>{{ $ormawa->name }}</td>
                                <td>{{ $ormawa->description ?: '-' }}</td>
                                @php($budget = $ormawa->budgets->first())
                                <td>
                                    {{ $budget ? 'Rp '.number_format((float) $budget->budget_amount, 2, ',', '.') : '-' }}
                                </td>
                                <td>
                                    {{ $budget ? 'Rp '.number_format((float) $budget->allocated_amount, 2, ',', '.') : '-' }}
                                </td>
                                <td>
                                    {{ $budget ? 'Rp '.number_format((float) $budget->remaining_amount, 2, ',', '.') : '-' }}
                                </td>
                                <td>
                                    <span class="siom-admin-badge {{ $ormawa->is_active ? 'siom-admin-badge-active' : 'siom-admin-badge-inactive' }}">
                                        {{ $ormawa->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="siom-admin-actions">
                                        <button
                                            type="button"
                                            class="siom-admin-btn siom-admin-btn-sm"
                                            wire:click="editBudget({{ $ormawa->id }})"
                                            @disabled(! $selectedPeriodId)
                                        >
                                            <i class="bi bi-cash-stack"></i> Atur Pagu
                                        </button>
                                        <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="edit({{ $ormawa->id }})">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <button type="button" class="siom-admin-btn siom-admin-btn-danger siom-admin-btn-sm" wire:click="delete({{ $ormawa->id }})" wire:confirm="Hapus ORMAWA ini? Data yang masih terhubung akan mencegah penghapusan.">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="siom-admin-empty">Belum ada data ORMAWA.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($ormawas->hasPages())
                <div class="siom-admin-pagination">{{ $ormawas->links() }}</div>
            @endif

            @if ($showBudgetForm)
                <div class="siom-admin-modal" role="dialog" aria-modal="true" aria-labelledby="ormawa-budget-title">
                    <div class="siom-admin-modal-card siom-admin-modal-card-sm">
                        <div class="siom-admin-modal-head">
                            <div>
                                <h2 id="ormawa-budget-title">Atur Pagu ORMAWA</h2>
                                <p>{{ $ormawas->firstWhere('id', $budgetOrmawaId)?->name }} · {{ $periods->firstWhere('id', $budgetPeriodId)?->name }}</p>
                            </div>
                            <button type="button" class="siom-admin-btn siom-admin-btn-sm" aria-label="Tutup" wire:click="closeBudgetForm">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <form wire:submit="saveBudget">
                            <div class="siom-admin-modal-body">
                                <div class="siom-admin-field">
                                    <label for="ormawa-budget-amount">Nominal Pagu (Rp) *</label>
                                    <input id="ormawa-budget-amount" type="number" min="0" step="0.01" wire:model="budgetAmount" class="@error('budgetAmount') is-invalid @enderror">
                                    @error('budgetAmount') <span class="siom-admin-error">{{ $message }}</span> @enderror
                                    <small class="siom-admin-help">Dana yang sudah disetujui tidak dapat melebihi nilai pagu baru.</small>
                                </div>
                            </div>
                            <div class="siom-admin-modal-foot">
                                <button type="button" class="siom-admin-btn" wire:click="closeBudgetForm">Batal</button>
                                <button type="submit" class="siom-admin-btn siom-admin-btn-primary" wire:loading.attr="disabled">
                                    <i class="bi bi-check-lg"></i> Simpan Pagu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </section>

    @if ($showForm)
        <div class="siom-admin-modal" role="dialog" aria-modal="true" aria-labelledby="ormawa-form-title">
            <div class="siom-admin-modal-card">
                <div class="siom-admin-modal-head">
                    <div>
                        <h2 id="ormawa-form-title">{{ $ormawaId ? 'Edit ORMAWA' : 'Tambah ORMAWA' }}</h2>
                        <p>{{ $ormawaId ? 'Perbarui informasi organisasi.' : 'Masukkan informasi organisasi baru.' }}</p>
                    </div>
                    <button type="button" class="siom-admin-btn siom-admin-btn-sm" aria-label="Tutup" wire:click="resetForm"><i class="bi bi-x-lg"></i></button>
                </div>
                <form wire:submit="save">
                    <div class="siom-admin-modal-body">
                        <div class="siom-admin-grid">
                            <div class="siom-admin-field siom-admin-span-8">
                                <label for="ormawa-name">Nama ORMAWA *</label>
                                <input id="ormawa-name" type="text" wire:model="name" class="@error('name') is-invalid @enderror">
                                @error('name') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="siom-admin-field siom-admin-span-4">
                                <label for="ormawa-code">Kode *</label>
                                <input id="ormawa-code" type="text" wire:model="code" class="@error('code') is-invalid @enderror">
                                @error('code') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="siom-admin-field siom-admin-span-12">
                                <label for="ormawa-description">Deskripsi</label>
                                <textarea id="ormawa-description" wire:model="description"></textarea>
                                @error('description') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <label class="siom-admin-check siom-admin-span-12">
                                <input type="checkbox" wire:model="is_active"> ORMAWA aktif
                            </label>
                        </div>
                    </div>
                    <div class="siom-admin-modal-foot">
                        <button type="button" class="siom-admin-btn" wire:click="resetForm">Batal</button>
                        <button type="submit" class="siom-admin-btn siom-admin-btn-primary" wire:loading.attr="disabled">
                            <i class="bi bi-check-lg"></i> Simpan ORMAWA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
