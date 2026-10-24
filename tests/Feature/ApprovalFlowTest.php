<?php

use App\Livewire\Approval\Index as ApprovalIndex;
use App\Livewire\Approval\Show as ApprovalShow;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Sekretaris\Pengajuan\Create as CreateSubmission;
use App\Livewire\Sekretaris\Pengajuan\Index as SubmissionIndex;
use App\Models\OrganizationBudget;
use App\Models\Ormawa;
use App\Models\Period;
use App\Models\Role;
use App\Models\Submission;
use App\Models\User;
use App\Services\ApprovalService;
use App\Services\SubmissionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use RuntimeException;

function createApprovalFlowFixture(): array
{
    Storage::fake('local');

    $roles = collect([
        'sekretaris_ormawa',
        'pembina_ormawa',
        'kaprodi',
        'kabag_kemahasiswaan',
        'warek',
    ])->mapWithKeys(function (string $name): array {
        return [$name => Role::create([
            'name' => $name,
            'display_name' => str($name)->replace('_', ' ')->title()->toString(),
        ])];
    });

    $period = Period::create([
        'name' => '2026',
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'status' => 'active',
    ]);

    $ormawa = Ormawa::create([
        'code' => 'BEM',
        'name' => 'BEM',
    ]);

    $submitter = User::factory()->create([
        'role_id' => $roles['sekretaris_ormawa']->id,
        'ormawa_id' => $ormawa->id,
    ]);

    $approvers = $roles
        ->except('sekretaris_ormawa')
        ->map(function (Role $role): User {
            $signaturePath = 'signatures/'.$role->name.'.png';
            Storage::disk('local')->put($signaturePath, 'test-signature');

            return User::factory()->create([
                'role_id' => $role->id,
                'signature_path' => $signaturePath,
            ]);
        });

    $submission = Submission::create([
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'submitted_by' => $submitter->id,
        'submission_number' => 'SUB/2026/TEST0001',
        'activity_name' => 'Seminar Mahasiswa',
        'status' => 'draft',
        'total_amount' => 2000,
    ]);

    $budget = OrganizationBudget::create([
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'budget_amount' => 10000,
        'allocated_amount' => 0,
        'remaining_amount' => 10000,
    ]);

    return compact('approvers', 'budget', 'roles', 'submission', 'submitter');
}

test('pengajuan berjalan berjenjang, dapat direvisi, dan mengalokasikan pagu setelah persetujuan akhir', function () {
    $fixture = createApprovalFlowFixture();
    $submission = $fixture['submission'];
    $approvalService = app(ApprovalService::class);
    $submissionService = app(SubmissionService::class);

    $submissionService->submit($submission);

    expect($submission->fresh()->status)->toBe('waiting_approval')
        ->and($submission->approvalSteps()->pluck('status')->all())
        ->toBe(['pending', 'waiting', 'waiting', 'waiting']);

    $approvalService->approve($submission, $fixture['approvers']['pembina_ormawa'], 'Disetujui.');
    $approvalService->requestRevision(
        $submission,
        $fixture['approvers']['kaprodi'],
        'Mohon sesuaikan jadwal kegiatan.'
    );

    expect($submission->fresh()->status)->toBe('revision')
        ->and($submission->approvalSteps()->where('sequence', 2)->value('status'))
        ->toBe('revision');

    $submissionService->resubmit($submission, $fixture['submitter']);

    expect($submission->fresh()->status)->toBe('waiting_approval')
        ->and($submission->approvalSteps()->where('sequence', 2)->value('status'))
        ->toBe('pending');

    $approvalService->approve($submission, $fixture['approvers']['kaprodi']);
    $approvalService->approve($submission, $fixture['approvers']['kabag_kemahasiswaan']);
    $approvalService->approve($submission, $fixture['approvers']['warek']);

    expect($submission->fresh()->status)->toBe('approved')
        ->and((float) $fixture['budget']->fresh()->allocated_amount)->toBe(2000.0)
        ->and((float) $fixture['budget']->fresh()->remaining_amount)->toBe(8000.0)
        ->and($submission->approvalLogs()->count())->toBe(6)
        ->and($submission->approvalSteps()->whereNotNull('signature_path')->count())->toBe(4)
        ->and($submission->approvalLogs()->where('action', 'resubmitted')->exists())->toBeTrue();
});

