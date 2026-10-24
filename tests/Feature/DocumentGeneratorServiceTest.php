<?php

use App\Livewire\Sekretaris\Pengajuan\Create as SubmissionCreate;
use App\Livewire\Sekretaris\Pengajuan\Index as SubmissionIndex;
use App\Models\ApprovalStep;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\OrganizationMember;
use App\Models\Ormawa;
use App\Models\Period;
use App\Models\Role;
use App\Models\Submission;
use App\Models\SubmissionItem;
use App\Models\User;
use App\Services\DocumentGeneratorService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PhpOffice\PhpWord\PhpWord;
use ZipArchive;

function createDocumentGenerationFixture(): array
{
    $period = Period::create([
        'name' => '2026/2027',
        'start_date' => '2026-08-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);

    $ormawa = Ormawa::create([
        'code' => 'BEM',
        'name' => 'Badan Eksekutif Mahasiswa',
    ]);

    $submitter = User::factory()->create([
        'ormawa_id' => $ormawa->id,
    ]);

    $submission = Submission::create([
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'submitted_by' => $submitter->id,
        'submission_number' => 'SUB/2026/DOC001',
        'activity_name' => 'Seminar Informatika',
        'background' => 'Latar belakang kegiatan',
        'objectives' => ['Meningkatkan pengetahuan'],
        'benefits' => ['Menambah wawasan'],
        'target_participants' => 'Mahasiswa',
        'status' => 'draft',
        'total_amount' => 12500,
        'chairman_name' => 'Ketua Manual',
        'chairman_nim' => '23001001',
        'pembina_name' => 'Pembina Manual',
        'pembina_nuptk' => '111222333',
        'kaprodi_name' => 'Kaprodi Manual',
        'kaprodi_nuptk' => '444555666',
    ]);

    return compact('ormawa', 'period', 'submission', 'submitter');
}

it('generates and downloads proposal and nota dinas together from the proposal template', function () {
    Storage::fake('public');
    $fixture = createDocumentGenerationFixture();
    $templatePath = 'document-templates/proposal.docx';
    $absoluteTemplatePath = Storage::disk('public')->path($templatePath);
    Storage::disk('public')->makeDirectory('document-templates');

    $word = new PhpWord;
    $section = $word->addSection();
    $section->addText('${nomor_nota} | ${meeting_date}');
    $section->addText('${circular_letter} | ${budget_code}');
    $section->addText('${chairman_name} | ${chairman_nim}');
    $section->addText('${leader_title} | ${leader_name} | ${leader_nim}');
    $section->addText('${kahim_title} | ${kahim_name} | ${kahim_nim}');
    $section->addText('${pembina_name} | ${pembina_nuptk}');
    $section->addText('${kaprodi_name} | ${kaprodi_nuptk}');
    $section->addText('${objectives}');
    $section->addText('${benefits}');
    $table = $section->addTable();
    $row = $table->addRow();
    $row->addCell()->addText('${item_name}');
    $row->addCell()->addText('${price}');
    $row->addCell()->addText('${subtotal}');
    $rab2Table = $section->addTable();
    $rab2Row = $rab2Table->addRow();
    $rab2Row->addCell()->addText('${rab2_item_name}');
    $rab2Row->addCell()->addText('${rab2_price}');
    $rab2Row->addCell()->addText('${rab2_subtotal}');
    $word->save($absoluteTemplatePath, 'Word2007');

    DocumentTemplate::create([
        'period_id' => $fixture['period']->id,
        'created_by' => $fixture['submitter']->id,
        'name' => 'Proposal dan Nota Dinas',
        'type' => 'proposal',
        'file_path' => $templatePath,
        'file_name' => 'proposal.docx',
        'version' => 1,
        'is_active' => true,
    ]);
    $fixture['submission']->update([
        'nota_number' => 'ND/2026/001',
        'meeting_date' => '2026-10-10',
        'circular_letter' => 'SK/01/2026',
        'budget_code' => 'ANG-100',
    ]);
    OrganizationMember::create([
        'period_id' => $fixture['period']->id,
        'ormawa_id' => $fixture['ormawa']->id,
        'name' => 'Presiden BEM',
        'birth_place' => 'Bandung',
        'birth_date' => '2002-01-01',
        'nim' => '23001002',
        'phone' => '08123456789',
        'position' => 'presiden_mahasiswa',
        'status' => 'active',
    ]);

    SubmissionItem::create([
        'submission_id' => $fixture['submission']->id,
        'item_name' => 'Spanduk',
        'qty' => 1,
        'unit' => 'buah',
        'price' => 12500,
        'total_price' => 12500,
    ]);

    $generatedPath = app(DocumentGeneratorService::class)
        ->generateProposal($fixture['submission']);

    expect(Storage::disk('public')->exists($generatedPath))->toBeTrue();

    $archive = new ZipArchive;
    expect($archive->open(Storage::disk('public')->path($generatedPath)))->toBeTrue();
    $documentXml = $archive->getFromName('word/document.xml');
    $archive->close();

    expect($documentXml)->toContain('ND/2026/001')
        ->toContain('10 October 2026')
        ->toContain('SK/01/2026')
        ->toContain('ANG-100')
        ->toContain('Ketua Manual')
        ->toContain('23001001')
        ->toContain('Presiden Mahasiswa')
        ->toContain('Presiden BEM')
        ->toContain('23001002')
        ->toContain('Pembina Manual')
        ->toContain('111222333')
        ->toContain('Kaprodi Manual')
        ->toContain('444555666')
        ->toContain('Meningkatkan pengetahuan')
        ->toContain('Menambah wawasan')
        ->toContain('Rp 12.500')
        ->toContain('Spanduk');

    expect($documentXml)->not->toContain('${chairman_name}');
    expect($documentXml)->not->toContain('${rab2_item_name}');
    expect(substr_count($documentXml, 'Spanduk'))->toBe(2);

    $secretaryRole = Role::create([
        'name' => 'sekretaris_ormawa',
        'display_name' => 'Sekretaris ORMAWA',
    ]);
    $fixture['submitter']->update(['role_id' => $secretaryRole->id]);
    $fixture['submission']->update(['status' => 'waiting_approval']);

    Livewire::actingAs($fixture['submitter'])
        ->test(SubmissionIndex::class)
        ->call('downloadProposal', $fixture['submission']->id)
        ->assertFileDownloaded('Proposal_seminar-informatika.docx');
});

it('reports a missing active document template instead of returning an empty document', function () {
    $fixture = createDocumentGenerationFixture();

    expect(fn () => app(DocumentGeneratorService::class)
        ->generateProposal($fixture['submission']))
        ->toThrow(RuntimeException::class, 'Template aktif proposal yang berisi Nota Dinas belum tersedia.');
});

it('shows a useful error when the secretary cannot download a proposal without an active template', function () {
    $fixture = createDocumentGenerationFixture();
    $secretaryRole = Role::create([
        'name' => 'sekretaris_ormawa',
        'display_name' => 'Sekretaris ORMAWA',
    ]);
    $fixture['submitter']->update(['role_id' => $secretaryRole->id]);
    $fixture['submission']->update(['status' => 'waiting_approval']);

    Livewire::actingAs($fixture['submitter'])
        ->test(SubmissionIndex::class)
        ->call('downloadProposal', $fixture['submission']->id)
        ->assertSee('Gagal mengunduh proposal: Template aktif proposal yang berisi Nota Dinas belum tersedia.');
});

it('places all approval snapshots in a downloadable final proposal', function () {
    Storage::fake('public');
    Storage::fake('local');
    $fixture = createDocumentGenerationFixture();
    $placeholderPath = 'document-templates/signed-proposal.docx';
    Storage::disk('public')->makeDirectory('document-templates');

    $word = new PhpWord;
    $section = $word->addSection();
    foreach (['pembina_ormawa', 'kaprodi', 'kabag_kemahasiswaan', 'warek'] as $roleName) {
        $section->addText('${signature_'.$roleName.'}');
    }
    $word->save(Storage::disk('public')->path($placeholderPath), 'Word2007');

    DocumentTemplate::create([
        'period_id' => $fixture['period']->id,
        'created_by' => $fixture['submitter']->id,
        'name' => 'Proposal bertanda tangan',
        'type' => 'proposal',
        'file_path' => $placeholderPath,
        'file_name' => 'signed-proposal.docx',
        'version' => 1,
        'is_active' => true,
    ]);

    foreach (['pembina_ormawa', 'kaprodi', 'kabag_kemahasiswaan', 'warek'] as $sequence => $roleName) {
        $role = Role::create(['name' => $roleName, 'display_name' => $roleName]);
        $approver = User::factory()->create(['role_id' => $role->id]);
        $signaturePath = UploadedFile::fake()->image($roleName.'.png')->store('signatures', 'local');
        ApprovalStep::create([
            'submission_id' => $fixture['submission']->id,
            'role_id' => $role->id,
            'sequence' => $sequence + 1,
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'signature_path' => $signaturePath,
        ]);
    }
    $fixture['submission']->update(['status' => 'approved']);

    $path = app(DocumentGeneratorService::class)->generateSignedProposal($fixture['submission']);

    expect(Storage::disk('local')->exists($path))->toBeTrue();
    $archive = new ZipArchive;
    expect($archive->open(Storage::disk('local')->path($path)))->toBeTrue();
    $documentXml = $archive->getFromName('word/document.xml');
    $mediaFiles = collect(range(0, $archive->numFiles - 1))
        ->map(fn (int $index) => $archive->getNameIndex($index))
        ->filter(fn (?string $name): bool => $name !== null && str_starts_with($name, 'word/media/'))
        ->count();
    $archive->close();

    expect($documentXml)->not->toContain('${signature_')
        ->and($mediaFiles)->toBe(4);
});

it('adds approval signatures to the secretary-uploaded proposal without a template', function () {
    Storage::fake('public');
    Storage::fake('local');
    $fixture = createDocumentGenerationFixture();
    $proposalPath = 'proposals/source/uploaded-proposal.docx';
    Storage::disk('local')->makeDirectory('proposals/source');
    $word = new PhpWord;
    $section = $word->addSection();
    foreach (['pembina_ormawa', 'kaprodi', 'kabag_kemahasiswaan', 'warek'] as $roleName) {
        $section->addText('${signature_'.$roleName.'}');
    }
    $word->save(Storage::disk('local')->path($proposalPath), 'Word2007');
    Document::create([
        'submission_id' => $fixture['submission']->id,
        'type' => 'proposal',
        'file_name' => 'proposal.docx',
        'file_path' => $proposalPath,
    ]);

    foreach (['pembina_ormawa', 'kaprodi', 'kabag_kemahasiswaan', 'warek'] as $sequence => $roleName) {
        $role = Role::create(['name' => $roleName, 'display_name' => $roleName]);
        $approver = User::factory()->create(['role_id' => $role->id]);
        $signaturePath = UploadedFile::fake()->image($roleName.'.png')->store('signatures', 'local');
        ApprovalStep::create([
            'submission_id' => $fixture['submission']->id,
            'role_id' => $role->id,
            'sequence' => $sequence + 1,
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'signature_path' => $signaturePath,
        ]);
    }
    $fixture['submission']->update(['status' => 'approved']);

    $path = app(DocumentGeneratorService::class)->generateSignedProposal($fixture['submission']);

    expect(Storage::disk('local')->exists($path))->toBeTrue();
    $archive = new ZipArchive;
    expect($archive->open(Storage::disk('local')->path($path)))->toBeTrue();
    $documentXml = $archive->getFromName('word/document.xml');
    $archive->close();

    expect($documentXml)->not->toContain('${signature_');
});

it('saves an uploaded DOCX proposal with the required tracking metadata', function () {
    Storage::fake('local');
    $fixture = createDocumentGenerationFixture();
    $word = new PhpWord;
    $word->addSection()->addText('Proposal siap diajukan');
    Storage::disk('local')->makeDirectory('temporary');
    $temporaryPath = Storage::disk('local')->path('temporary/source.docx');
    $word->save($temporaryPath, 'Word2007');
    $content = file_get_contents($temporaryPath);

    $secretaryRole = Role::create(['name' => 'sekretaris_ormawa', 'display_name' => 'Sekretaris ORMAWA']);
    $fixture['submitter']->update(['role_id' => $secretaryRole->id]);
    Livewire::actingAs($fixture['submitter'])
        ->test(SubmissionCreate::class)
        ->set('sourceType', 'upload')
        ->set('activity_name', 'Seminar Unggahan')
        ->set('uploadedTotalAmount', '25000')
        ->set('proposalUpload', UploadedFile::fake()->createWithContent('proposal.docx', $content))
        ->call('saveDraft')
        ->assertHasNoErrors()
        ->assertRedirect(route('sekretaris.pengajuan'));

    $submission = Submission::query()->where('activity_name', 'Seminar Unggahan')->firstOrFail();
    $document = Document::query()->where('submission_id', $submission->id)->where('type', 'proposal')->firstOrFail();

    expect((float) $submission->total_amount)->toBe(25000.0)
        ->and((float) $submission->items()->sum('total_price'))->toBe(25000.0)
        ->and(Storage::disk('local')->exists($document->file_path))->toBeTrue();
});

it('rejects a non-DOCX proposal upload without modifying the draft', function () {
    Storage::fake('local');
    $fixture = createDocumentGenerationFixture();
    $secretaryRole = Role::create(['name' => 'sekretaris_ormawa', 'display_name' => 'Sekretaris ORMAWA']);
    $fixture['submitter']->update(['role_id' => $secretaryRole->id]);

    Livewire::actingAs($fixture['submitter'])
        ->test(SubmissionCreate::class)
        ->set('sourceType', 'upload')
        ->set('activity_name', 'Draft Tidak Berubah')
        ->set('uploadedTotalAmount', '25000')
        ->set('proposalUpload', UploadedFile::fake()->create('proposal.pdf', 100, 'application/pdf'))
        ->call('saveDraft')
        ->assertHasErrors('proposalUpload');

    expect(Submission::query()->count())->toBe(1)
        ->and($fixture['submission']->fresh()->activity_name)->toBe('Seminar Informatika')
        ->and($fixture['submission']->fresh()->total_amount)->toBe('12500.00');
});

it('does not allow a secretary to generate a document for another ormawa', function () {
    $fixture = createDocumentGenerationFixture();
    $secretaryRole = Role::create([
        'name' => 'sekretaris_ormawa',
        'display_name' => 'Sekretaris ORMAWA',
    ]);
    $fixture['submitter']->update(['role_id' => $secretaryRole->id]);

    $otherOrmawa = Ormawa::create([
        'code' => 'HIMANERS',
        'name' => 'Himpunan Mahasiswa Keperawatan',
    ]);
    $otherSubmission = Submission::create([
        'period_id' => $fixture['period']->id,
        'ormawa_id' => $otherOrmawa->id,
        'submitted_by' => $fixture['submitter']->id,
        'submission_number' => 'SUB/2026/DOC002',
        'activity_name' => 'Kegiatan HIMA lain',
        'status' => 'draft',
    ]);

    expect(fn () => Livewire::actingAs($fixture['submitter'])
        ->test(SubmissionIndex::class)
        ->call('downloadProposal', $otherSubmission->id))
        ->toThrow(ModelNotFoundException::class);
});
