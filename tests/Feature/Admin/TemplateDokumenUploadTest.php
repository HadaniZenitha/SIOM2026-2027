<?php

use App\Livewire\Admin\TemplateDokumen\Index as TemplateDokumenIndex;
use App\Models\DocumentTemplate;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('allows an admin to upload a combined proposal and nota dinas template', function () {
    Storage::fake('public');
    $admin = User::factory()->create();

    Livewire::actingAs($admin)
        ->test(TemplateDokumenIndex::class)
        ->set('showForm', true)
        ->assertSee('Proposal Kegiatan')
        ->set('name', 'Template Proposal dan Nota Dinas')
        ->set('type', 'proposal')
        ->set('file', UploadedFile::fake()->create(
            'proposal.docx',
            100,
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ))
        ->call('save')
        ->assertHasNoErrors();

    $template = DocumentTemplate::query()
        ->where('type', 'proposal')
        ->firstOrFail();

    expect($template->name)->toBe('Template Proposal dan Nota Dinas')
        ->and($template->is_active)->toBeTrue();

    Storage::disk('public')->assertExists($template->file_path);
});
