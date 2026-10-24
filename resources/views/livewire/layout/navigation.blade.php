<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

@php
    $user = auth()->user();
    $isSuperadmin = $user?->hasRole('superadmin') ?? false;
    $isSecretary = $user?->hasRole('sekretaris_ormawa') ?? false;
    $canApprove = $user?->hasAnyRole([
        'pembina_ormawa',
        'kaprodi',
        'kabag_kemahasiswaan',
        'warek',
    ]) ?? false;
@endphp

<nav
    class="siom-topbar"
    x-data="{ open: false }"
    x-on:livewire:navigated.window="
        open = false;
        $el.querySelectorAll('details.siom-nav-group').forEach(d => d.open = false);
    "
>
    <div class="siom-topbar-inner">
        <div class="siom-topbar-main">
            <a href="{{ route('dashboard') }}" class="siom-nav-brand" wire:navigate>
                <span class="siom-nav-logo-icon"><i class="bi bi-music-note-beamed"></i></span>
                <span class="siom-nav-brand-text">
                    <span class="siom-nav-title">SIOM</span>
                    <span class="siom-nav-sub">Organisasi Mahasiswa</span>
                </span>
            </a>

            <div class="siom-nav-links">
                <a class="siom-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
                   href="{{ route('dashboard') }}"
                   wire:navigate>
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>

                @if ($isSuperadmin)
                    <details class="siom-nav-group" @if (request()->routeIs('admin.*')) open @endif>
                        <summary class="{{ request()->routeIs('admin.*') ? 'is-active' : '' }}">
                            <i class="bi bi-sliders"></i> Administrasi
                        </summary>
                        <div class="siom-nav-menu">
                            <a href="{{ route('admin.ormawa') }}" wire:navigate>
                                <i class="bi bi-diagram-3"></i> ORMAWA dan Pagu
                            </a>
                            <a href="{{ route('admin.program-studi') }}" wire:navigate>
                                <i class="bi bi-mortarboard"></i> Program Studi
                            </a>
                            <a href="{{ route('admin.periode') }}" wire:navigate>
                                <i class="bi bi-calendar3"></i> Periode
                            </a>
                            <a href="{{ route('admin.pengguna') }}" wire:navigate>
                                <i class="bi bi-people"></i> Pengguna
                            </a>
                            <a href="{{ route('admin.template-dokumen') }}" wire:navigate>
                                <i class="bi bi-file-earmark-word"></i> Template Dokumen
                            </a>
                        </div>
                    </details>
                @endif

                @if ($isSecretary)
                    <details class="siom-nav-group" @if (request()->routeIs('sekretaris.*')) open @endif>
                        <summary class="{{ request()->routeIs('sekretaris.*') ? 'is-active' : '' }}">
                            <i class="bi bi-briefcase"></i> Operasional ORMAWA
                        </summary>
                        <div class="siom-nav-menu">
                            <a href="{{ route('sekretaris.anggota') }}" wire:navigate>
                                <i class="bi bi-person-vcard"></i> Anggota
                            </a>
                            <a href="{{ route('sekretaris.pengajuan') }}" wire:navigate>
                                <i class="bi bi-send"></i> Pengajuan Dana
                            </a>
                            <a href="{{ route('sekretaris.notulensi') }}" wire:navigate>
                                <i class="bi bi-journal-text"></i> Notulensi
                            </a>
                            <a href="{{ route('sekretaris.kas') }}" wire:navigate>
                                <i class="bi bi-wallet2"></i> Kas ORMAWA
                            </a>
                            <a href="{{ route('sekretaris.lpj') }}" wire:navigate>
                                <i class="bi bi-clipboard-check"></i> LPJ
                            </a>
                        </div>
                    </details>
                @endif

                @if ($canApprove)
                    <a class="siom-nav-link {{ request()->routeIs('approval.*') ? 'is-active' : '' }}"
                       href="{{ route('approval.index') }}"
                       wire:navigate>
                        <i class="bi bi-check2-square"></i> Persetujuan
                    </a>
                @endif

                <a class="siom-nav-link {{ request()->routeIs('laporan') ? 'is-active' : '' }}"
                   href="{{ route('laporan') }}"
                   wire:navigate>
                    <i class="bi bi-bar-chart"></i> Laporan
                </a>
            </div>
        </div>

        <div class="siom-topbar-actions">
            @if ($user)
                <a href="{{ route('profile') }}" class="siom-nav-profile" wire:navigate>
                    <span class="siom-nav-avatar"><i class="bi bi-person"></i></span>
                    <span>{{ $user->name }}</span>
                </a>
                <button type="button" class="siom-pill-btn siom-pill-white siom-nav-logout" wire:click="logout">
                    <i class="bi bi-box-arrow-right"></i><span>Keluar</span>
                </button>
            @endif

            <button
                type="button"
                class="siom-mobile-toggle"
                :aria-label="open ? 'Tutup navigasi' : 'Buka navigasi'"
                :aria-expanded="open.toString()"
                @click="open = !open"
            >
                <i :class="open ? 'bi bi-x-lg' : 'bi bi-list'"></i>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div class="siom-mobile-menu" x-cloak x-show="open" x-transition>
            <a class="siom-mobile-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
               href="{{ route('dashboard') }}"
               wire:navigate
               @click="open = false">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>

            @if ($isSuperadmin)
                <span class="siom-mobile-heading">Administrasi</span>
                <a class="siom-mobile-link" href="{{ route('admin.ormawa') }}" wire:navigate @click="open = false">
                    <i class="bi bi-diagram-3"></i> ORMAWA dan Pagu
                </a>
                <a class="siom-mobile-link" href="{{ route('admin.program-studi') }}" wire:navigate @click="open = false">
                    <i class="bi bi-mortarboard"></i> Program Studi
                </a>
                <a class="siom-mobile-link" href="{{ route('admin.periode') }}" wire:navigate @click="open = false">
                    <i class="bi bi-calendar3"></i> Periode
                </a>
                <a class="siom-mobile-link" href="{{ route('admin.pengguna') }}" wire:navigate @click="open = false">
                    <i class="bi bi-people"></i> Pengguna
                </a>
                <a class="siom-mobile-link" href="{{ route('admin.template-dokumen') }}" wire:navigate @click="open = false">
                    <i class="bi bi-file-earmark-word"></i> Template Dokumen
                </a>
            @endif

            @if ($isSecretary)
                <span class="siom-mobile-heading">Operasional ORMAWA</span>
                <a class="siom-mobile-link" href="{{ route('sekretaris.anggota') }}" wire:navigate @click="open = false">
                    <i class="bi bi-person-vcard"></i> Anggota
                </a>
                <a class="siom-mobile-link" href="{{ route('sekretaris.pengajuan') }}" wire:navigate @click="open = false">
                    <i class="bi bi-send"></i> Pengajuan Dana
                </a>
                <a class="siom-mobile-link" href="{{ route('sekretaris.notulensi') }}" wire:navigate @click="open = false">
                    <i class="bi bi-journal-text"></i> Notulensi
                </a>
                <a class="siom-mobile-link" href="{{ route('sekretaris.kas') }}" wire:navigate @click="open = false">
                    <i class="bi bi-wallet2"></i> Kas ORMAWA
                </a>
                <a class="siom-mobile-link" href="{{ route('sekretaris.lpj') }}" wire:navigate @click="open = false">
                    <i class="bi bi-clipboard-check"></i> LPJ
                </a>
            @endif

            @if ($canApprove)
                <a class="siom-mobile-link {{ request()->routeIs('approval.*') ? 'is-active' : '' }}"
                   href="{{ route('approval.index') }}"
                   wire:navigate
                   @click="open = false">
                    <i class="bi bi-check2-square"></i> Persetujuan
                </a>
            @endif

            <a class="siom-mobile-link {{ request()->routeIs('laporan') ? 'is-active' : '' }}"
               href="{{ route('laporan') }}"
               wire:navigate
               @click="open = false">
                <i class="bi bi-bar-chart"></i> Laporan
            </a>

            @if ($user)
                <a class="siom-mobile-link" href="{{ route('profile') }}" wire:navigate @click="open = false">
                    <i class="bi bi-person"></i> Profil
                </a>
                <button type="button"
                        class="siom-mobile-link siom-mobile-logout"
                        wire:click="logout"
                        @click="open = false">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </button>
            @endif
        </div>
    </div>
</nav>