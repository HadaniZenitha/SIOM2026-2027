<div class="siom-admin-page">
    <header class="siom-admin-heading">
        <div>
            <h1>Program Studi</h1>
            <p>Kelola daftar program studi untuk data pengguna dan anggota.</p>
        </div>
        <button type="button" class="siom-admin-btn siom-admin-btn-primary" wire:click="create">
            <i class="bi bi-plus-lg"></i> Tambah Program Studi
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
                <h2>Daftar Program Studi</h2>
                <div class="siom-admin-field" style="width:min(100%, 360px)">
                    <label for="prodi-search">Cari program studi</label>
                    <input id="prodi-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Nama atau kode...">
                </div>
            </div>
            <div class="siom-admin-table-wrap">
                <table class="siom-admin-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kode</th>
                            <th>Nama Program Studi</th>
                            <th>Fakultas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($programStudis as $prodi)
                            <tr wire:key="program-studi-{{ $prodi->id }}">
                                <td>{{ $programStudis->firstItem() + $loop->index }}</td>
                                <td><strong>{{ $prodi->code }}</strong></td>
                                <td>{{ $prodi->name }}</td>
                                <td>{{ $prodi->fakultas ?: '-' }}</td>
                                <td>
                                    <span class="siom-admin-badge {{ $prodi->is_active ? 'siom-admin-badge-active' : 'siom-admin-badge-inactive' }}">
                                        {{ $prodi->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="siom-admin-actions">
                                        <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="edit({{ $prodi->id }})"><i class="bi bi-pencil"></i> Edit</button>
                                        <button type="button" class="siom-admin-btn siom-admin-btn-danger siom-admin-btn-sm" wire:click="delete({{ $prodi->id }})" wire:confirm="Hapus program studi ini? Program studi yang masih digunakan tidak dapat dihapus."><i class="bi bi-trash"></i> Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="siom-admin-empty">Belum ada program studi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($programStudis->hasPages())
                <div class="siom-admin-pagination">{{ $programStudis->links() }}</div>
            @endif
        </div>
    </section>

    @if ($showForm)
        <div class="siom-admin-modal" role="dialog" aria-modal="true" aria-labelledby="prodi-form-title">
            <div class="siom-admin-modal-card">
                <div class="siom-admin-modal-head">
                    <div><h2 id="prodi-form-title">{{ $programStudiId ? 'Edit Program Studi' : 'Tambah Program Studi' }}</h2><p>Data ini digunakan pada profil pengguna dan anggota.</p></div>
                    <button type="button" class="siom-admin-btn siom-admin-btn-sm" aria-label="Tutup" wire:click="resetForm"><i class="bi bi-x-lg"></i></button>
                </div>
                <form wire:submit="save">
                    <div class="siom-admin-modal-body">
                        <div class="siom-admin-grid">
                            <div class="siom-admin-field siom-admin-span-4">
                                <label for="prodi-code">Kode *</label>
                                <input id="prodi-code" type="text" wire:model="code" class="@error('code') is-invalid @enderror">
                                @error('code') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="siom-admin-field siom-admin-span-8">
                                <label for="prodi-name">Nama Program Studi *</label>
                                <input id="prodi-name" type="text" wire:model="name" class="@error('name') is-invalid @enderror">
                                @error('name') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="siom-admin-field siom-admin-span-8">
                                <label for="prodi-fakultas">Fakultas</label>
                                <input id="prodi-fakultas" type="text" wire:model="fakultas" class="@error('fakultas') is-invalid @enderror">
                                @error('fakultas') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <label class="siom-admin-check siom-admin-span-4">
                                <input type="checkbox" wire:model="is_active"> Program studi aktif
                            </label>
                        </div>
                    </div>
                    <div class="siom-admin-modal-foot">
                        <button type="button" class="siom-admin-btn" wire:click="resetForm">Batal</button>
                        <button type="submit" class="siom-admin-btn siom-admin-btn-primary" wire:loading.attr="disabled"><i class="bi bi-check-lg"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
