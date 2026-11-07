<div id="siom-doc-preview-modal" class="siom-doc-preview-modal" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="siom-doc-preview-title">
    <div class="siom-doc-preview-backdrop" onclick="window.closeDocPreview()"></div>

    <div class="siom-doc-preview-card">
        <!-- Header -->
        <header class="siom-doc-preview-head">
            <div class="siom-doc-preview-head-left">
                <span class="siom-doc-preview-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </span>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span id="siom-doc-preview-badge" class="siom-doc-preview-badge">Dokumen</span>
                        <span id="siom-doc-preview-format" class="siom-doc-preview-format">DOCX</span>
                    </div>
                    <h3 id="siom-doc-preview-title" class="siom-doc-preview-title">Pratinjau Dokumen</h3>
                </div>
            </div>

            <div class="siom-doc-preview-actions">
                <!-- Zoom Controls -->
                <div class="siom-doc-preview-zoom-group" id="siom-doc-zoom-controls">
                    <button type="button" class="siom-doc-tool-btn" onclick="window.zoomDocPreview(-0.1)" title="Perkecil (-10%)">
                        <i class="bi bi-zoom-out"></i>
                    </button>
                    <span id="siom-doc-zoom-label" class="siom-doc-zoom-label">100%</span>
                    <button type="button" class="siom-doc-tool-btn" onclick="window.zoomDocPreview(0.1)" title="Perbesar (+10%)">
                        <i class="bi bi-zoom-in"></i>
                    </button>
                    <button type="button" class="siom-doc-tool-btn" onclick="window.resetDocZoom()" title="Reset Ukuran (100%)">
                        <i class="bi bi-arrows-angle-contract"></i>
                    </button>
                </div>

                <!-- Print Button -->
                <button type="button" class="siom-doc-tool-btn" onclick="window.printDocPreview()" title="Cetak Dokumen">
                    <i class="bi bi-printer"></i>
                    <span class="d-none d-sm-inline">Cetak</span>
                </button>

                <!-- Open in New Tab -->
                <a id="siom-doc-preview-newtab" href="#" target="_blank" class="siom-doc-tool-btn" title="Buka di Tab Penuh">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span class="d-none d-sm-inline">Tab Baru</span>
                </a>

                <!-- Download Button -->
                <a id="siom-doc-preview-download" href="#" class="siom-doc-tool-btn siom-doc-tool-btn-primary" title="Unduh Berkas">
                    <i class="bi bi-download"></i>
                    <span class="d-none d-sm-inline">Unduh</span>
                </a>

                <!-- Close Button -->
                <button type="button" class="siom-doc-close-btn" onclick="window.closeDocPreview()" title="Tutup (Esc)">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </header>

        <!-- Body / Workspace -->
        <main class="siom-doc-preview-body">
            <!-- Loading Indicator -->
            <div id="siom-doc-preview-loading" class="siom-doc-preview-loading">
                <div class="spinner-border text-primary" role="status" style="width: 2.75rem; height: 2.75rem;">
                    <span class="visually-hidden">Memuat...</span>
                </div>
                <h4 class="mt-3 mb-1 fw-bold text-dark">Memuat Pratinjau Dokumen</h4>
                <p class="text-muted small">Menyiapkan halaman dokumen kampus resmi secara visual...</p>
            </div>

            <!-- Error Notification -->
            <div id="siom-doc-preview-error" class="siom-doc-preview-error" style="display: none;">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-1 mb-2"></i>
                <h5 class="fw-bold text-dark">Tidak dapat merender tampilan dokumen langsung</h5>
                <p id="siom-doc-preview-error-msg" class="text-muted small mb-3">Terjadi kendala saat memuat berkas dokumen.</p>
                <div class="d-flex justify-content-center gap-2">
                    <a id="siom-doc-preview-error-download" href="#" class="siom-admin-btn siom-admin-btn-primary">
                        <i class="bi bi-download"></i> Unduh Berkas untuk Dibuka di Word
                    </a>
                    <button type="button" class="siom-admin-btn" onclick="window.closeDocPreview()">Tutup</button>
                </div>
            </div>

            <!-- Content Workspace -->
            <div id="siom-doc-preview-content" class="siom-doc-preview-content">
                <!-- DOCX Pages Container -->
                <div id="siom-docx-pages-container" class="siom-docx-pages-wrapper"></div>

                <!-- PDF Container -->
                <div id="siom-pdf-preview-container" class="siom-pdf-preview-wrapper" style="display: none;">
                    <div class="siom-pdf-preview-toolbar">
                        <span class="small text-muted"><i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i> Penampil Dokumen PDF Resmi</span>
                        <div class="d-flex align-items-center gap-2">
                            <a id="siom-pdf-fullscreen-btn" href="#" target="_blank" class="btn btn-sm btn-outline-secondary" style="font-size: 0.78rem; padding: 0.2rem 0.65rem;">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Buka Layar Penuh
                            </a>
                        </div>
                    </div>
                    <iframe id="siom-pdf-preview-iframe" class="siom-pdf-preview-iframe" title="Pratinjau PDF"></iframe>
                </div>

                <!-- Image (PNG, JPG, WEBP) Container -->
                <div id="siom-image-preview-container" class="siom-image-preview-wrapper" style="display: none;">
                    <div class="siom-image-preview-toolbar">
                        <div class="d-flex align-items-center gap-2">
                            <span class="small fw-semibold text-muted">
                                <i class="bi bi-image text-primary me-1"></i> Penampil Berkas Gambar / Bukti Lampiran
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary" style="font-size: 0.78rem; padding: 0.2rem 0.65rem;" onclick="window.rotateDocImage()" title="Putar 90 Derajat">
                                <i class="bi bi-arrow-clockwise me-1"></i> Putar 90°
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" style="font-size: 0.78rem; padding: 0.2rem 0.65rem;" onclick="window.fitDocImage()" title="Paskan Ukuran">
                                <i class="bi bi-arrows-angle-expand me-1"></i> Paskan
                            </button>
                        </div>
                    </div>
                    <div class="siom-image-canvas-viewport" id="siom-image-canvas-viewport">
                        <div class="siom-image-canvas" id="siom-image-canvas">
                            <img id="siom-image-preview-img" src="" alt="Pratinjau Dokumen Gambar" class="siom-image-preview-img">
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
/* Modal Layout */
.siom-doc-preview-modal {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    overflow: hidden;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.siom-doc-preview-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.78);
    backdrop-filter: blur(6px);
    cursor: pointer;
}

