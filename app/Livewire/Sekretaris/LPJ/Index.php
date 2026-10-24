<?php

namespace App\Livewire\Sekretaris\LPJ;

use App\Models\Lpj;
use App\Models\User;
use App\Services\DocumentGeneratorService;
use App\Services\LpjApprovalService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
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

    public function delete(int $id): void
    {
        $this->authorizeSecretary();
        $lpj = $this->lpjsQuery()->findOrFail($id);
        abort_unless(in_array($lpj->status, ['draft', 'revision'], true), 403);
        $lpj->delete();
        session()->flash('success', 'LPJ berhasil dihapus.');
    }

    public function submit(int $id): void
    {
        $this->authorizeSecretary();
        $lpj = $this->lpjsQuery()->with('documents')->findOrFail($id);
        abort_unless(in_array($lpj->status, ['draft', 'revision'], true), 403);
        if (
            ! $lpj->documents->contains(fn ($document): bool => in_array($document->type, ['nota', 'keuangan'], true))
            || ! $lpj->documents->contains('type', 'dokumentasi')
        ) {
            $this->addError('documents', 'Unggah minimal satu scan nota dan satu dokumentasi kegiatan sebelum mengirim.');

            return;
        }

        app(LpjApprovalService::class)->submit($lpj);
        session()->flash('success', 'LPJ berhasil dikirim untuk persetujuan.');
    }

    public function downloadSigned(int $id, DocumentGeneratorService $generator): ?BinaryFileResponse
    {
        $this->authorizeSecretary();
        $lpj = $this->lpjsQuery()->findOrFail($id);
        abort_unless($lpj->status === 'approved', 404);

        try {
            $path = $generator->generateSignedLpj($lpj);
        } catch (RuntimeException $exception) {
            session()->flash('error', 'Gagal mengunduh LPJ bertanda tangan: '.$exception->getMessage());

            return null;
        }

        return response()->download(
            Storage::disk('local')->path($path),
            'LPJ_Tertandatangani_'.str($lpj->title)->slug().'.docx'
        );
    }

    public function render(): View
    {
        $this->authorizeSecretary();

        return view('livewire.sekretaris.l-p-j.index', [
            'lpjs' => $this->lpjsQuery()->with(['period', 'submission', 'documents', 'approvalSteps.role'])
                ->latest()->paginate(10),
        ]);
    }

    private function lpjsQuery(): Builder
    {
        return Lpj::query()->where('ormawa_id', Auth::user()->ormawa_id);
    }

    private function authorizeSecretary(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('sekretaris_ormawa') && $user->ormawa_id, 403);

        return $user;
    }
}
