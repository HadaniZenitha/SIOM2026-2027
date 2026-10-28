<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="siom-guest-card">
    <div class="siom-guest-card-heading">
        <span class="siom-login-form-tag"><i class="bi bi-key-fill me-1"></i> RESET KATA SANDI</span>
        <h1 class="siom-login-form-title">Lupa Kata Sandi?</h1>
        <p class="siom-login-form-sub">Masukkan alamat email resmi terdaftar Anda. Sistem akan mengirimkan tautan untuk mengatur ulang kata sandi.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink">
        <!-- Email Address -->
        <div class="siom-form-group">
            <label for="email" class="siom-form-label">
                <i class="bi bi-envelope-fill text-primary"></i> Alamat Email Terdaftar <span style="color: #ef4444;">*</span>
            </label>
            <div class="siom-input-wrap">
                <i class="bi bi-at siom-input-icon"></i>
                <input wire:model="email" id="email" class="siom-form-control" type="email" name="email" required autofocus placeholder="contoh: nama@siom.test" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <a href="{{ route('login') }}" class="siom-link-forgot" wire:navigate>
                <i class="bi bi-arrow-left"></i> Kembali ke Login
            </a>

            <button type="submit" class="siom-pill-btn siom-pill-blue" style="padding: 0.6rem 1.4rem; border: 2px solid #0f172a; border-radius: 9999px; font-family: 'Fredoka', sans-serif; font-weight: 700; box-shadow: 2px 2px 0px #0f172a;">
                <i class="bi bi-send-fill me-1"></i> Kirim Tautan Reset
            </button>
        </div>
    </form>
</div>
