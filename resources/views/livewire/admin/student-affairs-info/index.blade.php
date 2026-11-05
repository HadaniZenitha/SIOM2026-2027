<div class="siom-admin-page">
    <header class="siom-admin-heading">
        <div>
            <h1>Informasi Kemahasiswaan</h1>
            <p>Kelola surat edaran, surat keputusan, pengumuman, dan dokumen resmi yang ditampilkan di landing page.</p>
        </div>
        <button type="button" class="siom-admin-btn siom-admin-btn-primary" wire:click="create">
            <i class="bi bi-plus-lg"></i> Tambah Informasi
        </button>
    </header>

    @if (session('success'))
        <div class="siom-admin-alert siom-admin-alert-success" role="status">{{ session('success') }}</div>
    @endif

    <section class="siom-admin-card">
        <div class="siom-admin-table-wrap">
            <table class="siom-admin-table">
                <thead>
                    <tr>
                        <th>Jenis Informasi</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Dokumen</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($informations as $information)
                        <tr wire:key="student-affairs-info-{{ $information->id }}">
                            <td>{{ $information->categoryLabel() }}</td>
                            <td>
                                <strong>{{ $information->title }}</strong>
                                <span class="siom-admin-help">{{ \Illuminate\Support\Str::limit($information->summary, 100) }}</span>
                            </td>
                            <td>{{ $information->published_at?->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                @if ($information->document_path)
                                    <a href="{{ route('student-affairs-information.document', $information) }}" target="_blank" rel="noopener noreferrer">
                                        {{ $information->document_name ?: 'Lihat dokumen' }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="siom-admin-badge {{ $information->is_published ? 'siom-admin-badge-active' : 'siom-admin-badge-inactive' }}">
                                    {{ $information->is_published ? 'Ditampilkan' : 'Disembunyikan' }}
                                </span>
                            </td>
                            <td>
                                <div class="siom-admin-actions">
                                    <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="edit({{ $information->id }})">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="togglePublished({{ $information->id }})">
                                        <i class="bi bi-eye{{ $information->is_published ? '-slash' : '' }}"></i>
                                        {{ $information->is_published ? 'Sembunyikan' : 'Tampilkan' }}
                                    </button>
                                    <button type="button" class="siom-admin-btn siom-admin-btn-danger siom-admin-btn-sm" wire:click="delete({{ $information->id }})" wire:confirm="Hapus informasi ini beserta dokumennya?">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="siom-admin-empty">Belum ada informasi kemahasiswaan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($showForm)
        <div class="siom-admin-modal" role="dialog" aria-modal="true" aria-labelledby="student-affairs-form-title">
            <div class="siom-admin-modal-card">
                <div class="siom-admin-modal-head">
                    <div>
                        <h2 id="student-affairs-form-title">{{ $informationId ? 'Edit Informasi' : 'Tambah Informasi' }}</h2>
                        <p>Dokumen PDF atau DOC/DOCX dapat diunggah hingga ukuran 10 MB.</p>
                    </div>
                    <button type="button" class="siom-admin-btn siom-admin-btn-sm" aria-label="Tutup" wire:click="resetForm">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <form wire:submit="save">
                    <div class="siom-admin-modal-body">
                        <div class="siom-admin-grid">
                            <div class="siom-admin-field siom-admin-span-6">
                                <label for="student-affairs-category">Jenis informasi *</label>
                                <select id="student-affairs-category" wire:model="category">
                                    @foreach ($categories as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="siom-admin-field siom-admin-span-6">
                                <label for="student-affairs-date">Tanggal terbit</label>
                                <input id="student-affairs-date" type="date" wire:model="publishedAt" class="@error('publishedAt') is-invalid @enderror">
                                @error('publishedAt') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="siom-admin-field siom-admin-span-12">
                                <label for="student-affairs-title">Judul *</label>
                                <input id="student-affairs-title" type="text" wire:model="title" maxlength="200" class="@error('title') is-invalid @enderror">
                                @error('title') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="siom-admin-field siom-admin-span-12">
                                <label for="student-affairs-summary">Ringkasan *</label>
                                <textarea id="student-affairs-summary" rows="4" wire:model="summary" maxlength="5000" class="@error('summary') is-invalid @enderror"></textarea>
                                @error('summary') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="siom-admin-field siom-admin-span-12">
                                <label for="student-affairs-document">Dokumen (PDF, DOC, DOCX; maksimal 10 MB)</label>
                                <input id="student-affairs-document" type="file" wire:model="document" accept=".pdf,.doc,.docx">
                                @if ($informationId && ! $document)
                                    <span class="siom-admin-help">Dokumen saat ini akan dipertahankan jika tidak memilih file pengganti.</span>
                                @endif
                                @error('document') <span class="siom-admin-error">{{ $message }}</span> @enderror
                            </div>
                            <label class="siom-admin-check siom-admin-span-12">
                                <input type="checkbox" wire:model="isPublished"> Publikasikan di landing page
                            </label>
                        </div>
                    </div>
                    <div class="siom-admin-modal-foot">
                        <button type="button" class="siom-admin-btn" wire:click="resetForm">Batal</button>
                        <button type="submit" class="siom-admin-btn siom-admin-btn-primary" wire:loading.attr="disabled" wire:target="save,document">
                            <span wire:loading.remove wire:target="save">Simpan Informasi</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
