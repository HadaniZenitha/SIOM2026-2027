<?php

use App\Livewire\Admin\Pengguna\Index as UserIndex;
use App\Livewire\Approval\Index as ApprovalIndex;
use App\Livewire\Approval\LpjShow;
use App\Livewire\Sekretaris\Anggota\Index as MemberIndex;
use App\Livewire\Sekretaris\Kas\Form as CashForm;
use App\Livewire\Sekretaris\Kas\Index as CashIndex;
use App\Livewire\Sekretaris\LPJ\Form as LpjForm;
use App\Livewire\Sekretaris\LPJ\Index as LpjIndex;
use App\Livewire\Sekretaris\Notulensi\Form as MinutesForm;
use App\Livewire\Sekretaris\Notulensi\Index as MinutesIndex;
use App\Models\CashAccount;
use App\Models\Lpj;
use App\Models\LpjDocument;
use App\Models\Meeting;
use App\Models\OrganizationDivision;
use App\Models\OrganizationMember;
use App\Models\Ormawa;
use App\Models\Period;
use App\Models\ProgramStudi;
use App\Models\Role;
use App\Models\Submission;
use App\Models\User;
use App\Services\LpjApprovalService;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function operationalRole(string $name): Role
{
    return Role::create(['name' => $name, 'display_name' => str($name)->replace('_', ' ')->title()]);
}

