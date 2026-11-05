<?php

namespace App\Livewire\Admin\MenuAccess;

use App\Models\Role;
use App\Models\User;
use App\Services\MenuAccessService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    protected MenuAccessService $menuAccess;

    /**
     * @var list<array{name: string, display_name: string, menus: list<array{key: string, label: string, route: ?string, icon: string, enabled: bool, fixed: bool, auto_enable_at: ?string}>}>
     */
    public array $matrix = [];

    public string $userSearch = '';

    public ?int $selectedUserId = null;

    public string $autoEnableAt = '';

    public function boot(MenuAccessService $menuAccess): void
    {
        $this->menuAccess = $menuAccess;
    }

    public function mount(MenuAccessService $menuAccess): void
    {
        $this->authorizeSuperadmin();
        $this->matrix = $menuAccess->matrix();
        $this->autoEnableAt = now()->setTimezone('Asia/Jakarta')->addDays(7)->format('Y-m-d\TH:i');
    }

    public function toggle(string $roleName, string $menuKey, MenuAccessService $menuAccess): void
    {
        $this->authorizeSuperadmin();
        $autoEnableAt = $this->validatedAutoEnableAt();

        $role = Role::query()->where('name', $roleName)->firstOrFail();
        $enabled = $menuAccess->toggle($role, $menuKey, $autoEnableAt);
        $this->matrix = $menuAccess->matrix();
        $this->dispatch('menu-access-updated');

        session()->flash(
            'success',
            'Menu '.$menuAccess->definitions()[$menuKey]['label'].' untuk '.$role->display_name.' berhasil '.(
                $enabled ? 'diaktifkan.' : 'dinonaktifkan dan dijadwalkan aktif kembali pada '.$this->formatAutoEnableAt($autoEnableAt).'.'
            )
        );
    }

    public function scheduleRoleMenuEnable(string $roleName, string $menuKey, MenuAccessService $menuAccess): void
    {
        $this->authorizeSuperadmin();
        $autoEnableAt = $this->validatedAutoEnableAt();

        $role = Role::query()->where('name', $roleName)->firstOrFail();
        $menuAccess->scheduleRoleMenuEnable($role, $menuKey, $autoEnableAt);
        $this->matrix = $menuAccess->matrix();

        session()->flash(
            'success',
            'Menu '.$menuAccess->definitions()[$menuKey]['label'].' dijadwalkan aktif kembali pada '.$this->formatAutoEnableAt($autoEnableAt).'.'
        );
    }

    public function toggleUserMenu(int $userId, string $menuKey, MenuAccessService $menuAccess): void
    {
        $this->authorizeSuperadmin();
        $autoEnableAt = $this->validatedAutoEnableAt();

        $user = User::query()->with('role')->findOrFail($userId);
        $this->selectedUserId = $user->id;
        $enabled = $menuAccess->toggleForUser($user, $menuKey, $autoEnableAt);
        $this->dispatch('menu-access-updated');

        session()->flash(
            'success',
            'Menu '.$menuAccess->definitions()[$menuKey]['label'].' untuk '.$user->name.' berhasil '.(
                $enabled ? 'diaktifkan.' : 'dinonaktifkan dan dijadwalkan aktif kembali pada '.$this->formatAutoEnableAt($autoEnableAt).'.'
            )
        );
    }

    public function scheduleUserMenuEnable(int $userId, string $menuKey, MenuAccessService $menuAccess): void
    {
        $this->authorizeSuperadmin();
        $autoEnableAt = $this->validatedAutoEnableAt();

        $user = User::query()->with('role')->findOrFail($userId);
        $menuAccess->scheduleUserMenuEnable($user, $menuKey, $autoEnableAt);
        $this->selectedUserId = $user->id;
        $this->dispatch('menu-access-updated');

        session()->flash(
            'success',
            'Menu '.$menuAccess->definitions()[$menuKey]['label'].' untuk '.$user->name.' dijadwalkan aktif kembali pada '.$this->formatAutoEnableAt($autoEnableAt).'.'
        );
    }

    public function clearUserMenuOverride(int $userId, string $menuKey, MenuAccessService $menuAccess): void
    {
        $this->authorizeSuperadmin();

        $user = User::query()->with('role')->findOrFail($userId);
        $menuAccess->clearUserOverride($user, $menuKey);
        $this->selectedUserId = $user->id;
        $this->dispatch('menu-access-updated');

        session()->flash(
            'success',
            'Pengaturan menu '.$menuAccess->definitions()[$menuKey]['label'].' untuk '.$user->name.' kembali mengikuti role.'
        );
    }

    public function render(): View
    {
        $this->authorizeSuperadmin();
        $users = User::query()
            ->with(['role', 'ormawa'])
            ->whereHas('role', fn ($query) => $query->where('name', '!=', 'superadmin'))
            ->when($this->userSearch !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('name', 'like', '%'.$this->userSearch.'%')
                        ->orWhere('email', 'like', '%'.$this->userSearch.'%');
                });
            })
            ->orderBy('name')
            ->limit(20)
            ->get();
        $selectedUser = $this->selectedUserId
            ? User::query()
                ->with(['role', 'ormawa'])
                ->whereHas('role', fn ($query) => $query->where('name', '!=', 'superadmin'))
                ->find($this->selectedUserId)
            : null;
        $userMenus = $selectedUser ? $this->menuAccess->menusForUser($selectedUser) : [];

        return view('livewire.admin.menu-access.index', compact('selectedUser', 'userMenus', 'users'));
    }

    private function authorizeSuperadmin(): void
    {
        abort_unless(Auth::user()?->hasRole('superadmin'), 403);
    }

    private function validatedAutoEnableAt(): Carbon
    {
        $validated = $this->validate([
            'autoEnableAt' => ['required', 'date_format:Y-m-d\TH:i'],
        ]);
        $autoEnableAt = Carbon::parse($validated['autoEnableAt'], 'Asia/Jakarta')
            ->setTimezone(config('app.timezone'));

        if (! $autoEnableAt->isFuture()) {
            throw ValidationException::withMessages([
                'autoEnableAt' => 'Pilih tanggal dan jam yang masih akan datang.',
            ]);
        }

        return $autoEnableAt;
    }

    private function formatAutoEnableAt(Carbon $autoEnableAt): string
    {
        return $autoEnableAt->copy()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i').' WIB';
    }
}
