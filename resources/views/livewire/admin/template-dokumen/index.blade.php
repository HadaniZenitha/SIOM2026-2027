<div class="siom-admin-page">
    <div class="siom-admin-heading">
        <div>
            <h1>Master Template Dokumen</h1>
            <p>Kelola file master (.docx) Proposal (termasuk Nota Dinas) dan LPJ ORMAWA.</p>
        </div>
        <button type="button" class="siom-admin-btn siom-admin-btn-primary" wire:click="$toggle('showForm')">
            <i class="bi bi-plus-lg"></i>
            {{ $showForm ? 'Tutup Form' : 'Unggah Template Baru' }}
        </button>
    </div>

    @if (session('success'))
        <div class="siom-admin-alert siom-admin-alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="siom-admin-alert siom-admin-alert-error">{{ session('error') }}</div>
    @endif

    @if ($showForm)
        <div class="siom-admin-card">
            <div class="siom-admin-card-body">
                <h2 class="h5 mb-3">Form Unggah Master Template (.docx)</h2>
                <form wire:submit="save">
                    <div class="siom-admin-grid">
                        <div class="siom-admin-span-6 siom-admin-field">
                            <label for="template-name">Nama Template</label>
                            <input id="template-name" type="text" wire:model="name" placeholder="Contoh: Template Proposal 2026/2027">
                            @error('name') <span class="siom-admin-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="siom-admin-span-6 siom-admin-field">
                            <label for="template-type">Jenis Dokumen</label>
                            <select id="template-type" wire:model="type">
                                <option value="proposal">Proposal Kegiatan</option>
                                <option value="lpj">Laporan Pertanggungjawaban (LPJ)</option>
                            </select>
                            @error('type') <span class="siom-admin-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="siom-admin-span-6 siom-admin-field">
                            <label for="template-period">Periode Akademik (Opsional)</label>
                            <select id="template-period" wire:model="period_id">
                                <option value="">Berlaku untuk Semua Periode</option>
                                @foreach ($periods as $period)
                                    <option value="{{ $period->id }}">{{ $period->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="siom-admin-span-6 siom-admin-field">
                            <label for="template-ormawa-category">Kategori Ormawa</label>
                            <select id="template-ormawa-category" wire:model="ormawa_category">
                                <option value="">Semua Jenis Ormawa (HIMA, UKM, BEM, DPM)</option>
                                <option value="hima">HIMA & UKM (pakai kolom Kaprodi)</option>
                                <option value="bem">BEM & DPM (tanpa kolom Kaprodi)</option>
                            </select>
                            <span class="siom-admin-help">Pilih sesuai format dokumen yang berbeda placeholder tanda tangannya.</span>
                            @error('ormawa_category') <span class="siom-admin-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="siom-admin-span-6 siom-admin-field">
                            <label for="template-file">File Template (.docx)</label>
                            <input id="template-file" type="file" wire:model="file" accept=".docx">
                            <span class="siom-admin-help">File MS Word (.docx) yang sudah berisi tag placeholder.</span>
                            <span class="siom-admin-help">
                                Placeholder HIMA/UKM: <code>${signature_pembina_ormawa}</code>,
                                <code>${signature_kaprodi}</code>, <code>${signature_kabag_kemahasiswaan}</code>,
                                <code>${signature_warek}</code>.<br>
                                Placeholder BEM/DPM: <code>${signature_presma}</code>,
                                <code>${signature_kabag_kemahasiswaan}</code>, <code>${signature_warek}</code>.
                            </span>
                            @error('file') <span class="siom-admin-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="siom-admin-actions" style="justify-content: flex-end; margin-top: 1rem;">
                        <button type="button" class="siom-admin-btn" wire:click="$set('showForm', false)">Batal</button>
                        <button type="submit" class="siom-admin-btn siom-admin-btn-primary" wire:loading.attr="disabled" wire:target="save,file">
                            <span wire:loading.remove wire:target="save">Simpan Template</span>
                            <span wire:loading wire:target="save">Mengunggah...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="siom-admin-card">
        <div class="siom-admin-table-wrap">
            <table class="siom-admin-table">
                <thead>
                    <tr>
                        <th style="width: 56px;">No</th>
                        <th>Nama Template</th>
                        <th>Jenis</th>
                        <th>Kategori Ormawa</th>
                        <th>Periode</th>
                        <th>Versi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($templates as $template)
                        <tr wire:key="template-{{ $template->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $template->name }}</strong>
                                <span class="siom-admin-help">{{ $template->file_name }}</span>
                            </td>
                            <td>
                                @if ($template->type === 'proposal')
                                    <span class="siom-admin-badge siom-admin-badge-finished">Proposal</span>
                                @else
                                    <span class="siom-admin-badge siom-admin-badge-draft">{{ ucwords(str_replace('_', ' ', $template->type)) }}</span>
                                @endif
                            </td>
                            <td>
                                <select wire:change="updateCategory({{ $template->id }}, $event.target.value)"
                                    class="siom-admin-field-select"
                                    style="padding: 4px 8px; font-size: 0.825rem; font-weight: 600; border-radius: 6px; border: 1px solid #CBD5E1; background: #F8FAFC; color: #1E293B; cursor: pointer;">
                                    <option value="" @selected(!$template->ormawa_category)>Semua Ormawa</option>
                                    <option value="hima" @selected($template->ormawa_category === 'hima')>HIMA & UKM</option>
                                    <option value="bem" @selected($template->ormawa_category === 'bem')>BEM & DPM</option>
                                </select>
                            </td>
                            <td>{{ $template->period?->name ?? 'Semua Periode' }}</td>
                            <td>v{{ $template->version }}</td>
                            <td>
                                <button type="button"
                                    class="siom-admin-btn siom-admin-btn-sm"
                                    style="{{ $template->is_active ? 'background:#10B981;color:#fff;border-color:#059669;' : 'background:#F1F5F9;color:#64748B;' }}"
                                    wire:click="toggleStatus({{ $template->id }})">
                                    {{ $template->is_active ? '✓ Aktif' : '✗ Nonaktif' }}
                                </button>
                            </td>
                            <td>
                                <div class="siom-admin-actions">
                                    <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="download({{ $template->id }})" title="Unduh File Master">
                                        <i class="bi bi-download"></i> Unduh
                                    </button>
                                    <button type="button" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-danger" wire:click="delete({{ $template->id }})" wire:confirm="Yakin ingin menghapus template ini?" title="Hapus Template">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="siom-admin-empty">Belum ada template dokumen yang diunggah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>