function operationalPeriod(): Period
{
    return Period::create([
        'name' => '2026/2027',
        'start_date' => '2026-08-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);
}

test('admin dapat menyimpan program studi untuk sekretaris dan pengguna melihat relasinya', function () {
    $adminRole = operationalRole('superadmin');
    $secretaryRole = operationalRole('sekretaris_ormawa');
    $ormawa = Ormawa::create(['code' => 'HIMA', 'name' => 'HIMA']);
    $prodi = ProgramStudi::create(['code' => 'IF', 'name' => 'S1 Informatika', 'is_active' => true]);
    $division = OrganizationDivision::create(['ormawa_id' => $ormawa->id, 'name' => 'Kominfo']);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    Livewire::actingAs($admin)->test(UserIndex::class)
        ->call('create')
        ->set('name', 'Sekretaris HIMA')
        ->set('email', 'sekretaris@example.test')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role_id', $secretaryRole->id)
        ->set('ormawa_id', $ormawa->id)
        ->set('program_studi_id', $prodi->id)
        ->call('save')
        ->assertHasNoErrors();

    $user = User::query()->where('email', 'sekretaris@example.test')->firstOrFail();
    expect($user->program_studi_id)->toBe($prodi->id)
        ->and($user->fresh()->programStudi->name)->toBe('S1 Informatika');
});

test('sekretaris dapat mengelola data anggota dalam ORMAWA dan periode aktifnya', function () {
    $role = operationalRole('sekretaris_ormawa');
    $period = operationalPeriod();
    $ormawa = Ormawa::create(['code' => 'HIMA', 'name' => 'HIMA']);
    $prodi = ProgramStudi::create(['code' => 'IF', 'name' => 'S1 Informatika', 'is_active' => true]);
    $division = $ormawa->divisions()->create(['name' => 'Kominfo']);
    $secretary = User::factory()->create(['role_id' => $role->id, 'ormawa_id' => $ormawa->id]);

    Livewire::actingAs($secretary)->test(MemberIndex::class)
        ->call('create')
        ->set('name', 'Anggota HIMA')
        ->set('birth_place', 'Bandung')
        ->set('birth_date', '2005-01-01')
        ->set('nim', '20260001')
        ->set('phone', '08123456789')
        ->set('program_studi_id', $prodi->id)
        ->set('organization_division_id', $division->id)
        ->set('position', 'staff_muda')
        ->call('save')
        ->assertHasNoErrors();

    $member = OrganizationMember::query()->where('nim', '20260001')->firstOrFail();
    expect($member->period_id)->toBe($period->id)
        ->and($member->ormawa_id)->toBe($ormawa->id)
        ->and($member->program_studi_id)->toBe($prodi->id)
        ->and($member->organization_division_id)->toBe($division->id)
        ->and($member->position)->toBe('staff_muda');
});

test('sekretaris menyimpan pemasukan kas dan saldo akun diperbarui', function () {
    operationalPeriod();
    $role = operationalRole('sekretaris_ormawa');
    $ormawa = Ormawa::create(['code' => 'BEM', 'name' => 'BEM']);
    $secretary = User::factory()->create(['role_id' => $role->id, 'ormawa_id' => $ormawa->id]);

    Livewire::actingAs($secretary)->test(CashForm::class)
        ->set('type', 'income')
        ->set('category', 'Iuran')
        ->set('amount', '50000')
        ->set('transaction_date', '2026-10-10')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('sekretaris.kas', absolute: false));

    $account = CashAccount::query()->where('ormawa_id', $ormawa->id)->firstOrFail();
    expect((float) $account->current_balance)->toBe(50000.0)
        ->and((float) $account->transactions()->firstOrFail()->amount)->toBe(50000.0);

    Livewire::actingAs($secretary)->test(CashIndex::class)->assertSee('50.000');
});

test('sekretaris tidak dapat menghapus pemasukan yang sudah terpakai', function () {
    operationalPeriod();
    $role = operationalRole('sekretaris_ormawa');
    $ormawa = Ormawa::create(['code' => 'BEM', 'name' => 'BEM']);
    $secretary = User::factory()->create(['role_id' => $role->id, 'ormawa_id' => $ormawa->id]);

    Livewire::actingAs($secretary)->test(CashForm::class)
        ->set('type', 'income')
        ->set('category', 'Iuran')
        ->set('amount', '100')
        ->set('transaction_date', '2026-10-10')
        ->call('save')
        ->assertHasNoErrors();

    Livewire::actingAs($secretary)->test(CashForm::class)
        ->set('type', 'expense')
        ->set('category', 'Konsumsi')
        ->set('amount', '80')
        ->set('transaction_date', '2026-10-11')
        ->call('save')
        ->assertHasNoErrors();

    $income = CashAccount::query()->where('ormawa_id', $ormawa->id)
        ->firstOrFail()->transactions()->where('type', 'income')->firstOrFail();

    Livewire::actingAs($secretary)->test(CashIndex::class)
        ->call('delete', $income->id)
        ->assertSee('Transaksi tidak dapat dihapus karena saldo kas telah terpakai.');

    expect($income->fresh())->not->toBeNull()
        ->and((float) $income->cashAccount->fresh()->current_balance)->toBe(20.0);
});

test('sekretaris menyimpan notulensi beserta absensi anggota', function () {
    $period = operationalPeriod();
    $role = operationalRole('sekretaris_ormawa');
    $ormawa = Ormawa::create(['code' => 'BEM', 'name' => 'BEM']);
    $secretary = User::factory()->create(['role_id' => $role->id, 'ormawa_id' => $ormawa->id]);
    $member = OrganizationMember::create([
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'name' => 'Anggota',
        'birth_place' => 'Bandung',
        'birth_date' => '2005-01-02',
        'nim' => '20260002',
        'phone' => '08123456789',
        'status' => 'active',
    ]);

    Livewire::actingAs($secretary)->test(MinutesForm::class)
        ->set('title', 'Rapat kerja')
        ->set('meeting_date', '2026-10-10')
        ->set('minutes_text', 'Rapat membahas program kerja.')
        ->set("attendances.{$member->id}.status", 'hadir')
        ->call('save')
        ->assertHasNoErrors();

    $meeting = Meeting::query()->where('title', 'Rapat kerja')->firstOrFail();
    expect($meeting->attendances()->where('member_id', $member->id)->value('status'))->toBe('hadir');

    Livewire::actingAs($secretary)->test(MinutesIndex::class)->assertSee('Rapat kerja');
});

test('notulensi menolak absensi anggota di luar ORMAWA sekretaris', function () {
    $period = operationalPeriod();
    $role = operationalRole('sekretaris_ormawa');
    $ormawa = Ormawa::create(['code' => 'BEM', 'name' => 'BEM']);
    $otherOrmawa = Ormawa::create(['code' => 'HIMA', 'name' => 'HIMA']);
    $secretary = User::factory()->create(['role_id' => $role->id, 'ormawa_id' => $ormawa->id]);
    $otherMember = OrganizationMember::create([
        'period_id' => $period->id,
        'ormawa_id' => $otherOrmawa->id,
        'name' => 'Anggota HIMA',
        'birth_place' => 'Bandung',
        'birth_date' => '2005-01-02',
        'nim' => '20260003',
        'phone' => '08123456789',
        'status' => 'active',
    ]);

    Livewire::actingAs($secretary)->test(MinutesForm::class)
        ->set('title', 'Rapat kerja')
        ->set('meeting_date', '2026-10-10')
        ->set('minutes_text', 'Rapat membahas program kerja.')
        ->set("attendances.{$otherMember->id}.status", 'hadir')
        ->call('save')
        ->assertHasErrors('attendances');

    expect(Meeting::query()->where('title', 'Rapat kerja')->exists())->toBeFalse();
});

test('LPJ bergerak berurutan ke seluruh approver dan dapat diminta revisi', function () {
    Storage::fake('local');
    $secretaryRole = operationalRole('sekretaris_ormawa');
    $approvers = collect(['pembina_ormawa', 'kaprodi', 'kabag_kemahasiswaan', 'warek'])
        ->mapWithKeys(fn (string $roleName) => [$roleName => operationalRole($roleName)]);
    $period = operationalPeriod();
    $ormawa = Ormawa::create(['code' => 'BEM', 'name' => 'BEM']);
    $secretary = User::factory()->create(['role_id' => $secretaryRole->id, 'ormawa_id' => $ormawa->id]);
    $submission = Submission::create([
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'submitted_by' => $secretary->id,
        'submission_number' => 'SUB/2026/LPJ0001',
        'activity_name' => 'Seminar',
        'status' => 'approved',
        'total_amount' => 100,
    ]);
    $lpj = Lpj::create([
        'submission_id' => $submission->id,
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'created_by' => $secretary->id,
        'title' => 'LPJ Seminar',
        'status' => 'draft',
    ]);
    LpjDocument::create([
        'lpj_id' => $lpj->id,
        'type' => 'nota',
        'file_path' => 'lpj/receipt.pdf',
        'file_name' => 'receipt.pdf',
    ]);
    LpjDocument::create([
        'lpj_id' => $lpj->id,
        'type' => 'dokumentasi',
        'file_path' => 'lpj/activity.pdf',
        'file_name' => 'activity.pdf',
    ]);
    $service = app(LpjApprovalService::class);
    Livewire::actingAs($secretary)->test(LpjForm::class, ['lpj' => $lpj])->assertSee('Edit LPJ');
    Livewire::actingAs($secretary)
        ->test(LpjIndex::class)
        ->call('submit', $lpj->id)
        ->assertHasNoErrors();

    $lpj->refresh();

    Storage::disk('local')->put('signatures/pembina.png', 'test-signature');
    $pembina = User::factory()->create([
        'role_id' => $approvers['pembina_ormawa']->id,
        'signature_path' => 'signatures/pembina.png',
    ]);
    Livewire::actingAs($secretary)->test(LpjIndex::class)->assertSee('LPJ Seminar');
    $this->actingAs($secretary)
        ->get(route('sekretaris.lpj'))
        ->assertOk()
        ->assertSee('LPJ Seminar');
    Livewire::actingAs($pembina)->test(ApprovalIndex::class)->assertSee('LPJ Seminar');
    Livewire::actingAs($pembina)->test(LpjShow::class, ['lpj' => $lpj])->assertSee('LPJ Seminar');

    $service->decide($lpj, $pembina, 'approved');
    expect($lpj->approvalSteps()->where('sequence', 1)->value('signature_path'))
        ->toBe('signatures/pembina.png');
    $service->decide($lpj, User::factory()->create(['role_id' => $approvers['kaprodi']->id]), 'revision', 'Lengkapi lampiran.');

    expect($lpj->fresh()->status)->toBe('revision')
        ->and($lpj->approvalSteps()->where('status', 'revision')->value('sequence'))->toBe(2);

    $service->submit($lpj);
    expect($lpj->fresh()->status)->toBe('waiting_approval')
        ->and($lpj->approvalSteps()->where('status', 'pending')->value('sequence'))->toBe(2)
        ->and($lpj->approvalLogs()->where('action', 'resubmitted')->exists())->toBeTrue();
});

test('sekretaris dapat menghapus LPJ draft milik ormawanya', function () {
    $secretaryRole = operationalRole('sekretaris_ormawa');
    $period = operationalPeriod();
    $ormawa = Ormawa::create(['code' => 'BEM', 'name' => 'BEM']);
    $secretary = User::factory()->create(['role_id' => $secretaryRole->id, 'ormawa_id' => $ormawa->id]);
    $submission = Submission::create([
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'submitted_by' => $secretary->id,
        'submission_number' => 'SUB/2026/LPJ0002',
        'activity_name' => 'Seminar',
        'status' => 'approved',
        'total_amount' => 100,
    ]);
    $lpj = Lpj::create([
        'submission_id' => $submission->id,
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'created_by' => $secretary->id,
        'title' => 'LPJ Draft',
        'status' => 'draft',
    ]);

    Livewire::actingAs($secretary)
        ->test(LpjIndex::class)
        ->call('delete', $lpj->id)
        ->assertHasNoErrors();

    $this->assertModelMissing($lpj);
});

test('default Livewire page layout points to the application layout', function () {
    expect(config('livewire.layout'))->toBe('layouts.app')
        ->and(view()->exists(config('livewire.layout')))->toBeTrue();
});
