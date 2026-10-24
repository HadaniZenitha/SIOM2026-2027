<?php

namespace App\Livewire\Admin\ProgramStudi;

use App\Models\ProgramStudi;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public bool $showForm = false;

    public ?int $programStudiId = null;

    public string $name = '';

    public string $code = '';

    public string $fakultas = '';

    public bool $is_active = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('program_studis', 'code')->ignore($this->programStudiId),
            ],
            'fakultas' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    public function render(): View
    {
        $programStudis = ProgramStudi::query()
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('code', 'like', '%'.$this->search.'%')
                        ->orWhere('fakultas', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.program-studi.index', compact('programStudis'));
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $programStudi = ProgramStudi::findOrFail($id);

        $this->programStudiId = $programStudi->id;
        $this->name = $programStudi->name;
        $this->code = $programStudi->code;
        $this->fakultas = $programStudi->fakultas ?? '';
        $this->is_active = $programStudi->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        ProgramStudi::updateOrCreate(
            ['id' => $this->programStudiId],
            $validated
        );

        session()->flash(
            'success',
            $this->programStudiId
                ? 'Program studi berhasil diperbarui.'
                : 'Program studi berhasil ditambahkan.'
        );

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $programStudi = ProgramStudi::findOrFail($id);

        if ($programStudi->users()->exists() || $programStudi->members()->exists()) {
            session()->flash(
                'error',
                'Program studi tidak dapat dihapus karena masih digunakan oleh pengguna atau data anggota. Nonaktifkan jika tidak digunakan lagi.'
            );

            return;
        }

        $programStudi->delete();
        session()->flash('success', 'Program studi berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->reset(['programStudiId', 'name', 'code', 'fakultas']);
        $this->is_active = true;
        $this->showForm = false;
        $this->resetValidation();
    }
}