.siom-doc-preview-card {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 1100px;
    height: 94vh;
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border-radius: 20px;
    border: 2px solid #0f172a;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.4), 8px 8px 0 #0f172a;
    overflow: hidden;
}

/* Modal Header */
.siom-doc-preview-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 0.9rem 1.25rem;
    background: #ffffff;
    border-bottom: 2px solid #e2e8f0;
    flex-shrink: 0;
}

.siom-doc-preview-head-left {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    min-width: 0;
}

.siom-doc-preview-icon {
    width: 2.75rem;
    height: 2.75rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: #eff6ff;
    color: #2563eb;
    font-size: 1.35rem;
    flex-shrink: 0;
}

.siom-doc-preview-badge {
    display: inline-block;
    padding: 0.15rem 0.55rem;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    background: #dbeafe;
    color: #1e40af;
}

.siom-doc-preview-format {
    display: inline-block;
    padding: 0.15rem 0.45rem;
    border-radius: 4px;
    font-size: 0.68rem;
    font-weight: 800;
    background: #e2e8f0;
    color: #334155;
}

.siom-doc-preview-title {
    margin: 0.15rem 0 0;
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 480px;
}

/* Action Buttons */
.siom-doc-preview-actions {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    flex-shrink: 0;
}

.siom-doc-preview-zoom-group {
    display: flex;
    align-items: center;
    gap: 0.2rem;
    padding: 0.2rem 0.35rem;
    background: #f1f5f9;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
}

.siom-doc-zoom-label {
    min-width: 44px;
    text-align: center;
    font-size: 0.78rem;
    font-weight: 700;
    color: #334155;
}

.siom-doc-tool-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.45rem 0.75rem;
    font-size: 0.85rem;
    font-weight: 700;
    color: #334155;
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}

.siom-doc-tool-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
    border-color: #94a3b8;
}

.siom-doc-tool-btn-primary {
    background: #2563eb;
    color: #ffffff;
    border-color: #1d4ed8;
}

.siom-doc-tool-btn-primary:hover {
    background: #1d4ed8;
    color: #ffffff;
    border-color: #1e40af;
}

.siom-doc-close-btn {
    width: 2.35rem;
    height: 2.35rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    color: #64748b;
    background: #f1f5f9;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.siom-doc-close-btn:hover {
    background: #fee2e2;
    color: #ef4444;
    border-color: #fca5a5;
}

