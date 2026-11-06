<?php

namespace App\Livewire\Sekretaris\Logo;

use App\Models\Ormawa;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public ?int $ormawaId = null;

    public function mount(?int $ormawaId = null): void
    {
        $user = Auth::user();

        if ($ormawaId && $user?->hasRole('superadmin')) {
            $this->ormawaId = $ormawaId;
        } else {
            $this->ormawaId = $user?->ormawa_id;
        }
    }

    public function getOrmawaProperty(): ?Ormawa
    {
        if (! $this->ormawaId) {
            return null;
        }

        return Ormawa::find($this->ormawaId);
    }

    public function saveCalibratedLogo(string $imageDataUrl, array $config = []): void
    {
        $ormawa = $this->ormawa;
        if (! $ormawa) {
            session()->flash('error', 'ORMAWA tidak ditemukan.');

            return;
        }

        // Pastikan format data URL valid: data:image/png;base64,...
        if (! preg_match('/^data:image\/(png|jpeg|jpg|webp);base64,(.+)$/', $imageDataUrl, $matches)) {
            session()->flash('error', 'Format gambar tidak valid. Pastikan gambar berhasil diproses.');

            return;
        }

        $imageBinary = base64_decode($matches[2]);
        if ($imageBinary === false || strlen($imageBinary) < 20) {
            session()->flash('error', 'Data gambar rusak atau terlalu kecil.');

            return;
        }

        // Buat direktori penyimpanan
        Storage::disk('public')->makeDirectory('ormawa-logos');

        // Hapus logo kustom lama jika ada
        if ($ormawa->logo && Storage::disk('public')->exists($ormawa->logo)) {
            Storage::disk('public')->delete($ormawa->logo);
        }

        // Simpan file logo presisi baru
        $filename = 'ormawa-logos/logo_'.$ormawa->id.'_'.time().'.png';
        Storage::disk('public')->put($filename, $imageBinary);

        // Simpan konfigurasi presisi untuk restore edit berikutnya
        $configFilename = 'ormawa-logos/config_'.$ormawa->id.'.json';
        Storage::disk('public')->put($configFilename, json_encode($config, JSON_PRETTY_PRINT));

        // Update database
        $ormawa->update(['logo' => $filename]);

        session()->flash('success', 'Logo '.$ormawa->name.' berhasil diperbarui secara presisi!');
        $this->dispatch('logo-updated', logoUrl: asset('storage/'.$filename));
    }

    public function deleteLogo(): void
    {
        $ormawa = $this->ormawa;
        if (! $ormawa) {
            return;
        }

        if ($ormawa->logo && Storage::disk('public')->exists($ormawa->logo)) {
            Storage::disk('public')->delete($ormawa->logo);
        }

        $configFilename = 'ormawa-logos/config_'.$ormawa->id.'.json';
        if (Storage::disk('public')->exists($configFilename)) {
            Storage::disk('public')->delete($configFilename);
        }

        $ormawa->update(['logo' => null]);

        session()->flash('success', 'Logo ORMAWA telah dikembalikan ke logo default.');
        $this->dispatch('logo-updated', logoUrl: asset('images/default-ormawa-logo.png'));
    }

    public function render(): View
    {
        $ormawa = $this->ormawa;
        $savedConfig = [];
        if ($ormawa) {
            $configFilename = 'ormawa-logos/config_'.$ormawa->id.'.json';
            if (Storage::disk('public')->exists($configFilename)) {
                $savedConfig = json_decode(Storage::disk('public')->get($configFilename), true) ?: [];
            }
        }

        return view('livewire.sekretaris.logo.index', [
            'ormawa' => $ormawa,
            'savedConfig' => $savedConfig,
        ]);
    }
}
