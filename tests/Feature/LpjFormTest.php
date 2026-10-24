<?php

use App\Livewire\Sekretaris\LPJ\Form as LpjForm;
use App\Livewire\Sekretaris\LPJ\Index as LpjIndex;
use App\Models\DocumentTemplate;
use App\Models\Lpj;
use App\Models\LpjItem;
use App\Models\Ormawa;
use App\Models\Period;
use App\Models\Role;
use App\Models\Submission;
use App\Models\SubmissionItem;
use App\Models\User;
use App\Services\DocumentGeneratorService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PhpOffice\PhpWord\PhpWord;

function lpjFormFixture(): array
{
    $secretaryRole = Role::create([
        'name' => 'sekretaris_ormawa',
        'display_name' => 'Sekretaris ORMAWA',
    ]);
    $period = Period::create([
        'name' => '2026/2027',
        'start_date' => '2026-08-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);
    $ormawa = Ormawa::create(['code' => 'HIMA', 'name' => 'HIMA Informatika']);
    $secretary = User::factory()->create([
        'role_id' => $secretaryRole->id,
        'ormawa_id' => $ormawa->id,
    ]);
    $submission = Submission::create([
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'submitted_by' => $secretary->id,
        'submission_number' => 'SUB/2026/LPJ0001',
        'activity_name' => 'Seminar Informatika',
        'activity_theme' => 'Teknologi',
        'status' => 'approved',
        'total_amount' => 10000,
        'event_date' => '2026-10-10',
        'event_location' => 'Aula Kampus',
    ]);
    $submissionItem = SubmissionItem::create([
        'submission_id' => $submission->id,
        'item_name' => 'Konsumsi',
        'qty' => 2,
        'unit' => 'kotak',
        'price' => 5000,
        'total_price' => 10000,
    ]);

    return compact('period', 'ormawa', 'secretary', 'submission', 'submissionItem');
}

it('preloads an approved proposal, allows adjusting its budget, and saves receipts and activity documentation', function () {
    Storage::fake('local');
    $fixture = lpjFormFixture();
    $receipt = UploadedFile::fake()->create('nota.pdf', 100, 'application/pdf');
    $activityPhoto = UploadedFile::fake()->create('dokumentasi.pdf', 100, 'application/pdf');

    Livewire::actingAs($fixture['secretary'])
        ->test(LpjForm::class)
        ->set('submission_id', $fixture['submission']->id)
        ->assertSet('title', 'LPJ Seminar Informatika')
        ->assertSet('budgetItems.0.item_name', 'Konsumsi')
        ->set('budgetItems.0.qty', '3')
        ->set('budgetItems.0.price', '7000')
        ->set('description', 'Kegiatan berjalan lancar.')
        ->set('receiptFiles', [$receipt])
        ->set('activityFiles', [$activityPhoto])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('sekretaris.lpj.edit', Lpj::query()->firstOrFail()));

    $lpj = Lpj::query()->firstOrFail();
    $item = LpjItem::query()->firstOrFail();

    expect($lpj->submission_id)->toBe($fixture['submission']->id)
        ->and($item->submission_item_id)->toBe($fixture['submissionItem']->id)
        ->and((float) $item->total_price)->toBe(21000.0)
        ->and($lpj->documents()->where('type', 'nota')->count())->toBe(1)
        ->and($lpj->documents()->where('type', 'dokumentasi')->count())->toBe(1);

    foreach ($lpj->documents as $document) {
        Storage::disk('local')->assertExists($document->file_path);
    }
});

it('requires a scan of a receipt and activity documentation before an LPJ can be submitted', function () {
    $fixture = lpjFormFixture();
    $lpj = Lpj::create([
        'submission_id' => $fixture['submission']->id,
        'period_id' => $fixture['period']->id,
        'ormawa_id' => $fixture['ormawa']->id,
        'created_by' => $fixture['secretary']->id,
        'title' => 'LPJ Seminar Informatika',
        'status' => 'draft',
    ]);

    Livewire::actingAs($fixture['secretary'])
        ->test(LpjIndex::class)
        ->call('submit', $lpj->id)
        ->assertHasErrors('documents');

    expect($lpj->fresh()->status)->toBe('draft');
});

it('generates a Word LPJ from the active LPJ template using proposal placeholder names and adjusted budget', function () {
    Storage::fake('public');
    $fixture = lpjFormFixture();
    $templatePath = 'document-templates/lpj.docx';
    Storage::disk('public')->makeDirectory('document-templates');

    $word = new PhpWord;
    $section = $word->addSection();
    $section->addText('${activity_name} | ${activity_theme} | ${total_amount}');
    $section->addText('${total_amount_words} | ${execution_date} | ${execution_location}');
    $section->addText('${objectives}');
    $section->addText('${benefits}');
    $table = $section->addTable();
    $row = $table->addRow();
    $row->addCell()->addText('${item_name}');
    $row->addCell()->addText('${qty}');
    $row->addCell()->addText('${price}');
    $row->addCell()->addText('${subtotal}');
    $rab2Table = $section->addTable();
    $rab2Row = $rab2Table->addRow();
    $rab2Row->addCell()->addText('${rab2_item_name}');
    $rab2Row->addCell()->addText('${rab2_qty}');
    $rab2Row->addCell()->addText('${rab2_price}');
    $rab2Row->addCell()->addText('${rab2_subtotal}');
    $word->save(Storage::disk('public')->path($templatePath), 'Word2007');

    DocumentTemplate::create([
        'period_id' => $fixture['period']->id,
        'created_by' => $fixture['secretary']->id,
        'name' => 'Template LPJ',
        'type' => 'lpj',
        'file_path' => $templatePath,
        'file_name' => 'lpj.docx',
        'version' => 1,
        'is_active' => true,
    ]);
    $lpj = Lpj::create([
        'submission_id' => $fixture['submission']->id,
        'period_id' => $fixture['period']->id,
        'ormawa_id' => $fixture['ormawa']->id,
        'created_by' => $fixture['secretary']->id,
        'title' => 'LPJ Seminar Informatika',
        'status' => 'draft',
    ]);
    $lpj->items()->create([
        'submission_item_id' => $fixture['submissionItem']->id,
        'item_name' => 'Konsumsi',
        'qty' => 3,
        'unit' => 'kotak',
        'price' => 7000,
        'total_price' => 21000,
    ]);

    $generatedPath = app(DocumentGeneratorService::class)->generateLpj($lpj);
    $archive = new ZipArchive;
    expect($archive->open(Storage::disk('public')->path($generatedPath)))->toBeTrue();
    $documentXml = $archive->getFromName('word/document.xml');
    $archive->close();

    expect($documentXml)->toContain('Seminar Informatika')
        ->toContain('Teknologi')
        ->toContain('21.000')
        ->toContain('Konsumsi')
        ->not->toContain('${activity_name}');

    Livewire::actingAs($fixture['secretary'])
        ->test(LpjForm::class, ['lpj' => $lpj])
        ->call('downloadWord')
        ->assertFileDownloaded('LPJ_lpj-seminar-informatika.docx');
});
