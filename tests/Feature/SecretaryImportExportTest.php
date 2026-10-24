<?php

use App\Livewire\Sekretaris\Anggota\Index as MemberIndex;
use App\Livewire\Sekretaris\Kas\Index as CashIndex;
use App\Models\CashAccount;
use App\Models\OrganizationDivision;
use App\Models\OrganizationMember;
use App\Models\Ormawa;
use App\Models\Period;
use App\Models\ProgramStudi;
use App\Models\Role;
use App\Models\User;
use App\Services\SpreadsheetService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function secretaryExcelUpload(string $filename, array $headers, array $rows = []): UploadedFile
{
    $spreadsheet = new Spreadsheet;
    $worksheet = $spreadsheet->getActiveSheet();

    foreach ([$headers, ...$rows] as $rowIndex => $row) {
        foreach ($row as $columnIndex => $value) {
            $cell = $worksheet->getCell([$columnIndex + 1, $rowIndex + 1]);
            $header = $headers[$columnIndex] ?? null;
            $date = is_string($value) && in_array($header, ['birth_date', 'transaction_date'], true)
                ? \DateTimeImmutable::createFromFormat('!Y-m-d', $value)
                : false;

            if ($date !== false && $date->format('Y-m-d') === $value) {
                $cell->setValueExplicit(ExcelDate::PHPToExcel($date), DataType::TYPE_NUMERIC);
                $cell->getStyle()->getNumberFormat()->setFormatCode('yyyy-mm-dd');
            } else {
                $cell->setValueExplicit(
                    $value ?? '',
                    is_numeric($value) && ! is_string($value) ? DataType::TYPE_NUMERIC : DataType::TYPE_STRING
                );
            }
        }
    }

    ob_start();
    (new Xlsx($spreadsheet))->save('php://output');
    $contents = (string) ob_get_clean();
    $spreadsheet->disconnectWorksheets();

    $file = UploadedFile::fake()->createWithContent($filename, $contents);

    return $file;
}

