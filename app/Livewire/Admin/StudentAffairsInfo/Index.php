<?php

namespace App\Livewire\Admin\StudentAffairsInfo;

use App\Models\StudentAffairsInformation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use RuntimeException;
use Throwable;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFileUploads;

    private const CATEGORIES = [
        'surat_edaran',
        'surat_keputusan',
        'pengumuman',
        'informasi_lainnya',
    ];

    public bool $showForm = false;

    #[Locked]
    public ?int $informationId = null;

    public string $category = 'pengumuman';

    public string $title = '';

    public string $summary = '';

    public ?string $publishedAt = null;

    public $document = null;

    public bool $isPublished = false;

    public function mount(): void
    {
        $this->authorizeSuperadmin();
    }

    protected function rules(): array
    {
        return [
            'category' => ['required', 'in:'.implode(',', self::CATEGORIES)],
            'title' => ['required', 'string', 'max:200'],
            'summary' => ['required', 'string', 'max:5000'],
            'publishedAt' => ['nullable', 'date'],
            'document' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'isPublished' => ['boolean'],
        ];
    }

    public function render(): View
    {
        $this->authorizeSuperadmin();
        $informations = StudentAffairsInformation::query()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();
        $categories = $this->categories();

        return view('livewire.admin.student-affairs-info.index', compact('categories', 'informations'));
    }

    public function create(): void
    {
        $this->authorizeSuperadmin();
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeSuperadmin();
        $information = StudentAffairsInformation::query()->findOrFail($id);

        $this->informationId = $information->id;
        $this->category = $information->category;
        $this->title = $information->title;
        $this->summary = $information->summary;
        $this->publishedAt = $information->published_at?->format('Y-m-d');
        $this->isPublished = $information->is_published;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->authorizeSuperadmin();
        $validated = $this->validate();
        $information = $this->informationId
            ? StudentAffairsInformation::query()->findOrFail($this->informationId)
            : new StudentAffairsInformation;
        $oldDocumentPath = $information->document_path;
        $newDocumentPath = null;

        if ($this->document) {
            $newDocumentPath = $this->document->store('student-affairs', 'local');
            if (! is_string($newDocumentPath)) {
                throw new RuntimeException('Dokumen informasi gagal disimpan ke penyimpanan.');
            }
        }

        $information->fill([
            'category' => $validated['category'],
            'title' => $validated['title'],
            'summary' => $validated['summary'],
            'published_at' => $validated['publishedAt'],
            'is_published' => $validated['isPublished'],
        ]);

        if ($newDocumentPath) {
            $information->document_path = $newDocumentPath;
            $information->document_name = $this->document->getClientOriginalName();
        }

        try {
            $information->save();
        } catch (Throwable $exception) {
            if ($newDocumentPath) {
                Storage::disk('local')->delete($newDocumentPath);
            }

            throw $exception;
        }

        if ($newDocumentPath && $oldDocumentPath) {
            $this->deleteStoredDocument($oldDocumentPath);
        }

        session()->flash(
            'success',
            $this->informationId
                ? 'Informasi kemahasiswaan berhasil diperbarui.'
                : 'Informasi kemahasiswaan berhasil ditambahkan.'
        );

        $this->resetForm();
    }

    public function togglePublished(int $id): void
    {
        $this->authorizeSuperadmin();
        $information = StudentAffairsInformation::query()->findOrFail($id);
        $information->is_published = ! $information->is_published;
        $information->save();

        session()->flash(
            'success',
            $information->is_published
                ? 'Informasi berhasil ditampilkan di landing page.'
                : 'Informasi disembunyikan dari landing page.'
        );
    }

    public function delete(int $id): void
    {
        $this->authorizeSuperadmin();
        $information = StudentAffairsInformation::query()->findOrFail($id);
        $documentPath = $information->document_path;
        $information->delete();

        if ($documentPath) {
            $this->deleteStoredDocument($documentPath);
        }

        if ($this->informationId === $id) {
            $this->resetForm();
        }

        session()->flash('success', 'Informasi kemahasiswaan berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->authorizeSuperadmin();
        $this->reset(['informationId', 'title', 'summary', 'publishedAt', 'document']);
        $this->category = 'pengumuman';
        $this->isPublished = false;
        $this->showForm = false;
        $this->resetValidation();
    }

    /**
     * @return array<string, string>
     */
    public function categories(): array
    {
        return [
            'surat_edaran' => 'Surat Edaran',
            'surat_keputusan' => 'Surat Keputusan',
            'pengumuman' => 'Pengumuman',
            'informasi_lainnya' => 'Informasi Lainnya',
        ];
    }

    private function deleteStoredDocument(string $path): void
    {
        $disk = Storage::disk('local');
        if ($disk->exists($path) && ! $disk->delete($path)) {
            throw new RuntimeException('Dokumen informasi gagal dihapus dari penyimpanan.');
        }
    }

    private function authorizeSuperadmin(): void
    {
        abort_unless(Auth::user()?->hasRole('superadmin'), 403);
    }
}
