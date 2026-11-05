<?php

use App\Livewire\Admin\StudentAffairsInfo\Index as StudentAffairsInfoIndex;
use App\Models\Role;
use App\Models\RoleMenuSetting;
use App\Models\StudentAffairsInformation;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function createStudentAffairsRole(string $name): Role
{
    return Role::create([
        'name' => $name,
        'display_name' => str($name)->replace('_', ' ')->title()->toString(),
    ]);
}

it('shows a fixed administration flow and published student affairs information on the landing page', function () {
    StudentAffairsInformation::query()->create([
        'category' => 'surat_edaran',
        'title' => 'Surat Edaran Kegiatan Mahasiswa',
        'summary' => 'Ketentuan kegiatan mahasiswa terbaru.',
        'published_at' => '2026-11-04',
        'is_published' => true,
    ]);
    StudentAffairsInformation::query()->create([
        'category' => 'surat_keputusan',
        'title' => 'SK Internal',
        'summary' => 'Belum diumumkan.',
        'is_published' => false,
    ]);

    $this->get(route('welcome'))
        ->assertOk()
        ->assertSee('ALUR ADMINISTRASI ORMAWA')
        ->assertSee('Penyusunan proposal dan RAB')
        ->assertSee('KEUNGGULAN SISTEM SIOM')
        ->assertSee('INFORMASI KEMAHASISWAAN')
        ->assertSee('Surat Edaran Kegiatan Mahasiswa')
        ->assertDontSee('SK Internal');

    expect(Schema::hasTable('landing_page_process_steps'))->toBeFalse();
});

it('lets superadmin manage and publish student affairs notices with documents', function () {
    Storage::fake('local');
    $adminRole = createStudentAffairsRole('superadmin');
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($admin)
        ->get(route('admin.kemahasiswaan-info'))
        ->assertOk()
        ->assertSee('Informasi Kemahasiswaan');

    Livewire::actingAs($admin)
        ->test(StudentAffairsInfoIndex::class)
        ->call('create')
        ->set('category', 'surat_edaran')
        ->set('title', 'SE Kegiatan Mahasiswa')
        ->set('summary', 'Panduan kegiatan dan administrasi.')
        ->set('publishedAt', '2026-11-04')
        ->set('document', UploadedFile::fake()->create('surat-edaran.pdf', 100, 'application/pdf'))
        ->set('isPublished', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('berhasil ditambahkan');

    $information = StudentAffairsInformation::query()->where('title', 'SE Kegiatan Mahasiswa')->firstOrFail();
    $this->assertModelExists($information);
    Storage::disk('local')->assertExists($information->document_path);

    $this->get(route('welcome'))
        ->assertOk()
        ->assertSee('SE Kegiatan Mahasiswa')
        ->assertSee(route('student-affairs-information.document', $information));

    $this->get(route('student-affairs-information.document', $information))
        ->assertOk()
        ->assertHeader('content-disposition', 'attachment; filename=surat-edaran.pdf');

    Livewire::actingAs($admin)
        ->test(StudentAffairsInfoIndex::class)
        ->call('edit', $information->id)
        ->set('summary', 'Panduan kegiatan yang telah diperbarui.')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('student_affairs_information', [
        'id' => $information->id,
        'summary' => 'Panduan kegiatan yang telah diperbarui.',
    ]);
    Storage::disk('local')->assertExists($information->document_path);

    Livewire::actingAs($admin)
        ->test(StudentAffairsInfoIndex::class)
        ->call('togglePublished', $information->id)
        ->assertSee('disembunyikan dari landing page');

    $this->get(route('welcome'))
        ->assertOk()
        ->assertDontSee('SE Kegiatan Mahasiswa');

    $this->get(route('student-affairs-information.document', $information))
        ->assertNotFound();

    Livewire::actingAs($admin)
        ->test(StudentAffairsInfoIndex::class)
        ->call('delete', $information->id);

    $this->assertDatabaseMissing('student_affairs_information', ['id' => $information->id]);
    Storage::disk('local')->assertMissing($information->document_path);
});

it('validates uploaded documents and protects management to superadmin', function () {
    Storage::fake('local');
    $adminRole = createStudentAffairsRole('superadmin');
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $secretaryRole = createStudentAffairsRole('sekretaris_ormawa');
    $secretary = User::factory()->create(['role_id' => $secretaryRole->id]);

    Livewire::actingAs($admin)
        ->test(StudentAffairsInfoIndex::class)
        ->call('create')
        ->set('category', 'unknown')
        ->set('title', '')
        ->set('summary', '')
        ->set('publishedAt', 'not-a-date')
        ->set('document', UploadedFile::fake()->create('malware.exe', 100, 'application/octet-stream'))
        ->call('save')
        ->assertHasErrors(['category', 'title', 'summary', 'publishedAt', 'document']);

    $this->assertDatabaseCount('student_affairs_information', 0);

    $this->actingAs($secretary)
        ->get(route('admin.kemahasiswaan-info'))
        ->assertForbidden();

    RoleMenuSetting::query()->create([
        'role_id' => $adminRole->id,
        'menu_key' => 'admin.landing-info',
        'enabled' => false,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.kemahasiswaan-info'))
        ->assertOk();
});

it('escapes public notice content', function () {
    StudentAffairsInformation::query()->create([
        'category' => 'pengumuman',
        'title' => '<script>alert(1)</script>',
        'summary' => 'Informasi biasa.',
        'is_published' => true,
    ]);

    $this->get(route('welcome'))
        ->assertOk()
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
});
