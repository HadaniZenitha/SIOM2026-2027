<?php

namespace App\Livewire\Sekretaris\LPJ;

use App\Models\Lpj;
use App\Models\Submission;
use App\Models\User;
use App\Services\DocumentGeneratorService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Layout('layouts.app')]
class Form extends Component
{
    use WithFileUploads;

    public ?int $lpjId = null;

    public ?int $submission_id = null;

    public string $title = '';

    public string $description = '';

    /**
     * @var list<array{submission_item_id: ?int, item_name: string, qty: int|float|string, unit: string, price: int|float|string}>
     */
    public array $budgetItems = [];

    public array $receiptFiles = [];

    public array $activityFiles = [];

    public mixed $report_file = null;

    public mixed $financial_file = null;

    public function mount(?Lpj $lpj = null): void
    {
        $this->authorizeSecretary();

        if (! $lpj?->exists) {
            return;
        }

        $lpj = $this->lpjsQuery()->with(['submission.items', 'items', 'documents'])->findOrFail($lpj->id);
        abort_unless(in_array($lpj->status, ['draft', 'revision'], true), 403);

        $this->lpjId = $lpj->id;
        $this->submission_id = $lpj->submission_id;
        $this->title = $lpj->title;
        $this->description = $lpj->description ?? '';
        $lpjItems = $lpj->items()->get();
        $this->budgetItems = $lpjItems->isNotEmpty()
            ? $lpjItems->map(fn ($item): array => [
                'submission_item_id' => $item->submission_item_id,
                'item_name' => $item->item_name,
                'qty' => $item->qty,
                'unit' => $item->unit ?? '',
                'price' => $item->price,
            ])->all()
            : $this->proposalBudgetItems($lpj->submission);
    }

    public function updatedSubmissionId(): void
    {
        $this->authorizeSecretary();

        if (! $this->submission_id) {
            $this->budgetItems = [];
            $this->title = '';

            return;
        }

        $submission = $this->approvedSubmissionsQuery()->with('items')->findOrFail($this->submission_id);
        $this->budgetItems = $this->proposalBudgetItems($submission);
        $this->title = 'LPJ '.$submission->activity_name;
    }

    public function addBudgetItem(): void
    {
        $this->budgetItems[] = [
            'submission_item_id' => null,
            'item_name' => '',
            'qty' => 1,
            'unit' => '',
            'price' => 0,
        ];
    }

    public function removeBudgetItem(int $index): void
    {
        if (count($this->budgetItems) > 1 && array_key_exists($index, $this->budgetItems)) {
            unset($this->budgetItems[$index]);
            $this->budgetItems = array_values($this->budgetItems);
        }
    }

    public function removeDocument(int $documentId): void
    {
        $this->authorizeSecretary();
        $lpj = $this->lpjsQuery()->findOrFail($this->lpjId);
        abort_unless(in_array($lpj->status, ['draft', 'revision'], true), 403);

        $document = $lpj->documents()->findOrFail($documentId);
        Storage::disk('local')->delete($document->file_path);
        $document->delete();
    }

    public function downloadDocument(int $documentId): BinaryFileResponse
    {
        $this->authorizeSecretary();
        $lpj = $this->lpjsQuery()->findOrFail($this->lpjId);
        $document = $lpj->documents()->findOrFail($documentId);
        abort_unless(Storage::disk('local')->exists($document->file_path), 404);

        return response()->download(
            Storage::disk('local')->path($document->file_path),
            $document->file_name
        );
    }

