<?php

use App\Livewire\Admin\MenuAccess\Index as MenuAccessIndex;
use App\Livewire\Notifications\Center as NotificationCenter;
use App\Models\Ormawa;
use App\Models\Period;
use App\Models\Role;
use App\Models\RoleMenuSetting;
use App\Models\Submission;
use App\Models\SubmissionTimeline;
use App\Models\User;
use App\Services\MenuAccessService;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

function createReminderTestRole(string $name): Role
{
    return Role::create([
        'name' => $name,
        'display_name' => str($name)->replace('_', ' ')->title()->toString(),
    ]);
}

function createReminderTestSubmission(
    Period $period,
    Ormawa $ormawa,
    User $submitter,
    string $number,
    string $activityName,
    string $status,
    string $activityDate
): Submission {
    return Submission::create([
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'submitted_by' => $submitter->id,
        'submission_number' => $number,
        'activity_name' => $activityName,
        'status' => $status,
        'activity_date' => $activityDate,
    ]);
}

it('reminds sekretaris about pending approvals and LPJs within the appropriate deadlines', function () {
    $this->travelTo(Carbon::parse('2026-11-03 12:00:00'));

    $period = Period::create([
        'name' => '2026/2027',
        'start_date' => '2026-08-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);
    $ormawa = Ormawa::create([
        'name' => 'HIMA Informatika',
        'code' => 'HIMA_IF',
        'type' => 'hima',
    ]);
    $otherOrmawa = Ormawa::create([
        'name' => 'HIMA Sistem Informasi',
        'code' => 'HIMA_SI',
        'type' => 'hima',
    ]);
    $secretaryRole = createReminderTestRole('sekretaris_ormawa');
    $secretary = User::factory()->create([
        'role_id' => $secretaryRole->id,
        'ormawa_id' => $ormawa->id,
    ]);
    $otherSecretary = User::factory()->create([
        'role_id' => $secretaryRole->id,
        'ormawa_id' => $otherOrmawa->id,
    ]);

    createReminderTestSubmission($period, $ormawa, $secretary, 'SUB/REM/001', 'Proposal H-14', 'waiting_approval', '2026-11-17');
    createReminderTestSubmission($period, $ormawa, $secretary, 'SUB/REM/002', 'Proposal H-15', 'waiting_approval', '2026-11-18');
    createReminderTestSubmission($period, $otherOrmawa, $otherSecretary, 'SUB/REM/003', 'Proposal HIMA lain', 'waiting_approval', '2026-11-06');

    $dueLpjSubmission = createReminderTestSubmission($period, $ormawa, $secretary, 'SUB/REM/004', 'LPJ masih dalam tenggat', 'approved', '2026-10-27');
    $overdueLpjSubmission = createReminderTestSubmission($period, $ormawa, $secretary, 'SUB/REM/005', 'LPJ terlambat', 'completed', '2026-10-26');
    $existingLpjSubmission = createReminderTestSubmission($period, $ormawa, $secretary, 'SUB/REM/006', 'LPJ sudah dibuat', 'approved', '2026-11-01');
    $existingLpjSubmission->lpj()->create([
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'created_by' => $secretary->id,
        'title' => 'LPJ sudah dibuat',
        'status' => 'draft',
    ]);
    SubmissionTimeline::create([
        'submission_id' => $dueLpjSubmission->id,
        'stage' => 'pelaksanaan',
        'start_date' => '2026-10-26',
        'end_date' => '2026-10-27',
        'sort_order' => 3,
    ]);
    SubmissionTimeline::create([
        'submission_id' => $overdueLpjSubmission->id,
        'stage' => 'pelaksanaan',
        'start_date' => '2026-10-25',
        'end_date' => '2026-10-26',
        'sort_order' => 3,
    ]);

    expect(Submission::query()->where('activity_name', 'Proposal H-14')->value('status'))->toBe('waiting_approval');
    expect(Submission::query()
        ->where('ormawa_id', $ormawa->id)
        ->whereIn('status', ['submitted', 'revision', 'waiting_approval'])
        ->whereDate('activity_date', '>=', '2026-11-03')
        ->whereDate('activity_date', '<=', '2026-11-17')
        ->count())->toBe(1);

    $notifications = Livewire::actingAs($secretary)->test(NotificationCenter::class);

    $notifications
        ->assertSee('Proposal belum disetujui')
        ->assertSee('Proposal H-14')
        ->assertDontSee('Proposal H-15')
        ->assertDontSee('Proposal HIMA lain')
        ->assertSee('Waktunya membuat LPJ')
        ->assertSee('LPJ masih dalam tenggat')
        ->assertSee('LPJ melewati batas waktu')
        ->assertSee('LPJ terlambat')
        ->assertDontSee('LPJ sudah dibuat')
        ->assertSet('showLoginPopup', true);
});

it('keeps the first-login reminder popup open for at least 15 seconds', function () {
    $this->travelTo(Carbon::parse('2026-11-03 12:00:00'));

    $period = Period::create([
        'name' => '2026/2027',
        'start_date' => '2026-08-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);
    $ormawa = Ormawa::create([
        'name' => 'HIMA Informatika',
        'code' => 'HIMA_IF',
        'type' => 'hima',
    ]);
    $role = createReminderTestRole('sekretaris_ormawa');
    $secretary = User::factory()->create([
        'role_id' => $role->id,
        'ormawa_id' => $ormawa->id,
    ]);
    createReminderTestSubmission($period, $ormawa, $secretary, 'SUB/REM/007', 'Proposal dekat', 'waiting_approval', '2026-11-10');

    $popup = Livewire::actingAs($secretary)->test(NotificationCenter::class)
        ->assertSet('showLoginPopup', true);

    $popup->call('dismissLoginPopup')->assertSet('showLoginPopup', true);

    $this->travel(16)->seconds();

    $popup->call('dismissLoginPopup')->assertSet('showLoginPopup', false);
});

it('shows a reminder popup on the first dashboard visit of a login session', function () {
    $this->travelTo(Carbon::parse('2026-11-03 12:00:00'));

    $period = Period::create([
        'name' => '2026/2027',
        'start_date' => '2026-08-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);
    $ormawa = Ormawa::create([
        'name' => 'HIMA Informatika',
        'code' => 'HIMA_IF',
        'type' => 'hima',
    ]);
    $role = createReminderTestRole('sekretaris_ormawa');
    $secretary = User::factory()->create([
        'role_id' => $role->id,
        'ormawa_id' => $ormawa->id,
    ]);
    createReminderTestSubmission($period, $ormawa, $secretary, 'SUB/REM/008', 'Proposal belum disetujui', 'waiting_approval', '2026-11-10');

    $this->actingAs($secretary)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Pengingat untuk Anda')
        ->assertSee('Proposal belum disetujui');

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Pengingat untuk Anda');
});

it('lets superadmin disable and re-enable a role menu while blocking its route', function () {
    $this->travelTo(Carbon::parse('2026-11-04 10:00:00'));

    $adminRole = createReminderTestRole('superadmin');
    $secretaryRole = createReminderTestRole('sekretaris_ormawa');
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $secretary = User::factory()->create(['role_id' => $secretaryRole->id]);

    Livewire::actingAs($admin)
        ->test(MenuAccessIndex::class)
        ->set('autoEnableAt', '2026-11-11T17:00')
        ->assertSee('Pengaturan Akses Menu')
        ->assertSee('Sekretaris Ormawa')
        ->call('toggle', 'sekretaris_ormawa', 'sekretaris.anggota')
        ->assertSee('berhasil dinonaktifkan')
        ->assertDontSee(route('sekretaris.anggota'));

    $this->assertDatabaseHas('role_menu_settings', [
        'role_id' => $secretaryRole->id,
        'menu_key' => 'sekretaris.anggota',
        'enabled' => false,
        'auto_enable_at' => '2026-11-11 10:00:00',
    ]);

    expect(app(MenuAccessService::class)->isEnabled($secretary, 'sekretaris.anggota'))->toBeFalse();

    $this->actingAs($secretary)
        ->get(route('sekretaris.anggota'))
        ->assertForbidden();

    $this->actingAs($secretary)
        ->get(route('admin.menu-access'))
        ->assertForbidden();

    Livewire::actingAs($admin)
        ->test(MenuAccessIndex::class)
        ->call('toggle', 'sekretaris_ormawa', 'sekretaris.anggota')
        ->assertSee('berhasil diaktifkan');

    $this->assertDatabaseHas('role_menu_settings', [
        'role_id' => $secretaryRole->id,
        'menu_key' => 'sekretaris.anggota',
        'enabled' => true,
        'auto_enable_at' => null,
    ]);
});

it('overrides a role menu for one user and restores the role default on request', function () {
    $adminRole = createReminderTestRole('superadmin');
    $secretaryRole = createReminderTestRole('sekretaris_ormawa');
    $ormawa = Ormawa::create([
        'name' => 'HIMA Informatika',
        'code' => 'HIMA_IF',
        'type' => 'hima',
    ]);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $secretary = User::factory()->create([
        'role_id' => $secretaryRole->id,
        'ormawa_id' => $ormawa->id,
    ]);
    $anotherSecretary = User::factory()->create([
        'role_id' => $secretaryRole->id,
        'ormawa_id' => $ormawa->id,
    ]);
    $menuAccess = app(MenuAccessService::class);
    $menuAccess->toggle($secretaryRole, 'sekretaris.anggota', now()->addDays(7));

    Livewire::actingAs($admin)
        ->test(MenuAccessIndex::class)
        ->set('selectedUserId', $secretary->id)
        ->assertSee('Menu untuk '.$secretary->name)
        ->call('toggleUserMenu', $secretary->id, 'sekretaris.anggota')
        ->assertSee('berhasil diaktifkan')
        ->assertSee('Khusus pengguna');

    $this->assertDatabaseHas('role_menu_settings', [
        'role_id' => $secretaryRole->id,
        'user_id' => $secretary->id,
        'access_scope' => 'user:'.$secretary->id,
        'menu_key' => 'sekretaris.anggota',
        'enabled' => true,
    ]);
    expect($menuAccess->isEnabled($secretary, 'sekretaris.anggota'))->toBeTrue();
    expect($menuAccess->isEnabled($anotherSecretary, 'sekretaris.anggota'))->toBeFalse();

    $this->actingAs($secretary)
        ->get(route('sekretaris.anggota'))
        ->assertOk();
    $this->actingAs($anotherSecretary)
        ->get(route('sekretaris.anggota'))
        ->assertForbidden();

    Livewire::actingAs($admin)
        ->test(MenuAccessIndex::class)
        ->set('selectedUserId', $secretary->id)
        ->call('clearUserMenuOverride', $secretary->id, 'sekretaris.anggota')
        ->assertSee('kembali mengikuti role');

    $this->assertDatabaseMissing('role_menu_settings', [
        'user_id' => $secretary->id,
        'menu_key' => 'sekretaris.anggota',
    ]);
    expect($menuAccess->isEnabled($secretary, 'sekretaris.anggota'))->toBeFalse();

    $this->actingAs($secretary)
        ->get(route('sekretaris.anggota'))
        ->assertForbidden();
});

it('blocks only an individually disabled route and rejects menus outside the selected users role', function () {
    $adminRole = createReminderTestRole('superadmin');
    $secretaryRole = createReminderTestRole('sekretaris_ormawa');
    $advisorRole = createReminderTestRole('pembina_ormawa');
    $ormawa = Ormawa::create([
        'name' => 'HIMA Informatika',
        'code' => 'HIMA_IF',
        'type' => 'hima',
    ]);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $secretary = User::factory()->create([
        'role_id' => $secretaryRole->id,
        'ormawa_id' => $ormawa->id,
    ]);
    $anotherSecretary = User::factory()->create([
        'role_id' => $secretaryRole->id,
        'ormawa_id' => $ormawa->id,
    ]);
    $advisor = User::factory()->create([
        'role_id' => $advisorRole->id,
        'ormawa_id' => $ormawa->id,
    ]);

    Livewire::actingAs($admin)
        ->test(MenuAccessIndex::class)
        ->set('selectedUserId', $secretary->id)
        ->call('toggleUserMenu', $secretary->id, 'sekretaris.anggota')
        ->assertSee('berhasil dinonaktifkan');

    expect(app(MenuAccessService::class)->isEnabled($secretary, 'sekretaris.anggota'))->toBeFalse()
        ->and(app(MenuAccessService::class)->isEnabled($anotherSecretary, 'sekretaris.anggota'))->toBeTrue();

    $this->actingAs($secretary)
        ->get(route('sekretaris.anggota'))
        ->assertForbidden();
    $this->actingAs($anotherSecretary)
        ->get(route('sekretaris.anggota'))
        ->assertOk();

    Livewire::actingAs($admin)
        ->test(MenuAccessIndex::class)
        ->call('toggleUserMenu', $advisor->id, 'sekretaris.anggota')
        ->assertNotFound();

    $this->assertDatabaseMissing('role_menu_settings', [
        'user_id' => $advisor->id,
        'menu_key' => 'sekretaris.anggota',
    ]);
});

it('automatically re-enables a restricted secretary menu and shows its timed access notice', function () {
    $this->travelTo(Carbon::parse('2026-11-04 10:00:00'));

    $adminRole = createReminderTestRole('superadmin');
    $secretaryRole = createReminderTestRole('sekretaris_ormawa');
    $ormawa = Ormawa::create([
        'name' => 'HIMA Informatika',
        'code' => 'HIMA_IF',
        'type' => 'hima',
    ]);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $secretary = User::factory()->create([
        'role_id' => $secretaryRole->id,
        'ormawa_id' => $ormawa->id,
    ]);

    Livewire::actingAs($admin)
        ->test(MenuAccessIndex::class)
        ->set('autoEnableAt', '2026-11-07T17:00')
        ->set('selectedUserId', $secretary->id)
        ->call('toggleUserMenu', $secretary->id, 'sekretaris.anggota')
        ->assertSee('dijadwalkan aktif kembali pada 07/11/2026 17:00 WIB');

    $this->assertDatabaseHas('role_menu_settings', [
        'role_id' => $secretaryRole->id,
        'user_id' => $secretary->id,
        'menu_key' => 'sekretaris.anggota',
        'enabled' => false,
        'auto_enable_at' => '2026-11-07 10:00:00',
    ]);

    Livewire::actingAs($admin)
        ->test(MenuAccessIndex::class)
        ->set('autoEnableAt', '2026-11-07T19:00')
        ->call('scheduleUserMenuEnable', $secretary->id, 'sekretaris.anggota')
        ->assertSee('dijadwalkan aktif kembali pada 07/11/2026 19:00 WIB');

    $this->assertDatabaseHas('role_menu_settings', [
        'role_id' => $secretaryRole->id,
        'user_id' => $secretary->id,
        'menu_key' => 'sekretaris.anggota',
        'enabled' => false,
        'auto_enable_at' => '2026-11-07 12:00:00',
    ]);

    $popup = Livewire::actingAs($secretary)
        ->test(NotificationCenter::class)
        ->assertSet('showLoginPopup', true)
        ->assertSet('showMenuAccessPopup', true)
        ->assertSee('Anda tidak dapat mengakses menu Anggota sampai 07/11/2026 19:00 WIB')
        ->assertSee('karena administrasi belum terlengkapi')
        ->assertSee('Silakan menghubungi Superadmin')
        ->assertSee('Tutup pemberitahuan akses menu');

    $popup->call('dismissLoginPopup')->assertSet('showLoginPopup', true);

    $this->travel(16)->seconds();
    $popup->call('dismissLoginPopup')->assertSet('showLoginPopup', false);

    $this->actingAs($secretary)
        ->get(route('sekretaris.anggota'))
        ->assertForbidden();

    $this->travel(3)->days();
    $this->travel(3)->hours();

    expect(app(MenuAccessService::class)->isEnabled($secretary, 'sekretaris.anggota'))->toBeTrue();

    $this->actingAs($secretary)
        ->get(route('sekretaris.anggota'))
        ->assertOk();
});

it('allows menu access controls for every non-superadmin role but not superadmin', function () {
    $adminRole = createReminderTestRole('superadmin');
    $secretaryRole = createReminderTestRole('sekretaris_ormawa');
    $advisorRole = createReminderTestRole('pembina_ormawa');
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $advisor = User::factory()->create(['role_id' => $advisorRole->id]);
    $ormawa = Ormawa::create([
        'name' => 'HIMA Informatika',
        'code' => 'HIMA_IF',
        'type' => 'hima',
    ]);
    $secretary = User::factory()->create([
        'role_id' => $secretaryRole->id,
        'ormawa_id' => $ormawa->id,
    ]);
    RoleMenuSetting::create([
        'role_id' => $advisorRole->id,
        'access_scope' => 'role',
        'menu_key' => 'laporan',
        'enabled' => false,
    ]);
    RoleMenuSetting::create([
        'role_id' => $adminRole->id,
        'access_scope' => 'role',
        'menu_key' => 'laporan',
        'enabled' => false,
    ]);

    Livewire::actingAs($admin)
        ->test(MenuAccessIndex::class)
        ->assertSee('Sekretaris Ormawa')
        ->assertSee('Pembina Ormawa')
        ->assertSee($secretary->email)
        ->assertSee($advisor->email);

    expect(app(MenuAccessService::class)->matrix())
        ->toHaveCount(2)
        ->and(app(MenuAccessService::class)->isEnabled($admin, 'admin.ormawa'))->toBeTrue()
        ->and(app(MenuAccessService::class)->isEnabled($advisor, 'laporan'))->toBeFalse();

    $this->actingAs($admin)
        ->get(route('laporan'))
        ->assertOk();
    $this->actingAs($advisor)
        ->get(route('laporan'))
        ->assertForbidden();

    Livewire::actingAs($advisor)
        ->test(NotificationCenter::class)
        ->assertSet('showLoginPopup', true)
        ->assertSet('showMenuAccessPopup', true)
        ->assertSee('Beberapa menu akun Anda dibatasi sementara');

    Livewire::actingAs($admin)
        ->test(MenuAccessIndex::class)
        ->call('toggle', 'superadmin', 'admin.ormawa')
        ->assertNotFound();

    Livewire::actingAs($admin)
        ->test(MenuAccessIndex::class)
        ->call('toggleUserMenu', $advisor->id, 'sekretaris.anggota')
        ->assertNotFound();

    Livewire::actingAs($admin)
        ->test(MenuAccessIndex::class)
        ->set('selectedUserId', $secretary->id)
        ->set('autoEnableAt', '2026-11-03T17:00')
        ->call('toggleUserMenu', $secretary->id, 'sekretaris.anggota')
        ->assertHasErrors('autoEnableAt')
        ->assertSee('Pilih tanggal dan jam yang masih akan datang.');

    $this->assertDatabaseHas('role_menu_settings', [
        'role_id' => $advisorRole->id,
        'menu_key' => 'laporan',
        'enabled' => false,
    ]);
});

it('does not show the login popup for roles without secretary reminders', function () {
    $role = createReminderTestRole('pembina_ormawa');
    $user = User::factory()->create(['role_id' => $role->id]);

    Livewire::actingAs($user)
        ->test(NotificationCenter::class)
        ->assertSet('reminderCount', 0)
        ->assertSet('showLoginPopup', false)
        ->assertSee('Belum ada pengingat');

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Informasi notifikasi')
        ->assertSee('Belum ada pengingat');
});
