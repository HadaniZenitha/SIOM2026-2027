<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        Session::forget('url.intended');

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="siom-login-card-wrapper">
    <!-- LEFT PANEL: Institutional Identity & System Highlights -->
    <div class="siom-login-brand-panel">
        <div>
            <div class="siom-login-brand-badge">
                <i class="bi bi-patch-check-fill"></i> PORTAL RESMI ORMAWA
            </div>

            <h2 class="siom-login-brand-title">
                SISTEM INFORMASI <span>ORGANISASI MAHASISWA</span>
            </h2>

            <p class="siom-login-brand-sub">
                Pusat digitalisasi administrasi ormawa, pengajuan proposal kegiatan, verifikasi bertingkat, dan pelaporan LPJ di <strong>ITSK RS dr. Soepraoen Kesdam V/Brw Malang</strong>.
            </p>

            <div class="siom-login-features">
                <div class="siom-login-feature-item">
                    <div class="siom-login-feature-icon" style="background: rgba(37, 99, 235, 0.25); color: #60a5fa;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="siom-login-feature-text">
                        <h4>Proteksi Regulasi H-30 Hari</h4>
                        <p>Validasi otomatis tanggal kegiatan minimal 30 hari kalender sebelum hari pelaksanaan.</p>
                    </div>
                </div>

                <div class="siom-login-feature-item">
                    <div class="siom-login-feature-icon" style="background: rgba(22, 163, 74, 0.25); color: #4ade80;">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <div class="siom-login-feature-text">
                        <h4>Approval Fleksibel & Transparan</h4>
                        <p>Dukungan alur verifikasi HIMA (4 Tahap) dan UKM/DPM (3 Tahap Sentral).</p>
                    </div>
                </div>

                <div class="siom-login-feature-item">
                    <div class="siom-login-feature-icon" style="background: rgba(245, 158, 11, 0.25); color: #fbbf24;">
                        <i class="bi bi-pen-fill"></i>
                    </div>
                    <div class="siom-login-feature-text">
                        <h4>TTE Canvas & Auto DOCX</h4>
                        <p>Tanda tangan digital transparan serta generate 8 template dokumen kampus otomatis.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="siom-login-brand-footer">
            <i class="bi bi-building-check"></i>
            <div class="siom-login-brand-footer-text">
                <strong>Biro Kemahasiswaan & Alumni (BKA)</strong><br>
                Gedung Rektorat Lt. 1 • Jl. S. Supriadi No. 22 Malang
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Authentication Form -->
    <div class="siom-login-form-panel">
        <div class="siom-login-form-header">
            <span class="siom-login-form-tag">
                <i class="bi bi-door-open-fill me-1"></i> MASUK KE SISTEM
            </span>
            <h1 class="siom-login-form-title">Selamat Datang</h1>
            <p class="siom-login-form-sub">Masukkan email institusi dan kata sandi Anda untuk melanjutkan ke SIOM.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Form Login -->
        <form wire:submit="login">
            <!-- Email Field -->
            <div class="siom-form-group">
                <label for="email" class="siom-form-label">
                    <i class="bi bi-envelope-fill text-primary"></i> Alamat Email Resmi <span style="color: #ef4444;">*</span>
                </label>
                <div class="siom-input-wrap">
                    <i class="bi bi-at siom-input-icon"></i>
                    <input wire:model="form.email" id="email" class="siom-form-control" type="email" name="email" required autofocus autocomplete="username" placeholder="contoh: nama@siom.test" />
                </div>
                <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
            </div>

            <!-- Password Field with Visibility Toggle -->
            <div x-data="{ showPass: false }" class="siom-form-group">
                <label for="password" class="siom-form-label">
                    <i class="bi bi-shield-lock-fill text-primary"></i> Kata Sandi <span style="color: #ef4444;">*</span>
                </label>
                <div class="siom-input-wrap">
                    <i class="bi bi-key-fill siom-input-icon"></i>
                    <input :type="showPass ? 'text' : 'password'" wire:model="form.password" id="password" class="siom-form-control" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi Anda" />
                    <button type="button" class="siom-pass-toggle" @click="showPass = !showPass" :title="showPass ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                        <i :class="showPass ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="siom-row-options">
                <label for="remember" class="siom-checkbox-label">
                    <input wire:model="form.remember" id="remember" type="checkbox" class="siom-checkbox-input" name="remember">
                    <span>Ingat saya di perangkat ini</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="siom-link-forgot" href="{{ route('password.request') }}" wire:navigate>
                        Lupa kata sandi?
                    </a>
                @endif
            </div>

            <!-- Submit Button with Loading State -->
            <button type="submit" class="siom-btn-submit" wire:loading.attr="disabled">
                <span wire:loading.remove>
                    <i class="bi bi-box-arrow-in-right me-1"></i> MASUK KE DASHBOARD
                </span>
                <span wire:loading style="display: none;">
                    <i class="bi bi-hourglass-split me-1"></i> MEMVERIFIKASI...
                </span>
            </button>
        </form>

        <div class="siom-login-help-note">
            Belum memiliki hak akses peran atau terkendala login? <br>
            Silakan hubungi <a href="mailto:bka@itsk-soepraoen.ac.id">Biro Kemahasiswaan & Alumni (BKA)</a>
        </div>
    </div>
</div>