test('approver cannot approve proposal until a signature is stored', function () {
    $fixture = createApprovalFlowFixture();
    $submission = $fixture['submission'];
    app(SubmissionService::class)->submit($submission);

    $approver = $fixture['approvers']['pembina_ormawa'];
    Storage::disk('local')->delete($approver->signature_path);

    expect(fn () => app(ApprovalService::class)->approve($submission, $approver))
        ->toThrow(RuntimeException::class, 'Unggah tanda tangan Anda pada dashboard sebelum menyetujui proposal.');

    expect($submission->fresh()->status)->toBe('waiting_approval')
        ->and($submission->approvalSteps()->where('sequence', 1)->value('status'))->toBe('pending')
        ->and($submission->approvalLogs()->count())->toBe(0);
});

test('approver can store a private signature image from the dashboard', function () {
    Storage::fake('local');
    $role = Role::create(['name' => 'pembina_ormawa', 'display_name' => 'Pembina Ormawa']);
    $user = User::factory()->create(['role_id' => $role->id]);

    Livewire::actingAs($user)
        ->test(DashboardIndex::class)
        ->set('signatureFile', UploadedFile::fake()->image('signature.png'))
        ->call('uploadSignature')
        ->assertHasNoErrors()
        ->assertSee('Tanda tangan berhasil disimpan.');

    $path = $user->fresh()->signature_path;
    expect($path)->toStartWith('signatures/');
    expect(Storage::disk('local')->exists($path))->toBeTrue();
});

test('approver cannot store a non-image as a signature', function () {
    Storage::fake('local');
    $role = Role::create(['name' => 'pembina_ormawa', 'display_name' => 'Pembina Ormawa']);
    $user = User::factory()->create(['role_id' => $role->id]);

    Livewire::actingAs($user)
        ->test(DashboardIndex::class)
        ->set('signatureFile', UploadedFile::fake()->create('signature.pdf', 100, 'application/pdf'))
        ->call('uploadSignature')
        ->assertHasErrors('signatureFile');

    expect($user->fresh()->signature_path)->toBeNull();
});

test('approver dashboard shows approved proposals and the complete decision history', function () {
    $fixture = createApprovalFlowFixture();
    $submission = $fixture['submission'];
    app(SubmissionService::class)->submit($submission);
    $approvalService = app(ApprovalService::class);

    foreach (['pembina_ormawa', 'kaprodi', 'kabag_kemahasiswaan', 'warek'] as $roleName) {
        $approvalService->approve($submission, $fixture['approvers'][$roleName]);
    }

    Livewire::actingAs($fixture['approvers']['warek'])
        ->test(DashboardIndex::class)
        ->assertSee('Proposal yang Telah Disetujui')
        ->assertSee($submission->activity_name)
        ->assertSee('Keputusan Persetujuan Saya')
        ->assertSee('Warek');
});

test('hanya pemilik tahap aktif yang dapat mengambil keputusan', function () {
    $fixture = createApprovalFlowFixture();
    $submission = $fixture['submission'];
    $submissionService = app(SubmissionService::class);
    $approvalService = app(ApprovalService::class);

    $submissionService->submit($submission);

    expect(fn () => $approvalService->approve(
        $submission,
        $fixture['approvers']['warek']
    ))->toThrow(RuntimeException::class, 'Anda tidak memiliki tahap approval yang sedang menunggu.');

    expect($submission->fresh()->status)->toBe('waiting_approval')
        ->and($submission->approvalSteps()->where('status', 'pending')->value('sequence'))
        ->toBe(1)
        ->and($submission->approvalLogs()->count())->toBe(0);
});