    public function save(): void
    {
        $user = $this->authorizeSecretary();
        $data = $this->validate([
            'submission_id' => [
                'required',
                'integer',
                Rule::unique('lpjs', 'submission_id')->ignore($this->lpjId),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'budgetItems' => ['required', 'array', 'min:1', 'max:100'],
            'budgetItems.*.submission_item_id' => ['nullable', 'integer', 'exists:submission_items,id'],
            'budgetItems.*.item_name' => ['required', 'string', 'max:255'],
            'budgetItems.*.qty' => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99'],
            'budgetItems.*.unit' => ['nullable', 'string', 'max:50'],
            'budgetItems.*.price' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'receiptFiles' => ['nullable', 'array', 'max:20'],
            'receiptFiles.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'activityFiles' => ['nullable', 'array', 'max:20'],
            'activityFiles.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'report_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'financial_file' => ['nullable', 'file', 'mimes:pdf,xls,xlsx,csv', 'max:10240'],
        ]);

        $submission = $this->approvedSubmissionsQuery()->findOrFail($data['submission_id']);
        $proposalItemIds = $submission->items()->get()->modelKeys();

        foreach ($data['budgetItems'] as $index => $budgetItem) {
            if (
                $budgetItem['submission_item_id'] !== null
                && ! in_array((int) $budgetItem['submission_item_id'], $proposalItemIds, true)
            ) {
                throw ValidationException::withMessages([
                    "budgetItems.{$index}.submission_item_id" => 'Item anggaran harus berasal dari proposal yang dipilih.',
                ]);
            }
        }

        DB::transaction(function () use ($data, $user, $submission): void {
            $lpj = $this->lpjId
                ? $this->lpjsQuery()->findOrFail($this->lpjId)
                : new Lpj;
            abort_unless(! $lpj->exists || in_array($lpj->status, ['draft', 'revision'], true), 403);

            $lpj->fill([
                'submission_id' => $submission->id,
                'period_id' => $submission->period_id,
                'ormawa_id' => $user->ormawa_id,
                'created_by' => $lpj->created_by ?? $user->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);
            $lpj->status = $lpj->status === 'revision' ? 'revision' : 'draft';
            $lpj->save();
            $lpj->items()->delete();

            foreach ($data['budgetItems'] as $index => $item) {
                $lpj->items()->create([
                    'submission_item_id' => $item['submission_item_id'] ?? null,
                    'item_name' => $item['item_name'],
                    'qty' => $item['qty'],
                    'unit' => $item['unit'] ?? null,
                    'price' => $item['price'],
                    'total_price' => (float) $item['qty'] * (float) $item['price'],
                    'sort_order' => $index,
                ]);
            }

            $uploads = [
                'receiptFiles' => 'nota',
                'activityFiles' => 'dokumentasi',
                'report_file' => 'laporan',
                'financial_file' => 'keuangan',
            ];

            foreach ($uploads as $property => $type) {
                $files = is_array($this->{$property}) ? $this->{$property} : [$this->{$property}];

                foreach ($files as $file) {
                    if (! $file) {
                        continue;
                    }

                    $path = $file->store('lpj/'.$lpj->id, 'local');
                    $lpj->documents()->create([
                        'type' => $type,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                }
            }

            $this->lpjId = $lpj->id;
        });

        $this->reset(['receiptFiles', 'activityFiles', 'report_file', 'financial_file']);
        session()->flash('success', 'LPJ berhasil disimpan sebagai draft.');
        $this->redirectRoute('sekretaris.lpj.edit', $this->lpjId);
    }

    public function downloadWord(DocumentGeneratorService $generator): ?BinaryFileResponse
    {
        $this->authorizeSecretary();

        if (! $this->lpjId) {
            $this->addError('document', 'Simpan draft LPJ terlebih dahulu sebelum mengunduh dokumen Word.');

            return null;
        }

        $lpj = $this->lpjsQuery()->with('submission')->findOrFail($this->lpjId);

        try {
            $filePath = $generator->generateLpj($lpj);

            return response()->download(
                Storage::disk('public')->path($filePath),
                'LPJ_'.str($lpj->title)->slug().'.docx'
            );
        } catch (RuntimeException $exception) {
            $this->addError('document', $exception->getMessage());

            return null;
        }
    }

    public function render(): View
    {
        $this->authorizeSecretary();

        return view('livewire.sekretaris.l-p-j.form', [
            'submissions' => $this->approvedSubmissionsQuery()->with('period')->latest()->get(),
            'documents' => $this->lpjId
                ? $this->lpjsQuery()->findOrFail($this->lpjId)->documents
                : collect(),
            'grandTotal' => collect($this->budgetItems)->sum(
                fn (array $item): float => (float) ($item['qty'] ?? 0) * (float) ($item['price'] ?? 0)
            ),
        ]);
    }

    private function lpjsQuery(): Builder
    {
        return Lpj::query()->where('ormawa_id', Auth::user()->ormawa_id);
    }

    private function approvedSubmissionsQuery(): Builder
    {
        return Submission::query()
            ->where('ormawa_id', Auth::user()->ormawa_id)
            ->where('status', 'approved');
    }

    private function proposalBudgetItems(Submission $submission): array
    {
        return $submission->items->map(fn ($item): array => [
            'submission_item_id' => $item->id,
            'item_name' => $item->item_name,
            'qty' => $item->qty,
            'unit' => $item->unit ?? '',
            'price' => $item->price,
        ])->all();
    }

    private function authorizeSecretary(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('sekretaris_ormawa') && $user->ormawa_id, 403);

        return $user;
    }
}
