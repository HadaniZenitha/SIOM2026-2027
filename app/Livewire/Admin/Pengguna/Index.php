<?php

namespace App\Livewire\Admin\Pengguna;

use App\Models\ApprovalLog;
use App\Models\DocumentTemplate;
use App\Models\Ormawa;
use App\Models\ProgramStudi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $roleFilter = '';

    public string $statusFilter = '';

    public bool $showModal = false;

    public bool $isEdit = false;

    public ?int $userId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?int $role_id = null;

    public ?int $ormawa_id = null;

    public ?int $program_studi_id = null;

    public string $phone = '';

    public string $nuptk = '';

    public bool $is_active = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();

        $this->showModal = true;
        $this->isEdit = false;
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->role_id = $user->role_id;
        $this->ormawa_id = $user->ormawa_id;
        $this->program_studi_id = $user->program_studi_id;
        $this->phone = $user->phone ?? '';
        $this->nuptk = $user->nuptk ?? '';
        $this->is_active = $user->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                $this->isEdit
                    ? 'unique:users,email,'.$this->userId
                    : 'unique:users,email',
            ],
            'role_id' => ['required', 'exists:roles,id'],
            'ormawa_id' => ['nullable', 'exists:ormawas,id'],
            'program_studi_id' => ['nullable', 'exists:program_studis,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'nuptk' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ];

        if (! $this->isEdit) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } elseif ($this->password !== '') {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        $validated = $this->validate(
            $rules,
            [
                'name.required' => 'Nama wajib diisi.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email tersebut sudah digunakan.',
                'role_id.required' => 'Role wajib dipilih.',
                'role_id.exists' => 'Role yang dipilih tidak valid.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            ]
        );

        $role = Role::findOrFail($validated['role_id']);

        /*
        |--------------------------------------------------------------------------
        | Aturan relasi berdasarkan role
        |--------------------------------------------------------------------------
        */

        $requiresOrmawa = in_array($role->name, [
            'sekretaris_ormawa',
            'pembina_ormawa',
        ]);

        $requiresProdi = in_array($role->name, [
            'sekretaris_ormawa',
            'kaprodi',
        ], true);

        if ($requiresOrmawa && ! $this->ormawa_id) {
            $this->addError(
                'ormawa_id',
                'ORMAWA wajib dipilih untuk role ini.'
            );

            return;
        }

        if ($requiresProdi && ! $this->program_studi_id) {
            $this->addError(
                'program_studi_id',
                'Program Studi wajib dipilih untuk Sekretaris ORMAWA dan KAPRODI.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Role tertentu tidak membutuhkan ORMAWA / Program Studi
        |--------------------------------------------------------------------------
        */

        if (! $requiresOrmawa) {
            $validated['ormawa_id'] = null;
        }

        if (! $requiresProdi) {
            $validated['program_studi_id'] = null;
        }

        DB::transaction(function () use ($validated) {
            if ($this->isEdit) {
                $user = User::findOrFail($this->userId);

                $user->name = $validated['name'];
                $user->email = $validated['email'];
                $user->role_id = $validated['role_id'];
                $user->ormawa_id = $validated['ormawa_id'];
                $user->program_studi_id = $validated['program_studi_id'];
                $user->phone = $validated['phone'] ?? '';
                $user->nuptk = $validated['nuptk'] ?? '';
                $user->is_active = $validated['is_active'];

                if ($this->password !== '') {
                    $user->password = $this->password;
                }

                $user->save();
            } else {
                User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => $this->password,
                    'role_id' => $validated['role_id'],
                    'ormawa_id' => $validated['ormawa_id'],
                    'program_studi_id' => $validated['program_studi_id'],
                    'phone' => $validated['phone'] ?? '',
                    'nuptk' => $validated['nuptk'] ?? '',
                    'is_active' => $validated['is_active'],
                ]);
            }
        });

        session()->flash(
            'success',
            $this->isEdit
                ? 'Data pengguna berhasil diperbarui.'
                : 'Pengguna berhasil dibuat.'
        );

        $this->closeModal();
    }

    public function toggleStatus(int $id): void
    {
        $user = User::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Superadmin tidak boleh menonaktifkan dirinya sendiri
        |--------------------------------------------------------------------------
        */

        if ($user->id === Auth::user()?->id) {
            session()->flash(
                'error',
                'Anda tidak dapat menonaktifkan akun yang sedang digunakan.'
            );

            return;
        }

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        session()->flash(
            'success',
            $user->is_active
                ? 'Akun berhasil diaktifkan.'
                : 'Akun berhasil dinonaktifkan.'
        );
    }

    public function deleteUser(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');

            return;
        }

        if (
            $user->submissions()->exists()
            || $user->cashTransactions()->exists()
            || $user->meetings()->exists()
            || $user->lpjs()->exists()
            || ApprovalLog::query()->where('user_id', $user->id)->exists()
            || DocumentTemplate::query()->where('created_by', $user->id)->exists()
        ) {
            session()->flash(
                'error',
                'Pengguna tidak dapat dihapus karena masih memiliki riwayat kegiatan atau dokumen. Nonaktifkan akun jika tidak digunakan lagi.'
            );

            return;
        }

        $user->delete();
        session()->flash('success', 'Pengguna berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm(): void
    {
        $this->reset([
            'userId',
            'name',
            'email',
            'password',
            'password_confirmation',
            'role_id',
            'ormawa_id',
            'program_studi_id',
            'phone',
            'nuptk',
        ]);

        $this->is_active = true;
        $this->isEdit = false;
    }

    public function render()
    {
        $users = User::query()
            ->with(['role', 'ormawa', 'programStudi'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->where('role_id', $this->roleFilter);
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.pengguna.index', [
            'users' => $users,
            'roles' => Role::orderBy('display_name')->get(),
            'ormawas' => Ormawa::where('is_active', true)
                ->orderBy('name')
                ->get(),
            'programStudis' => ProgramStudi::where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }
}
