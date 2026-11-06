@extends('layouts.app')

@section('content')
<div class="siom-doc-fullscreen-page">
    <header class="siom-doc-topbar">
        <div class="siom-doc-topbar-left">
            <a href="javascript:history.back()" class="siom-admin-btn siom-admin-btn-sm" title="Kembali">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <div class="siom-doc-topbar-info">
                <div class="d-flex align-items-center gap-2">
                    <span class="siom-doc-badge">{{ $documentType }}</span>
                    <span class="siom-status-pill is-{{ $status }}">
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </span>
                </div>
                <h1 class="siom-doc-page-title">{{ $title }}</h1>
            </div>
        </div>

        <div class="siom-doc-topbar-right">
            <!-- Tab Switcher -->
            <div class="siom-doc-tab-switcher">
                <button type="button" class="siom-tab-btn active" id="btn-tab-docx" onclick="switchPreviewTab('docx')">
                    @if (($format ?? '') === 'pdf')
                        <i class="bi bi-file-earmark-pdf text-danger"></i> Dokumen PDF (Visual)
                    @elseif (($format ?? '') === 'image')
                        <i class="bi bi-file-earmark-image text-primary"></i> Berkas Gambar (Visual)
                    @else
                        <i class="bi bi-file-earmark-word text-primary"></i> Dokumen Asli (Visual)
                    @endif
                </button>
                <button type="button" class="siom-tab-btn" id="btn-tab-html" onclick="switchPreviewTab('html')">
                    <i class="bi bi-file-earmark-text"></i> Tampilan Web
                </button>
            </div>

            <!-- Zoom Controls (for docx) -->
            <div class="siom-doc-preview-zoom-group" id="topbar-zoom-controls">
                <button type="button" class="siom-doc-tool-btn" onclick="zoomPagePreview(-0.1)" title="Perkecil">
                    <i class="bi bi-zoom-out"></i>
                </button>
                <span id="page-zoom-label" class="siom-doc-zoom-label">100%</span>
                <button type="button" class="siom-doc-tool-btn" onclick="zoomPagePreview(0.1)" title="Perbesar">
                    <i class="bi bi-zoom-in"></i>
                </button>
                <button type="button" class="siom-doc-tool-btn" onclick="resetPageZoom()" title="Reset">
                    <i class="bi bi-arrows-angle-contract"></i>
                </button>
            </div>

            <button type="button" class="siom-admin-btn siom-admin-btn-sm" onclick="window.print()" title="Cetak Dokumen">
                <i class="bi bi-printer"></i> Cetak
            </button>

            <a href="{{ $downloadUrl }}" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-primary" title="Unduh Berkas">
                <i class="bi bi-download"></i> Unduh Berkas
            </a>
        </div>
    </header>

    <main class="siom-doc-main-container">
        <!-- Tab 1: DOCX / PDF / Image Visual View -->
        <div id="preview-tab-docx-content" class="siom-tab-pane active">
            <div id="page-preview-loading" class="siom-doc-loading-panel">
                <div class="spinner-border text-primary" role="status" style="width: 2.75rem; height: 2.75rem;">
                    <span class="visually-hidden">Memuat...</span>
                </div>
                <h4 class="mt-3 mb-1 fw-bold text-dark">Memuat Tampilan Dokumen</h4>
                <p class="text-muted small">Menyusun tampilan dokumen secara visual...</p>
            </div>

            <div id="page-preview-error" class="siom-doc-error-panel" style="display: none;">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-1 mb-2"></i>
                <h5 class="fw-bold text-dark">Tidak dapat merender berkas langsung</h5>
                <p class="text-muted small mb-3">Dokumen tetap dapat diunduh langsung untuk dibuka di aplikasi lokal atau dilihat pada tab "Tampilan Web".</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="siom-admin-btn" onclick="switchPreviewTab('html')">
                        <i class="bi bi-file-earmark-text"></i> Buka Tampilan Web
                    </button>
                    <a href="{{ $downloadUrl }}" class="siom-admin-btn siom-admin-btn-primary">
                        <i class="bi bi-download"></i> Unduh Dokumen
                    </a>
                </div>
            </div>

            <!-- PDF Viewer -->
            <div id="page-pdf-container" style="display: none; width: 100%; height: 100%; min-height: 80vh;">
                <iframe id="page-pdf-viewer" class="siom-pdf-page-iframe" style="width: 100%; height: 82vh; border: 2px solid #0f172a; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); background: #ffffff;" title="Pratinjau PDF"></iframe>
            </div>

            <!-- Image Viewer (PNG / JPG / WEBP) -->
            <div id="page-image-container" style="display: none; width: 100%; min-height: 75vh;" class="d-flex flex-column align-items-center justify-content-center py-4">
                <div class="siom-image-canvas p-3 mb-2" style="background: #ffffff; background-image: linear-gradient(45deg, #f1f5f9 25%, transparent 25%), linear-gradient(-45deg, #f1f5f9 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #f1f5f9 75%), linear-gradient(-45deg, transparent 75%, #f1f5f9 75%); background-size: 20px 20px; border: 2px solid #0f172a; border-radius: 14px; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.15), 5px 5px 0 #0f172a;">
                    <img id="page-image-viewer" src="" alt="Pratinjau Gambar" class="siom-image-preview-img" style="max-width: 100%; max-height: 75vh; object-fit: contain; border-radius: 6px; transition: transform 0.2s ease-out;">
                </div>
            </div>

            <!-- DOCX Viewer -->
            <div id="page-docx-viewer" class="siom-docx-pages-wrapper"></div>
        </div>

        <!-- Tab 2: HTML Clean Document Sheet View -->
        <div id="preview-tab-html-content" class="siom-tab-pane" style="display: none;">
            <article class="siom-paper-sheet">
                <!-- Kop Surat Kampus -->
                <header class="siom-kop-header position-relative">
                    @php
                        $docOrmawa = $submission?->ormawa ?? $lpj?->ormawa;
                    @endphp
                    @if ($docOrmawa)
                        <div class="text-center mb-2">
                            <img src="{{ $docOrmawa->logoUrl() }}" alt="Logo {{ $docOrmawa->name }}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: contain; border: 2px solid #0f172a; padding: 3px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        </div>
                    @endif
                    <h2>INSTITUT TEKNOLOGI SAINS DAN KESEHATAN RS dr. SOEPRAOEN</h2>
                    @if ($submission)
                        <h3>{{ mb_strtoupper($submission->ormawa?->name ?? 'ORGANISASI MAHASISWA') }}</h3>
                    @elseif ($lpj)
                        <h3>{{ mb_strtoupper($lpj->ormawa?->name ?? 'ORGANISASI MAHASISWA') }}</h3>
                    @endif
                    <p class="siom-kop-address">Jl. Sudanco Supriadi No. 22 Sukun, Kota Malang, Jawa Timur 65147</p>
                    <hr class="siom-kop-divider">
                </header>

                @if ($submission)
                    <!-- Detail Proposal / Nota Dinas -->
                    <div class="siom-doc-sheet-heading">
                        <h4>{{ strtoupper($documentType) }}</h4>
                        <p class="mb-1"><strong>Nomor:</strong> {{ $submission->nota_number ?? '-' }}</p>
                        <p class="mb-3"><strong>Tanggal:</strong> {{ $submission->event_date ? $submission->event_date->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</p>
                    </div>

                    <div class="siom-sheet-section">
                        <h5>A. INFORMASI KEGIATAN</h5>
                        <table class="table table-bordered siom-sheet-table">
                            <tr>
                                <td style="width: 25%;"><strong>Nama Kegiatan</strong></td>
                                <td>{{ $submission->activity_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tema Kegiatan</strong></td>
                                <td>{{ $submission->activity_theme ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Waktu & Tempat</strong></td>
                                <td>
                                    {{ $submission->event_day ?? '-' }}, {{ $submission->event_date?->translatedFormat('d F Y') ?? '-' }}
                                    ({{ $submission->event_start_time?->format('H:i') ?? '08:00' }} - {{ $submission->event_end_time?->format('H:i') ?? 'Selesai' }} WIB)
                                    <br>Lokasi: {{ $submission->event_location ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Sasaran Peserta</strong></td>
                                <td>{{ $submission->target_participants ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Anggaran Diajukan</strong></td>
                                <td><strong class="text-primary fs-6">Rp {{ number_format((float) $submission->total_amount, 0, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    </div>

                    @if ($submission->background)
                        <div class="siom-sheet-section">
                            <h5>B. LATAR BELAKANG</h5>
                            <p style="text-align: justify; line-height: 1.6;">{{ $submission->background }}</p>
                        </div>
                    @endif

                    @if ($submission->objectives && count($submission->objectives) > 0)
                        <div class="siom-sheet-section">
                            <h5>C. TUJUAN KEGIATAN</h5>
                            <ol style="padding-left: 20px; line-height: 1.8; list-style: decimal; margin: 0;">
                                @foreach ($submission->objectives as $obj)
                                    @if (trim($obj) !== '')
                                        <li style="margin-bottom: 4px;">{{ preg_replace('/^\d+[\.\)]\s*/', '', $obj) }}</li>
                                    @endif
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    @if ($submission->benefits && count($submission->benefits) > 0)
                        <div class="siom-sheet-section">
                            <h5>D. MANFAAT KEGIATAN</h5>
                            <ol style="padding-left: 20px; line-height: 1.8; list-style: decimal; margin: 0;">
                                @foreach ($submission->benefits as $ben)
                                    @if (trim($ben) !== '')
                                        <li style="margin-bottom: 4px;">{{ preg_replace('/^\d+[\.\)]\s*/', '', $ben) }}</li>
                                    @endif
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    <div class="siom-sheet-section">
                        <h5>E. PELAKSANAAN KEGIATAN</h5>
                        <table style="width: auto; border: none; margin-left: 5px; line-height: 1.8;">
                            <tr>
                                <td style="width: 120px; border: none;">1. Hari</td>
                                <td style="width: 20px; border: none;">:</td>
                                <td style="border: none;">{{ $submission->event_day ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td style="border: none;">2. Tanggal</td>
                                <td style="border: none;">:</td>
                                <td style="border: none;">{{ $submission->event_date ? $submission->event_date->translatedFormat('d F Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td style="border: none;">3. Waktu</td>
                                <td style="border: none;">:</td>
                                <td style="border: none;">{{ ($submission->event_start_time ? $submission->event_start_time->format('H:i') : '08:00') }} - {{ ($submission->event_end_time ? $submission->event_end_time->format('H:i') : 'Selesai') }} WIB</td>
                            </tr>
                            <tr>
                                <td style="border: none;">4. Tempat</td>
                                <td style="border: none;">:</td>
                                <td style="border: none;">{{ $submission->event_location ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>

                    @if ($submission->target_participants)
                        <div class="siom-sheet-section">
                            <h5>F. SASARAN KEGIATAN</h5>
                            <p style="margin-left: 5px;">Sasaran dalam kegiatan {{ $submission->activity_name }} adalah {{ $submission->target_participants }}.</p>
                        </div>
                    @endif

                    @if ($submission->timelines && $submission->timelines->isNotEmpty())
                        <div class="siom-sheet-section">
                            <h5>G. PERSIAPAN RENCANA KEGIATAN</h5>
                            <ol style="padding-left: 20px; line-height: 1.8; counter-reset: list-counter; list-style: decimal;" start="1">
                                @php
                                    $stages = [
                                        'perencanaan' => 'Perencanaan',
                                        'persiapan' => 'Persiapan',
                                        'pelaksanaan' => 'Pelaksanaan',
                                        'penutupan' => 'Penutupan'
                                    ];
                                @endphp
                                @foreach ($stages as $key => $stageLabel)
                                    @php $t = $submission->timelines->where('stage', $key)->first(); @endphp
                                    @if ($t)
                                        <li class="mb-2">
                                            <strong>{{ $stageLabel }}.</strong>
                                            Dari Tanggal {{ $t->start_date ? $t->start_date->format('d/m/Y') : '-' }} sampai dengan {{ $t->end_date ? $t->end_date->format('d/m/Y') : '-' }}.
                                            @if ($t->description && is_array($t->description))
                                                <ul style="list-style-type: disc; padding-left: 20px; margin-top: 4px;">
                                                    @foreach ($t->description as $descItem)
                                                        @if (trim($descItem) !== '')
                                                            <li>{{ preg_replace('/^[-•*]\s*|^\d+[\.\)]\s*/', '', $descItem) }}</li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endif
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    <!-- Tabel Rundown -->
                    @if ($submission->rundowns && $submission->rundowns->isNotEmpty())
                        <div class="siom-sheet-section">
                            <h5>H. RUNDOWN / SUSUNAN ACARA</h5>
                            <table class="table table-bordered siom-sheet-table mb-3">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">No.</th>
                                        <th style="width: 170px;" class="text-center">Waktu</th>
                                        <th>Kegiatan</th>
                                        <th style="width: 170px;">Penanggung Jawab</th>
                                        <th style="width: 140px;">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($submission->rundowns as $rIdx => $rundown)
                                        <tr>
                                            <td class="text-center">{{ $rIdx + 1 }}</td>
                                            <td class="text-center">{{ $rundown->time_slot }}</td>
                                            <td>{{ $rundown->event_name }}</td>
                                            <td>{{ $rundown->pic ?: '-' }}</td>
                                            <td>{{ $rundown->note ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <!-- Tabel Susunan Panitia -->
                    @if ($submission->committees && $submission->committees->isNotEmpty())
                        <div class="siom-sheet-section">
                            <h5>I. SUSUNAN KEPANITIAAN</h5>
                            <table class="table table-bordered siom-sheet-table mb-3">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">No.</th>
                                        <th>Nama Lengkap</th>
                                        <th style="width: 190px;">Jabatan / Divisi</th>
                                        <th style="width: 150px;">Status Struktural</th>
                                        <th style="width: 140px;">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($submission->committees as $cIdx => $committee)
                                        <tr>
                                            <td class="text-center">{{ $cIdx + 1 }}</td>
                                            <td>{{ $committee->name }}</td>
                                            <td><strong>{{ $committee->committee_role ?: '-' }}</strong></td>
                                            <td>{{ $committee->structural_role ?: '-' }}</td>
                                            <td>{{ $committee->description ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <!-- Tabel RAB -->
                    @if ($submission->items->isNotEmpty())
                        <div class="siom-sheet-section">
                            <h5>J. RINCIAN ANGGARAN BIAYA (RAB)</h5>
                            <table class="table table-bordered siom-sheet-rab-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">No.</th>
                                        <th>Rincian Kebutuhan</th>
                                        <th style="width: 140px;" class="text-center">Kuantitas</th>
                                        <th style="width: 160px;" class="text-end">Harga Satuan</th>
                                        <th style="width: 170px;" class="text-end">Total Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($submission->items as $idx => $item)
                                        <tr>
                                            <td class="text-center">{{ $idx + 1 }}</td>
                                            <td class="fw-bold">{{ $item->item_name }}</td>
                                            <td class="text-center font-monospace">{{ $item->formatted_qty }}</td>
                                            <td class="text-end">Rp {{ number_format((float) $item->price, 0, ',', '.') }}</td>
                                            <td class="text-end">Rp {{ number_format((float) $item->total_price, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light fw-bold">
                                        <td colspan="4" class="text-center">Total Anggaran</td>
                                        <td class="text-end">Rp {{ number_format((float) $submission->total_amount, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif

                    <!-- Susunan Penandatangan -->
                    <div class="siom-sheet-section mt-5">
                        <div class="row text-center">
                            <div class="col-6">
                                <p class="mb-5">Ketua Pelaksana,</p>
                                <p class="fw-bold mb-0 text-decoration-underline">{{ $submission->chairman_name ?: ($submission->submitter?->name ?? '-') }}</p>
                                <small>NIM. {{ $submission->chairman_nim ?: '-' }}</small>
                            </div>
                            <div class="col-6">
                                <p class="mb-5">
                                    Malang, {{ now()->translatedFormat('d F Y') }}<br>
                                    {{ $isBemOrDpm ? 'Presiden Mahasiswa,' : 'Ketua ORMAWA,' }}
                                </p>
                                <p class="fw-bold mb-0 text-decoration-underline">{{ $submission->submitter?->name ?? '-' }}</p>
                                <small>NIM. {{ $submission->submitter?->nim ?? '-' }}</small>
                            </div>
                        </div>
                    </div>
                @elseif ($lpj)
                    <!-- Detail LPJ -->
                    <div class="siom-doc-sheet-heading">
                        <h4>LAPORAN PERTANGGUNGJAWABAN (LPJ)</h4>
                        <h5>{{ $lpj->title }}</h5>
                        <p class="mb-1"><strong>Kegiatan Terkait:</strong> {{ $lpj->submission?->activity_name ?? '-' }}</p>
                        <p class="mb-3"><strong>Status Dokumen:</strong> {{ ucfirst(str_replace('_', ' ', $lpj->status)) }}</p>
                    </div>

                    @if ($lpj->description)
                        <div class="siom-sheet-section">
                            <h5>A. RINGKASAN PELAKSANAAN KEGIATAN</h5>
                            <p style="text-align: justify; line-height: 1.6;">{{ $lpj->description }}</p>
                        </div>
                    @endif

                    <!-- Realisasi Anggaran LPJ -->
                    @if ($lpj->items->isNotEmpty())
                        <div class="siom-sheet-section">
                            <h5>B. REALISASI ANGGARAN PENGELUARAN</h5>
                            <table class="table table-bordered siom-sheet-rab-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">No.</th>
                                        <th>Rincian Kebutuhan</th>
                                        <th style="width: 140px;" class="text-center">Kuantitas</th>
                                        <th style="width: 160px;" class="text-end">Harga Satuan</th>
                                        <th style="width: 170px;" class="text-end">Total Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalRealized = 0;
                                    @endphp
                                    @foreach ($lpj->items as $idx => $item)
                                        @php
                                            $totalRealized += (float) $item->total_price;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $idx + 1 }}</td>
                                            <td class="fw-bold">{{ $item->item_name }}</td>
                                            <td class="text-center font-monospace">{{ $item->formatted_qty }}</td>
                                            <td class="text-end">Rp {{ number_format((float) $item->price, 0, ',', '.') }}</td>
                                            <td class="text-end">Rp {{ number_format((float) $item->total_price, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light fw-bold">
                                        <td colspan="4" class="text-center">Total Realisasi Anggaran</td>
                                        <td class="text-end">Rp {{ number_format($totalRealized, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif

                    <!-- Dokumen Lampiran LPJ -->
                    @if ($lpj->documents->isNotEmpty())
                        <div class="siom-sheet-section">
                            <h5>C. BERKAS LAMPIRAN DOKUMEN</h5>
                            <div class="row g-2">
                                @foreach ($lpj->documents as $doc)
                                    <div class="col-md-6">
                                        <div class="p-2 border rounded d-flex align-items-center justify-content-between bg-light">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-paperclip fs-5 text-primary"></i>
                                                <div>
                                                    <span class="d-block fw-semibold small text-truncate" style="max-width: 250px;">{{ $doc->file_name }}</span>
                                                    <small class="text-muted">{{ ucfirst($doc->type) }}</small>
                                                </div>
                                            </div>
                                            <a href="{{ route('documents.preview.attachment', $doc->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Lihat
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark-arrow-down fs-1 text-muted"></i>
                        <h5 class="fw-bold mt-2">Lampiran Berkas</h5>
                        <p class="text-muted small">Silakan lihat tampilan berkas pada tab "Visual Dokumen" atau unduh melalui tombol di atas.</p>
                    </div>
                @endif
            </article>
        </div>
    </main>
</div>

<style>
.siom-doc-fullscreen-page {
    display: flex;
    flex-direction: column;
    min-height: calc(100vh - 4.5rem);
    background: #e2e8f0;
}

.siom-doc-topbar {
    position: sticky;
    top: 0;
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 0.85rem 1.5rem;
    background: #ffffff;
    border-bottom: 2px solid #cbd5e1;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.siom-doc-topbar-left {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 0;
}

.siom-doc-topbar-info {
    min-width: 0;
}

.siom-doc-badge {
    display: inline-block;
    padding: 0.15rem 0.55rem;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    background: #dbeafe;
    color: #1e40af;
}

.siom-doc-page-title {
    margin: 0.15rem 0 0;
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 480px;
}

.siom-doc-topbar-right {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

.siom-doc-tab-switcher {
    display: flex;
    align-items: center;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 0.2rem;
    gap: 0.2rem;
}

.siom-tab-btn {
    border: none;
    background: transparent;
    padding: 0.35rem 0.75rem;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease;
}

.siom-tab-btn.active {
    background: #ffffff;
    color: #2563eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.siom-doc-main-container {
    flex: 1;
    padding: 2rem 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    overflow-y: auto;
}

.siom-tab-pane {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.siom-doc-loading-panel,
.siom-doc-error-panel {
    background: #ffffff;
    border-radius: 16px;
    border: 1.5px solid #cbd5e1;
    padding: 3rem 2rem;
    text-align: center;
    width: min(100%, 650px);
    margin: 2rem auto;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
}

/* Paper Sheet Style (for HTML view) */
.siom-paper-sheet {
    background: #ffffff;
    width: min(100%, 860px);
    padding: 3.5rem 3rem;
    border-radius: 6px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12), 0 1px 3px rgba(0, 0, 0, 0.08);
    border: 1px solid #cbd5e1;
    color: #0f172a;
}

.siom-kop-header {
    text-align: center;
    margin-bottom: 2rem;
}

.siom-kop-header h2 {
    font-size: 1.15rem;
    font-weight: 800;
    margin: 0;
    letter-spacing: 0.02em;
    color: #0f172a;
}

.siom-kop-header h3 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0.25rem 0 0;
    color: #1e3a8a;
}

.siom-kop-address {
    font-size: 0.8rem;
    color: #64748b;
    margin: 0.25rem 0 0;
}

.siom-kop-divider {
    border-top: 3px double #0f172a;
    opacity: 1;
    margin: 0.75rem 0 1.5rem;
}

.siom-doc-sheet-heading {
    text-align: center;
    margin-bottom: 2rem;
}

.siom-doc-sheet-heading h4 {
    font-size: 1.1rem;
    font-weight: 800;
    text-decoration: underline;
    margin-bottom: 0.5rem;
}

.siom-sheet-section {
    margin-bottom: 2rem;
}

.siom-sheet-section h5 {
    font-size: 0.92rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 0.75rem;
}

.siom-sheet-table td {
    padding: 0.6rem 0.85rem;
    font-size: 0.88rem;
}

.siom-sheet-rab-table th,
.siom-sheet-rab-table td {
    padding: 0.6rem 0.75rem;
    font-size: 0.88rem;
}

/* Pastikan seluruh gambar, logo cover, VML/SVG, dan DrawingML pada paragraf tengah berpusat secara presisi */
.siom-docx-pages-wrapper .docx p[style*="text-align: center"],
.siom-docx-pages-wrapper .docx p[style*="text-align:center"] {
    text-align: center !important;
}

.siom-docx-pages-wrapper .docx p[style*="text-align: center"] > div,
.siom-docx-pages-wrapper .docx p[style*="text-align:center"] > div,
.siom-docx-pages-wrapper .docx p[style*="text-align: center"] span,
.siom-docx-pages-wrapper .docx p[style*="text-align:center"] span {
    text-align: center !important;
}

.siom-docx-pages-wrapper .docx p[style*="text-align: center"] svg,
.siom-docx-pages-wrapper .docx p[style*="text-align:center"] svg,
.siom-docx-pages-wrapper .docx p[style*="text-align: center"] img,
.siom-docx-pages-wrapper .docx p[style*="text-align:center"] img {
    display: inline-block !important;
    margin-left: auto !important;
    margin-right: auto !important;
    vertical-align: middle !important;
}

.siom-docx-pages-wrapper .docx div:has(> svg) {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    width: 100% !important;
    margin-left: auto !important;
    margin-right: auto !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/docx-preview@0.3.3/dist/docx-preview.min.js"></script>

<script>
let pageZoom = 1.0;

function switchPreviewTab(tab) {
    const docxTab = document.getElementById('preview-tab-docx-content');
    const htmlTab = document.getElementById('preview-tab-html-content');
    const btnDocx = document.getElementById('btn-tab-docx');
    const btnHtml = document.getElementById('btn-tab-html');
    const zoomControls = document.getElementById('topbar-zoom-controls');

    if (tab === 'docx') {
        docxTab.style.display = 'flex';
        htmlTab.style.display = 'none';
        btnDocx.classList.add('active');
        btnHtml.classList.remove('active');
        zoomControls.style.display = 'flex';
    } else {
        docxTab.style.display = 'none';
        htmlTab.style.display = 'flex';
        btnDocx.classList.remove('active');
        btnHtml.classList.add('active');
        zoomControls.style.display = 'none';
    }
}

function zoomPagePreview(delta) {
    pageZoom = Math.min(2.0, Math.max(0.5, pageZoom + delta));
    updatePageZoom();
}

function resetPageZoom() {
    pageZoom = 1.0;
    updatePageZoom();
}

function updatePageZoom() {
    const docxContainer = document.getElementById('page-docx-viewer');
    const imgEl = document.getElementById('page-image-viewer');
    const label = document.getElementById('page-zoom-label');
    if (label) label.textContent = Math.round(pageZoom * 100) + '%';
    if (docxContainer && docxContainer.style.display !== 'none') {
        docxContainer.style.transform = `scale(${pageZoom})`;
    }
    if (imgEl) {
        imgEl.style.transform = `scale(${pageZoom})`;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const fileUrl = '{{ $fileUrl }}';
    const declaredFormat = '{{ $format ?? '' }}';
    const container = document.getElementById('page-docx-viewer');
    const pdfContainer = document.getElementById('page-pdf-container');
    const pdfViewer = document.getElementById('page-pdf-viewer');
    const imageContainer = document.getElementById('page-image-container');
    const imageViewer = document.getElementById('page-image-viewer');
    const loading = document.getElementById('page-preview-loading');
    const errorEl = document.getElementById('page-preview-error');
    const zoomControls = document.getElementById('topbar-zoom-controls');

    const urlLower = fileUrl.toLowerCase();
    const isImage = declaredFormat === 'image' || /\.(png|jpe?g|webp|gif|svg)(\?.*)?$/i.test(urlLower);
    const isPdf = declaredFormat === 'pdf' || urlLower.endsWith('.pdf');

    if (isImage) {
        if (container) container.style.display = 'none';
        if (pdfContainer) pdfContainer.style.display = 'none';
        if (imageContainer) imageContainer.style.display = 'flex';
        if (zoomControls) zoomControls.style.display = 'flex';

        imageViewer.onload = function() {
            loading.style.display = 'none';
        };
        imageViewer.onerror = function() {
            loading.style.display = 'none';
            errorEl.style.display = 'block';
        };
        imageViewer.src = fileUrl;
        return;
    }

    if (isPdf) {
        if (container) container.style.display = 'none';
        if (imageContainer) imageContainer.style.display = 'none';
        if (pdfContainer) pdfContainer.style.display = 'flex';
        if (zoomControls) zoomControls.style.display = 'none';

        pdfViewer.onload = function() {
            loading.style.display = 'none';
        };
        pdfViewer.onerror = function() {
            loading.style.display = 'none';
            errorEl.style.display = 'block';
        };
        pdfViewer.src = fileUrl;
        return;
    }

    // Default DOCX
    if (pdfContainer) pdfContainer.style.display = 'none';
    if (imageContainer) imageContainer.style.display = 'none';
    if (container) container.style.display = 'flex';
    if (zoomControls) zoomControls.style.display = 'flex';

    fetch(fileUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Status: ' + res.status);
        const contentType = (res.headers.get('content-type') || '').toLowerCase();

        if (contentType.includes('image/')) {
            if (container) container.style.display = 'none';
            if (imageContainer) imageContainer.style.display = 'flex';
            imageViewer.onload = () => { loading.style.display = 'none'; };
            imageViewer.src = fileUrl;
            return null;
        }

        if (contentType.includes('application/pdf')) {
            if (container) container.style.display = 'none';
            if (pdfContainer) pdfContainer.style.display = 'flex';
            if (zoomControls) zoomControls.style.display = 'none';
            pdfViewer.onload = () => { loading.style.display = 'none'; };
            pdfViewer.src = fileUrl;
            return null;
        }

        return res.blob();
    })
    .then(blob => {
        if (!blob) return;
        return docx.renderAsync(blob, container, null, {
            className: 'docx',
            inWrapper: true,
            ignoreWidth: false,
            ignoreHeight: false,
            breakPages: true
        });
    })
    .then(() => {
        centerDocxVisualImages(container);
        loading.style.display = 'none';
    })
    .catch(err => {
        console.error('Render error:', err);
        loading.style.display = 'none';
        errorEl.style.display = 'block';
    });
});

function centerDocxVisualImages(container) {
    if (!container) return;
    const paragraphs = container.querySelectorAll('.docx p');
    paragraphs.forEach(p => {
        const style = p.getAttribute('style') || '';
        const isCenter = p.style.textAlign === 'center' || style.includes('text-align: center') || style.includes('text-align:center');
        if (isCenter) {
            const svgs = p.querySelectorAll('svg');
            const imgs = p.querySelectorAll('img');
            const divs = p.querySelectorAll('div');

            if (svgs.length > 0 || imgs.length > 0) {
                p.style.display = 'flex';
                p.style.flexDirection = 'column';
                p.style.alignItems = 'center';
                p.style.justifyContent = 'center';
                p.style.textAlign = 'center';

                divs.forEach(d => {
                    d.style.display = 'flex';
                    d.style.justifyContent = 'center';
                    d.style.alignItems = 'center';
                    d.style.margin = '0 auto';
                    d.style.width = '100%';
                });

                svgs.forEach(s => {
                    s.style.display = 'block';
                    s.style.margin = '0 auto';
                });

                imgs.forEach(m => {
                    m.style.display = 'block';
                    m.style.margin = '0 auto';
                });
            }
        }
    });
}
</script>
@endsection

