<?php

namespace App\Services;

use App\Models\Role;
use App\Models\RoleMenuSetting;
use App\Models\User;
use Illuminate\Support\Carbon;

class MenuAccessService
{
    /**
     * @return array<string, array{label: string, route: string, icon: string, roles: list<string>}>
     */
    public function definitions(): array
    {
        return [
            'admin.ormawa' => ['label' => 'ORMAWA dan Pagu', 'route' => 'admin.ormawa', 'icon' => 'bi-diagram-3', 'roles' => ['superadmin']],
            'admin.program-studi' => ['label' => 'Program Studi', 'route' => 'admin.program-studi', 'icon' => 'bi-mortarboard', 'roles' => ['superadmin']],
            'admin.periode' => ['label' => 'Periode', 'route' => 'admin.periode', 'icon' => 'bi-calendar3', 'roles' => ['superadmin']],
            'admin.pengguna' => ['label' => 'Pengguna', 'route' => 'admin.pengguna', 'icon' => 'bi-people', 'roles' => ['superadmin']],
            'admin.template-dokumen' => ['label' => 'Template Dokumen', 'route' => 'admin.template-dokumen', 'icon' => 'bi-file-earmark-word', 'roles' => ['superadmin']],
            'admin.landing-info' => ['label' => 'Informasi Kemahasiswaan', 'route' => 'admin.kemahasiswaan-info', 'icon' => 'bi-megaphone', 'roles' => ['superadmin']],
            'sekretaris.anggota' => ['label' => 'Anggota', 'route' => 'sekretaris.anggota', 'icon' => 'bi-person-vcard', 'roles' => ['sekretaris_ormawa']],
            'sekretaris.pengajuan' => ['label' => 'Pengajuan Dana', 'route' => 'sekretaris.pengajuan', 'icon' => 'bi-send', 'roles' => ['sekretaris_ormawa']],
            'sekretaris.notulensi' => ['label' => 'Notulensi', 'route' => 'sekretaris.notulensi', 'icon' => 'bi-journal-text', 'roles' => ['sekretaris_ormawa']],
            'sekretaris.kas' => ['label' => 'Kas ORMAWA', 'route' => 'sekretaris.kas', 'icon' => 'bi-wallet2', 'roles' => ['sekretaris_ormawa']],
            'sekretaris.lpj' => ['label' => 'LPJ', 'route' => 'sekretaris.lpj', 'icon' => 'bi-clipboard-check', 'roles' => ['sekretaris_ormawa']],
            'approval' => ['label' => 'Persetujuan', 'route' => 'approval.index', 'icon' => 'bi-check2-square', 'roles' => ['pembina_ormawa', 'kaprodi', 'kabag_kemahasiswaan', 'warek']],
            'laporan' => ['label' => 'Laporan', 'route' => 'laporan', 'icon' => 'bi-bar-chart', 'roles' => ['superadmin', 'sekretaris_ormawa', 'pembina_ormawa', 'kaprodi', 'kabag_kemahasiswaan', 'warek']],
        ];
    }

    /**
     * @return list<string>
     */
    public function enabledMenus(?User $user): array
    {
        if (! $user?->role) {
            return [];
        }

        $roleName = $user->role->name;
        $roleMenus = collect($this->definitions())
            ->filter(fn (array $menu): bool => in_array($roleName, $menu['roles'], true));

        if ($roleName === 'superadmin') {
            return $roleMenus->keys()->all();
        }

        $roleSettings = RoleMenuSetting::query()
            ->where('role_id', $user->role_id)
            ->where('access_scope', 'role')
            ->get()
            ->keyBy('menu_key');
        $userSettings = RoleMenuSetting::query()
            ->where('role_id', $user->role_id)
            ->where('access_scope', 'user:'.$user->id)
            ->get()
            ->keyBy('menu_key');

        return $roleMenus
            ->filter(fn (array $menu, string $key): bool => $this->isSettingEnabled(
                $userSettings->get($key) ?? $roleSettings->get($key)
            ))
            ->keys()
            ->all();
    }

    public function isEnabled(?User $user, string $menuKey): bool
    {
        return in_array($menuKey, $this->enabledMenus($user), true);
    }