test('persetujuan akhir gagal secara atomik saat sisa pagu tidak mencukupi', function () {
    $fixture = createApprovalFlowFixture();
    $submission = $fixture['submission'];
    $submissionService = app(SubmissionService::class);
    $approvalService = app(ApprovalService::class);

    $submissionService->submit($submission);
    $approvalService->approve($submission, $fixture['approvers']['pembina_ormawa']);
    $approvalService->approve($submission, $fixture['approvers']['kaprodi']);
    $approvalService->approve($submission, $fixture['approvers']['kabag_kemahasiswaan']);
    $fixture['budget']->update(['remaining_amount' => 100]);

    expect(fn () => $approvalService->approve(
        $submission,
        $fixture['approvers']['warek']
    ))->toThrow(RuntimeException::class, 'Sisa pagu tidak mencukupi.');

    expect($submission->fresh()->status)->toBe('waiting_approval')
        ->and($submission->approvalSteps()->where('sequence', 4)->value('status'))
        ->toBe('pending')
        ->and($submission->approvalLogs()->count())->toBe(3)
        ->and((float) $fixture['budget']->fresh()->allocated_amount)->toBe(0.0);
});

test('antrian hanya menampilkan pengajuan untuk tahap approver yang sedang aktif', function () {
    $fixture = createApprovalFlowFixture();
    $submission = $fixture['submission'];
    app(SubmissionService::class)->submit($submission);

    Livewire::actingAs($fixture['approvers']['pembina_ormawa'])
        ->test(ApprovalIndex::class)
        ->assertSee($submission->submission_number);

    Livewire::actingAs($fixture['approvers']['warek'])
        ->test(ApprovalIndex::class)
        ->assertDontSee($submission->submission_number);
});

test('sekretaris melihat jabatan peninjau pada status pengajuan yang menunggu persetujuan', function () {
    $fixture = createApprovalFlowFixture();
    $submission = $fixture['submission'];
    app(SubmissionService::class)->submit($submission);

    Livewire::actingAs($fixture['submitter'])
        ->test(SubmissionIndex::class)
        ->assertSee('Sedang ditinjau oleh Pembina Ormawa');
});

test('approver dapat mengirim permintaan revisi melalui halaman detail', function () {
    $fixture = createApprovalFlowFixture();
    $submission = $fixture['submission'];
    app(SubmissionService::class)->submit($submission);

    Livewire::actingAs($fixture['approvers']['pembina_ormawa'])
        ->test(ApprovalShow::class, ['submission' => $submission])
        ->set('note', 'Mohon lengkapi rincian kegiatan.')
        ->call('requestRevision')
        ->assertHasNoErrors();

    expect($submission->fresh()->status)->toBe('revision')
        ->and($submission->approvalLogs()->value('note'))
        ->toBe('Mohon lengkapi rincian kegiatan.');
});

test('form pengajuan mempertahankan konteks timeline sebagai array', function () {
    $fixture = createApprovalFlowFixture();

    Livewire::actingAs($fixture['submitter'])
        ->test(CreateSubmission::class)
        ->set('plan_context.0', 'Rapat penyusunan konsep')
        ->assertSet('plan_context.0', 'Rapat penyusunan konsep');
});

test('sekretaris dapat menyimpan draft dengan tujuan kegiatan sebagai teks', function () {
    $fixture = createApprovalFlowFixture();

    Livewire::actingAs($fixture['submitter'])
        ->test(CreateSubmission::class)
        ->set('activity_name', 'Seminar Mahasiswa')
        ->set('background', 'Latar belakang kegiatan.')
        ->set('objectives', ['Meningkatkan pengetahuan peserta.', 'Mendorong kolaborasi.'])
        ->set('target_participants', 'Mahasiswa')
        ->set('event_date', '2026-11-01')
        ->set('event_start_time', '08:00')
        ->set('event_end_time', '09:00')
        ->set('event_location', 'Aula Kampus')
        ->set('committees.0.name', 'Panitia')
        ->set('committees.0.committee_role', 'Ketua')
        ->set('rundowns.0.time_slot', '08:00 - 09:00')
        ->set('rundowns.0.event_name', 'Pembukaan')
        ->set('items.0.item_name', 'Spanduk')
        ->set('items.0.unit', 'pcs')
        ->set('items.0.price', 250)
        ->call('saveDraft')
        ->assertHasNoErrors()
        ->assertRedirect(route('sekretaris.pengajuan', absolute: false));

    $submission = Submission::query()
        ->where('activity_name', 'Seminar Mahasiswa')
        ->latest('id')
        ->firstOrFail();

    expect($submission->purpose)->toBe("Meningkatkan pengetahuan peserta.\nMendorong kolaborasi.");
});

