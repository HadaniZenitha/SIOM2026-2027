<?php

use App\Livewire\Admin\Ormawa\Index as OrmawaIndex;
use App\Livewire\Admin\Pengguna\Index as PenggunaIndex;
use App\Livewire\Admin\Periode\Index as PeriodeIndex;
use App\Livewire\Admin\ProgramStudi\Index as ProgramStudiIndex;
use App\Models\OrganizationBudget;
use App\Models\OrganizationMember;
use App\Models\Ormawa;
use App\Models\Period;
use App\Models\ProgramStudi;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $role = Role::create([
        'name' => 'superadmin',
        'display_name' => 'Superadmin',
    ]);

    $this->admin = User::factory()->create(['role_id' => $role->id]);
});

it('creates and updates an ormawa while preventing deletion when it is in use', function () {
    $admin = $this->admin;

    Livewire::actingAs($admin)
        ->test(OrmawaIndex::class)
        ->call('create')
        ->set('name', 'Himpunan Mahasiswa Informatika')
        ->set('code', 'HIMATIF')
        ->set('description', 'Organisasi program studi informatika')
        ->call('save')
        ->assertHasNoErrors();

    $ormawa = Ormawa::query()->where('code', 'HIMATIF')->firstOrFail();

    expect($ormawa->name)->toBe('Himpunan Mahasiswa Informatika');

    Livewire::actingAs($admin)
        ->test(OrmawaIndex::class)
        ->call('edit', $ormawa->id)
        ->set('name', 'HIMA Informatika')
        ->call('save')
        ->assertHasNoErrors();

    $ormawa->refresh();
    expect($ormawa->name)->toBe('HIMA Informatika');

    User::factory()->create(['ormawa_id' => $ormawa->id]);

    Livewire::actingAs($admin)
        ->test(OrmawaIndex::class)
        ->call('delete', $ormawa->id)
        ->assertSee('ORMAWA tidak dapat dihapus');

    $this->assertModelExists($ormawa);
});

it('persists all program study fields and prevents deletion when in use', function () {
    $admin = $this->admin;

    Livewire::actingAs($admin)
        ->test(ProgramStudiIndex::class)
        ->call('create')
        ->set('code', 'IF')
        ->set('name', 'Informatika')
        ->set('fakultas', 'Teknik')
        ->set('is_active', false)
        ->call('save')
        ->assertHasNoErrors();

    $programStudi = ProgramStudi::query()->where('code', 'IF')->firstOrFail();

    expect($programStudi->fakultas)->toBe('Teknik')
        ->and($programStudi->is_active)->toBeFalse();

    User::factory()->create(['program_studi_id' => $programStudi->id]);

    Livewire::actingAs($admin)
        ->test(ProgramStudiIndex::class)
        ->call('delete', $programStudi->id)
        ->assertSee('Program studi tidak dapat dihapus');

    $this->assertModelExists($programStudi);
});

it('keeps only one active period and rejects an end date before its start date', function () {
    $admin = $this->admin;
    $component = Livewire::actingAs($admin)->test(PeriodeIndex::class);

    $component
        ->call('create')
        ->set('name', '2025/2026')
        ->set('start_date', '2025-08-01')
        ->set('end_date', '2026-07-31')
        ->set('status', 'active')
        ->call('save')
        ->assertHasNoErrors();

    $firstPeriod = Period::query()->where('name', '2025/2026')->firstOrFail();

    $component
        ->call('create')
        ->set('name', '2026/2027')
        ->set('start_date', '2026-08-01')
        ->set('end_date', '2027-07-31')
        ->set('status', 'active')
        ->call('save')
        ->assertHasNoErrors();

    expect($firstPeriod->fresh()->status)->toBe('finished')
        ->and(Period::query()->where('status', 'active')->count())->toBe(1);

    $component
        ->call('create')
        ->set('name', '2027/2028')
        ->set('start_date', '2027-08-01')
        ->set('end_date', '2027-07-31')
        ->call('save')
        ->assertHasErrors(['end_date']);

    expect(Period::query()->where('name', '2027/2028')->exists())->toBeFalse();
});

it('does not delete a period that still owns organization member records', function () {
    $admin = $this->admin;
    $period = Period::create([
        'name' => '2025/2026',
        'start_date' => '2025-08-01',
        'end_date' => '2026-07-31',
        'status' => 'active',
    ]);
    $ormawa = Ormawa::create([
        'name' => 'Badan Eksekutif Mahasiswa',
        'code' => 'BEM',
        'is_active' => true,
    ]);
    $member = OrganizationMember::create([
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'name' => 'Anggota',
        'birth_place' => 'Bandung',
        'birth_date' => '2000-01-01',
        'nim' => '123456',
        'phone' => '08123456789',
        'status' => 'active',
    ]);

    Livewire::actingAs($admin)
        ->test(PeriodeIndex::class)
        ->call('delete', $period->id)
        ->assertSee('Periode tidak dapat dihapus');

    $this->assertModelExists($period);
    $this->assertModelExists($member);
});

