<?php

use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Laporan\Index as LaporanIndex;
use App\Models\Lpj;
use App\Models\LpjItem;
use App\Models\OrganizationBudget;
use App\Models\Ormawa;
use App\Models\Period;
use App\Models\ProgramStudi;
use App\Models\Role;
use App\Models\Submission;
use App\Models\User;
use Livewire\Livewire;

it('shows pagu and approved LPJ realization to sekretaris and pembina', function () {
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
    $roles = collect(['sekretaris_ormawa', 'pembina_ormawa'])->mapWithKeys(
        fn (string $name): array => [$name => Role::create([
            'name' => $name,
            'display_name' => str($name)->replace('_', ' ')->title()->toString(),
        ])]
    );
    $submitter = User::factory()->create([
        'role_id' => $roles['sekretaris_ormawa']->id,
        'ormawa_id' => $ormawa->id,
    ]);

    OrganizationBudget::create([
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'budget_amount' => 1000000,
        'allocated_amount' => 400000,
        'remaining_amount' => 600000,
    ]);
    $submission = Submission::create([
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'submitted_by' => $submitter->id,
        'submission_number' => 'SUB/2026/001',
        'activity_name' => 'Seminar Informatika',
        'status' => 'approved',
        'total_amount' => 400000,
    ]);
    $lpj = Lpj::create([
        'submission_id' => $submission->id,
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'created_by' => $submitter->id,
        'title' => 'LPJ Seminar Informatika',
        'status' => 'approved',
    ]);
    LpjItem::create([
        'lpj_id' => $lpj->id,
        'item_name' => 'Konsumsi',
        'qty' => 1,
        'unit' => 'paket',
        'price' => 300000,
        'total_price' => 300000,
        'sort_order' => 1,
    ]);

    foreach ($roles as $role) {
        $user = User::factory()->create([
            'role_id' => $role->id,
            'ormawa_id' => $ormawa->id,
        ]);

        Livewire::actingAs($user)
            ->test(DashboardIndex::class)
            ->assertSee('Kondisi Pagu Saat Ini')
            ->assertSee('Seminar Informatika')
            ->assertSee('Rp 1.000.000')
            ->assertSee('Rp 600.000')
            ->assertSee('Rp 300.000');
    }
});

