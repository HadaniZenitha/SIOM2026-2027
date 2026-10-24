<?php

namespace App\Livewire\Admin\Periode;

use App\Models\DocumentTemplate;
use App\Models\Period;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public ?int $periodId = null;

    public string $name = '';

    public string $start_date = '';

    public string $end_date = '';

    public string $status = 'draft';

    public string $description = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('periods', 'name')->ignore($this->periodId),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(['draft', 'active', 'finished'])],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function render(): View
    {
        $periods = Period::query()
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.periode.index', compact('periods'));
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $period = Period::findOrFail($id);

        $this->periodId = $period->id;
        $this->name = $period->name;
        $this->start_date = $period->start_date->format('Y-m-d');
        $this->end_date = $period->end_date->format('Y-m-d');
        $this->status = $period->status;
        $this->description = $period->description ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated): void {
            if ($validated['status'] === 'active') {
                Period::query()
                    ->where('status', 'active')
                    ->when($this->periodId, fn ($query) => $query->where('id', '!=', $this->periodId))
                    ->update(['status' => 'finished']);
            }

            if ($this->periodId === null) {
                Period::create($validated + ['created_by' => Auth::id()]);

                return;
            }

            Period::query()->findOrFail($this->periodId)->update($validated);
        });

        session()->flash(
            'success',
            $this->periodId
                ? 'Periode berhasil diperbarui.'
                : 'Periode berhasil ditambahkan.'
        );

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $period = Period::findOrFail($id);

        if (
            $period->members()->exists()
            || $period->budgets()->exists()
            || $period->cashAccounts()->exists()
            || $period->submissions()->exists()
            || $period->meetings()->exists()
            || $period->lpjs()->exists()
            || DocumentTemplate::query()->where('period_id', $id)->exists()
        ) {
            session()->flash(
                'error',
                'Periode tidak dapat dihapus karena masih memiliki data kegiatan, keuangan, anggota, atau template.'
            );

            return;
        }

        $period->delete();
        session()->flash('success', 'Periode berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->reset(['periodId', 'name', 'start_date', 'end_date', 'description']);
        $this->status = 'draft';
        $this->showForm = false;
        $this->resetValidation();
    }
}