    /**
     * @return list<array{name: string, display_name: string, menus: list<array{key: string, label: string, route: ?string, icon: string, enabled: bool, fixed: bool, auto_enable_at: ?string}>}>
     */
    public function matrix(): array
    {
        $roles = Role::query()->where('name', '!=', 'superadmin')->orderBy('display_name')->get();
        $settings = RoleMenuSetting::query()
            ->whereIn('role_id', $roles->pluck('id'))
            ->where('access_scope', 'role')
            ->get()
            ->keyBy(fn (RoleMenuSetting $setting): string => $setting->role_id.'|'.$setting->menu_key);

        $fixedMenus = [
            ['key' => 'dashboard', 'label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'bi-grid-1x2'],
            ['key' => 'profile', 'label' => 'Profil', 'route' => 'profile', 'icon' => 'bi-person'],
            ['key' => 'notifications', 'label' => 'Informasi Notifikasi', 'route' => null, 'icon' => 'bi-info-circle'],
            ['key' => 'logout', 'label' => 'Keluar', 'route' => null, 'icon' => 'bi-box-arrow-right'],
        ];

        return $roles->map(function (Role $role) use ($fixedMenus, $settings): array {
            $menus = collect($fixedMenus)
                ->map(fn (array $menu): array => [
                    ...$menu,
                    'enabled' => true,
                    'fixed' => true,
                    'auto_enable_at' => null,
                ])
                ->merge(collect($this->definitions())
                    ->filter(fn (array $menu): bool => in_array($role->name, $menu['roles'], true))
                    ->map(function (array $menu, string $key) use ($role, $settings): array {
                        $setting = $settings->get($role->id.'|'.$key);

                        return [
                            'key' => $key,
                            'label' => $menu['label'],
                            'route' => $menu['route'],
                            'icon' => $menu['icon'],
                            'enabled' => $this->isSettingEnabled($setting),
                            'fixed' => false,
                            'auto_enable_at' => $setting?->auto_enable_at
                                ? $setting->auto_enable_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i').' WIB'
                                : null,
                        ];
                    }))
                ->values()
                ->all();

            return [
                'name' => $role->name,
                'display_name' => $role->display_name,
                'menus' => $menus,
            ];
        })->all();
    }

    public function toggle(Role $role, string $menuKey, ?Carbon $autoEnableAt = null): bool
    {
        $this->authorizeRoleMenu($role->name, $menuKey);

        $setting = RoleMenuSetting::query()->firstOrNew([
            'role_id' => $role->id,
            'access_scope' => 'role',
            'user_id' => null,
            'menu_key' => $menuKey,
        ]);
        $enabled = ! $this->isSettingEnabled($setting);
        if (! $enabled) {
            abort_unless($autoEnableAt instanceof Carbon, 422);
            $this->validateAutoEnableAt($autoEnableAt);
        }

        $setting->enabled = $enabled;
        $setting->auto_enable_at = $setting->enabled
            ? null
            : $autoEnableAt;
        $setting->save();

        return $setting->enabled;
    }

    /**
     * @return list<array{key: string, label: string, route: string, icon: string, enabled: bool, overridden: bool, auto_enable_at: ?string}>
     */
    public function menusForUser(User $user): array
    {
        if (! $user->role || $user->hasRole('superadmin')) {
            return [];
        }

        $roleSettings = RoleMenuSetting::query()
            ->where('role_id', $user->role_id)
            ->where('access_scope', 'role')
            ->get()
            ->keyBy('menu_key');
        $userSettings = RoleMenuSetting::query()
            ->where('role_id', $user->role_id)
            ->where('access_scope', 'user:'.$user->id)
            ->get()
            ->keyBy('menu_key');

        return collect($this->definitions())
            ->filter(fn (array $menu): bool => in_array($user->role->name, $menu['roles'], true))
            ->map(function (array $menu, string $key) use ($roleSettings, $userSettings): array {
                $override = $userSettings->get($key);
                $setting = $override ?? $roleSettings->get($key);
                $autoEnableAt = $setting?->auto_enable_at;

                return [
                    'key' => $key,
                    'label' => $menu['label'],
                    'route' => $menu['route'],
                    'icon' => $menu['icon'],
                    'enabled' => $this->isSettingEnabled($setting),
                    'overridden' => $override !== null,
                    'auto_enable_at' => $autoEnableAt
                        ? $autoEnableAt->setTimezone('Asia/Jakarta')->format('d/m/Y H:i').' WIB'
                        : null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return list<array{key: string, label: string, route: string, message: string, auto_enable_at: ?string}>
     */
    public function disabledMenusForUser(User $user): array
    {
        return collect($this->menusForUser($user))
            ->reject(fn (array $menu): bool => $menu['enabled'])
            ->map(function (array $menu): array {
                $duration = $menu['auto_enable_at'] !== null
                    ? 'sampai '.$menu['auto_enable_at']
                    : 'sampai diaktifkan kembali oleh Superadmin';

                return [
                    'key' => $menu['key'],
                    'label' => $menu['label'],
                    'route' => $menu['route'],
                    'auto_enable_at' => $menu['auto_enable_at'],
                    'message' => 'Anda tidak dapat mengakses menu '.$menu['label'].' '.$duration.' karena administrasi belum terlengkapi. Silakan menghubungi Superadmin.',
                ];
            })
            ->values()
            ->all();
    }

    public function toggleForUser(User $user, string $menuKey, ?Carbon $autoEnableAt = null): bool
    {
        abort_unless($user->role, 404);
        $this->authorizeRoleMenu($user->role->name, $menuKey);

        $setting = RoleMenuSetting::query()->firstOrNew([
            'role_id' => $user->role_id,
            'access_scope' => 'user:'.$user->id,
            'user_id' => $user->id,
            'menu_key' => $menuKey,
        ]);
        $roleSetting = RoleMenuSetting::query()
            ->where('role_id', $user->role_id)
            ->where('access_scope', 'role')
            ->where('menu_key', $menuKey)
            ->first();
        $enabled = ! $this->isSettingEnabled($setting->exists ? $setting : $roleSetting);
        if (! $enabled) {
            abort_unless($autoEnableAt instanceof Carbon, 422);
            $this->validateAutoEnableAt($autoEnableAt);
        }

        $setting->enabled = $enabled;
        $setting->auto_enable_at = $setting->enabled
            ? null
            : $autoEnableAt;

        $setting->save();

        return $setting->enabled;
    }

    public function scheduleRoleMenuEnable(Role $role, string $menuKey, Carbon $autoEnableAt): void
    {
        $this->authorizeRoleMenu($role->name, $menuKey);
        $this->validateAutoEnableAt($autoEnableAt);

        $setting = RoleMenuSetting::query()->firstOrNew([
            'role_id' => $role->id,
            'access_scope' => 'role',
            'user_id' => null,
            'menu_key' => $menuKey,
        ]);
        $setting->enabled = false;
        $setting->auto_enable_at = $autoEnableAt;
        $setting->save();
    }

    public function scheduleUserMenuEnable(User $user, string $menuKey, Carbon $autoEnableAt): void
    {
        abort_unless($user->role, 404);
        $this->authorizeRoleMenu($user->role->name, $menuKey);
        $this->validateAutoEnableAt($autoEnableAt);

        RoleMenuSetting::query()->updateOrCreate(
            [
                'role_id' => $user->role_id,
                'access_scope' => 'user:'.$user->id,
                'menu_key' => $menuKey,
            ],
            [
                'user_id' => $user->id,
                'enabled' => false,
                'auto_enable_at' => $autoEnableAt,
            ]
        );
    }

    public function clearUserOverride(User $user, string $menuKey): void
    {
        abort_unless($user->role, 404);
        $this->authorizeRoleMenu($user->role->name, $menuKey);

        RoleMenuSetting::query()
            ->where('role_id', $user->role_id)
            ->where('access_scope', 'user:'.$user->id)
            ->where('menu_key', $menuKey)
            ->delete();
    }

    private function isSettingEnabled(?RoleMenuSetting $setting): bool
    {
        return $setting === null
            || ! $setting->exists
            || $setting->enabled
            || ($setting->auto_enable_at !== null && $setting->auto_enable_at->isPast());
    }

    private function authorizeRoleMenu(string $roleName, string $menuKey): void
    {
        $menu = $this->definitions()[$menuKey] ?? null;
        abort_unless(
            $roleName !== 'superadmin'
                && $menu
                && in_array($roleName, $menu['roles'], true),
            404
        );
    }

    private function validateAutoEnableAt(Carbon $autoEnableAt): void
    {
        abort_unless($autoEnableAt->isFuture(), 422);
    }
}