/* Modal Body */
.siom-doc-preview-body {
    position: relative;
    flex: 1;
    min-height: 0;
    background: #e2e8f0;
    overflow-y: auto;
    overflow-x: auto;
    display: flex;
    flex-direction: column;
}

.siom-doc-preview-loading,
.siom-doc-preview-error {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    z-index: 20;
    padding: 2rem;
    text-align: center;
}

.siom-doc-preview-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 2rem 1rem;
    width: 100%;
    min-height: 100%;
}

.siom-docx-pages-wrapper {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.75rem;
    transform-origin: top center;
    transition: transform 0.2s ease-out;
}

/* Stylings for docx-preview generated pages */
.siom-docx-pages-wrapper .docx-wrapper {
    background: transparent !important;
    padding: 0 !important;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.75rem;
    width: 100%;
}

.siom-docx-pages-wrapper .docx {
    background: #ffffff !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    margin-bottom: 0 !important;
    box-sizing: border-box;
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

.siom-pdf-preview-iframe {
    width: 100%;
    flex: 1;
    min-height: 75vh;
    border: none;
    border-radius: 8px;
    background: #ffffff;
}

/* Image Preview Layout */
.siom-image-preview-wrapper {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.siom-image-preview-toolbar,
.siom-pdf-preview-toolbar {
    width: 100%;
    max-width: 950px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0.85rem;
    margin-bottom: 0.85rem;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.siom-image-canvas-viewport {
    flex: 1;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: auto;
    padding: 1.5rem 1rem;
}

.siom-image-canvas {
    padding: 12px;
    background: #ffffff;
    background-image: 
        linear-gradient(45deg, #f1f5f9 25%, transparent 25%), 
        linear-gradient(-45deg, #f1f5f9 25%, transparent 25%), 
        linear-gradient(45deg, transparent 75%, #f1f5f9 75%), 
        linear-gradient(-45deg, transparent 75%, #f1f5f9 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
    border: 2px solid #0f172a;
    border-radius: 14px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.15), 5px 5px 0 #0f172a;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    max-width: 95%;
    max-height: 82vh;
}

.siom-image-preview-img {
    max-width: 100%;
    max-height: 75vh;
    object-fit: contain;
    border-radius: 6px;
    transition: transform 0.2s ease-out;
    transform-origin: center center;
    user-select: none;
}

.siom-pdf-preview-wrapper {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Responsivitas Mobile (HP / Layar Kecil) */
@media (max-width: 768px) {
    .siom-doc-preview-modal {
        padding: 0;
    }
    .siom-doc-preview-card {
        max-width: 100vw;
        height: 100vh;
        height: 100dvh;
        border-radius: 0;
        border: none;
        box-shadow: none;
    }
    .siom-doc-preview-head {
        padding: 0.65rem 0.85rem;
        gap: 0.5rem;
    }
    .siom-doc-preview-icon {
        display: none;
    }
    .siom-doc-preview-title {
        max-width: 140px;
        font-size: 0.92rem;
    }
    .siom-doc-preview-actions {
        gap: 0.25rem;
    }
    .siom-doc-tool-btn {
        padding: 0.35rem 0.55rem;
        font-size: 0.8rem;
    }
    .siom-doc-close-btn {
        width: 2.15rem;
        height: 2.15rem;
    }
    .siom-doc-preview-content {
        padding: 0.85rem 0.25rem;
    }
}

@media (max-width: 480px) {
    .siom-doc-preview-zoom-group {
        display: none !important;
    }
    .siom-doc-preview-title {
        max-width: 110px;
        font-size: 0.85rem;
    }
    .siom-doc-preview-badge,
    .siom-doc-preview-format {
        font-size: 0.62rem;
        padding: 0.1rem 0.35rem;
    }
}

/* Print Handling */
@media print {
    body * {
        visibility: hidden !important;
    }
    #siom-doc-preview-modal,
    #siom-doc-preview-modal * {
        visibility: visible !important;
    }
    #siom-doc-preview-modal {
        position: absolute !important;
        inset: 0 !important;
        background: transparent !important;
        padding: 0 !important;
    }
    .siom-doc-preview-head,
    .siom-doc-preview-backdrop,
    .siom-doc-close-btn,
    .siom-doc-preview-actions,
    .siom-image-preview-toolbar,
    .siom-pdf-preview-toolbar {
        display: none !important;
    }
    .siom-doc-preview-card {
        border: none !important;
        box-shadow: none !important;
        width: 100% !important;
        height: auto !important;
    }
    .siom-doc-preview-body {
        background: #ffffff !important;
        overflow: visible !important;
    }
    .siom-docx-pages-wrapper .docx {
        box-shadow: none !important;
        border: none !important;
        page-break-after: always !important;
    }
    .siom-image-canvas {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
    }
}
</style>

<script>
(function() {
    let currentZoom = 1.0;
    let currentRotation = 0;
    let currentDocType = 'docx'; // 'docx' | 'pdf' | 'image'

    window.openDocPreview = function(options) {
        if (typeof options === 'string') {
            options = {
                fileUrl: arguments[0] || '',
                pageUrl: arguments[1] || arguments[0] || '#',
                title: arguments[2] || 'Pratinjau Dokumen',
                format: arguments[3] || '',
                downloadUrl: arguments[4] || arguments[0] || '#'
            };
        }

        const modal = document.getElementById('siom-doc-preview-modal');
        const titleEl = document.getElementById('siom-doc-preview-title');
        const badgeEl = document.getElementById('siom-doc-preview-badge');
        const formatEl = document.getElementById('siom-doc-preview-format');
        const newtabEl = document.getElementById('siom-doc-preview-newtab');
        const downloadEl = document.getElementById('siom-doc-preview-download');
        const errorDownloadEl = document.getElementById('siom-doc-preview-error-download');
        const loadingEl = document.getElementById('siom-doc-preview-loading');
        const errorEl = document.getElementById('siom-doc-preview-error');
        const docxContainer = document.getElementById('siom-docx-pages-container');
        const pdfContainer = document.getElementById('siom-pdf-preview-container');
        const pdfIframe = document.getElementById('siom-pdf-preview-iframe');
        const pdfFullscreenBtn = document.getElementById('siom-pdf-fullscreen-btn');
        const imageContainer = document.getElementById('siom-image-preview-container');
        const imageEl = document.getElementById('siom-image-preview-img');
        const zoomControls = document.getElementById('siom-doc-zoom-controls');

        if (!modal) return;

        // Reset state
        currentZoom = 1.0;
        currentRotation = 0;
        updateZoomDisplay();
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        titleEl.textContent = options.title || 'Pratinjau Dokumen';
        badgeEl.textContent = options.documentType || 'Dokumen';

        const fileUrl = options.fileUrl || '#';
        const pageUrl = options.pageUrl || fileUrl;
        const downloadUrl = options.downloadUrl || fileUrl;

        newtabEl.href = pageUrl;
        downloadEl.href = downloadUrl;
        errorDownloadEl.href = downloadUrl;
        if (pdfFullscreenBtn) pdfFullscreenBtn.href = pageUrl;

        loadingEl.style.display = 'flex';
        errorEl.style.display = 'none';
        docxContainer.innerHTML = '';
        docxContainer.style.display = 'none';
        if (pdfContainer) pdfContainer.style.display = 'none';
        if (imageContainer) imageContainer.style.display = 'none';
        pdfIframe.src = 'about:blank';
        imageEl.src = '';

        // Deteksi Tipe Format Berkas
        const urlLower = (fileUrl || '').toLowerCase();
        const titleLower = ((options.title || '') + ' ' + (options.fileName || '')).toLowerCase();
        const rawFormat = (options.format || '').toLowerCase();

        const isExplicitImage = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg', 'image', 'gambar'].includes(rawFormat)
            || /\.(png|jpe?g|webp|gif|svg)(\?.*)?$/i.test(urlLower)
            || /\.(png|jpe?g|webp|gif|svg)$/i.test(titleLower);

        const isExplicitPdf = rawFormat === 'pdf'
            || urlLower.endsWith('.pdf')
            || titleLower.endsWith('.pdf')
            || (urlLower.includes('/file') && titleLower.includes('.pdf'));

        if (isExplicitImage) {
            renderAsImage(fileUrl, titleLower || rawFormat);
            return;
        }

        if (isExplicitPdf) {
            renderAsPdf(fileUrl);
            return;
        }

        // Jika belum pasti atau bertipe docx, periksa respon terlebih dahulu
        renderWithContentDetection(fileUrl);
    };

    function renderAsImage(fileUrl, filename) {
        currentDocType = 'image';
        const formatEl = document.getElementById('siom-doc-preview-format');
        const zoomControls = document.getElementById('siom-doc-zoom-controls');
        const imageContainer = document.getElementById('siom-image-preview-container');
        const imageEl = document.getElementById('siom-image-preview-img');
        const loadingEl = document.getElementById('siom-doc-preview-loading');

        let imgFormat = 'PNG';
        if (filename.includes('.jpg') || filename.includes('.jpeg')) imgFormat = 'JPG';
        else if (filename.includes('.webp')) imgFormat = 'WEBP';
        else if (filename.includes('.svg')) imgFormat = 'SVG';

        formatEl.textContent = imgFormat;
        formatEl.style.background = '#ede9fe';
        formatEl.style.color = '#5b21b6';
        formatEl.style.border = '1.5px solid #8b5cf6';

        zoomControls.style.display = 'flex';
        imageContainer.style.display = 'flex';

        imageEl.onload = function() {
            loadingEl.style.display = 'none';
            currentZoom = 1.0;
            currentRotation = 0;
            updateZoomDisplay();
        };

        imageEl.onerror = function() {
            showError('Gagal memuat berkas gambar. Berkas mungkin tidak ditemukan di server.');
        };

        imageEl.src = fileUrl;
    }

    function renderAsPdf(fileUrl) {
        currentDocType = 'pdf';
        const formatEl = document.getElementById('siom-doc-preview-format');
        const zoomControls = document.getElementById('siom-doc-zoom-controls');
        const pdfContainer = document.getElementById('siom-pdf-preview-container');
        const pdfIframe = document.getElementById('siom-pdf-preview-iframe');
        const loadingEl = document.getElementById('siom-doc-preview-loading');

        formatEl.textContent = 'PDF';
        formatEl.style.background = '#fee2e2';
        formatEl.style.color = '#991b1b';
        formatEl.style.border = '1.5px solid #ef4444';

        zoomControls.style.display = 'none';
        pdfContainer.style.display = 'flex';

        pdfIframe.onload = function() {
            loadingEl.style.display = 'none';
        };

        pdfIframe.onerror = function() {
            showError('Gagal memuat pratinjau berkas PDF.');
        };

        pdfIframe.src = fileUrl;
    }

    function renderWithContentDetection(fileUrl) {
        const loadingEl = document.getElementById('siom-doc-preview-loading');
        const formatEl = document.getElementById('siom-doc-preview-format');
        const zoomControls = document.getElementById('siom-doc-zoom-controls');
        const docxContainer = document.getElementById('siom-docx-pages-container');

        fetch(fileUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal mengambil berkas (' + response.status + ' ' + response.statusText + ')');
            }
            const contentType = (response.headers.get('content-type') || '').toLowerCase();

            if (contentType.includes('image/')) {
                renderAsImage(fileUrl, contentType);
                return null;
            }

            if (contentType.includes('application/pdf')) {
                renderAsPdf(fileUrl);
                return null;
            }

            // DOCX format
            currentDocType = 'docx';
            formatEl.textContent = 'DOCX';
            formatEl.style.background = '#dbeafe';
            formatEl.style.color = '#1e40af';
            formatEl.style.border = '1.5px solid #3b82f6';
            zoomControls.style.display = 'flex';
            docxContainer.style.display = 'flex';

            return response.blob();
        })
        .then(blob => {
            if (!blob) return;

            if (typeof docx === 'undefined') {
                loadDocxPreviewLibraries().then(() => {
                    renderDocxBlob(blob, docxContainer, loadingEl);
                }).catch(() => {
                    showError('Library penampil Word belum siap dimuat. Anda dapat mengunduh berkas secara langsung.');
                });
            } else {
                renderDocxBlob(blob, docxContainer, loadingEl);
            }
        })
        .catch(err => {
            console.error('Doc preview error:', err);
            showError('Gagal memuat pratinjau: ' + (err.message || 'Terjadi kesalahan jaringan atau berkas tidak ditemukan.'));
        });
    }

    function renderDocxBlob(blob, container, loadingEl) {
        container.innerHTML = '';
        docx.renderAsync(blob, container, null, {
            className: 'docx',
            inWrapper: true,
            ignoreWidth: false,
            ignoreHeight: false,
            ignoreFonts: false,
            breakPages: true,
            experimental: false,
            useBase64URL: true,
            trimXml: true
        })
        .then(() => {
            centerDocxVisualImages(container);
            loadingEl.style.display = 'none';
        })
        .catch(err => {
            console.error('DOCX render error:', err);
            showError('Format Word tidak dapat diproses: ' + (err.message || 'Berkas tidak kompatibel.'));
        });
    }

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

    function showError(message) {
        const loadingEl = document.getElementById('siom-doc-preview-loading');
        const errorEl = document.getElementById('siom-doc-preview-error');
        const errorMsgEl = document.getElementById('siom-doc-preview-error-msg');
        const docxContainer = document.getElementById('siom-docx-pages-container');
        const pdfContainer = document.getElementById('siom-pdf-preview-container');
        const imageContainer = document.getElementById('siom-image-preview-container');

        loadingEl.style.display = 'none';
        docxContainer.style.display = 'none';
        if (pdfContainer) pdfContainer.style.display = 'none';
        if (imageContainer) imageContainer.style.display = 'none';
        if (errorMsgEl) errorMsgEl.textContent = message;
        errorEl.style.display = 'flex';
    }

    function loadDocxPreviewLibraries() {
        return new Promise((resolve, reject) => {
            if (typeof window.docx !== 'undefined') {
                resolve();
                return;
            }

            const loadScript = (src) => new Promise((res, rej) => {
                const s = document.createElement('script');
                s.src = src;
                s.onload = res;
                s.onerror = rej;
                document.head.appendChild(s);
            });

            loadScript('https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js')
                .then(() => loadScript('https://cdn.jsdelivr.net/npm/docx-preview@0.3.3/dist/docx-preview.min.js'))
                .then(resolve)
                .catch(reject);
        });
    }

    window.closeDocPreview = function() {
        const modal = document.getElementById('siom-doc-preview-modal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
            const pdfIframe = document.getElementById('siom-pdf-preview-iframe');
            if (pdfIframe) pdfIframe.src = 'about:blank';
            const docxContainer = document.getElementById('siom-docx-pages-container');
            if (docxContainer) docxContainer.innerHTML = '';
            const imageEl = document.getElementById('siom-image-preview-img');
            if (imageEl) imageEl.src = '';
        }
    };

    window.zoomDocPreview = function(delta) {
        currentZoom = Math.min(3.0, Math.max(0.3, currentZoom + delta));
        updateZoomDisplay();
    };

    window.resetDocZoom = function() {
        currentZoom = 1.0;
        updateZoomDisplay();
    };

    window.rotateDocImage = function() {
        currentRotation = (currentRotation + 90) % 360;
        updateZoomDisplay();
    };

    window.fitDocImage = function() {
        currentZoom = 1.0;
        currentRotation = 0;
        updateZoomDisplay();
    };

    function updateZoomDisplay() {
        const label = document.getElementById('siom-doc-zoom-label');
        if (label) label.textContent = Math.round(currentZoom * 100) + '%';

        if (currentDocType === 'image') {
            const img = document.getElementById('siom-image-preview-img');
            if (img) {
                img.style.transform = `scale(${currentZoom}) rotate(${currentRotation}deg)`;
            }
        } else if (currentDocType === 'docx') {
            const wrapper = document.getElementById('siom-docx-pages-container');
            if (wrapper) {
                wrapper.style.transform = `scale(${currentZoom})`;
            }
        }
    }

    window.printDocPreview = function() {
        if (currentDocType === 'image') {
            const img = document.getElementById('siom-image-preview-img');
            if (img && img.src) {
                const printWindow = window.open('', '_blank');
                if (printWindow) {
                    const doc = printWindow.document;
                    doc.title = 'Cetak Berkas Lampiran';
                    doc.body.style.margin = '0';
                    doc.body.style.display = 'flex';
                    doc.body.style.alignItems = 'center';
                    doc.body.style.justifyContent = 'center';
                    doc.body.style.minHeight = '100vh';
                    doc.body.style.background = '#ffffff';

                    const printImg = doc.createElement('img');
                    printImg.src = img.src;
                    printImg.style.maxWidth = '100%';
                    printImg.style.maxHeight = '100vh';
                    printImg.style.objectFit = 'contain';
                    doc.body.appendChild(printImg);

                    printImg.onload = function() {
                        printWindow.focus();
                        printWindow.print();
                    };
                    return;
                }
            }
        }
        window.print();
    };

    // Keyboard navigation (Esc to close)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('siom-doc-preview-modal');
            if (modal && modal.style.display !== 'none') {
                window.closeDocPreview();
            }
        }
    });
})();
</script>
