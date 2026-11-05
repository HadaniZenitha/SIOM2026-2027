<div class="siom-menu-access">
    <style>
        .siom-menu-access .siom-menu-access-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }
        .siom-menu-access .siom-menu-access-header h1 { margin: 0; }
        .siom-menu-access .siom-menu-access-header p { margin: .35rem 0 0; color: #475569; }
        .siom-menu-access .siom-menu-role {
            margin-top: 1rem;
            padding: 1rem 1.15rem;
            border: 2px solid #0f172a;
            border-radius: 16px;
            background: #fff;
            box-shadow: 3px 3px 0 #0f172a;
        }
        .siom-menu-access .siom-menu-role h2 { margin: 0 0 .75rem; font-size: 1.05rem; }
        .siom-menu-access .siom-menu-table-wrap { overflow-x: auto; }
        .siom-menu-access .siom-menu-table { width: 100%; border-collapse: collapse; }
        .siom-menu-access .siom-menu-table th,
        .siom-menu-access .siom-menu-table td {
            padding: .65rem;
            border-bottom: 1px solid #cbd5e1;
            text-align: left;
        }
        .siom-menu-access .siom-menu-table th { font-size: .78rem; }
        .siom-menu-access .siom-menu-state {
            display: inline-flex;
            padding: .3rem .6rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 800;
        }
        .siom-menu-access .siom-menu-state.is-on { background: #dcfce7; color: #166534; }
        .siom-menu-access .siom-menu-state.is-off { background: #fee2e2; color: #991b1b; }
        .siom-menu-access .siom-menu-toggle {
            min-height: 2.25rem;
            padding: .35rem .7rem;
            border: 1.5px solid #0f172a;
            border-radius: 999px;
            background: #fff;
            color: #0f172a;
            font: inherit;
            font-size: .8rem;
            font-weight: 700;
            cursor: pointer;
        }
        .siom-menu-access .siom-menu-toggle:disabled { cursor: wait; opacity: .6; }
        .siom-menu-access .siom-menu-actions { display: flex; flex-wrap: wrap; gap: .45rem; }
        .siom-menu-access .siom-menu-timer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .5rem;
            margin: .5rem 0;
            color: #334155;
            font-size: .82rem;
        }
        .siom-menu-access .siom-menu-timer input[type="datetime-local"] {
            min-height: 2.25rem;
            padding: .3rem .55rem;
            border: 1.5px solid #64748b;
            border-radius: 8px;
            background: #fff;
            color: #0f172a;
        }
        .siom-menu-access .siom-menu-schedule {
            display: block;
            margin-top: .35rem;
            color: #475569;
            font-size: .75rem;
        }
        .siom-menu-access .siom-menu-success {
            margin-bottom: 1rem;
            padding: .8rem 1rem;
            border: 1.5px solid #166534;
            border-radius: 12px;
            background: #dcfce7;
            color: #166534;
        }
        @media (max-width: 640px) {
            .siom-menu-access .siom-menu-access-header { flex-direction: column; }
        }
    </style>

    <header class="siom-menu-access-header">
        <div>
            <h1 class="h3">Pengaturan Akses Menu</h1>
            <p>Atur akses menu per role dan per pengguna untuk semua role. Akses Superadmin tidak dibatasi oleh pengaturan ini.</p>
        </div>
    </header>

    <label class="siom-menu-timer" for="auto-enable-at">
        Jika menonaktifkan menu, aktifkan kembali pada tanggal dan jam (WIB):
        <input
            id="auto-enable-at"
            type="datetime-local"
            wire:model="autoEnableAt"
        >
    </label>
    @error('autoEnableAt')
        <p class="text-danger" role="alert">{{ $message }}</p>
    @enderror

    @if (session('success'))
        <div class="siom-menu-success" role="status">{{ session('success') }}</div>
    @endif

    @foreach ($matrix as $role)
        <section class="siom-menu-role" wire:key="menu-role-{{ $role['name'] }}">
            <h2>{{ $role['display_name'] }}</h2>
            @if ($role['menus'] !== [])
                <div class="siom-menu-table-wrap">
                    <table class="siom-menu-table">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($role['menus'] as $menu)
                                <tr wire:key="menu-{{ $role['name'] }}-{{ $menu['key'] }}">
                                    <td>
                                        <i class="bi {{ $menu['icon'] }}"></i> {{ $menu['label'] }}
                                        @if ($menu['route'])
                                            <small class="d-block text-muted">{{ route($menu['route'], absolute: false) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($menu['fixed'])
                                            <span class="siom-menu-state is-on">Tetap aktif</span>
                                        @else
                                            <span class="siom-menu-state {{ $menu['enabled'] ? 'is-on' : 'is-off' }}">
                                                {{ $menu['enabled'] ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                            @if ($menu['auto_enable_at'])
                                                <small class="siom-menu-schedule">
                                                    {{ $menu['enabled'] ? 'Sudah aktif pada' : 'Otomatis aktif pada' }} {{ $menu['auto_enable_at'] }}
                                                </small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if ($menu['fixed'])
                                            <span class="text-muted">Menu inti</span>
                                        @else
                                            <div class="siom-menu-actions">
                                                <button
                                                    type="button"
                                                    class="siom-menu-toggle"
                                                    wire:click="toggle('{{ $role['name'] }}', '{{ $menu['key'] }}')"
                                                    wire:loading.attr="disabled"
                                                    wire:target="toggle('{{ $role['name'] }}', '{{ $menu['key'] }}')"
                                                    aria-label="{{ $menu['enabled'] ? 'Nonaktifkan' : 'Aktifkan' }} menu {{ $menu['label'] }} untuk {{ $role['display_name'] }}"
                                                >
                                                    {{ $menu['enabled'] ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                                @if (! $menu['enabled'])
                                                    <button
                                                        type="button"
                                                        class="siom-menu-toggle"
                                                        wire:click="scheduleRoleMenuEnable('{{ $role['name'] }}', '{{ $menu['key'] }}')"
                                                        wire:loading.attr="disabled"
                                                    >
                                                        Jadwalkan ulang
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="mb-0 text-muted">Role ini belum memiliki menu yang dapat dikonfigurasi.</p>
            @endif
        </section>
    @endforeach

    <section class="siom-menu-role">
        <h2>Pengaturan Khusus Pengguna</h2>
        <p class="text-muted">Pilih pengguna non-Superadmin untuk membuat pengaturan khusus yang mengesampingkan pengaturan default role.</p>

        <div class="siom-menu-table-wrap">
            <label for="menu-user-search">Cari pengguna berdasarkan nama atau email</label>
            <input
                id="menu-user-search"
                type="search"
                class="form-control mb-3"
                wire:model.live.debounce.300ms="userSearch"
                placeholder="Ketik nama atau email..."
            >

            <table class="siom-menu-table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>ORMAWA</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr wire:key="menu-user-{{ $user->id }}">
                            <td>
                                <strong>{{ $user->name }}</strong>
                                <small class="d-block text-muted">{{ $user->email }}</small>
                            </td>
                            <td>{{ $user->role?->display_name ?? '-' }}</td>
                            <td>{{ $user->ormawa?->name ?? '-' }}</td>
                            <td>
                                <button
                                    type="button"
                                    class="siom-menu-toggle"
                                    wire:click="$set('selectedUserId', {{ $user->id }})"
                                >
                                    {{ $selectedUser?->id === $user->id ? 'Dipilih' : 'Atur menu' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">Pengguna tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($selectedUser)
        <section class="siom-menu-role">
            <h2>Menu untuk {{ $selectedUser->name }}</h2>
            <p class="text-muted">
                {{ $selectedUser->role?->display_name ?? 'Tanpa role' }}
                @if ($selectedUser->ormawa)
                    · {{ $selectedUser->ormawa->name }}
                @endif
            </p>

            @if ($userMenus !== [])
                <div class="siom-menu-table-wrap">
                    <table class="siom-menu-table">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Status efektif</th>
                                <th>Sumber akses</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($userMenus as $menu)
                                <tr wire:key="user-menu-{{ $selectedUser->id }}-{{ $menu['key'] }}">
                                    <td>
                                        <i class="bi {{ $menu['icon'] }}"></i> {{ $menu['label'] }}
                                        <small class="d-block text-muted">{{ route($menu['route'], absolute: false) }}</small>
                                    </td>
                                    <td>
                                        <span class="siom-menu-state {{ $menu['enabled'] ? 'is-on' : 'is-off' }}">
                                            {{ $menu['enabled'] ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                        @if ($menu['auto_enable_at'])
                                            <small class="siom-menu-schedule">
                                                {{ $menu['enabled'] ? 'Sudah aktif pada' : 'Otomatis aktif pada' }} {{ $menu['auto_enable_at'] }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ $menu['overridden'] ? 'Khusus pengguna' : 'Default role' }}</td>
                                    <td>
                                        <div class="siom-menu-actions">
                                            <button
                                                type="button"
                                                class="siom-menu-toggle"
                                                wire:click="toggleUserMenu({{ $selectedUser->id }}, '{{ $menu['key'] }}')"
                                                wire:loading.attr="disabled"
                                                aria-label="{{ $menu['enabled'] ? 'Nonaktifkan' : 'Aktifkan' }} {{ $menu['label'] }} untuk {{ $selectedUser->name }}"
                                            >
                                                {{ $menu['enabled'] ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                            @if (! $menu['enabled'])
                                                <button
                                                    type="button"
                                                    class="siom-menu-toggle"
                                                    wire:click="scheduleUserMenuEnable({{ $selectedUser->id }}, '{{ $menu['key'] }}')"
                                                    wire:loading.attr="disabled"
                                                >
                                                    Jadwalkan ulang
                                                </button>
                                            @endif
                                            @if ($menu['overridden'])
                                                <button
                                                    type="button"
                                                    class="siom-menu-toggle"
                                                    wire:click="clearUserMenuOverride({{ $selectedUser->id }}, '{{ $menu['key'] }}')"
                                                    wire:loading.attr="disabled"
                                                >
                                                    Ikuti default role
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="mb-0 text-muted">Pengguna ini belum memiliki role yang dapat dikonfigurasi.</p>
            @endif
        </section>
    @endif
</div>
