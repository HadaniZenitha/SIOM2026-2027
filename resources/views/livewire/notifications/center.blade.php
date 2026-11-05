<div
    class="siom-notification-center"
    x-on:livewire:navigated.window="$wire.refreshMenuAccess()"
>
    <style>
        .siom-notification-center { position: relative; }
        .siom-notification-center .siom-notification-button {
            position: relative;
            display: inline-flex;
            min-width: 2.45rem;
            min-height: 2.45rem;
            align-items: center;
            justify-content: center;
            border: 1.5px solid #0f172a;
            border-radius: 999px;
            background: #fff;
            color: #0f172a;
            font-size: 1rem;
            cursor: pointer;
        }
        .siom-notification-center .siom-notification-count {
            position: absolute;
            top: -.35rem;
            right: -.35rem;
            min-width: 1.2rem;
            padding: .08rem .25rem;
            border: 1px solid #fff;
            border-radius: 999px;
            background: #dc2626;
            color: #fff;
            font-size: .65rem;
            font-weight: 800;
            line-height: 1.2;
        }
        .siom-notification-center .siom-notification-dropdown {
            position: absolute;
            z-index: 80;
            top: calc(100% + .55rem);
            right: 0;
            width: min(23rem, calc(100vw - 2rem));
            max-height: min(70vh, 32rem);
            overflow-y: auto;
            padding: .85rem;
            border: 2px solid #0f172a;
            border-radius: 14px;
            background: #fff;
            color: #0f172a;
            box-shadow: 4px 4px 0 #0f172a;
        }
        .siom-notification-center .siom-notification-dropdown h2 { margin: 0 0 .65rem; font-size: .95rem; }
        .siom-notification-center .siom-notification-item {
            display: block;
            padding: .7rem;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            color: inherit;
            text-decoration: none;
        }
        .siom-notification-center .siom-notification-item + .siom-notification-item { margin-top: .55rem; }
        .siom-notification-center .siom-notification-item:hover { background: #f8fafc; }
        .siom-notification-center .siom-notification-item strong,
        .siom-notification-center .siom-notification-item small { display: block; }
        .siom-notification-center .siom-notification-item p { margin: .25rem 0; font-size: .8rem; }
        .siom-notification-center .siom-notification-item small { color: #475569; }
        .siom-notification-center .siom-notification-empty { margin: 0; color: #475569; font-size: .85rem; }
        .siom-notification-center .siom-menu-restriction {
            margin-bottom: .65rem;
            padding: .7rem;
            border: 1.5px solid #b91c1c;
            border-radius: 10px;
            background: #fef2f2;
            color: #7f1d1d;
        }
        .siom-notification-center .siom-menu-restriction strong { display: block; }
        .siom-notification-center .siom-menu-restriction p { margin: .25rem 0 0; font-size: .8rem; }
        .siom-notification-center .siom-menu-restriction small { display: block; margin-top: .25rem; }
        .siom-notification-center .siom-notification-severity {
            display: inline-flex;
            margin-bottom: .2rem;
            padding: .15rem .45rem;
            border-radius: 999px;
            background: #fef9c3;
            color: #854d0e;
            font-size: .68rem;
            font-weight: 800;
        }
        .siom-notification-center .siom-notification-severity.is-urgent { background: #fee2e2; color: #991b1b; }
        .siom-notification-center .siom-reminder-overlay {
            position: fixed;
            z-index: 2000;
            inset: 0;
            display: grid;
            place-items: center;
            padding: 1rem;
            background: rgb(15 23 42 / 70%);
        }
        .siom-notification-center .siom-reminder-dialog {
            width: min(34rem, 100%);
            max-height: min(85vh, 42rem);
            overflow-y: auto;
            padding: 1.25rem;
            border: 3px solid #0f172a;
            border-radius: 18px;
            background: #fff;
            box-shadow: 6px 6px 0 #0f172a;
            color: #0f172a;
        }
        .siom-notification-center .siom-reminder-dialog h2 { margin: 0; font-size: 1.25rem; }
        .siom-notification-center .siom-reminder-dialog > p { margin: .4rem 0 1rem; color: #475569; }
        .siom-notification-center .siom-reminder-close {
            width: 100%;
            min-height: 2.6rem;
            margin-top: .9rem;
            border: 2px solid #0f172a;
            border-radius: 999px;
            background: #2563eb;
            color: #fff;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
        }
        .siom-notification-center .siom-reminder-close:disabled { background: #64748b; cursor: not-allowed; }
        .siom-notification-center .siom-access-close {
            position: absolute;
            top: .7rem;
            right: .7rem;
            display: grid;
            width: 2.5rem;
            height: 2.5rem;
            place-items: center;
            border: 2px solid #0f172a;
            border-radius: 999px;
            background: #fff;
            color: #0f172a;
            font-size: 1.25rem;
            font-weight: 800;
            cursor: pointer;
        }
        .siom-notification-center .siom-reminder-dialog.is-access-notice { position: relative; }
    </style>

    <div x-data="{ open: false }" class="siom-notification-menu">
        <button
            type="button"
            class="siom-notification-button"
            aria-label="Informasi notifikasi"
            aria-haspopup="true"
            :aria-expanded="open.toString()"
            @click="open = !open"
            @click.outside="open = false"
        >
            <i class="bi bi-info-circle"></i>
            @if ($reminderCount > 0)
                <span class="siom-notification-count">{{ $reminderCount > 99 ? '99+' : $reminderCount }}</span>
            @endif
        </button>

        <div class="siom-notification-dropdown" x-cloak x-show="open" x-transition>
            <h2>Informasi Notifikasi</h2>
            @foreach ($disabledMenus as $menu)
                <div class="siom-menu-restriction" wire:key="restricted-menu-{{ $menu['key'] }}">
                    <strong>{{ $menu['label'] }} dinonaktifkan</strong>
                    <p>{{ $menu['message'] }}</p>
                    @if ($menu['auto_enable_at'])
                        <small>Aktif otomatis pada {{ $menu['auto_enable_at'] }}.</small>
                    @endif
                </div>
            @endforeach
            @forelse ($reminders as $reminder)
                <a class="siom-notification-item" href="{{ $reminder['url'] }}" wire:navigate @click="open = false" wire:key="notification-{{ $loop->index }}">
                    <span class="siom-notification-severity {{ $reminder['severity'] === 'urgent' ? 'is-urgent' : '' }}">
                        {{ $reminder['severity'] === 'urgent' ? 'Perlu segera' : 'Pengingat' }}
                    </span>
                    <strong>{{ $reminder['title'] }}</strong>
                    <p>{{ $reminder['message'] }}</p>
                    <small>Tanggal kegiatan: {{ $reminder['date'] }}</small>
                </a>
            @empty
                <p class="siom-notification-empty">Belum ada pengingat untuk akun Anda.</p>
            @endforelse
        </div>
    </div>

    @if ($showLoginPopup)
        <div class="siom-reminder-overlay" role="presentation">
            <section
                class="siom-reminder-dialog {{ $showMenuAccessPopup ? 'is-access-notice' : '' }}"
                role="dialog"
                aria-modal="true"
                aria-labelledby="siom-reminder-title"
                x-data="{ seconds: {{ $popupSeconds }}, timer: null }"
                x-init="timer = setInterval(() => { seconds > 0 ? seconds-- : clearInterval(timer) }, 1000)"
            >
                @if ($showMenuAccessPopup)
                    <h2 id="siom-reminder-title">Pemberitahuan Akses Menu</h2>
                    <p>Beberapa menu akun Anda dibatasi sementara karena administrasi belum terlengkapi.</p>
                    @foreach ($disabledMenus as $menu)
                        <div class="siom-menu-restriction" wire:key="popup-restricted-menu-{{ $menu['key'] }}">
                            <strong>{{ $menu['label'] }}</strong>
                            <p>{{ $menu['message'] }}</p>
                            @if ($menu['auto_enable_at'])
                                <small>Aktif otomatis pada {{ $menu['auto_enable_at'] }}.</small>
                            @endif
                        </div>
                    @endforeach
                    @if ($reminderCount > 0)
                        <p>Ada {{ $reminderCount }} pengingat administrasi lainnya yang perlu ditindaklanjuti.</p>
                    @endif
                @else
                    <h2 id="siom-reminder-title">Pengingat untuk Anda</h2>
                    <p>Ada {{ $reminderCount }} hal yang perlu ditindaklanjuti. Tombol tutup tersedia setelah hitung mundur 15 detik.</p>
                @endif

                @if (! $showMenuAccessPopup)
                    @foreach (array_slice($reminders, 0, 5) as $reminder)
                        <div class="siom-notification-item" wire:key="popup-notification-{{ $loop->index }}">
                            <span class="siom-notification-severity {{ $reminder['severity'] === 'urgent' ? 'is-urgent' : '' }}">
                                {{ $reminder['severity'] === 'urgent' ? 'Perlu segera' : 'Pengingat' }}
                            </span>
                            <strong>{{ $reminder['title'] }}</strong>
                            <p>{{ $reminder['message'] }}</p>
                            <small>Tanggal kegiatan: {{ $reminder['date'] }}</small>
                        </div>
                    @endforeach
                @endif

                @if (! $showMenuAccessPopup && $reminderCount > 5)
                    <p>Dan {{ $reminderCount - 5 }} pengingat lainnya tersedia pada tombol informasi notifikasi.</p>
                @endif

                @if ($showMenuAccessPopup)
                    <p aria-live="polite" x-show="seconds > 0">
                        Notifikasi tidak dapat ditutup selama <span x-text="seconds"></span> detik.
                    </p>
                    <button
                        type="button"
                        class="siom-access-close"
                        x-cloak
                        x-show="seconds === 0"
                        wire:click="dismissLoginPopup"
                        aria-label="Tutup pemberitahuan akses menu"
                    >
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                    </button>
                @else
                    <button
                        type="button"
                        class="siom-reminder-close"
                        x-show="seconds > 0"
                        disabled
                        aria-live="polite"
                    >
                        Tunggu <span x-text="seconds"></span> detik untuk menutup
                    </button>
                    <button
                        type="button"
                        class="siom-reminder-close"
                        x-cloak
                        x-show="seconds === 0"
                        wire:click="dismissLoginPopup"
                    >
                        Tutup Pengingat
                    </button>
                @endif
            </section>
        </div>
    @endif
</div>
