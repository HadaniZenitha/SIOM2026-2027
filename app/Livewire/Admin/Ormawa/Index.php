<?php

namespace App\Livewire\Admin\Ormawa;

use App\Models\OrganizationBudget;
use App\Models\Ormawa;
use App\Models\Period;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public ?int $ormawaId = null;

    public string $name = '';

    public string $code = '';

    public string $type = 'hima';

    public ?int $program_studi_id = null;

    public string $description = '';

    public bool $is_active = true;

    public ?int $selectedPeriodId = null;

    public bool $showBudgetForm = false;

    public ?int $budgetOrmawaId = null;

    public ?int $budgetPeriodId = null;

    public string $budgetAmount = '';

    protected $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        $this->selectedPeriodId = Period::query()
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderByDesc('start_date')
            ->value('id');
    }

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
                Rule::unique('ormawas', 'code')->ignore($this->ormawaId),
            ],
            'type' => ['nullable', 'string', 'in:hima,ukm,bem,dpm'],
            'program_studi_id' => ['nullable', 'exists:program_studis,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function render(): View
    {
        $ormawas = Ormawa::query()
            ->with([
                'programStudi',
                'budgets' => fn ($query) => $query->where('period_id', $this->selectedPeriodId),
            ])
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('code', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.ormawa.index', [
            'ormawas' => $ormawas,
            'periods' => Period::query()->orderByDesc('start_date')->orderByDesc('id')->get(),
            'programStudis' => \App\Models\ProgramStudi::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function create(): void
    {
        $this->resetForm();

        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $ormawa = Ormawa::findOrFail($id);

        $this->ormawaId = $ormawa->id;
        $this->name = $ormawa->name;
        $this->code = $ormawa->code;
        $this->type = $ormawa->type ?? 'hima';
        $this->program_studi_id = $ormawa->program_studi_id;
        $this->description = $ormawa->description ?? '';
        $this->is_active = $ormawa->is_active;

        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $type = $this->type ?: 'hima';

        Ormawa::updateOrCreate(
            ['id' => $this->ormawaId],
            [
                'name' => $this->name,
                'code' => $this->code,
                'type' => $type,
                'program_studi_id' => $type === 'hima' ? $this->program_studi_id : null,
                'description' => $this->description ?: null,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash(
            'success',
            $this->ormawaId
                ? 'Data ORMAWA berhasil diperbarui.'
                : 'Data ORMAWA berhasil ditambahkan.'
        );

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $ormawa = Ormawa::findOrFail($id);

        if (
            $ormawa->users()->exists()
            || $ormawa->members()->exists()
            || $ormawa->budgets()->exists()
            || $ormawa->cashAccounts()->exists()
            || $ormawa->submissions()->exists()
            || $ormawa->meetings()->exists()
            || $ormawa->lpjs()->exists()
        ) {
            session()->flash(
                'error',
                'ORMAWA tidak dapat dihapus karena masih digunakan oleh data lain. Nonaktifkan jika tidak digunakan lagi.'
            );

            return;
        }

        $ormawa->delete();

        session()->flash(
            'success',
            'Data ORMAWA berhasil dihapus.'
        );
    }

    public function editBudget(int $ormawaId): void
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);

        if (! $this->selectedPeriodId) {
            session()->flash('error', 'Buat periode terlebih dahulu sebelum mengatur pagu.');

            return;
        }

        $ormawa = Ormawa::findOrFail($ormawaId);
        $budget = OrganizationBudget::query()
            ->where('period_id', $this->selectedPeriodId)
            ->where('ormawa_id', $ormawa->id)
            ->first();

        $this->budgetOrmawaId = $ormawa->id;
        $this->budgetPeriodId = $this->selectedPeriodId;
        $this->budgetAmount = $budget?->budget_amount ?? '0.00';
        $this->showBudgetForm = true;
        $this->resetValidation();
    }

    public function saveBudget(): void
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);

        $validated = $this->validate([
            'budgetPeriodId' => ['required', 'integer', 'exists:periods,id'],
            'budgetOrmawaId' => ['required', 'integer', 'exists:ormawas,id'],
            'budgetAmount' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999999999.99'],
        ]);

        $budgetCanBeUpdated = DB::transaction(function () use ($validated): bool {
            $budget = OrganizationBudget::query()
                ->where('period_id', $validated['budgetPeriodId'])
                ->where('ormawa_id', $validated['budgetOrmawaId'])
                ->lockForUpdate()
                ->first();

            if (
                $budget
                && OrganizationBudget::query()
                    ->whereKey($budget->id)
                    ->where('allocated_amount', '>', $validated['budgetAmount'])
                    ->exists()
            ) {
                return false;
            }

            if (! $budget) {
                OrganizationBudget::create([
                    'period_id' => $validated['budgetPeriodId'],
                    'ormawa_id' => $validated['budgetOrmawaId'],
                    'budget_amount' => $validated['budgetAmount'],
                    'allocated_amount' => 0,
                    'remaining_amount' => $validated['budgetAmount'],
                ]);

                return true;
            }

            $budget->budget_amount = $validated['budgetAmount'];
            $budget->save();

            OrganizationBudget::query()
                ->whereKey($budget->id)
                ->update([
                    'remaining_amount' => DB::raw('budget_amount - allocated_amount'),
                ]);

            return true;
        });

        if (! $budgetCanBeUpdated) {
            $this->addError('budgetAmount', 'Pagu tidak boleh lebih kecil dari dana yang sudah disetujui.');

            return;
        }

        session()->flash('success', 'Pagu ORMAWA berhasil disimpan.');
        $this->showBudgetForm = false;
        $this->budgetOrmawaId = null;
        $this->budgetPeriodId = null;
        $this->budgetAmount = '';
        $this->resetValidation();
    }

    public function closeBudgetForm(): void
    {
        $this->showBudgetForm = false;
        $this->budgetOrmawaId = null;
        $this->budgetPeriodId = null;
        $this->budgetAmount = '';
        $this->resetValidation();
    }

    public function resetForm(): void
    {
        $this->reset([
            'ormawaId',
            'name',
            'code',
            'type',
            'program_studi_id',
            'description',
        ]);

        $this->type = 'hima';
        $this->is_active = true;
        $this->showForm = false;
        $this->resetValidation();
    }
}
