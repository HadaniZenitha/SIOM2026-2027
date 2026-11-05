<?php

use App\Models\Document;
use App\Models\Lpj;
use App\Models\Ormawa;
use App\Models\Period;
use App\Models\Role;
use App\Models\Submission;
use App\Models\User;
use App\Services\DocumentGeneratorService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function createPreviewTestFixture(): array
{
    Storage::fake('local');
    Storage::fake('public');

    $roleSecretary = Role::firstOrCreate(['name' => 'sekretaris_ormawa'], ['display_name' => 'Sekretaris ORMAWA']);
    $rolePembina = Role::firstOrCreate(['name' => 'pembina_ormawa'], ['display_name' => 'Pembina ORMAWA']);
    $roleWarek = Role::firstOrCreate(['name' => 'warek'], ['display_name' => 'Wakil Rektor']);

    $period = Period::firstOrCreate(['name' => '2026/2027'], [
        'start_date' => '2026-08-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);

    $ormawa = Ormawa::firstOrCreate(['code' => 'HIMA-IF'], [
        'name' => 'HIMA S1 Informatika',
        'type' => 'hima',
    ]);

    $secretary = User::factory()->create([
        'role_id' => $roleSecretary->id,
        'ormawa_id' => $ormawa->id,
    ]);

    $otherSecretary = User::factory()->create([
        'role_id' => $roleSecretary->id,
        'ormawa_id' => null,
    ]);

    $submission = Submission::create([
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'submitted_by' => $secretary->id,
        'submission_number' => 'SUB/2026/001',
        'activity_name' => 'Seminar Teknologi 2026',
        'status' => 'draft',
        'total_amount' => 1500000,
    ]);

    $lpj = Lpj::create([
        'submission_id' => $submission->id,
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'created_by' => $secretary->id,
        'title' => 'LPJ Seminar Teknologi 2026',
        'status' => 'draft',
    ]);

    return compact('secretary', 'otherSecretary', 'submission', 'lpj', 'ormawa', 'period');
}

it('redirects guests to login when accessing preview routes', function () {
    $fixture = createPreviewTestFixture();

    $this->get(route('documents.preview.proposal', $fixture['submission']->id))
        ->assertRedirect(route('login'));

    $this->get(route('documents.preview.proposal.file', $fixture['submission']->id))
        ->assertRedirect(route('login'));

    $this->get(route('documents.preview.lpj', $fixture['lpj']->id))
        ->assertRedirect(route('login'));

    $this->get(route('documents.preview.lpj.file', $fixture['lpj']->id))
        ->assertRedirect(route('login'));
});

it('denies access to users from different ormawa without approval role', function () {
    $fixture = createPreviewTestFixture();

    $this->actingAs($fixture['otherSecretary'])
        ->get(route('documents.preview.proposal', $fixture['submission']->id))
        ->assertForbidden();

    $this->actingAs($fixture['otherSecretary'])
        ->get(route('documents.preview.proposal.file', $fixture['submission']->id))
        ->assertForbidden();

    $this->actingAs($fixture['otherSecretary'])
        ->get(route('documents.preview.lpj', $fixture['lpj']->id))
        ->assertForbidden();
});

it('allows authorized secretary to view proposal preview page', function () {
    $fixture = createPreviewTestFixture();

    $this->actingAs($fixture['secretary'])
        ->get(route('documents.preview.proposal', $fixture['submission']->id))
        ->assertOk()
        ->assertSee('Seminar Teknologi 2026')
        ->assertSee('HIMA S1 Informatika');
});

it('allows authorized secretary to view lpj preview page', function () {
    $fixture = createPreviewTestFixture();

    $this->actingAs($fixture['secretary'])
        ->get(route('documents.preview.lpj', $fixture['lpj']->id))
        ->assertOk()
        ->assertSee('LPJ Seminar Teknologi 2026');
});

it('streams uploaded proposal file inline with correct content-disposition', function () {
    $fixture = createPreviewTestFixture();

    $uploadedFile = UploadedFile::fake()->create('proposal_resmi.pdf', 100, 'application/pdf');
    $path = $uploadedFile->store('documents/proposals', 'local');

    $fixture['submission']->documents()->create([
        'file_name' => 'proposal_resmi.pdf',
        'file_path' => $path,
        'type' => 'proposal',
        'uploaded_by' => $fixture['secretary']->id,
    ]);

    $response = $this->actingAs($fixture['secretary'])
        ->get(route('documents.preview.proposal.file', $fixture['submission']->id));

    $response->assertOk();
    $disposition = $response->headers->get('content-disposition');
    expect($disposition)->toContain('inline');
    expect($disposition)->toContain('proposal_resmi.pdf');
    expect($response->headers->get('content-type'))->toBe('application/pdf');
});

it('streams uploaded attachment file inline', function () {
    $fixture = createPreviewTestFixture();

    $uploadedFile = UploadedFile::fake()->create('nota_konsumsi.png', 50, 'image/png');
    $path = $uploadedFile->store('documents/receipts', 'local');

    $doc = Document::create([
        'submission_id' => $fixture['submission']->id,
        'lpj_id' => $fixture['lpj']->id,
        'file_name' => 'nota_konsumsi.png',
        'file_path' => $path,
        'type' => 'nota',
        'uploaded_by' => $fixture['secretary']->id,
    ]);

    $response = $this->actingAs($fixture['secretary'])
        ->get(route('documents.preview.attachment.file', $doc->id));

    $response->assertOk();
    $disposition = $response->headers->get('content-disposition');
    expect($disposition)->toContain('inline');
    expect($response->headers->get('content-type'))->toBe('image/png');
});

it('restricts signed document preview to approved status only', function () {
    $fixture = createPreviewTestFixture();

    // Draft status should return 404
    $this->actingAs($fixture['secretary'])
        ->get(route('documents.preview.signed-proposal', $fixture['submission']->id))
        ->assertNotFound();

    $this->actingAs($fixture['secretary'])
        ->get(route('documents.preview.signed-lpj', $fixture['lpj']->id))
        ->assertNotFound();
});
