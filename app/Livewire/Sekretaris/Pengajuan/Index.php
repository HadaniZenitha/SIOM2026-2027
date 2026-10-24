<?php

namespace App\Livewire\Sekretaris\Pengajuan;

use App\Models\Submission;
use App\Services\DocumentGeneratorService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function deleteDraft(int $submissionId): void
    {
        $submission = $this->findSubmission($submissionId);
        abort_unless(in_array($submission->status, ['draft', 'revision'], true), 403);
        abort_if($submission->approvalSteps()->exists(), 403);
        $submission->delete();
        session()->flash('success', 'Draft pengajuan berhasil dihapus.');
    }

    public function render(): View
    {
        $submissions = Submission::query()
            ->with([
                'period',
                'ormawa',
                'submitter',
                'approvalSteps.role',
                'approvalSteps.approver',
            ])
            ->where('ormawa_id', Auth::user()->ormawa_id)
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query
                        ->where('submission_number', 'like', '%'.$this->search.'%')
                        ->orWhere('activity_name', 'like', '%'.$this->search.'%');
                });
            })
            ->latest()
            ->paginate(10);

        return view(
            'livewire.sekretaris.pengajuan.index',
            compact('submissions')
        );
    }

    public function downloadProposal(
        int $submissionId,
        DocumentGeneratorService $generator
    ): ?BinaryFileResponse {
        $submission = $this->findSubmission($submissionId);

        try {
            $uploadedProposal = $submission->documents()
                ->where('type', 'proposal')
                ->latest('id')
                ->first();
            if ($uploadedProposal) {
                abort_unless(Storage::disk('local')->exists($uploadedProposal->file_path), 404);

                return response()->download(
                    Storage::disk('local')->path($uploadedProposal->file_path),
                    $uploadedProposal->file_name
                );
            }

            $filePath = $generator->generateProposal($submission);

            return response()->download(
                Storage::disk('public')->path($filePath),
                'Proposal_'.str($submission->activity_name)->slug().'.docx'
            );
        } catch (RuntimeException $exception) {
            session()->flash('error', 'Gagal mengunduh proposal: '.$exception->getMessage());

            return null;
        }
    }

    public function downloadSignedProposal(
        int $submissionId,
        DocumentGeneratorService $generator
    ): ?BinaryFileResponse {
        $submission = $this->findSubmission($submissionId);
        abort_unless($submission->status === 'approved', 404);

        try {
            $path = $generator->generateSignedProposal($submission);

            return response()->download(
                Storage::disk('local')->path($path),
                'Proposal_Tertandatangani_'.str($submission->activity_name)->slug().'.docx'
            );
        } catch (RuntimeException $exception) {
            session()->flash('error', 'Gagal mengunduh proposal bertanda tangan: '.$exception->getMessage());

            return null;
        }
    }

    protected function findSubmission(int $submissionId): Submission
    {
        return Submission::query()
            ->where('ormawa_id', Auth::user()->ormawa_id)
            ->findOrFail($submissionId);
    }
}