function secretarySpreadsheetFixture(float $openingBalance = 0): array
{
    $role = Role::create([
        'name' => 'sekretaris_ormawa',
        'display_name' => 'Sekretaris ORMAWA',
    ]);
    $period = Period::create([
        'name' => '2026/2027',
        'start_date' => '2026-08-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);
    $ormawa = Ormawa::create(['code' => 'BEM', 'name' => 'BEM']);
    $division = $ormawa->divisions()->create(['name' => 'Kominfo']);
    $user = User::factory()->create([
        'role_id' => $role->id,
        'ormawa_id' => $ormawa->id,
    ]);
    $account = CashAccount::create([
        'period_id' => $period->id,
        'ormawa_id' => $ormawa->id,
        'opening_balance' => $openingBalance,
        'current_balance' => $openingBalance,
        'carry_over_amount' => 0,
    ]);

    return compact('period', 'ormawa', 'division', 'user', 'account');
}

it('imports members from Excel into the secretary organization and active period', function () {
    $fixture = secretarySpreadsheetFixture();
    ProgramStudi::create([
        'code' => 'IF',
        'name' => 'Informatika',
        'is_active' => true,
    ]);
    $test = Livewire::actingAs($fixture['user'])
        ->test(MemberIndex::class)
        ->set('importFile', secretaryExcelUpload('anggota.xlsx', [
            'name', 'birth_place', 'birth_date', 'nim', 'phone', 'program_studi',
            'division_name', 'position', 'position_number', 'status',
        ], [[
            'Nama, Satu', 'Malang', '2000-01-02', '23001001', '08123456789', 'Informatika',
            'Kominfo', 'Wakil Presiden Mahasiswa', 2, 'active',
        ]]))
        ->call('importExcel');
    $test->assertHasNoErrors();

    $member = OrganizationMember::query()->firstOrFail();
    expect($member->period_id)->toBe($fixture['period']->id)
        ->and($member->ormawa_id)->toBe($fixture['ormawa']->id)
        ->and($member->program_studi_id)->toBe(ProgramStudi::query()->value('id'))
        ->and($member->organization_division_id)->toBe($fixture['division']->id)
        ->and($member->position)->toBe('wakil_presiden_mahasiswa')
        ->and($member->position_number)->toBe(2)
        ->and($member->name)->toBe('Nama, Satu');
});

it('rejects member imports that reference an unknown division', function () {
    $fixture = secretarySpreadsheetFixture();
    Livewire::actingAs($fixture['user'])
        ->test(MemberIndex::class)
        ->set('importFile', secretaryExcelUpload('anggota.xlsx', [
            'name', 'birth_place', 'birth_date', 'nim', 'phone', 'program_studi',
            'division_name', 'position', 'position_number', 'status',
        ], [[
            'Anggota', 'Malang', '2000-01-02', '23001030', '08123456789', '', 'Divisi Lain',
            'Staff Ahli', '', 'active',
        ]]))
        ->call('importExcel')
        ->assertHasErrors('importFile');

    expect(OrganizationMember::query()->count())->toBe(0);
});

it('rejects an invalid member spreadsheet atomically and supports the member export', function () {
    $fixture = secretarySpreadsheetFixture();
    Livewire::actingAs($fixture['user'])
        ->test(MemberIndex::class)
        ->set('importFile', secretaryExcelUpload('anggota.xlsx', [
            'name', 'birth_place', 'birth_date', 'nim', 'phone', 'program_studi',
            'division_name', 'position', 'position_number', 'status',
        ], [
            ['Anggota Satu', 'Malang', '2000-01-02', '23001001', '08123456789', '', 'Kominfo', 'Presiden Mahasiswa', '', 'active'],
            ['Anggota Dua', 'Malang', 'bukan-tanggal', '23001002', '08123456788', '', 'Kominfo', 'Presiden Mahasiswa', '', 'active'],
        ]))
        ->call('importExcel')
        ->assertHasErrors('importFile');

    expect(OrganizationMember::query()->count())->toBe(0);

    Livewire::actingAs($fixture['user'])
        ->test(MemberIndex::class)
        ->call('downloadImportTemplate')
        ->assertFileDownloaded('template-impor-anggota.xlsx');

    Livewire::actingAs($fixture['user'])
        ->test(MemberIndex::class)
        ->call('exportExcel')
        ->assertFileDownloaded('anggota-ormawa.xlsx');
});

it('generates a valid Excel workbook without interpreting text as formulas', function () {
    $response = app(SpreadsheetService::class)->download(
        'anggota.xlsx',
        'Anggota',
        ['name', 'nim'],
        [['=HYPERLINK("https://example.test")', '00123']]
    );
    ob_start();
    $response->sendContent();
    $contents = (string) ob_get_clean();
    $temporaryPath = tempnam(sys_get_temp_dir(), 'siom-test-xlsx-');

    expect($response->headers->get('Content-Type'))
        ->toBe('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    expect($contents)->toStartWith("PK\x03\x04");
    expect($temporaryPath)->not->toBeFalse();

    try {
        file_put_contents($temporaryPath, $contents);
        $spreadsheet = (new XlsxReader)->load($temporaryPath);
        $worksheet = $spreadsheet->getActiveSheet();

        expect($worksheet->getTitle())->toBe('Anggota')
            ->and($worksheet->getCell('A2')->getValue())->toBe('=HYPERLINK("https://example.test")')
            ->and($worksheet->getCell('A2')->getDataType())->toBe(DataType::TYPE_STRING)
            ->and($worksheet->getCell('B2')->getValue())->toBe('00123')
            ->and($worksheet->getCell('B2')->getDataType())->toBe(DataType::TYPE_STRING);

        $spreadsheet->disconnectWorksheets();
    } finally {
        unlink($temporaryPath);
    }
});

it('saves secretary member divisions and numbered BEM deputy positions', function () {
    $fixture = secretarySpreadsheetFixture();

    Livewire::actingAs($fixture['user'])
        ->test(MemberIndex::class)
        ->assertSee('Presiden Mahasiswa')
        ->assertDontSee('Ketua Himpunan')
        ->call('create')
        ->set('name', 'Anggota BEM')
        ->set('birth_place', 'Bandung')
        ->set('birth_date', '2002-03-04')
        ->set('nim', '23001009')
        ->set('phone', '08123456789')
        ->set('organization_division_id', $fixture['division']->id)
        ->set('position', 'wakil_presiden_mahasiswa')
        ->call('save')
        ->assertHasErrors('position_number')
        ->set('position_number', '2')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Wakil Presiden Mahasiswa II')
        ->call('create')
        ->set('name', 'Anggota BEM Kedua')
        ->set('birth_place', 'Bandung')
        ->set('birth_date', '2002-03-05')
        ->set('nim', '23001010')
        ->set('phone', '08123456788')
        ->set('position', 'wakil_presiden_mahasiswa')
        ->set('position_number', '2')
        ->call('save')
        ->assertHasErrors('position_number');

    $member = OrganizationMember::query()->where('nim', '23001009')->firstOrFail();
    expect($member->organization_division_id)->toBe($fixture['division']->id)
        ->and($member->position)->toBe('wakil_presiden_mahasiswa')
        ->and($member->position_number)->toBe(2);
});

it('lets a secretary manage divisions only for their own organization', function () {
    $fixture = secretarySpreadsheetFixture();
    $otherOrmawa = Ormawa::create(['code' => 'HIMA', 'name' => 'Himpunan Informatika']);
    $otherDivision = $otherOrmawa->divisions()->create(['name' => 'Akademik']);

    $component = Livewire::actingAs($fixture['user'])
        ->test(MemberIndex::class)
        ->call('openDivisionManager')
        ->set('divisionName', 'Media')
        ->call('saveDivision')
        ->assertHasNoErrors()
        ->assertSee('Media')
        ->call('editDivision', $fixture['division']->id)
        ->set('divisionName', 'Komunikasi')
        ->call('saveDivision')
        ->assertHasNoErrors()
        ->assertSee('Komunikasi');

    expect(fn () => $component->call('editDivision', $otherDivision->id))
        ->toThrow(ModelNotFoundException::class);

    expect($fixture['ormawa']->divisions()->where('name', 'Komunikasi')->exists())->toBeTrue()
        ->and($otherOrmawa->divisions()->where('name', 'Akademik')->exists())->toBeTrue();
});

it('prevents deleting a division assigned to an organization member', function () {
    $fixture = secretarySpreadsheetFixture();
    OrganizationMember::create([
        'period_id' => $fixture['period']->id,
        'ormawa_id' => $fixture['ormawa']->id,
        'organization_division_id' => $fixture['division']->id,
        'name' => 'Anggota',
        'birth_place' => 'Bandung',
        'birth_date' => '2000-01-01',
        'nim' => '23001020',
        'phone' => '08123456789',
        'status' => 'active',
    ]);

    Livewire::actingAs($fixture['user'])
        ->test(MemberIndex::class)
        ->call('openDivisionManager')
        ->call('deleteDivision', $fixture['division']->id)
        ->assertHasErrors('divisionName');

    $this->assertModelExists($fixture['division']);
});

it('does not allow a secretary to assign another organization division to a member', function () {
    $fixture = secretarySpreadsheetFixture();
    $otherOrmawa = Ormawa::create(['code' => 'HIMA', 'name' => 'Himpunan Informatika']);
    $otherDivision = $otherOrmawa->divisions()->create(['name' => 'Akademik']);

    Livewire::actingAs($fixture['user'])
        ->test(MemberIndex::class)
        ->call('create')
        ->set('name', 'Anggota BEM')
        ->set('birth_place', 'Bandung')
        ->set('birth_date', '2002-03-04')
        ->set('nim', '23001040')
        ->set('phone', '08123456789')
        ->set('organization_division_id', $otherDivision->id)
        ->set('position', 'staff_muda')
        ->call('save')
        ->assertHasErrors('organization_division_id');

    expect(OrganizationMember::query()->count())->toBe(0);
});

it('offers the DPM leadership title instead of HIMA or BEM titles', function () {
    $fixture = secretarySpreadsheetFixture();
    $fixture['ormawa']->update([
        'code' => 'DPM',
        'name' => 'Dewan Perwakilan Mahasiswa',
    ]);

    Livewire::actingAs($fixture['user'])
        ->test(MemberIndex::class)
        ->assertSee('Ketua Dewan')
        ->assertDontSee('Ketua Himpunan')
        ->assertDontSee('Presiden Mahasiswa');
});

it('offers HIMA leadership titles for other organization types', function () {
    $fixture = secretarySpreadsheetFixture();
    $fixture['ormawa']->update([
        'code' => 'HIMATIF',
        'name' => 'Himpunan Mahasiswa Informatika',
    ]);

    Livewire::actingAs($fixture['user'])
        ->test(MemberIndex::class)
        ->assertSee('Ketua Himpunan')
        ->assertSee('Wakil Ketua Himpunan')
        ->assertDontSee('Presiden Mahasiswa')
        ->assertDontSee('Ketua Dewan');
});

it('allows secretary and treasurer positions for each organization type', function () {
    $fixture = secretarySpreadsheetFixture();

    Livewire::actingAs($fixture['user'])
        ->test(MemberIndex::class)
        ->assertSee('Sekretaris')
        ->assertSee('Bendahara')
        ->call('create')
        ->set('name', 'Sekretaris BEM')
        ->set('birth_place', 'Bandung')
        ->set('birth_date', '2002-03-04')
        ->set('nim', '23001050')
        ->set('phone', '08123456789')
        ->set('position', 'sekretaris')
        ->call('save')
        ->assertHasNoErrors();

    $member = OrganizationMember::query()->where('nim', '23001050')->firstOrFail();
    expect($member->position)->toBe('sekretaris')
        ->and($fixture['ormawa']->memberPositionOptions())->toHaveKey('bendahara', 'Bendahara');
});

it('keeps create actions out of the secretary dashboard shortcuts', function () {
    $fixture = secretarySpreadsheetFixture();

    $this->actingAs($fixture['user'])
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Buat Pengajuan');
});

it('imports cash transactions and updates the organization balance', function () {
    $fixture = secretarySpreadsheetFixture(1000);

    Livewire::actingAs($fixture['user'])
        ->test(CashIndex::class)
        ->set('importFile', secretaryExcelUpload('kas.xlsx', [
            'type', 'category', 'amount', 'description', 'transaction_date',
        ], [
            ['income', 'Iuran anggota', 2500, 'Setoran kas', '2026-10-01'],
            ['expense', 'Konsumsi', 500, 'Konsumsi rapat', '2026-10-02'],
        ]))
        ->call('importExcel')
        ->assertHasNoErrors();

    expect((float) $fixture['account']->fresh()->current_balance)->toBe(3000.0)
        ->and($fixture['account']->transactions()->count())->toBe(2);

    Livewire::actingAs($fixture['user'])
        ->test(CashIndex::class)
        ->call('downloadImportTemplate')
        ->assertFileDownloaded('template-impor-kas.xlsx');

    Livewire::actingAs($fixture['user'])
        ->test(CashIndex::class)
        ->call('exportExcel')
        ->assertFileDownloaded('kas-ormawa.xlsx');
});

it('rejects an Excel cash import that would take the balance below zero without importing partial rows', function () {
    $fixture = secretarySpreadsheetFixture(100);

    Livewire::actingAs($fixture['user'])
        ->test(CashIndex::class)
        ->set('importFile', secretaryExcelUpload('kas.xlsx', [
            'type', 'category', 'amount', 'description', 'transaction_date',
        ], [
            ['income', 'Iuran', 50, 'Setoran', '2026-10-01'],
            ['expense', 'Konsumsi', 200, 'Belanja', '2026-10-02'],
        ]))
        ->call('importExcel')
        ->assertHasErrors('importFile');

    expect($fixture['account']->fresh()->current_balance)->toBe('100.00')
        ->and($fixture['account']->transactions()->count())->toBe(0);
});
