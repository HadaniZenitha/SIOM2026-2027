<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Lpj;
use App\Models\LpjDocument;
use App\Models\Submission;
use App\Models\User;
use App\Services\DocumentGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentPreviewController extends Controller
{
    public function __construct(
        protected DocumentGeneratorService $generator
    ) {}

    /**
     * Stream file proposal (Word/PDF) secara inline untuk preview di browser.
     */
    public function streamProposal(Submission $submission): Response
    {
        $this->authorizeSubmissionAccess($submission);

        $uploaded = $submission->documents()
            ->where('type', 'proposal')
            ->latest('id')
            ->first();

        if ($uploaded && Storage::disk('local')->exists($uploaded->file_path)) {
            $path = Storage::disk('local')->path($uploaded->file_path);

            return $this->inlineResponse($path, $uploaded->file_name);
        }

        try {
            $relPath = $this->generator->generateProposal($submission);
            $fullPath = Storage::disk('public')->path($relPath);
            $filename = 'Proposal_'.Str::slug($submission->activity_name).'.docx';

            return $this->inlineResponse($fullPath, $filename);
        } catch (RuntimeException $e) {
            abort(500, 'Gagal menghasilkan dokumen proposal: '.$e->getMessage());
        }
    }

    /**
     * Stream file Nota Dinas BEM secara inline.
     */
    public function streamNotaDinas(Submission $submission): Response
    {
        $this->authorizeSubmissionAccess($submission);

        try {
            $relPath = $this->generator->generateNotaDinas($submission);
            $fullPath = Storage::disk('public')->path($relPath);
            $filename = 'Nota_Dinas_'.Str::slug($submission->activity_name).'.docx';

            return $this->inlineResponse($fullPath, $filename);
        } catch (RuntimeException $e) {
            abort(500, 'Gagal menghasilkan dokumen Nota Dinas: '.$e->getMessage());
        }
    }

    /**
     * Stream file proposal bertanda tangan digital secara inline.
     */
    public function streamSignedProposal(Submission $submission): Response
    {
        $this->authorizeSubmissionAccess($submission);
        abort_unless($submission->status === 'approved', 404, 'Proposal belum disetujui penuh.');

        try {
            $relPath = $this->generator->generateSignedProposal($submission);
            $fullPath = Storage::disk('local')->path($relPath);
            $filename = 'Proposal_Tertandatangani_'.Str::slug($submission->activity_name).'.docx';

            return $this->inlineResponse($fullPath, $filename);
        } catch (RuntimeException $e) {
            abort(500, 'Gagal menghasilkan proposal bertanda tangan: '.$e->getMessage());
        }
    }

    /**
     * Stream file LPJ secara inline.
     */
    public function streamLpj(Lpj $lpj): Response
    {
        $this->authorizeLpjAccess($lpj);

        try {
            $relPath = $this->generator->generateLpj($lpj);
            $fullPath = Storage::disk('public')->path($relPath);
            $filename = 'LPJ_'.Str::slug($lpj->title).'.docx';

            return $this->inlineResponse($fullPath, $filename);
        } catch (RuntimeException $e) {
            abort(500, 'Gagal menghasilkan dokumen LPJ: '.$e->getMessage());
        }
    }

    /**
     * Stream file LPJ bertanda tangan digital secara inline.
     */
    public function streamSignedLpj(Lpj $lpj): Response
    {
        $this->authorizeLpjAccess($lpj);
        abort_unless($lpj->status === 'approved', 404, 'LPJ belum disetujui penuh.');

        try {
            $relPath = $this->generator->generateSignedLpj($lpj);
            $fullPath = Storage::disk('local')->path($relPath);
            $filename = 'LPJ_Tertandatangani_'.Str::slug($lpj->title).'.docx';

            return $this->inlineResponse($fullPath, $filename);
        } catch (RuntimeException $e) {
            abort(500, 'Gagal menghasilkan LPJ bertanda tangan: '.$e->getMessage());
        }
    }

    /**
     * Stream file lampiran (nota, dokumentasi, bukti) secara inline.
     * Mendukung Document (Proposal) maupun LpjDocument (LPJ).
     */
    public function streamAttachment(int|string $id): Response
    {
        $doc = Document::find($id) ?? LpjDocument::find($id);
        abort_unless($doc, 404, 'Berkas lampiran tidak ditemukan.');

        $this->authorizeAttachmentAccess($doc);

        $fullPath = $this->resolveFilePath($doc->file_path);
        abort_unless($fullPath && file_exists($fullPath), 404, 'Berkas fisik lampiran tidak ditemukan di penyimpanan server.');

        return $this->inlineResponse($fullPath, $doc->file_name);
    }

    /**
     * Halaman penuh untuk preview lampiran dokumen (PNG, PDF, DOCX).
     */
    public function viewAttachment(int|string $id)
    {
        $doc = Document::find($id) ?? LpjDocument::find($id);
        abort_unless($doc, 404, 'Berkas lampiran tidak ditemukan.');

        $this->authorizeAttachmentAccess($doc);

        $fileUrl = route('documents.preview.attachment.file', $doc->id);
        $downloadUrl = route('documents.preview.attachment.file', $doc->id);
        $ext = strtolower(pathinfo($doc->file_name, PATHINFO_EXTENSION));

        $format = match ($ext) {
            'pdf' => 'pdf',
            'png', 'jpg', 'jpeg', 'webp', 'gif', 'svg' => 'image',
            default => 'docx',
        };

        $submission = null;
        $lpj = null;
        $status = 'uploaded';

        if ($doc instanceof Document) {
            $submission = $doc->submission;
            $status = $submission?->status ?? 'uploaded';
        } elseif ($doc instanceof LpjDocument) {
            $lpj = $doc->lpj;
            $submission = $lpj?->submission;
            $status = $lpj?->status ?? 'uploaded';
        }

        return view('documents.preview', [
            'title' => 'Lampiran: '.$doc->file_name,
            'documentType' => 'Lampiran Berkas ('.strtoupper($ext ?: 'File').')',
            'status' => $status,
            'fileUrl' => $fileUrl,
            'downloadUrl' => $downloadUrl,
            'submission' => $submission,
            'lpj' => $lpj,
            'format' => $format,
            'isBemOrDpm' => $submission?->ormawa && ($submission->ormawa->isBem() || $submission->ormawa->isDpm()),
        ]);
    }

    /**
     * Halaman penuh (standalone page) untuk preview proposal.
     */
    public function viewProposal(Submission $submission)
    {
        $this->authorizeSubmissionAccess($submission);
        $submission->loadMissing(['ormawa', 'period', 'items', 'rundowns', 'committees', 'timelines', 'approvalSteps.role', 'approvalSteps.approver']);

        $uploaded = $submission->documents()
            ->where('type', 'proposal')
            ->latest('id')
            ->first();

        $ext = $uploaded ? strtolower(pathinfo($uploaded->file_name, PATHINFO_EXTENSION)) : 'docx';
        $format = match ($ext) {
            'pdf' => 'pdf',
            'png', 'jpg', 'jpeg', 'webp', 'gif', 'svg' => 'image',
            default => 'docx',
        };

        $fileUrl = route('documents.preview.proposal.file', $submission);
        $downloadUrl = route('documents.preview.proposal.file', $submission);

        return view('documents.preview', [
            'title' => 'Proposal: '.$submission->activity_name,
            'documentType' => 'Proposal Kegiatan',
            'status' => $submission->status,
            'fileUrl' => $fileUrl,
            'downloadUrl' => $downloadUrl,
            'submission' => $submission,
            'lpj' => null,
            'format' => $format,
            'isBemOrDpm' => $submission->ormawa && ($submission->ormawa->isBem() || $submission->ormawa->isDpm()),
        ]);
    }

    /**
     * Halaman penuh untuk preview Nota Dinas BEM.
     */
    public function viewNotaDinas(Submission $submission)
    {
        $this->authorizeSubmissionAccess($submission);
        $submission->loadMissing(['ormawa', 'period', 'items', 'approvalSteps.role', 'approvalSteps.approver']);

        $fileUrl = route('documents.preview.nota-dinas.file', $submission);
        $downloadUrl = route('documents.preview.nota-dinas.file', $submission);

        return view('documents.preview', [
            'title' => 'Nota Dinas: '.$submission->activity_name,
            'documentType' => 'Nota Dinas Pengajuan Dana',
            'status' => $submission->status,
            'fileUrl' => $fileUrl,
            'downloadUrl' => $downloadUrl,
            'submission' => $submission,
            'lpj' => null,
            'format' => 'docx',
            'isBemOrDpm' => true,
        ]);
    }

    /**
     * Halaman penuh untuk preview proposal bertanda tangan.
     */
    public function viewSignedProposal(Submission $submission)
    {
        $this->authorizeSubmissionAccess($submission);
        abort_unless($submission->status === 'approved', 404);
        $submission->loadMissing(['ormawa', 'period', 'items', 'rundowns', 'committees', 'timelines', 'approvalSteps.role', 'approvalSteps.approver']);

        $fileUrl = route('documents.preview.signed-proposal.file', $submission);
        $downloadUrl = route('documents.preview.signed-proposal.file', $submission);

        return view('documents.preview', [
            'title' => 'Proposal Tertandatangani: '.$submission->activity_name,
            'documentType' => 'Proposal Bertanda Tangan Digital',
            'status' => $submission->status,
            'fileUrl' => $fileUrl,
            'downloadUrl' => $downloadUrl,
            'submission' => $submission,
            'lpj' => null,
            'format' => 'docx',
            'isBemOrDpm' => $submission->ormawa && ($submission->ormawa->isBem() || $submission->ormawa->isDpm()),
        ]);
    }

    /**
     * Halaman penuh untuk preview LPJ.
     */
    public function viewLpj(Lpj $lpj)
    {
        $this->authorizeLpjAccess($lpj);
        $lpj->loadMissing(['submission.ormawa', 'period', 'items', 'documents', 'approvalSteps.role', 'approvalSteps.approver']);

        $format = 'docx';
        $reportDoc = $lpj->documents()->where('type', 'laporan')->latest('id')->first();
        if ($reportDoc) {
            $ext = strtolower(pathinfo($reportDoc->file_name, PATHINFO_EXTENSION));
            if ($ext === 'pdf') {
                $format = 'pdf';
            } elseif (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg'], true)) {
                $format = 'image';
            }
        }

        $fileUrl = route('documents.preview.lpj.file', $lpj);
        $downloadUrl = route('documents.preview.lpj.file', $lpj);

        return view('documents.preview', [
            'title' => 'LPJ: '.$lpj->title,
            'documentType' => 'Laporan Pertanggungjawaban',
            'status' => $lpj->status,
            'fileUrl' => $fileUrl,
            'downloadUrl' => $downloadUrl,
            'submission' => $lpj->submission,
            'lpj' => $lpj,
            'format' => $format,
            'isBemOrDpm' => $lpj->submission?->ormawa && ($lpj->submission->ormawa->isBem() || $lpj->submission->ormawa->isDpm()),
        ]);
    }

    /**
     * Halaman penuh untuk preview LPJ bertanda tangan.
     */
    public function viewSignedLpj(Lpj $lpj)
    {
        $this->authorizeLpjAccess($lpj);
        abort_unless($lpj->status === 'approved', 404);
        $lpj->loadMissing(['submission.ormawa', 'period', 'items', 'documents', 'approvalSteps.role', 'approvalSteps.approver']);

        $fileUrl = route('documents.preview.signed-lpj.file', $lpj);
        $downloadUrl = route('documents.preview.signed-lpj.file', $lpj);

        return view('documents.preview', [
            'title' => 'LPJ Tertandatangani: '.$lpj->title,
            'documentType' => 'LPJ Bertanda Tangan Digital',
            'status' => $lpj->status,
            'fileUrl' => $fileUrl,
            'downloadUrl' => $downloadUrl,
            'submission' => $lpj->submission,
            'lpj' => $lpj,
            'format' => 'docx',
            'isBemOrDpm' => $lpj->submission?->ormawa && ($lpj->submission->ormawa->isBem() || $lpj->submission->ormawa->isDpm()),
        ]);
    }

    protected function resolveFilePath(?string $filePath): ?string
    {
        if (! $filePath) {
            return null;
        }

        if (Storage::disk('local')->exists($filePath)) {
            return Storage::disk('local')->path($filePath);
        }

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->path($filePath);
        }

        $candidates = [
            storage_path('app/'.$filePath),
            storage_path('app/private/'.$filePath),
            storage_path('app/public/'.$filePath),
            public_path($filePath),
        ];

        foreach ($candidates as $cand) {
            if (file_exists($cand)) {
                return $cand;
            }
        }

        return null;
    }

    protected function inlineResponse(string $absolutePath, string $filename): StreamedResponse
    {
        abort_unless(file_exists($absolutePath), 404, 'File dokumen tidak ditemukan.');

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $contentType = match ($extension) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'gif' => 'image/gif',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'doc' => 'application/msword',
            default => 'application/octet-stream',
        };

        $fileSize = filesize($absolutePath);
        $headers = [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ];

        if ($fileSize !== false && $fileSize > 0) {
            $headers['Content-Length'] = (string) $fileSize;
        }

        return response()->stream(function () use ($absolutePath) {
            $stream = fopen($absolutePath, 'rb');
            if ($stream !== false) {
                fpassthru($stream);
                fclose($stream);
            }
        }, 200, $headers);
    }

    protected function authorizeAttachmentAccess(Document|LpjDocument $doc): void
    {
        if ($doc instanceof Document && $doc->submission) {
            $this->authorizeSubmissionAccess($doc->submission);

            return;
        }

        if ($doc instanceof LpjDocument && $doc->lpj) {
            $this->authorizeLpjAccess($doc->lpj);

            return;
        }

        $user = Auth::user();
        abort_unless($user instanceof User, 401);
        abort_unless($user->hasRole('superadmin'), 403, 'Anda tidak memiliki hak akses untuk melihat lampiran ini.');
    }

    protected function authorizeSubmissionAccess(Submission $submission): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        $hasAccess = $user->hasRole('superadmin')
            || ($user->hasRole('sekretaris_ormawa') && $submission->ormawa_id === $user->ormawa_id)
            || ($user->hasRole('pembina_ormawa') && $submission->ormawa_id === $user->ormawa_id)
            || ($user->hasRole('kaprodi') && (
                $submission->submitter?->program_studi_id === $user->program_studi_id
                || $submission->ormawa?->program_studi_id === $user->program_studi_id
            ))
            || $user->hasAnyRole(['kabag_kemahasiswaan', 'warek'])
            || $submission->approvalSteps()->where('role_id', $user->role_id)->exists();

        abort_unless($hasAccess, 403, 'Anda tidak memiliki hak akses untuk melihat dokumen ini.');
    }

    protected function authorizeLpjAccess(Lpj $lpj): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        if ($lpj->submission) {
            $this->authorizeSubmissionAccess($lpj->submission);

            return;
        }

        $hasAccess = $user->hasRole('superadmin')
            || ($user->hasRole('sekretaris_ormawa') && $lpj->ormawa_id === $user->ormawa_id)
            || ($user->hasRole('pembina_ormawa') && $lpj->ormawa_id === $user->ormawa_id)
            || $user->hasAnyRole(['kabag_kemahasiswaan', 'warek'])
            || $lpj->approvalSteps()->where('role_id', $user->role_id)->exists();

        abort_unless($hasAccess, 403, 'Anda tidak memiliki hak akses untuk melihat dokumen ini.');
    }
}
