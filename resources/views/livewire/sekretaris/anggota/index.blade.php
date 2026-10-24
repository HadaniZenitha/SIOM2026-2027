<div class="siom-admin-page">
    {{-- Header --}}
    <div class="siom-admin-heading">
        <div>
            <h1>Anggota ORMAWA</h1>
            <p>Kelola keanggotaan untuk periode aktif.</p>
        </div>
        <button type="button" class="siom-admin-btn siom-admin-btn-primary" wire:click="create">
            <i class="bi bi-person-plus me-1"></i> Tambah Anggota
        </button>
    </div>

    {{-- Alert Success --}}
    @if (session('success'))
        <div class="siom-admin-alert siom-admin-alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Main Card --}}
    <div class="siom-admin-card">
        <div class="siom-admin-card-body">

            {{-- Import / Export (layout diperbaiki) --}}
            <div class="rounded-3 p-3 mb-4" style="background: #f0f7ff; border: 1.5px solid #93c5fd;">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-lg-5">
                        <h2 class="h6 fw-semibold mb-1">Impor & Ekspor Excel</h2>
                        <p class="text-muted small mb-0">
                            Impor anggota ke periode aktif. Gunakan format tanggal <code>YYYY-MM-DD</code>.
                            Program studi dan divisi dicocokkan berdasarkan nama. Isi jabatan; untuk wakil,
                            masukkan nomor urut agar tampil sebagai I, II, dan seterusnya.
                            Divisi yang tersedia: {{ $divisions->pluck('name')->join(', ') ?: 'belum ada' }}.
                            Jabatan untuk ORMAWA ini: {{ implode(', ', array_values($positionOptions)) }}.
                        </p>
                    </div>

                    <div class="col-12 col-lg-7">
                        <form class="row g-2 align-items-end" wire:submit="importExcel">
                            <div class="col-12 col-md siom-admin-field mb-0">
                                <label for="member-excel">File Excel anggota (.xlsx)</label>
                                <input
                                    type="file"
                                    id="member-excel"
                                    accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                    wire:model="importFile"
                                >
                                @error('importFile')
                                    <span class="siom-admin-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-auto">
                                <button
                                    type="submit"
                                    class="siom-admin-btn siom-admin-btn-primary w-100"
                                    wire:loading.attr="disabled"
                                    wire:target="importExcel,importFile"
                                >
                                    <span wire:loading.remove wire:target="importExcel">
                                        <i class="bi bi-upload me-1"></i> Impor Excel
                                    </span>
                                    <span wire:loading wire:target="importExcel,importFile">
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        Memproses...
                                    </span>
                                </button>
                            </div>
                        </form>

                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="downloadImportTemplate">
                                <i class="bi bi-download me-1"></i> Unduh Template
                            </button>
                            <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="exportExcel">
                                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Ekspor Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="h6 fw-semibold mb-1">Divisi ORMAWA</h2>
                    <p class="text-muted small mb-0">Kelola daftar divisi yang tersedia untuk anggota ORMAWA ini.</p>
                </div>
                <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="openDivisionManager">
                    <i class="bi bi-diagram-3 me-1"></i> Kelola Divisi
                </button>
            </div>

            {{-- Search --}}
            <div class="siom-admin-toolbar mb-3">
                <h2 class="h6 fw-semibold mb-0">Daftar Anggota</h2>
                <div class="siom-admin-field mb-0" style="width: min(100%, 280px);">
                    <input
                        type="search"
                        placeholder="Cari nama atau NIM..."
                        wire:model.live.debounce.300ms="search"
                    >
                </div>
            </div>

            {{-- Table --}}
            <div class="siom-admin-table-wrap">
                <table class="siom-admin-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No.</th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Program Studi</th>
                            <th>Divisi &amp; Jabatan</th>
                            <th>Status</th>
                            <th style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $member)
                            <tr wire:key="member-{{ $member->id }}">
                                <td class="text-muted">
                                    {{ $loop->iteration + ($members->currentPage() - 1) * $members->perPage() }}
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $member->name }}</div>
                                </td>
                                <td>{{ $member->nim }}</td>
                                <td>{{ $member->programStudi?->name ?? '-' }}</td>
                                <td>
                                    <div>{{ $member->division?->name ?? '-' }}</div>
                                    <small class="text-muted">
                                        {{ $ormawa->formatMemberPosition($member->position, $member->position_number) }}
                                    </small>
                                </td>
                                <td>
                                    @if ($member->status === 'active')
                                        <span class="siom-admin-badge siom-admin-badge-active">Aktif</span>
                                    @else
                                        <span class="siom-admin-badge siom-admin-badge-inactive">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="siom-admin-actions">
                                        <button
                                            type="button"
                                            class="siom-admin-btn siom-admin-btn-sm"
                                            wire:click="edit({{ $member->id }})"
                                        >
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <button
                                            type="button"
                                            class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-danger"
                                            wire:click="delete({{ $member->id }})"
                                            wire:confirm="Hapus anggota ini?"
                                        >
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="siom-admin-empty">
                                    Belum ada anggota pada periode aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($members->hasPages())
                <div class="siom-admin-pagination mt-3">
                    {{ $members->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal --}}
    @if ($showForm)
        <div
            class="siom-admin-modal"
            wire:click.self="resetForm"
            @keydown.escape.window="$wire.resetForm()"
        >
            <div class="siom-admin-modal-card" @click.stop>
                <form wire:submit="save">
                    <div class="siom-admin-modal-head">
                        <div>
                            <h2 class="h5 fw-semibold mb-1">
                                {{ $memberId ? 'Edit Anggota' : 'Tambah Anggota' }}
                            </h2>
                            <p class="text-muted small mb-0">
                                Lengkapi informasi anggota untuk periode aktif.
                            </p>
                        </div>
                        <button
                            type="button"
                            class="siom-admin-btn siom-admin-btn-sm"
                            wire:click="resetForm"
                            aria-label="Tutup"
                        >
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <div class="siom-admin-modal-body">
                        <div class="siom-admin-grid">
                            <div class="siom-admin-span-6 siom-admin-field">
                                <label for="member-name">Nama <span class="text-danger">*</span></label>
                                <input type="text" id="member-name" wire:model="name" autocomplete="name" autofocus>
                                @error('name') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="siom-admin-span-6 siom-admin-field">
                                <label for="member-nim">NIM <span class="text-danger">*</span></label>
                                <input type="text" id="member-nim" wire:model="nim" inputmode="numeric">
                                @error('nim') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="siom-admin-span-6 siom-admin-field">
                                <label for="member-study">Program Studi <span class="text-danger">*</span></label>
                                <select id="member-study" wire:model="program_studi_id">
                                    <option value="">Pilih Program Studi</option>
                                    @foreach ($programStudis as $prodi)
                                        <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                                    @endforeach
                                </select>
                                @error('program_studi_id') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="siom-admin-span-6 siom-admin-field">
                                <label for="member-division">Divisi / Komite</label>
                                <select id="member-division" wire:model="organization_division_id">
                                    <option value="">Tanpa divisi</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                                @error('organization_division_id') <span class="siom-admin-error">{{ $message }}</span> @enderror
                                @if ($divisions->isEmpty())
                                    <small class="text-muted">Belum ada divisi. Tambahkan melalui tombol Kelola Divisi.</small>
                                @endif
                            </div>

                            <div class="siom-admin-span-6 siom-admin-field">
                                <label for="member-position">Jabatan <span class="text-danger">*</span></label>
                                <select id="member-position" wire:model.live="position">
                                    <option value="">Pilih Jabatan</option>
                                    @foreach ($positionOptions as $positionValue => $positionLabel)
                                        <option value="{{ $positionValue }}">{{ $positionLabel }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    Jabatan pimpinan menyesuaikan ORMAWA: {{ $ormawa->leadershipTitle() }}.
                                </small>
                                @error('position') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>

                            @if ($position === $ormawa->deputyLeadershipPosition())
                                <div class="siom-admin-span-6 siom-admin-field">
                                    <label for="member-position-number">Nomor Wakil <span class="text-danger">*</span></label>
                                    <input
                                        type="number"
                                        id="member-position-number"
                                        wire:model="position_number"
                                        min="1"
                                        max="3999"
                                        step="1"
                                        placeholder="Contoh: 1"
                                    >
                                    <small class="text-muted">Akan ditampilkan sebagai {{ $ormawa->deputyLeadershipTitle() }} I, II, dan seterusnya.</small>
                                    @error('position_number') <span class="siom-admin-error">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <div class="siom-admin-span-6 siom-admin-field">
                                <label for="member-phone">Telepon</label>
                                <input type="tel" id="member-phone" wire:model="phone" autocomplete="tel">
                                @error('phone') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="siom-admin-span-6 siom-admin-field">
                                <label for="member-birth-place">Tempat Lahir</label>
                                <input type="text" id="member-birth-place" wire:model="birth_place">
                                @error('birth_place') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="siom-admin-span-6 siom-admin-field">
                                <label for="member-birth-date">Tanggal Lahir</label>
                                <input type="date" id="member-birth-date" wire:model="birth_date">
                                @error('birth_date') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="siom-admin-span-12">
                                <label class="siom-admin-check">
                                    <input type="checkbox" wire:model="status" value="active" @checked($status === 'active')>
                                    Anggota aktif
                                </label>
                                @error('status') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="siom-admin-modal-foot">
                        <button type="button" class="siom-admin-btn" wire:click="resetForm">
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="siom-admin-btn siom-admin-btn-primary"
                            wire:loading.attr="disabled"
                            wire:target="save"
                        >
                            <span wire:loading.remove wire:target="save">
                                <i class="bi bi-check-lg me-1"></i>
                                {{ $memberId ? 'Update Anggota' : 'Simpan Anggota' }}
                            </span>
                            <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showDivisionManager)
        <div
            class="siom-admin-modal"
            wire:click.self="closeDivisionManager"
            @keydown.escape.window="$wire.closeDivisionManager()"
        >
            <div class="siom-admin-modal-card" @click.stop>
                <div class="siom-admin-modal-head">
                    <div>
                        <h2 class="h5 fw-semibold mb-1">Kelola Divisi ORMAWA</h2>
                        <p class="text-muted small mb-0">Daftar ini hanya tersedia untuk ORMAWA Anda.</p>
                    </div>
                    <button
                        type="button"
                        class="siom-admin-btn siom-admin-btn-sm"
                        wire:click="closeDivisionManager"
                        aria-label="Tutup"
                    >
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="siom-admin-modal-body">
                    <form wire:submit="saveDivision" class="mb-4">
                        <div class="siom-admin-grid">
                            <div class="siom-admin-span-12 siom-admin-field">
                                <label for="division-name">Nama Divisi <span class="text-danger">*</span></label>
                                <input
                                    id="division-name"
                                    type="text"
                                    wire:model="divisionName"
                                    placeholder="Contoh: Kominfo"
                                >
                                @error('divisionName') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="siom-admin-span-12 d-flex gap-2">
                                <button type="submit" class="siom-admin-btn siom-admin-btn-primary">
                                    {{ $divisionId ? 'Simpan Perubahan Divisi' : 'Tambah Divisi' }}
                                </button>
                                @if ($divisionId)
                                    <button type="button" class="siom-admin-btn" wire:click="resetDivisionForm">
                                        Batal Edit
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>

                    <div class="siom-admin-table-wrap">
                        <table class="siom-admin-table">
                            <thead>
                                <tr>
                                    <th>Nama Divisi</th>
                                    <th style="width: 180px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($divisions as $division)
                                    <tr wire:key="division-{{ $division->id }}">
                                        <td>{{ $division->name }}</td>
                                        <td>
                                            <div class="siom-admin-actions">
                                                <button
                                                    type="button"
                                                    class="siom-admin-btn siom-admin-btn-sm"
                                                    wire:click="editDivision({{ $division->id }})"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    type="button"
                                                    class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-danger"
                                                    wire:click="deleteDivision({{ $division->id }})"
                                                    wire:confirm="Hapus divisi ini?"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="siom-admin-empty">Belum ada divisi untuk ORMAWA ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>