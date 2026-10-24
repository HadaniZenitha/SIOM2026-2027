<?php

namespace App\Livewire\Sekretaris\Anggota;

use App\Models\OrganizationMember;
use App\Models\OrganizationDivision;
use App\Models\Ormawa;
use App\Models\Period;
use App\Models\ProgramStudi;
use App\Models\User;
use App\Services\SpreadsheetService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public bool $showDivisionManager = false;

    public ?int $memberId = null;

    public string $name = '';

    public string $birth_place = '';

    public ?string $birth_date = null;

    public string $nim = '';

    public string $phone = '';

    public ?int $program_studi_id = null;

    public ?int $organization_division_id = null;

    public string $position = '';

    public ?string $position_number = null;

    public string $status = 'active';

    public mixed $importFile = null;

    public ?int $divisionId = null;

    public string $divisionName = '';

    public function create(): void
    {
        $this->authorizeSecretary();
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeSecretary();
        $member = $this->membersQuery()->findOrFail($id);
        $this->memberId = $member->id;
        $this->name = $member->name;
        $this->birth_place = $member->birth_place ?? '';
        $this->birth_date = $member->birth_date?->format('Y-m-d');
        $this->nim = $member->nim;
        $this->phone = $member->phone ?? '';
        $this->program_studi_id = $member->program_studi_id;
        $this->organization_division_id = $member->organization_division_id;
        $this->position = $member->position ?? '';
        $this->position_number = $member->position_number !== null
            ? (string) $member->position_number
            : null;
        $this->status = $member->status;
        $this->showForm = true;
    }

    public function updatedPosition(): void
    {
        if (! $this->isNumberedDeputyPosition()) {
            $this->position_number = null;
        }
    }

    public function openDivisionManager(): void
    {
        $this->authorizeSecretary();
        $this->resetDivisionForm();
        $this->showDivisionManager = true;
    }

    public function saveDivision(): void
    {
        $ormawa = $this->authorizedOrmawa();
        $this->divisionName = trim($this->divisionName);
        $validated = $this->validate([
            'divisionName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('organization_divisions', 'name')
                    ->where('ormawa_id', $ormawa->id)
                    ->ignore($this->divisionId),
            ],
        ]);

        $division = $this->divisionId
            ? $ormawa->divisions()->findOrFail($this->divisionId)
            : new OrganizationDivision;
        $division->fill([
            'ormawa_id' => $ormawa->id,
            'name' => trim($validated['divisionName']),
        ])->save();

        session()->flash('success', $this->divisionId ? 'Divisi diperbarui.' : 'Divisi ditambahkan.');
        $this->resetDivisionForm();
    }

    public function editDivision(int $divisionId): void
    {
        $division = $this->authorizedOrmawa()->divisions()->findOrFail($divisionId);
        $this->divisionId = $division->id;
        $this->divisionName = $division->name;
        $this->resetValidation();
    }

    public function deleteDivision(int $divisionId): void
    {
        $division = $this->authorizedOrmawa()->divisions()->findOrFail($divisionId);
        if ($division->members()->exists()) {
            throw ValidationException::withMessages([
                'divisionName' => 'Divisi tidak dapat dihapus selama masih digunakan anggota.',
            ]);
        }

        $division->delete();
        if ($this->divisionId === $divisionId) {
            $this->resetDivisionForm();
        }
        session()->flash('success', 'Divisi dihapus.');
    }

    public function resetDivisionForm(): void
    {
        $this->reset(['divisionId', 'divisionName']);
        $this->resetValidation();
    }

    public function closeDivisionManager(): void
    {
        $this->resetDivisionForm();
        $this->showDivisionManager = false;
    }

    public function save(): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('sekretaris_ormawa') && $user->ormawa_id, 403);
        $ormawa = Ormawa::query()->findOrFail($user->ormawa_id);
        $period = Period::query()->where('status', 'active')->first()
            ?? Period::query()->latest('start_date')->firstOrFail();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'birth_place' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'nim' => [
                'required',
                'string',
                'max:50',
                Rule::unique('organization_members', 'nim')
                    ->where('period_id', $period->id)
                    ->where('ormawa_id', $user->ormawa_id)
                    ->ignore($this->memberId),
            ],
            'phone' => ['required', 'string', 'max:30'],
            'program_studi_id' => ['nullable', 'exists:program_studis,id'],
            'organization_division_id' => [
                'nullable',
                Rule::exists('organization_divisions', 'id')->where('ormawa_id', $ormawa->id),
            ],
            'position' => ['required', Rule::in(array_keys($ormawa->memberPositionOptions()))],
            'position_number' => $this->position === $ormawa->deputyLeadershipPosition()
                ? ['required', 'integer', 'min:1', 'max:3999']
                : ['prohibited'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
        $validated['position_number'] = isset($validated['position_number'])
            ? (int) $validated['position_number']
            : null;

        $isLeadershipPosition = $validated['position'] === $ormawa->leadershipPosition();
        $isDeputyPosition = $validated['position'] === $ormawa->deputyLeadershipPosition();
        if (
            $validated['status'] === 'active'
            && ($isLeadershipPosition || $isDeputyPosition)
            && $this->hasActivePositionAssignment(
                $period->id,
                $ormawa->id,
                $validated['position'],
                $validated['position_number'],
                $this->memberId
            )
        ) {
            $errorKey = $isDeputyPosition ? 'position_number' : 'position';
            $errorMessage = $isDeputyPosition
                ? 'Nomor jabatan wakil tersebut sudah digunakan.'
                : 'Jabatan pimpinan tertinggi sudah memiliki anggota aktif.';
            throw ValidationException::withMessages([
                $errorKey => $errorMessage,
            ]);
        }

        $member = $this->memberId
            ? $this->membersQuery()->findOrFail($this->memberId)
            : new OrganizationMember;

        $member->fill([
            ...$validated,
            'period_id' => $period->id,
            'ormawa_id' => $user->ormawa_id,
        ]);
        $member->save();

        session()->flash('success', $this->memberId ? 'Data anggota diperbarui.' : 'Anggota ditambahkan.');
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $this->authorizeSecretary();
        $this->membersQuery()->findOrFail($id)->delete();
        session()->flash('success', 'Anggota berhasil dihapus.');
    }

    public function exportExcel(SpreadsheetService $spreadsheet): StreamedResponse
    {
        $this->authorizeSecretary();
        $ormawa = $this->authorizedOrmawa();
        $members = $this->membersQuery()->with(['programStudi', 'division'])->orderBy('name')->get();
        $headers = [
            'name', 'birth_place', 'birth_date', 'nim', 'phone', 'program_studi',
            'division_name', 'position', 'position_number', 'status',
        ];
        $rows = $members->map(fn (OrganizationMember $member): array => [
            $member->name,
            $member->birth_place,
            $member->birth_date->format('Y-m-d'),
            $member->nim,
            $member->phone,
            $member->programStudi?->name ?? '',
            $member->division?->name ?? '',
            $ormawa->memberPositionOptions()[$member->position] ?? $member->position ?? '',
            $member->position_number,
            $member->status,
        ])->all();

        return $spreadsheet->download('anggota-ormawa.xlsx', 'Anggota', $headers, $rows);
    }

    public function downloadImportTemplate(SpreadsheetService $spreadsheet): StreamedResponse
    {
        $this->authorizeSecretary();

        return $spreadsheet->download('template-impor-anggota.xlsx', 'Anggota', [
            'name', 'birth_place', 'birth_date', 'nim', 'phone', 'program_studi',
            'division_name', 'position', 'position_number', 'status',
        ], []);
    }

    public function importExcel(SpreadsheetService $spreadsheet): void
    {
        $this->authorizeSecretary();
        $this->validate([
            'importFile' => ['required', 'file', 'mimes:xlsx', 'extensions:xlsx', 'max:5120'],
        ]);

        $period = Period::query()->where('status', 'active')->first()
            ?? Period::query()->latest('start_date')->firstOrFail();
        $ormawa = $this->authorizedOrmawa();
        $positionOptions = $ormawa->memberPositionOptions();
        $records = $spreadsheet->read($this->importFile, [
            'name', 'birth_place', 'birth_date', 'nim', 'phone', 'program_studi',
            'division_name', 'position', 'position_number', 'status',
        ]);
        $members = [];
        $seenNims = [];
        $seenPositionAssignments = [];

        foreach ($records as $record) {
            $values = $record['values'];
            $values['status'] = $values['status'] ?: 'active';
            $position = array_key_exists($values['position'], $positionOptions)
                ? $values['position']
                : array_search($values['position'], $positionOptions, true);
            if (! is_string($position)) {
                throw ValidationException::withMessages([
                    'importFile' => 'Baris '.$record['line'].': Jabatan tidak sesuai dengan jenis ORMAWA.',
                ]);
            }
            $values['position'] = $position;
            $values['position_number'] = $values['position_number'] !== ''
                ? $values['position_number']
                : null;
            $validator = validator($values, [
                'name' => ['required', 'string', 'max:255'],
                'birth_place' => ['required', 'string', 'max:255'],
                'birth_date' => ['required', 'date'],
                'nim' => ['required', 'string', 'max:50'],
                'phone' => ['required', 'string', 'max:30'],
                'program_studi' => ['nullable', 'string', 'max:255'],
                'division_name' => ['nullable', 'string', 'max:255'],
                'position' => ['required', Rule::in(array_keys($positionOptions))],
                'position_number' => $position === $ormawa->deputyLeadershipPosition()
                    ? ['required', 'integer', 'min:1', 'max:3999']
                    : ['prohibited'],
                'status' => ['required', Rule::in(['active', 'inactive'])],
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages([
                    'importFile' => 'Baris '.$record['line'].': '.$validator->errors()->first(),
                ]);
            }

            $isLeadershipPosition = $position === $ormawa->leadershipPosition();
            $isDeputyPosition = $position === $ormawa->deputyLeadershipPosition();
            if ($values['status'] === 'active' && ($isLeadershipPosition || $isDeputyPosition)) {
                $positionNumber = $isDeputyPosition ? (int) $values['position_number'] : null;
                $assignmentKey = $position.':'.($positionNumber ?? 'leader');
                if (
                    in_array($assignmentKey, $seenPositionAssignments, true)
                    || $this->hasActivePositionAssignment(
                        $period->id,
                        $ormawa->id,
                        $position,
                        $positionNumber
                    )
                ) {
                    throw ValidationException::withMessages([
                        'importFile' => 'Baris '.$record['line'].': Jabatan pimpinan atau nomor wakil tersebut sudah digunakan.',
                    ]);
                }
                $seenPositionAssignments[] = $assignmentKey;
            }

            $duplicate = in_array($values['nim'], $seenNims, true)
                || OrganizationMember::query()
                    ->where('period_id', $period->id)
                    ->where('ormawa_id', Auth::user()->ormawa_id)
                    ->where('nim', $values['nim'])
                    ->exists();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'importFile' => 'Baris '.$record['line'].': NIM '.$values['nim'].' sudah terdaftar.',
                ]);
            }

            $programStudiId = null;
            if ($values['program_studi'] !== '') {
                $programStudiId = ProgramStudi::query()
                    ->where('is_active', true)
                    ->where('name', $values['program_studi'])
                    ->value('id');

                if (! $programStudiId) {
                    throw ValidationException::withMessages([
                        'importFile' => 'Baris '.$record['line'].': Program studi tidak ditemukan atau tidak aktif.',
                    ]);
                }
            }

            $divisionId = null;
            if ($values['division_name'] !== '') {
                $divisionId = $ormawa->divisions()
                    ->where('name', $values['division_name'])
                    ->value('id');
                if (! $divisionId) {
                    throw ValidationException::withMessages([
                        'importFile' => 'Baris '.$record['line'].': Divisi tidak ditemukan untuk ORMAWA ini.',
                    ]);
                }
            }

            $seenNims[] = $values['nim'];
            $members[] = [
                'period_id' => $period->id,
                'ormawa_id' => Auth::user()->ormawa_id,
                'program_studi_id' => $programStudiId,
                'organization_division_id' => $divisionId,
                'position' => $position,
                'position_number' => $values['position_number'] !== null
                    ? (int) $values['position_number']
                    : null,
                'name' => $values['name'],
                'birth_place' => $values['birth_place'],
                'birth_date' => $values['birth_date'],
                'nim' => $values['nim'],
                'phone' => $values['phone'],
                'status' => $values['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::transaction(fn () => OrganizationMember::query()->insert($members));

        $this->reset('importFile');
        session()->flash('success', count($members).' anggota berhasil diimpor.');
    }

    public function resetForm(): void
    {
        $this->reset([
            'memberId', 'name', 'birth_place', 'birth_date', 'nim', 'phone',
            'program_studi_id', 'organization_division_id', 'position', 'position_number',
        ]);
        $this->status = 'active';
        $this->showForm = false;
        $this->resetValidation();
    }

    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('sekretaris_ormawa') && $user->ormawa_id, 403);
        $ormawa = Ormawa::query()->findOrFail($user->ormawa_id);

        return view('livewire.sekretaris.anggota.index', [
            'members' => $this->membersQuery()->with(['programStudi', 'division'])
                ->when($this->search !== '', fn ($query) => $query->where(
                    fn ($query) => $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('nim', 'like', '%'.$this->search.'%')
                        ->orWhereHas('division', fn ($divisionQuery) => $divisionQuery->where(
                            'name',
                            'like',
                            '%'.$this->search.'%'
                        ))
                ))->latest()->paginate(10),
            'programStudis' => ProgramStudi::query()->where('is_active', true)->orderBy('name')->get(),
            'divisions' => $ormawa->divisions()->orderBy('name')->get(),
            'ormawa' => $ormawa,
            'positionOptions' => $ormawa->memberPositionOptions(),
        ]);
    }

    private function membersQuery(): Builder
    {
        return OrganizationMember::query()
            ->where('ormawa_id', Auth::user()->ormawa_id)
            ->whereHas('period', fn ($query) => $query->where('status', 'active'));
    }

    private function authorizeSecretary(): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('sekretaris_ormawa') && $user->ormawa_id, 403);
    }

    private function authorizedOrmawa(): Ormawa
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('sekretaris_ormawa') && $user->ormawa_id, 403);

        return Ormawa::query()->findOrFail($user->ormawa_id);
    }

    private function isNumberedDeputyPosition(): bool
    {
        $ormawa = $this->authorizedOrmawa();

        return $this->position === $ormawa->deputyLeadershipPosition();
    }

    private function hasActivePositionAssignment(
        int $periodId,
        int $ormawaId,
        string $position,
        ?int $positionNumber,
        ?int $ignoreMemberId = null
    ): bool {
        return OrganizationMember::query()
            ->where('period_id', $periodId)
            ->where('ormawa_id', $ormawaId)
            ->where('position', $position)
            ->where('status', 'active')
            ->when(
                $positionNumber === null,
                fn ($query) => $query->whereNull('position_number'),
                fn ($query) => $query->where('position_number', $positionNumber)
            )
            ->when($ignoreMemberId, fn ($query) => $query->where('id', '!=', $ignoreMemberId))
            ->exists();
    }


}
