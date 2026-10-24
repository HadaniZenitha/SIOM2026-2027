<?php

namespace App\Livewire\Approval;

use App\Models\Lpj;
use App\Services\LpjApprovalService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use RuntimeException;

#[Layout('layouts.app')]
class LpjShow extends Component
{
    #[Locked]
    public Lpj $lpj;

    public string $note = '';

    public function mount(Lpj $lpj): void
    {
        abort_unless($lpj->approvalSteps()->where('role_id', Auth::user()->role_id)->exists(), 404);
        $this->lpj = $lpj;
    }

    public function approve(LpjApprovalService $service): void
    {
        $this->decide($service, 'approved', false);
    }

    public function reject(LpjApprovalService $service): void
    {
        $this->decide($service, 'rejected', true);
    }

    public function requestRevision(LpjApprovalService $service): void
    {
        $this->decide($service, 'revision', true);
    }

    public function download(int $documentId)
    {
        $document = $this->lpj->documents()->findOrFail($documentId);
        abort_unless(Storage::disk('local')->exists($document->file_path), 404);

        return Storage::disk('local')->download($document->file_path, $document->file_name);
    }

    private function decide(LpjApprovalService $service, string $action, bool $requiresNote): void
    {
        $this->validate(['note' => [$requiresNote ? 'required' : 'nullable', 'string', 'max:2000']]);

        try {
            $service->decide($this->lpj, Auth::user(), $action, $this->note);
        } catch (RuntimeException $exception) {
            $this->addError('approval', $exception->getMessage());

            return;
        }

        session()->flash('success', 'Keputusan LPJ berhasil disimpan.');
        $this->redirectRoute('approval.index');
    }

    public function render(): View
    {
        $this->lpj->load(['period', 'ormawa', 'creator', 'submission', 'documents', 'approvalSteps.role', 'approvalSteps.approver', 'approvalLogs.user']);
        $activeStep = $this->lpj->approvalSteps->firstWhere('status', 'pending');

        return view('livewire.approval.lpj-show', [
            'activeStep' => $activeStep,
            'canDecide' => $activeStep?->role_id === Auth::user()->role_id,
        ]);
    }
}