it('stores nuptk for newly created users and prevents deleting the current account', function () {
    $admin = $this->admin;
    $role = Role::create([
        'name' => 'kabag_kemahasiswaan',
        'display_name' => 'Kabag Kemahasiswaan',
    ]);

    Livewire::actingAs($admin)
        ->test(PenggunaIndex::class)
        ->call('create')
        ->set('name', 'Pembina')
        ->set('email', 'pembina@example.test')
        ->set('role_id', $role->id)
        ->set('nuptk', 'NUPTK-123')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('save')
        ->assertHasNoErrors();

    expect(User::query()->where('email', 'pembina@example.test')->value('nuptk'))
        ->toBe('NUPTK-123');

    Livewire::actingAs($admin)
        ->test(PenggunaIndex::class)
        ->call('deleteUser', $admin->id)
        ->assertSee('Anda tidak dapat menghapus akun yang sedang digunakan.');

    $this->assertModelExists($admin);
});

it('renders each master data page and its admin navigation for superadmins', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.ormawa'))
        ->assertOk()
        ->assertSee('Administrasi')
        ->assertSee('Master ORMAWA');

    $this->actingAs($this->admin)
        ->get(route('admin.program-studi'))
        ->assertOk()
        ->assertSee('Program Studi');

    $this->actingAs($this->admin)
        ->get(route('admin.periode'))
        ->assertOk()
        ->assertSee('Periode Organisasi');

    $this->actingAs($this->admin)
        ->get(route('admin.pengguna'))
        ->assertOk()
        ->assertSee('Pengguna');

    $this->actingAs($this->admin)
        ->get(route('admin.ormawa'))
        ->assertSee('ORMAWA dan Pagu')
        ->assertSee('Template Dokumen');
});

it('creates and updates an ormawa budget without reducing it below approved allocations', function () {
    $admin = $this->admin;
    $period = Period::create([
        'name' => '2028/2029',
        'start_date' => '2028-08-01',
        'end_date' => '2029-07-31',
        'status' => 'active',
    ]);
    $ormawa = Ormawa::create([
        'name' => 'Badan Eksekutif Mahasiswa',
        'code' => 'BEM',
        'is_active' => true,
    ]);

    Livewire::actingAs($admin)
        ->test(OrmawaIndex::class)
        ->call('editBudget', $ormawa->id)
        ->set('budgetAmount', '100000.00')
        ->call('saveBudget')
        ->assertHasNoErrors()
        ->assertSet('showBudgetForm', false);

    $budget = OrganizationBudget::query()
        ->where('period_id', $period->id)
        ->where('ormawa_id', $ormawa->id)
        ->firstOrFail();

    expect($budget->budget_amount)->toBe('100000.00')
        ->and($budget->remaining_amount)->toBe('100000.00');

    $budget->update([
        'allocated_amount' => 80000,
        'remaining_amount' => 20000,
    ]);

    Livewire::actingAs($admin)
        ->test(OrmawaIndex::class)
        ->call('editBudget', $ormawa->id)
        ->set('budgetAmount', '70000.00')
        ->call('saveBudget')
        ->assertHasErrors(['budgetAmount']);

    expect($budget->fresh()->budget_amount)->toBe('100000.00')
        ->and($budget->fresh()->remaining_amount)->toBe('20000.00');
});

it('shows each signed-in role only the navigation links it can use', function () {
    $secretaryRole = Role::create([
        'name' => 'sekretaris_ormawa',
        'display_name' => 'Sekretaris ORMAWA',
    ]);
    $secretary = User::factory()->create(['role_id' => $secretaryRole->id]);

    $this->actingAs($secretary)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Operasional ORMAWA')
        ->assertSee('Pengajuan Dana')
        ->assertSee('Laporan')
        ->assertDontSee('Administrasi')
        ->assertDontSee('Template Dokumen');

    $approverRole = Role::create([
        'name' => 'kaprodi',
        'display_name' => 'Kaprodi',
    ]);
    $approver = User::factory()->create(['role_id' => $approverRole->id]);

    $this->actingAs($approver)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Persetujuan')
        ->assertSee('Laporan')
        ->assertDontSee('Operasional ORMAWA')
        ->assertDontSee('Administrasi');
});

it('denies pagu changes from users other than superadmins', function () {
    $secretaryRole = Role::create([
        'name' => 'sekretaris_ormawa',
        'display_name' => 'Sekretaris ORMAWA',
    ]);
    $secretary = User::factory()->create(['role_id' => $secretaryRole->id]);

    Livewire::actingAs($secretary)
        ->test(OrmawaIndex::class)
        ->set('selectedPeriodId', 1)
        ->set('budgetOrmawaId', 1)
        ->set('budgetAmount', '1000.00')
        ->call('saveBudget')
        ->assertForbidden();
});
