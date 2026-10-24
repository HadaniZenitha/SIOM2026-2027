<div class="siom-admin-page siom-lpj-form">
    <style>
        .siom-lpj-form .siom-lpj-back {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            margin-bottom: .6rem;
            color: #1d4ed8;
            font-size: .85rem;
            font-weight: 700;
            text-decoration: none;
        }
        .siom-lpj-form .siom-admin-card { width: 100%; }
        .siom-lpj-form .siom-lpj-total {
            text-align: right;
        }
        .siom-lpj-form .siom-lpj-total strong {
            display: block;
            color: #166534;
            font-family: Fredoka, 'Plus Jakarta Sans', sans-serif;
            font-size: 1.6rem;
        }
        .siom-lpj-form .siom-lpj-summary {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: .75rem 1rem;
            margin-top: .85rem;
            padding: .9rem 1rem;
            border: 1.5px solid #93c5fd;
            border-radius: 14px;
            background: #f0f7ff;
        }
        .siom-lpj-form .siom-lpj-summary span {
            display: block;
            color: #475569;
            font-size: .75rem;
            font-weight: 700;
        }
        .siom-lpj-form .siom-budget-sheet { width: 100%; overflow-x: auto; }
        .siom-lpj-form .siom-budget-table {
            width: 100%;
            min-width: 820px;
            border-collapse: collapse;
        }
        .siom-lpj-form .siom-budget-table th,
        .siom-lpj-form .siom-budget-table td {
            padding: .65rem .5rem;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
            text-align: left;
        }
        .siom-lpj-form .siom-budget-table th {
            background: #e0f2fe;
            font-family: Fredoka, 'Plus Jakarta Sans', sans-serif;
            font-size: .82rem;
        }
        .siom-lpj-form .siom-budget-table tfoot th { background: #fff; }
        .siom-lpj-form .siom-doc-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .75rem 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .siom-lpj-form .siom-admin-actions {
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .6rem;
            margin-top: .25rem;
        }
        .siom-lpj-form .siom-lpj-save {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: .6rem;
        }
        @media (max-width: 760px) {
            .siom-lpj-form .siom-admin-heading,
            .siom-lpj-form .siom-lpj-total { text-align: left; }
            .siom-lpj-form .siom-lpj-summary { grid-template-columns: 1fr; }
            .siom-lpj-form .siom-doc-row,
            .siom-lpj-form .siom-admin-actions,
            .siom-lpj-form .siom-lpj-save { align-items: stretch; flex-direction: column; }
            .siom-lpj-form .siom-admin-btn { width: 100%; }
        }
    </style>

    <a class="siom-lpj-back" href="{{ route('sekretaris.lpj') }}">
        <i class="bi bi-arrow-left"></i> Kembali ke daftar LPJ
    </a>

    <div class="siom-admin-heading">
        <div>
            <h1>{{ $lpjId ? 'Edit LPJ' : 'Buat LPJ' }}</h1>
            <p>Data kegiatan diambil dari proposal yang telah disetujui.</p>
        </div>
        <div class="siom-lpj-total">
            <span class="small text-muted">Total anggaran LPJ</span>
            <strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
        </div>
    </div>

    @if (session('success'))
        <div class="siom-admin-alert siom-admin-alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->has('document'))
        <div class="siom-admin-alert siom-admin-alert-error">{{ $errors->first('document') }}</div>
    @endif

    <form wire:submit="save">
        <section class="siom-admin-card">
            <div class="siom-admin-card-body">
                <h2 class="h5 mb-3">Proposal kegiatan</h2>
                <div class="siom-admin-field">
                    <label for="lpj-submission">Pilih proposal yang disetujui</label>
                    <select id="lpj-submission" wire:model.live="submission_id">
                        <option value="">Pilih proposal</option>
                        @foreach ($submissions as $submission)
                            <option value="{{ $submission->id }}">{{ $submission->submission_number }} — {{ $submission->activity_name }}</option>
                        @endforeach
                    </select>
                    @error('submission_id') <span class="siom-admin-error">{{ $message }}</span> @enderror
                </div>

                @if ($submission_id)
                    @php($selectedSubmission = $submissions->firstWhere('id', $submission_id))
                    @if ($selectedSubmission)
                        <div class="siom-lpj-summary">
                            <div>
                                <span>Kegiatan</span>
                                <strong>{{ $selectedSubmission->activity_name }}</strong>
                            </div>
                            <div>
                                <span>Periode</span>
                                <strong>{{ $selectedSubmission->period?->name ?? '-' }}</strong>
                            </div>
                            <div>
                                <span>Tanggal</span>
                                <strong>{{ $selectedSubmission->event_date?->format('d/m/Y') ?? '-' }}</strong>
                            </div>
                            <div style="grid-column: 1 / -1;">
                                <span>Lokasi</span>
                                {{ $selectedSubmission->event_location ?? '-' }}
                            </div>
                        </div>
                    @endif
                @else
                    <p class="siom-admin-help">Pilih proposal untuk menyalin detail kegiatan dan daftar anggarannya.</p>
                @endif

                <div class="siom-admin-grid mt-3">
                    <div class="siom-admin-span-12 siom-admin-field">
                        <label for="lpj-title">Judul laporan</label>
                        <input id="lpj-title" type="text" wire:model="title" placeholder="Judul LPJ dari kegiatan">
                        @error('title') <span class="siom-admin-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="siom-admin-span-12 siom-admin-field">
                        <label for="lpj-description">Ringkasan pelaksanaan kegiatan</label>
                        <textarea id="lpj-description" rows="4" wire:model="description" placeholder="Ceritakan pelaksanaan dan hasil kegiatan"></textarea>
                        @error('description') <span class="siom-admin-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </section>

        <section class="siom-admin-card">
            <div class="siom-admin-card-body">
                <div class="siom-admin-heading" style="margin-bottom: 1rem;">
                    <div>
                        <h2 class="h5 mb-1">Anggaran kegiatan</h2>
                        <p>Sesuaikan item anggaran proposal. Total diperbarui otomatis. Geser ke samping pada layar kecil.</p>
                    </div>
                    <button class="siom-admin-btn siom-admin-btn-sm" type="button" wire:click="addBudgetItem">
                        <i class="bi bi-plus-lg"></i> Tambah item
                    </button>
                </div>
                @error('budgetItems') <span class="siom-admin-error">{{ $message }}</span> @enderror

                <div class="siom-budget-sheet">
                    <table class="siom-budget-table">
                        <thead>
                            <tr>
                                <th>Nama item</th>
                                <th style="width: 110px;">Jumlah</th>
                                <th style="width: 120px;">Satuan</th>
                                <th style="width: 170px;">Harga satuan</th>
                                <th style="width: 160px;">Subtotal</th>
                                <th style="width: 72px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($budgetItems as $index => $item)
                                <tr wire:key="lpj-budget-{{ $index }}">
                                    <td>
                                        <div class="siom-admin-field mb-0">
                                            <input aria-label="Nama item {{ $index + 1 }}" wire:model="budgetItems.{{ $index }}.item_name">
                                            @error("budgetItems.{$index}.item_name") <span class="siom-admin-error">{{ $message }}</span> @enderror
                                        </div>
                                    </td>
                                    <td>
                                        <div class="siom-admin-field mb-0">
                                            <input type="number" min="0.01" step="0.01" aria-label="Jumlah {{ $index + 1 }}" wire:model.live="budgetItems.{{ $index }}.qty">
                                            @error("budgetItems.{$index}.qty") <span class="siom-admin-error">{{ $message }}</span> @enderror
                                        </div>
                                    </td>
                                    <td>
                                        <div class="siom-admin-field mb-0">
                                            <input aria-label="Satuan {{ $index + 1 }}" wire:model="budgetItems.{{ $index }}.unit">
                                            @error("budgetItems.{$index}.unit") <span class="siom-admin-error">{{ $message }}</span> @enderror
                                        </div>
                                    </td>
                                    <td>
                                        <div class="siom-admin-field mb-0">
                                            <input type="number" min="0" step="0.01" aria-label="Harga satuan {{ $index + 1 }}" wire:model.live="budgetItems.{{ $index }}.price">
                                            @error("budgetItems.{$index}.price") <span class="siom-admin-error">{{ $message }}</span> @enderror
                                        </div>
                                    </td>
                                    <td>Rp {{ number_format((float) ($item['qty'] ?? 0) * (float) ($item['price'] ?? 0), 0, ',', '.') }}</td>
                                    <td>
                                        <button class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-danger" type="button" aria-label="Hapus item {{ $index + 1 }}" wire:click="removeBudgetItem({{ $index }})" @disabled(count($budgetItems) <= 1)>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="siom-admin-empty">Pilih proposal untuk memuat item anggaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($budgetItems !== [])
                            <tfoot>
                                <tr>
                                    <th colspan="4" style="text-align: right;">Total</th>
                                    <th>Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </section>

        <div class="siom-admin-grid">
            <section class="siom-admin-card siom-admin-span-6">
                <div class="siom-admin-card-body">
                    <h2 class="h5 mb-1">Scan nota</h2>
                    <p class="siom-admin-help">PDF atau gambar, maksimal 10 MB per file.</p>
                    <div class="siom-admin-field">
                        <label for="lpj-receipts">File nota</label>
                        <input id="lpj-receipts" type="file" accept=".pdf,.jpg,.jpeg,.png" multiple wire:model="receiptFiles">
                        @error('receiptFiles') <span class="siom-admin-error">{{ $message }}</span> @enderror
                        @foreach ($errors->get('receiptFiles.*') as $messages)
                            @foreach ($messages as $message)
                                <span class="siom-admin-error">{{ $message }}</span>
                            @endforeach
                        @endforeach
                    </div>
                    @if ($receiptFiles !== [])
                        <ul class="small mb-0">
                            @foreach ($receiptFiles as $file)
                                <li>{{ $file->getClientOriginalName() }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>

            <section class="siom-admin-card siom-admin-span-6">
                <div class="siom-admin-card-body">
                    <h2 class="h5 mb-1">Dokumentasi kegiatan</h2>
                    <p class="siom-admin-help">Foto atau PDF, maksimal 10 MB per file.</p>
                    <div class="siom-admin-field">
                        <label for="lpj-activity-files">File dokumentasi</label>
                        <input id="lpj-activity-files" type="file" accept=".pdf,.jpg,.jpeg,.png" multiple wire:model="activityFiles">
                        @error('activityFiles') <span class="siom-admin-error">{{ $message }}</span> @enderror
                        @foreach ($errors->get('activityFiles.*') as $messages)
                            @foreach ($messages as $message)
                                <span class="siom-admin-error">{{ $message }}</span>
                            @endforeach
                        @endforeach
                    </div>
                    @if ($activityFiles !== [])
                        <ul class="small mb-0">
                            @foreach ($activityFiles as $file)
                                <li>{{ $file->getClientOriginalName() }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>
        </div>

        @if ($documents->isNotEmpty())
            <section class="siom-admin-card">
                <div class="siom-admin-card-body">
                    <h2 class="h5 mb-2">Dokumen tersimpan</h2>
                    @foreach ($documents as $document)
                        <div class="siom-doc-row" wire:key="lpj-document-{{ $document->id }}">
                            <div>
                                <span class="siom-admin-badge siom-admin-badge-finished">
                                    {{ match ($document->type) { 'nota', 'keuangan' => 'Nota', 'dokumentasi' => 'Dokumentasi', default => 'Laporan' } }}
                                </span>
                                <span>{{ $document->file_name }}</span>
                            </div>
                            <div class="siom-admin-actions mb-0" style="margin-top: 0;">
                                <button class="siom-admin-btn siom-admin-btn-sm" type="button" wire:click="downloadDocument({{ $document->id }})">Unduh</button>
                                <button class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-danger" type="button" wire:click="removeDocument({{ $document->id }})" wire:confirm="Hapus dokumen ini?">Hapus</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <div class="siom-admin-actions">
            <a class="siom-admin-btn" href="{{ route('sekretaris.lpj') }}">Batal</a>
            <div class="siom-lpj-save">
                @if ($lpjId)
                    <button class="siom-admin-btn" type="button" wire:click="downloadWord" wire:loading.attr="disabled" wire:target="downloadWord">
                        <i class="bi bi-file-earmark-word"></i> Unduh Word LPJ
                    </button>
                @endif
                <button class="siom-admin-btn siom-admin-btn-primary" type="submit" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Simpan Draft</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </form>
</div>