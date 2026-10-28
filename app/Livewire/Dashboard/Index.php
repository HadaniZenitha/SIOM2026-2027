<?php

namespace App\Livewire\Dashboard;

use App\Models\ApprovalStep;
use App\Models\Lpj;
use App\Models\LpjApprovalStep;
use App\Models\Period;
use App\Models\Submission;
use App\Models\User;
use App\Services\DocumentGeneratorService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFileUploads;

    public mixed $signatureFile = null;

    public function uploadSignature(): void
    {
        $user = $this->currentUser();
        abort_unless($user->hasAnyRole([
            'pembina_ormawa',
            'kaprodi',
            'kabag_kemahasiswaan',
            'warek',
        ]), 403);

        $this->validate([
            'signatureFile' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048', 'dimensions:max_width=2000,max_height=1000'],
        ]);

        $oldPath = $user->signature_path;
        $path = $this->signatureFile->store('signatures', 'local');
        if (! is_string($path) || $path === '') {
            throw new RuntimeException('Tanda tangan tidak dapat disimpan. Coba unggah kembali.');
        }

        try {
            $user->signature_path = $path;
            $user->save();
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);

            throw $exception;
        }
        if (
            $oldPath
            && $oldPath !== $path
            && ! ApprovalStep::query()->where('signature_path', $oldPath)->exists()
            && ! LpjApprovalStep::query()->where('signature_path', $oldPath)->exists()
        ) {
            Storage::disk('local')->delete($oldPath);
        }

        $this->reset('signatureFile');
        session()->flash('success', 'Tanda tangan berhasil disimpan.');
    }

    public function saveDrawnSignature(string $signatureDataUrl): void
    {
        $user = $this->currentUser();
        abort_unless($user->hasAnyRole([
            'pembina_ormawa',
            'kaprodi',
            'kabag_kemahasiswaan',
            'warek',
        ]), 403);

        if (! preg_match('/^data:image\/(png|jpeg);base64,/', $signatureDataUrl)) {
            $this->addError('signatureDrawn', 'Format tanda tangan digital tidak valid.');

            return;
        }

        $base64Data = substr($signatureDataUrl, strpos($signatureDataUrl, ',') + 1);
        $decoded = base64_decode($base64Data, true);

        if ($decoded === false || strlen($decoded) < 200) {
            $this->addError('signatureDrawn', 'Coretan tanda tangan tidak boleh kosong.');

            return;
        }

        if (strlen($decoded) > 2 * 1024 * 1024) {
            $this->addError('signatureDrawn', 'Ukuran tanda tangan digital melebihi batas 2 MB.');

            return;
        }

        $imageInfo = @getimagesizefromstring($decoded);
        if ($imageInfo === false) {
            $this->addError('signatureDrawn', 'Berkas gambar tanda tangan tidak valid.');

            return;
        }

        [$width, $height] = $imageInfo;
        if ($width > 2000 || $height > 1000) {
            $this->addError('signatureDrawn', 'Dimensi tanda tangan digital melebihi batas 2000x1000 piksel.');

            return;
        }

        $oldPath = $user->signature_path;
        $filename = 'signatures/sig_'.Str::random(36).'.png';

        try {
            Storage::disk('local')->put($filename, $decoded);
            $user->signature_path = $filename;
            $user->save();
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($filename);

            throw $exception;
        }

        if (
            $oldPath
            && $oldPath !== $filename
            && ! ApprovalStep::query()->where('signature_path', $oldPath)->exists()
            && ! LpjApprovalStep::query()->where('signature_path', $oldPath)->exists()
        ) {
            Storage::disk('local')->delete($oldPath);
        }

        session()->flash('success', 'Tanda tangan digital berhasil disimpan.');
    }

    public function downloadSignedProposal(
        int $submissionId,
        DocumentGeneratorService $generator
    ): ?BinaryFileResponse {
        $submission = Submission::query()->with('submitter')->findOrFail($submissionId);
        abort_unless($this->canAccessSubmission($submission), 403);
        abort_unless($submission->status === 'approved', 404);

        try {
            $path = $generator->generateSignedProposal($submission);
        } catch (RuntimeException $exception) {
            session()->flash('error', 'Dokumen proposal belum dapat dibuat: '.$exception->getMessage());

            return null;
        }

        return response()->download(
            Storage::disk('local')->path($path),
            'Proposal_Tertandatangani_'.str($submission->activity_name)->slug().'.docx'
        );
    }

    public function downloadSignedLpj(int $lpjId, DocumentGeneratorService $generator): ?BinaryFileResponse
    {
        $lpj = Lpj::query()->with('submission.submitter')->findOrFail($lpjId);
        abort_unless($lpj->submission && $this->canAccessSubmission($lpj->submission), 403);
        abort_unless($lpj->status === 'approved', 404);

        try {
            $path = $generator->generateSignedLpj($lpj);
        } catch (RuntimeException $exception) {
            session()->flash('error', 'Dokumen LPJ belum dapat dibuat: '.$exception->getMessage());

            return null;
        }

        return response()->download(
            Storage::disk('local')->path($path),
            'LPJ_Tertandatangani_'.str($lpj->title)->slug().'.docx'
        );
    }

    public function render(): View
    {
        $user = $this->currentUser();
        $approvedProposals = collect();
        $myDecisions = collect();
        $approvedLpjs = collect();
        $trackedSubmissions = collect();
        $trackedLpjs = collect();
        $isApprover = $user->hasAnyRole(['pembina_ormawa', 'kaprodi', 'kabag_kemahasiswaan', 'warek']);

        if ($isApprover) {
            $approvedQuery = Submission::query()
                ->with(['ormawa', 'period', 'approvalSteps.role', 'approvalSteps.approver'])
                ->where('status', 'approved');
            if ($user->hasRole('pembina_ormawa')) {
                $approvedQuery->where('ormawa_id', $user->ormawa_id);
            } elseif ($user->hasRole('kaprodi')) {
                $approvedQuery->where(function ($query) use ($user): void {
                    $query->whereHas('submitter', fn ($sub) => $sub->where('program_studi_id', $user->program_studi_id))
                        ->orWhereHas('ormawa', fn ($sub) => $sub->where('program_studi_id', $user->program_studi_id));
                });
            }
            $approvedProposals = $approvedQuery->latest('submitted_at')->limit(10)->get();

            $myDecisions = Submission::query()
                ->with(['ormawa', 'approvalSteps' => fn ($query) => $query->where('approved_by', $user->id)->with('role')])
                ->whereHas('approvalSteps', fn ($query) => $query->where('approved_by', $user->id))
                ->latest('updated_at')
                ->limit(10)
                ->get();

            $approvedLpjQuery = Lpj::query()
                ->with(['submission.submitter', 'ormawa', 'approvalSteps.role', 'approvalSteps.approver'])
                ->where('status', 'approved')
                ->latest('submitted_at');
            if ($user->hasRole('pembina_ormawa')) {
                $approvedLpjQuery->where('ormawa_id', $user->ormawa_id);
            } elseif ($user->hasRole('kaprodi')) {
                $approvedLpjQuery->where(function ($query) use ($user): void {
                    $query->whereHas('submission.submitter', fn ($sub) => $sub->where('program_studi_id', $user->program_studi_id))
                        ->orWhereHas('ormawa', fn ($sub) => $sub->where('program_studi_id', $user->program_studi_id));
                });
            }
            $approvedLpjs = $approvedLpjQuery->limit(10)->get();
        }

        if ($user->hasRole('superadmin') || $user->hasRole('sekretaris_ormawa')) {
            $trackedSubmissions = Submission::query()
                ->with(['ormawa', 'approvalSteps.role', 'approvalSteps.approver'])
                ->when(
                    $user->hasRole('sekretaris_ormawa'),
                    fn ($query) => $query->where('ormawa_id', $user->ormawa_id)
                )
                ->latest()
                ->limit(15)
                ->get();
            $trackedLpjs = Lpj::query()
                ->with(['submission', 'approvalSteps.role', 'approvalSteps.approver'])
                ->when(
                    $user->hasRole('sekretaris_ormawa'),
                    fn ($query) => $query->where('ormawa_id', $user->ormawa_id)
                )
                ->latest()
                ->limit(15)
                ->get();
        }

        $savedSignaturePreview = null;
        if ($isApprover && $user->signature_path && Storage::disk('local')->exists($user->signature_path)) {
            $savedSignaturePreview = 'data:image/png;base64,'.base64_encode((string) Storage::disk('local')->get($user->signature_path));
        }

        return view('livewire.dashboard.index', [
            'user' => $user,
            'activePeriod' => Period::query()
                ->where('status', 'active')
                ->orderByDesc('start_date')
                ->first(),
            'approvedProposals' => $approvedProposals,
            'myDecisions' => $myDecisions,
            'approvedLpjs' => $approvedLpjs,
            'trackedSubmissions' => $trackedSubmissions,
            'trackedLpjs' => $trackedLpjs,
            'isApprover' => $isApprover,
            'savedSignaturePreview' => $savedSignaturePreview,
        ]);
    }

    private function canAccessSubmission(Submission $submission): bool
    {
        $user = $this->currentUser();

        return $user->hasRole('superadmin')
            || ($user->hasRole('sekretaris_ormawa') && $submission->ormawa_id === $user->ormawa_id)
            || ($user->hasRole('pembina_ormawa') && $submission->ormawa_id === $user->ormawa_id)
            || ($user->hasRole('kaprodi') && (
                $submission->submitter?->program_studi_id === $user->program_studi_id
                || $submission->ormawa?->program_studi_id === $user->program_studi_id
            ))
            || $user->hasAnyRole(['kabag_kemahasiswaan', 'warek']);
    }

    private function currentUser(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        return $user;
    }
}