it('shows the same read-only LPJ report to sekretaris and pembina within their ormawa', function () {
    $period = Period::create([
        'name' => '2026/2027',
        'start_date' => '2026-08-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);
    $ownOrmawa = Ormawa::create([
        'name' => 'HIMA Informatika',
        'code' => 'HIMA_IF',
        'type' => 'hima',
    ]);
    $otherOrmawa = Ormawa::create([
        'name' => 'HIMA Sistem Informasi',
        'code' => 'HIMA_SI',
        'type' => 'hima',
    ]);
    $roles = collect(['sekretaris_ormawa', 'pembina_ormawa'])->mapWithKeys(
        fn (string $name): array => [$name => Role::create([
            'name' => $name,
            'display_name' => str($name)->replace('_', ' ')->title()->toString(),
        ])]
    );
    OrganizationBudget::create([
        'period_id' => $period->id,
        'ormawa_id' => $ownOrmawa->id,
        'budget_amount' => 1500000,
        'allocated_amount' => 250000,
        'remaining_amount' => 1250000,
    ]);
    OrganizationBudget::create([
        'period_id' => $period->id,
        'ormawa_id' => $otherOrmawa->id,
        'budget_amount' => 9999999,
        'allocated_amount' => 0,
        'remaining_amount' => 9999999,
    ]);
    $submitterRole = $roles['sekretaris_ormawa'];

    foreach ([$ownOrmawa, $otherOrmawa] as $index => $ormawa) {
        $submitter = User::factory()->create([
            'role_id' => $submitterRole->id,
            'ormawa_id' => $ormawa->id,
        ]);
        $submission = Submission::create([
            'period_id' => $period->id,
            'ormawa_id' => $ormawa->id,
            'submitted_by' => $submitter->id,
            'submission_number' => 'SUB/2026/00'.($index + 1),
            'activity_name' => 'Kegiatan '.$ormawa->name,
            'status' => 'approved',
            'total_amount' => 250000,
        ]);
        Lpj::create([
            'submission_id' => $submission->id,
            'period_id' => $period->id,
            'ormawa_id' => $ormawa->id,
            'created_by' => $submitter->id,
            'title' => 'LPJ '.$ormawa->name,
            'status' => 'submitted',
        ]);
    }

    foreach ($roles as $role) {
        $user = User::factory()->create([
            'role_id' => $role->id,
            'ormawa_id' => $ownOrmawa->id,
        ]);

        $report = Livewire::actingAs($user)
            ->test(LaporanIndex::class)
            ->assertSee('Ringkasan Laporan Pertanggungjawaban')
            ->assertSee('Rp 1.500.000')
            ->assertDontSee('Rp 9.999.999')
            ->assertSee('LPJ HIMA Informatika')
            ->assertDontSee('LPJ HIMA Sistem Informasi');

        if ($role->name === 'pembina_ormawa') {
            $report->assertSee('Hanya dapat melihat');
        }

        $this->actingAs($user)
            ->get(route('laporan'))
            ->assertOk()
            ->assertSee('Ringkasan Laporan Pertanggungjawaban')
            ->assertDontSee('LPJ HIMA Sistem Informasi');
    }
});

it('limits kaprodi reports to proposal and LPJ activity from their program study HIMA', function () {
    $period = Period::create([
        'name' => '2026/2027',
        'start_date' => '2026-08-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);
    $programStudi = ProgramStudi::create([
        'name' => 'Informatika',
        'code' => 'IF',
        'fakultas' => 'Teknik',
    ]);
    $otherProgramStudi = ProgramStudi::create([
        'name' => 'Sistem Informasi',
        'code' => 'SI',
        'fakultas' => 'Teknik',
    ]);
    $hima = Ormawa::create([
        'name' => 'Himpunan Mahasiswa Informatika',
        'code' => 'HIMA_IF',
        'type' => 'hima',
        'program_studi_id' => $programStudi->id,
    ]);
    $otherHima = Ormawa::create([
        'name' => 'Himpunan Mahasiswa Sistem Informasi',
        'code' => 'HIMA_SI',
        'type' => 'hima',
        'program_studi_id' => $otherProgramStudi->id,
    ]);
    $bem = Ormawa::create([
        'name' => 'Badan Eksekutif Mahasiswa',
        'code' => 'BEM',
        'type' => 'bem',
        'program_studi_id' => $programStudi->id,
    ]);
    $role = Role::create([
        'name' => 'kaprodi',
        'display_name' => 'Kaprodi',
    ]);
    $submitterRole = Role::create([
        'name' => 'sekretaris_ormawa',
        'display_name' => 'Sekretaris ORMAWA',
    ]);

    foreach ([$hima, $otherHima, $bem] as $index => $ormawa) {
        $submitter = User::factory()->create([
            'role_id' => $submitterRole->id,
            'ormawa_id' => $ormawa->id,
        ]);
        $submission = Submission::create([
            'period_id' => $period->id,
            'ormawa_id' => $ormawa->id,
            'submitted_by' => $submitter->id,
            'submission_number' => 'SUB/2026/00'.($index + 1),
            'activity_name' => 'Proposal '.$ormawa->name,
            'status' => 'submitted',
        ]);
        Lpj::create([
            'submission_id' => $submission->id,
            'period_id' => $period->id,
            'ormawa_id' => $ormawa->id,
            'created_by' => $submitter->id,
            'title' => 'LPJ '.$ormawa->name,
            'status' => 'submitted',
        ]);
    }

    $user = User::factory()->create([
        'role_id' => $role->id,
        'program_studi_id' => $programStudi->id,
    ]);

    Livewire::actingAs($user)
        ->test(LaporanIndex::class)
        ->assertSee('Aktivitas Pengajuan Proposal HIMA')
        ->assertSee('Proposal Himpunan Mahasiswa Informatika')
        ->assertSee('LPJ Himpunan Mahasiswa Informatika')
        ->assertDontSee('Proposal Himpunan Mahasiswa Sistem Informasi')
        ->assertDontSee('Proposal Badan Eksekutif Mahasiswa')
        ->assertDontSee('LPJ Himpunan Mahasiswa Sistem Informasi')
        ->assertDontSee('LPJ Badan Eksekutif Mahasiswa');
});

it('shows period-wide proposal and LPJ activity for every ormawa to superadmin', function () {
    $activePeriod = Period::create([
        'name' => '2026/2027',
        'start_date' => '2026-08-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);
    $previousPeriod = Period::create([
        'name' => '2025/2026',
        'start_date' => '2025-08-01',
        'end_date' => '2026-07-31',
        'status' => 'finished',
    ]);
    $activeOrmawa = Ormawa::create([
        'name' => 'HIMA Informatika',
        'code' => 'HIMA_IF',
        'type' => 'hima',
    ]);
    $inactiveOrmawa = Ormawa::create([
        'name' => 'UKM Musik',
        'code' => 'UKM_MUSIK',
        'type' => 'ukm',
    ]);
    $previouslyActiveOrmawa = Ormawa::create([
        'name' => 'BEM',
        'code' => 'BEM',
        'type' => 'bem',
    ]);
    $secretaryRole = Role::create([
        'name' => 'sekretaris_ormawa',
        'display_name' => 'Sekretaris ORMAWA',
    ]);
    $secretary = User::factory()->create([
        'role_id' => $secretaryRole->id,
        'ormawa_id' => $activeOrmawa->id,
    ]);
    $firstSubmission = Submission::create([
        'period_id' => $activePeriod->id,
        'ormawa_id' => $activeOrmawa->id,
        'submitted_by' => $secretary->id,
        'submission_number' => 'SUB/REPORT/001',
        'activity_name' => 'Seminar Informatika',
        'status' => 'approved',
    ]);
    Submission::create([
        'period_id' => $activePeriod->id,
        'ormawa_id' => $activeOrmawa->id,
        'submitted_by' => $secretary->id,
        'submission_number' => 'SUB/REPORT/002',
        'activity_name' => 'Pelatihan Informatika',
        'status' => 'submitted',
    ]);
    Submission::create([
        'period_id' => $previousPeriod->id,
        'ormawa_id' => $previouslyActiveOrmawa->id,
        'submitted_by' => $secretary->id,
        'submission_number' => 'SUB/REPORT/003',
        'activity_name' => 'Program BEM tahun lalu',
        'status' => 'approved',
    ]);
    Lpj::create([
        'submission_id' => $firstSubmission->id,
        'period_id' => $activePeriod->id,
        'ormawa_id' => $activeOrmawa->id,
        'created_by' => $secretary->id,
        'title' => 'LPJ Seminar Informatika',
        'status' => 'submitted',
    ]);
    $adminRole = Role::create([
        'name' => 'superadmin',
        'display_name' => 'Superadmin',
    ]);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    Livewire::actingAs($admin)
        ->test(LaporanIndex::class)
        ->assertViewHas('totalProposalCount', 2)
        ->assertViewHas('totalLpjCount', 1)
        ->assertViewHas('activeOrmawaCount', 1)
        ->assertSee('Grafik Aktivitas ORMAWA')
        ->assertSee('1 / 3')
        ->assertSee('HIMA Informatika')
        ->assertSee('UKM Musik')
        ->assertSee('BEM')
        ->assertSee('Belum ada aktivitas');
});
