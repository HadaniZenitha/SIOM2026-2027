<?php

use App\Livewire\Sekretaris\Logo\Index as LogoIndex;
use App\Models\Ormawa;
use App\Models\Period;
use App\Models\Role;
use App\Models\Submission;
use App\Models\User;
use App\Services\DocumentGeneratorService;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');

    $this->roleSecretary = Role::firstOrCreate(['name' => 'sekretaris_ormawa'], ['display_name' => 'Sekretaris ORMAWA']);
    $this->period = Period::firstOrCreate(['name' => '2026/2027'], [
        'start_date' => '2026-08-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);

    $this->ormawa = Ormawa::firstOrCreate(['code' => 'HIMA-TI'], [
        'name' => 'Himpunan Mahasiswa Teknologi Informasi',
        'type' => 'hima',
    ]);

    $this->secretary = User::factory()->create([
        'role_id' => $this->roleSecretary->id,
        'ormawa_id' => $this->ormawa->id,
    ]);
});

it('provides default logo url when no custom logo is set', function () {
    expect($this->ormawa->hasCustomLogo())->toBeFalse();
    expect($this->ormawa->logoUrl())->toContain('default-ormawa-logo.png');
});

it('allows authorized secretary to access logo settings page', function () {
    $this->actingAs($this->secretary)
        ->get(route('sekretaris.logo'))
        ->assertOk()
        ->assertSee('Pengaturan Logo Presisi')
        ->assertSee($this->ormawa->name);
});

it('saves calibrated logo and updates ormawa record', function () {
    // Generate valid 1x1 transparent png data url
    $pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
    $dataUrl = 'data:image/png;base64,' . $pngBase64;

    Livewire::actingAs($this->secretary)
        ->test(LogoIndex::class)
        ->call('saveCalibratedLogo', $dataUrl, [
            'zoom' => 1.25,
            'offsetX' => 10,
            'offsetY' => -5,
            'rotation' => 0,
            'bgWhite' => true,
        ])
        ->assertDispatched('logo-updated');

    $this->ormawa->refresh();
    expect($this->ormawa->hasCustomLogo())->toBeTrue();
    expect($this->ormawa->logo)->not->toBeNull();
    Storage::disk('public')->assertExists($this->ormawa->logo);
    Storage::disk('public')->assertExists('ormawa-logos/config_' . $this->ormawa->id . '.json');
});

it('allows secretary to delete custom logo and revert to default', function () {
    $this->ormawa->update(['logo' => 'ormawa-logos/test_logo.png']);
    Storage::disk('public')->put('ormawa-logos/test_logo.png', 'fake image bytes');

    Livewire::actingAs($this->secretary)
        ->test(LogoIndex::class)
        ->call('deleteLogo')
        ->assertDispatched('logo-updated');

    $this->ormawa->refresh();
    expect($this->ormawa->hasCustomLogo())->toBeFalse();
    expect($this->ormawa->logo)->toBeNull();
    Storage::disk('public')->assertMissing('ormawa-logos/test_logo.png');
});

