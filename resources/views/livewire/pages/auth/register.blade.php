<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="siom-guest-card">
    <div class="siom-guest-card-heading">
        <span class="siom-login-form-tag"><i class="bi bi-person-plus-fill me-1"></i> REGISTRASI</span>
        <h1 class="siom-login-form-title">Pendaftaran Akun</h1>
        <p class="siom-login-form-sub">Lengkapi data berikut untuk membuat akun baru pada sistem SIOM.</p>
    </div>

    <form wire:submit="register">
        <!-- Name -->
        <div class="siom-form-group">
            <label for="name" class="siom-form-label">
                <i class="bi bi-person-fill text-primary"></i> Nama Lengkap <span style="color: #ef4444;">*</span>
            </label>
            <div class="siom-input-wrap">
                <i class="bi bi-person siom-input-icon"></i>
                <input wire:model="name" id="name" class="siom-form-control" type="text" name="name" required autofocus autocomplete="name" placeholder="Nama Lengkap Anda" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div class="siom-form-group">
            <label for="email" class="siom-form-label">
                <i class="bi bi-envelope-fill text-primary"></i> Alamat Email Resmi <span style="color: #ef4444;">*</span>
            </label>
            <div class="siom-input-wrap">
                <i class="bi bi-at siom-input-icon"></i>
                <input wire:model="email" id="email" class="siom-form-control" type="email" name="email" required autocomplete="username" placeholder="contoh: nama@siom.test" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="siom-form-group">
            <label for="password" class="siom-form-label">
                <i class="bi bi-shield-lock-fill text-primary"></i> Kata Sandi <span style="color: #ef4444;">*</span>
            </label>
            <div class="siom-input-wrap">
                <i class="bi bi-key-fill siom-input-icon"></i>
                <input wire:model="password" id="password" class="siom-form-control" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div class="siom-form-group">
            <label for="password_confirmation" class="siom-form-label">
                <i class="bi bi-check2-circle text-primary"></i> Konfirmasi Kata Sandi <span style="color: #ef4444;">*</span>
            </label>
            <div class="siom-input-wrap">
                <i class="bi bi-key-fill siom-input-icon"></i>
                <input wire:model="password_confirmation" id="password_confirmation" class="siom-form-control" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <a href="{{ route('login') }}" class="siom-link-forgot" wire:navigate>
                Sudah memiliki akun? Masuk
            </a>

            <button type="submit" class="siom-pill-btn siom-pill-blue" style="padding: 0.6rem 1.4rem; border: 2px solid #0f172a; border-radius: 9999px; font-family: 'Fredoka', sans-serif; font-weight: 700; box-shadow: 2px 2px 0px #0f172a;">
                <i class="bi bi-person-check-fill me-1"></i> Daftar Akun
            </button>
        </div>
    </form>
</div>
