<?php

namespace App\Livewire\Approval;

use App\Models\Submission;
use App\Services\ApprovalService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use RuntimeException;

#[Layout('layouts.app')]
class Show extends Component
{
    #[Locked]
    public Submission $submission;

    public string $note = '';

    public function mount(Submission $submission): void
    {
        abort_unless(
            $submission->approvalSteps()
                ->where('role_id', Auth::user()->role_id)
                ->exists(),
            404
        );

        $this->submission = $submission;
    }

    public function approve(ApprovalService $approvalService): void
    {
        $this->validate([
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $approvalService->approve($this->submission, Auth::user(), $this->note);
        } catch (RuntimeException $exception) {
            $this->addError('approval', $exception->getMessage());

            return;
        }

        session()->flash('success', 'Pengajuan berhasil disetujui.');
        $this->redirectRoute('approval.index');
    }

    public function reject(ApprovalService $approvalService): void
    {
        $this->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $approvalService->reject($this->submission, Auth::user(), $this->note);
        } catch (RuntimeException $exception) {
            $this->addError('approval', $exception->getMessage());

            return;
        }

        session()->flash('success', 'Pengajuan berhasil ditolak.');
        $this->redirectRoute('approval.index');
    }

    public function requestRevision(ApprovalService $approvalService): void
    {
        $this->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $approvalService->requestRevision($this->submission, Auth::user(), $this->note);
        } catch (RuntimeException $exception) {
            $this->addError('approval', $exception->getMessage());

            return;
        }

        session()->flash('success', 'Permintaan revisi berhasil dikirim.');
        $this->redirectRoute('approval.index');
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $this->submission->load([
            'period',
            'ormawa',
            'submitter',
            'items',
            'timelines',
            'committees',
            'rundowns',
            'approvalSteps.role',
            'approvalSteps.approver',
            'approvalLogs.user',
        ]);

        $activeStep = $this->submission->approvalSteps
            ->firstWhere('status', 'pending');

        return view('livewire.approval.show', [
            'activeStep' => $activeStep,
            'canDecide' => $activeStep?->role_id === Auth::user()->role_id,
        ]);
    }
}