test('sekretaris dapat memperbaiki dan mengirim ulang pengajuan pada tahap revisi', function () {
    $fixture = createApprovalFlowFixture();
    $submission = $fixture['submission'];
    app(SubmissionService::class)->submit($submission);
    app(ApprovalService::class)->requestRevision(
        $submission,
        $fixture['approvers']['pembina_ormawa'],
        'Lengkapi data proposal.'
    );
    $submission->update([
        'chairman_name' => 'Ketua Pelaksana Lama',
        'chairman_nim' => '23001000',
        'pembina_name' => 'Pembina Lama',
        'pembina_nuptk' => '111000000',
        'kaprodi_name' => 'Kaprodi Lama',
        'kaprodi_nuptk' => '444000000',
    ]);

    Livewire::actingAs($fixture['submitter'])
        ->test(CreateSubmission::class, ['submission' => $submission->fresh()])
        ->assertSet('chairman_name', 'Ketua Pelaksana Lama')
        ->assertSet('chairman_nim', '23001000')
        ->assertSet('pembina_name', 'Pembina Lama')
        ->assertSet('pembina_nuptk', '111000000')
        ->assertSet('kaprodi_name', 'Kaprodi Lama')
        ->assertSet('kaprodi_nuptk', '444000000')
        ->set('activity_name', 'Seminar Mahasiswa Revisi')
        ->set('chairman_name', 'Ketua Pelaksana Manual')
        ->set('chairman_nim', '23001001')
        ->set('pembina_name', 'Pembina Manual')
        ->set('pembina_nuptk', '111222333')
        ->set('kaprodi_name', 'Kaprodi Manual')
        ->set('kaprodi_nuptk', '444555666')
        ->set('background', 'Latar belakang kegiatan.')
        ->set('objectives', ['Meningkatkan pengetahuan peserta.'])
        ->set('target_participants', '50 mahasiswa')
        ->set('event_date', '2026-11-01')
        ->set('event_start_time', '08:00')
        ->set('event_end_time', '09:00')
        ->set('event_location', 'Aula Kampus')
        ->set('committees.0.name', 'Panitia')
        ->set('committees.0.committee_role', 'Ketua')
        ->set('rundowns.0.time_slot', '08:00 - 09:00')
        ->set('rundowns.0.event_name', 'Pembukaan')
        ->set('items.0.item_name', 'Spanduk')
        ->set('items.0.unit', 'pcs')
        ->set('items.0.price', 250)
        ->set('plan_context.0', 'Rapat persiapan')
        ->call('submit')
        ->assertHasNoErrors();

    expect($submission->fresh()->activity_name)->toBe('Seminar Mahasiswa Revisi')
        ->and($submission->fresh()->chairman_name)->toBe('Ketua Pelaksana Manual')
        ->and($submission->fresh()->chairman_nim)->toBe('23001001')
        ->and($submission->fresh()->pembina_name)->toBe('Pembina Manual')
        ->and($submission->fresh()->pembina_nuptk)->toBe('111222333')
        ->and($submission->fresh()->kaprodi_name)->toBe('Kaprodi Manual')
        ->and($submission->fresh()->kaprodi_nuptk)->toBe('444555666')
        ->and((float) $submission->fresh()->total_amount)->toBe(250.0)
        ->and($submission->fresh()->status)->toBe('waiting_approval')
        ->and($submission->approvalSteps()->where('sequence', 1)->value('status'))->toBe('pending');
});

test('penolakan melalui halaman detail mewajibkan alasan dan menghentikan alur', function () {
    $fixture = createApprovalFlowFixture();
    $submission = $fixture['submission'];
    app(SubmissionService::class)->submit($submission);

    Livewire::actingAs($fixture['approvers']['pembina_ormawa'])
        ->test(ApprovalShow::class, ['submission' => $submission])
        ->call('reject')
        ->assertHasErrors(['note' => 'required'])
        ->set('note', 'Tidak sesuai ketentuan.')
        ->call('reject')
        ->assertHasNoErrors();

    expect($submission->fresh()->status)->toBe('rejected')
        ->and($submission->approvalSteps()->where('sequence', 1)->value('status'))
        ->toBe('rejected')
        ->and($submission->approvalLogs()->value('action'))
        ->toBe('rejected');
});
