<?php

namespace App\Livewire\Sekretaris\Pengajuan;

use App\Models\Document;
use App\Models\Ormawa;
use App\Models\Period;
use App\Models\Submission;
use App\Models\SubmissionCommittee;
use App\Models\SubmissionItem;
use App\Models\SubmissionRundown;
use App\Models\SubmissionTimeline;
use App\Models\User;
use App\Services\SubmissionService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use RuntimeException;

#[Layout('layouts.app')]
class Create extends Component
{
    use WithFileUploads;

    public int $step = 1;

    public string $sourceType = 'form';

    public mixed $proposalUpload = null;

    public ?string $uploadedTotalAmount = null;

    public ?Period $period = null;

    public ?Ormawa $ormawa = null;

    #[Locked]
    public ?Submission $submission = null;

    /* STEP 1: UTAMA & NOTA DINAS */
    public string $activity_name = '';

    public string $activity_theme = '';

    public string $activity_type = '';

    public string $divisi_name = '';

    public ?string $meeting_date = null;

    public string $circular_letter = '';

    public string $budget_code = '';

    public string $chairman_name = '';

    public string $chairman_nim = '';

    public string $pembina_name = '';

    public string $pembina_nuptk = '';

    public string $kaprodi_name = '';

    public string $kaprodi_nuptk = '';

    /* STEP 2: KONTEN PROPOSAL & PELAKSANAAN */
    public string $background = '';

    public array $objectives = [''];

    public array $benefits = [''];

    public string $target_participants = '';

    public string $event_day = '';

    public ?string $event_date = null;

    public ?string $event_start_time = null;

    public ?string $event_end_time = null;

    public string $event_location = '';

    /* STEP 2: TIMELINE SPESIFIK TEMPLATE */
    public ?string $plan_start = null;

    public ?string $plan_end = null;

    public ?string $prepare_start = null;

    public ?string $prepare_end = null;

    public ?string $action_start = null;

    public ?string $action_end = null;

    public ?string $closing_start = null;

    public ?string $closing_end = null;

    public array $plan_context = [''];

    public array $prepare_context = [''];

    public array $action_context = [''];

    public array $closing_context = [''];

    /* DYNAMIC DATA: PANITIA, RUNDOWN, RAB */
    public array $committees = [];

    public array $rundowns = [];

    public array $items = [];

    public array $timelines = [];

    public function mount(?Submission $submission = null): void
    {
        $this->timelines = [
            [
                'stage' => 'Perencanaan',
                'start_date' => '',
                'end_date' => '',
                'description' => '',
            ],
            [
                'stage' => 'Persiapan',
                'start_date' => '',
                'end_date' => '',
                'description' => '',
            ],
            [
                'stage' => 'Pelaksanaan',
                'start_date' => '',
                'end_date' => '',
                'description' => '',
            ],
            [
                'stage' => 'Penutupan',
                'start_date' => '',
                'end_date' => '',
                'description' => '',
            ],
        ];

        $user = Auth::user();

        abort_unless(
            $user instanceof User && $user->hasRole('sekretaris_ormawa'),
            403
        );

        abort_if(! $user->ormawa_id, 403, 'Akun Sekretaris belum terhubung dengan ORMAWA.');

        if ($submission) {
            abort_unless(
                $submission->submitted_by === $user->id
                    && in_array($submission->status, ['draft', 'revision'], true),
                404
            );

            $this->submission = $submission->load([
                'period',
                'ormawa',
                'documents',
                'items',
                'timelines',
                'committees',
                'rundowns',
            ]);
            $this->period = $submission->period;
            $this->ormawa = $submission->ormawa;
            $this->sourceType = $submission->documents->contains('type', 'proposal') ? 'upload' : 'form';
            $this->uploadedTotalAmount = (string) $submission->total_amount;
            $this->loadRevisionData($submission);
        } else {
            $this->period = Period::query()->where('status', 'active')->first() ?? Period::latest()->first();
            abort_if(! $this->period, 403, 'Periode kegiatan belum tersedia.');

            $this->ormawa = Ormawa::query()->whereKey($user->ormawa_id)->firstOrFail();
        }

        if ($this->committees === []) {
            $this->addCommittee();
        }

        if ($this->rundowns === []) {
            $this->addRundown();
        }

        if ($this->items === []) {
            $this->addItem();
        }
    }

    public function nextStep(): void
    {
        $this->validateCurrentStep();
        if ($this->step < 4) {
            $this->step++;
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step < 1 || $step > 4) {
            return;
        }
        if ($step > $this->step) {
            $this->validateCurrentStep();
        }
        $this->step = $step;
    }

    protected function validateCurrentStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'activity_name' => ['required', 'string', 'max:255'],
                'activity_theme' => ['nullable', 'string', 'max:255'],
                'divisi_name' => ['nullable', 'string', 'max:255'],
                'meeting_date' => ['nullable', 'date'],
                'circular_letter' => ['nullable', 'string', 'max:255'],
                'budget_code' => ['nullable', 'string', 'max:100'],
            ]);
        }

        if ($this->step === 2) {
            $this->validate([
                'background' => ['required', 'string'],
                'objectives' => ['required', 'array', 'min:1'],
                'objectives.*' => ['required', 'string', 'max:1000'],
                'benefits' => ['nullable', 'array', 'min:1'],
                'benefits.*' => ['nullable', 'string', 'max:1000'],
                'target_participants' => ['required', 'string'],
                'event_day' => ['nullable', 'string', 'max:30'],
                'event_date' => [
                    'required',
                    'date',
                    'after_or_equal:'.now()->addDays(30)->toDateString(),
                ],
                'event_start_time' => ['required'],
                'event_end_time' => ['required'],
                'event_location' => ['required', 'string', 'max:255'],

                'plan_start' => ['nullable', 'date'],
                'plan_end' => ['nullable', 'date'],
                'plan_context' => ['nullable', 'array', 'min:1'],
                'plan_context.*' => ['nullable', 'string', 'max:1000'],
                'prepare_start' => ['nullable', 'date'],
                'prepare_end' => ['nullable', 'date'],
                'prepare_context' => ['nullable', 'array', 'min:1'],
                'prepare_context.*' => ['nullable', 'string', 'max:1000'],
                'action_start' => ['nullable', 'date'],
                'action_end' => ['nullable', 'date'],
                'action_context' => ['nullable', 'array', 'min:1'],
                'action_context.*' => ['nullable', 'string', 'max:1000'],
                'closing_start' => ['nullable', 'date'],
                'closing_end' => ['nullable', 'date'],
                'closing_context' => ['nullable', 'array', 'min:1'],
                'closing_context.*' => ['nullable', 'string', 'max:1000'],
            ]);
        }

        if ($this->step === 3) {
            $this->validate([
                'chairman_name' => ['nullable', 'string', 'max:255'],
                'chairman_nim' => ['nullable', 'string', 'max:100'],
                'pembina_name' => ['nullable', 'string', 'max:255'],
                'pembina_nuptk' => ['nullable', 'string', 'max:100'],
                'kaprodi_name' => ['nullable', 'string', 'max:255'],
                'kaprodi_nuptk' => ['nullable', 'string', 'max:100'],
                'committees' => ['required', 'array', 'min:1'],
                'committees.*.name' => ['required', 'string', 'max:255'],
                'committees.*.structural_role' => ['nullable', 'string', 'max:100'],
                'committees.*.committee_role' => ['required', 'string', 'max:100'],
                'committees.*.description' => ['nullable', 'string', 'max:255'],

                'rundowns' => ['required', 'array', 'min:1'],
                'rundowns.*.day_date' => ['nullable', 'string', 'max:100'],
                'rundowns.*.time_slot' => ['required', 'string', 'max:100'],
                'rundowns.*.event_name' => ['required', 'string', 'max:255'],
                'rundowns.*.pic' => ['nullable', 'string', 'max:255'],
                'rundowns.*.note' => ['nullable', 'string', 'max:255'],
            ]);
        }

        if ($this->step === 4) {
            $this->validate([
                'items' => ['required', 'array', 'min:1'],
                'items.*.item_name' => ['required', 'string', 'max:255'],
                'items.*.qty' => ['required', 'numeric', 'min:0.01'],
                'items.*.unit' => ['required', 'string', 'max:50'],
                'items.*.price' => ['required', 'numeric', 'min:0'],
            ]);
        }
    }

    public function addCommittee(): void
    {
        $this->committees[] = [
            'name' => '',
            'structural_role' => '',
            'committee_role' => '',
            'description' => '-',
        ];
    }

    public function removeCommittee(int $index): void
    {
        if (count($this->committees) > 1) {
            unset($this->committees[$index]);
            $this->committees = array_values($this->committees);
        }
    }

    public function addRundown(): void
    {
        $this->rundowns[] = ['day_date' => 'Hari Pertama', 'time_slot' => '08:00 - 09:00 WIB', 'event_name' => '', 'pic' => '', 'note' => '-'];
    }

    public function removeRundown(int $index): void
    {
        if (count($this->rundowns) > 1) {
            unset($this->rundowns[$index]);
            $this->rundowns = array_values($this->rundowns);
        }
    }

    public function addItem(): void
    {
        $this->items[] = ['item_name' => '', 'qty' => 1, 'unit' => '', 'price' => 0];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function getGrandTotalProperty(): float
    {
        return collect($this->items)->sum(fn ($i) => (float) ($i['qty'] ?? 0) * (float) ($i['price'] ?? 0));
    }

    public function itemTotal(int $index): float
    {
        $item = $this->items[$index] ?? [];

        return (float) ($item['qty'] ?? 0) * (float) ($item['price'] ?? 0);
    }

    public function saveDraft(): void
    {
        if ($this->sourceType === 'upload') {
            $this->saveUploadedSubmission('draft');

            return;
        }

        $this->validateAll();
        $this->saveSubmission('draft');
    }

    public function addObjective(): void
    {
        $this->objectives[] = '';
    }

    public function removeObjective(int $index): void
    {
        if (count($this->objectives) > 1) {
            unset($this->objectives[$index]);
            $this->objectives = array_values($this->objectives);
        }
    }

    public function addBenefit(): void
    {
        $this->benefits[] = '';
    }

    public function removeBenefit(int $index): void
    {
        if (count($this->benefits) > 1) {
            unset($this->benefits[$index]);
            $this->benefits = array_values($this->benefits);
        }
    }

    public function addTimelineContext(string $stage): void
    {
        $property = $stage.'_context';

        if (property_exists($this, $property) && is_array($this->$property)) {
            $this->{$property}[] = '';
        }
    }

    public function removeTimelineContext(string $stage, int $index): void
    {
        $property = $stage.'_context';

        if (
            property_exists($this, $property)
            && is_array($this->$property)
            && count($this->$property) > 1
        ) {
            unset($this->{$property}[$index]);
            $this->{$property} = array_values($this->{$property});
        }
    }

    public function submit(SubmissionService $submissionService): void
    {
        if ($this->sourceType === 'upload') {
            $this->saveUploadedSubmission('submitted', $submissionService);

            return;
        }

        $this->validateAll();
        $this->saveSubmission('submitted', $submissionService);
    }

    protected function saveUploadedSubmission(
        string $status,
        ?SubmissionService $submissionService = null
    ): void {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('sekretaris_ormawa'), 403);

        $existingDocument = $this->submission?->documents()
            ->where('type', 'proposal')
            ->latest('id')
            ->first();

        $data = $this->validate([
            'activity_name' => ['required', 'string', 'max:255'],
            'uploadedTotalAmount' => ['required', 'numeric', 'min:1', 'max:9999999999999.99'],
            'event_date' => [
                'required',
                'date',
                'after_or_equal:'.now()->addDays(30)->toDateString(),
            ],
            'proposalUpload' => [
                $existingDocument ? 'nullable' : 'required',
                'file',
                'mimes:docx',
                'extensions:docx',
                'max:20480',
            ],
        ]);

        if ($status === 'submitted' && ! $submissionService) {
            throw new \LogicException('Layanan pengajuan harus tersedia saat mengirim pengajuan.');
        }

        if (! $this->proposalUpload && (! $existingDocument || ! Storage::disk('local')->exists($existingDocument->file_path))) {
            throw ValidationException::withMessages([
                'proposalUpload' => 'Unggah dokumen proposal DOCX sebelum menyimpan.',
            ]);
        }

        $newPath = $this->proposalUpload?->store('proposals/source', 'local');
        if ($this->proposalUpload && (! is_string($newPath) || $newPath === '')) {
            throw new RuntimeException('File proposal tidak dapat disimpan. Coba unggah kembali.');
        }

        $oldPath = $existingDocument?->file_path;
        try {
            DB::transaction(function () use ($data, $user, $status, $submissionService, $newPath): void {
                $submission = $this->submission
                    ? Submission::query()->where('submitted_by', $user->id)
                        ->whereIn('status', ['draft', 'revision'])
                        ->lockForUpdate()
                        ->findOrFail($this->submission->id)
                    : Submission::create([
                        'period_id' => $this->period?->id,
                        'ormawa_id' => $user->ormawa_id,
                        'submitted_by' => $user->id,
                        'submission_number' => $this->generateSubmissionNumber(),
                        'nota_number' => $this->generateNotaNumber(),
                        'activity_name' => $data['activity_name'],
                        'total_amount' => $data['uploadedTotalAmount'],
                        'event_date' => $data['event_date'],
                        'activity_date' => $data['event_date'],
                        'status' => 'draft',
                    ]);

                $submission->update([
                    'activity_name' => $data['activity_name'],
                    'total_amount' => $data['uploadedTotalAmount'],
                    'event_date' => $data['event_date'],
                    'activity_date' => $data['event_date'],
                ]);
                $submission->items()->delete();
                $submission->items()->create([
                    'item_name' => 'Anggaran dari proposal unggahan',
                    'qty' => 1,
                    'unit' => 'paket',
                    'price' => $data['uploadedTotalAmount'],
                    'total_price' => $data['uploadedTotalAmount'],
                ]);

                if ($newPath) {
                    $document = $submission->documents()->where('type', 'proposal')->latest('id')->first();
                    if ($document) {
                        $document->update([
                            'file_name' => 'Proposal_'.Str::slug($data['activity_name']).'.docx',
                            'file_path' => $newPath,
                        ]);
                    } else {
                        $submission->documents()->create([
                            'type' => 'proposal',
                            'file_name' => 'Proposal_'.Str::slug($data['activity_name']).'.docx',
                            'file_path' => $newPath,
                        ]);
                    }
                }

                if ($status === 'submitted') {
                    if ($this->submission?->status === 'revision') {
                        $submissionService->resubmit($submission, $user);
                    } else {
                        $submissionService->submit($submission);
                    }
                }
            });
        } catch (\Throwable $exception) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }

            throw $exception;
        }

        if ($newPath && $oldPath && $oldPath !== $newPath) {
            Storage::disk('local')->delete($oldPath);
        }

        session()->flash(
            'success',
            $status === 'submitted'
                ? 'Pengajuan berhasil dikirim untuk persetujuan.'
                : ($this->submission ? 'Perubahan revisi berhasil disimpan.' : 'Draft berhasil disimpan.')
        );
        $this->redirectRoute('sekretaris.pengajuan');
    }

    protected function validateAll(): void
    {
        $curr = $this->step;
        for ($i = 1; $i <= 4; $i++) {
            $this->step = $i;
            $this->validateCurrentStep();
        }
        $this->step = $curr;
    }

    protected function saveSubmission(
        string $status,
        ?SubmissionService $submissionService = null
    ): void {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        DB::transaction(function () use ($user, $status, $submissionService): void {
            $attributes = [
                'activity_name' => $this->activity_name,
                'activity_theme' => $this->activity_theme,
                'divisi_name' => $this->divisi_name,
                'meeting_date' => $this->meeting_date,
                'circular_letter' => $this->circular_letter,
                'budget_code' => $this->budget_code,
                'chairman_name' => $this->chairman_name,
                'chairman_nim' => $this->chairman_nim,
                'pembina_name' => $this->pembina_name,
                'pembina_nuptk' => $this->pembina_nuptk,
                'kaprodi_name' => $this->kaprodi_name,
                'kaprodi_nuptk' => $this->kaprodi_nuptk,
                'background' => $this->background,
                'objectives' => $this->objectives,
                'benefits' => $this->benefits,
                'target_participants' => $this->target_participants,
                'event_day' => $this->event_day,
                'event_date' => $this->event_date,
                'event_start_time' => $this->event_start_time,
                'event_end_time' => $this->event_end_time,
                'event_location' => $this->event_location,
                'activity_date' => $this->event_date,
                'start_time' => $this->event_start_time,
                'end_time' => $this->event_end_time,
                'location' => $this->event_location,
                'description' => collect($this->plan_context)
                    ->filter(fn (string $context): bool => trim($context) !== '')
                    ->implode("\n"),
                'purpose' => collect($this->objectives)
                    ->filter(fn (string $objective): bool => trim($objective) !== '')
                    ->implode("\n"),
                'total_amount' => $this->grandTotal,
            ];

            if ($this->submission) {
                $submission = $this->submission;
                $submission->update($attributes);
                $submission->items()->delete();
                $submission->timelines()->delete();
                $submission->committees()->delete();
                $submission->rundowns()->delete();
            } else {
                $submission = Submission::create([
                    ...$attributes,
                    'period_id' => $this->period?->id,
                    'ormawa_id' => $user->ormawa_id,
                    'submitted_by' => $user->id,
                    'submission_number' => $this->generateSubmissionNumber(),
                    'nota_number' => $this->generateNotaNumber(),
                    'status' => 'draft',
                ]);
            }

            foreach ($this->items as $item) {
                SubmissionItem::create([
                    'submission_id' => $submission->id,
                    'unit' => $item['unit'] ?? 'pcs',
                    'item_name' => $item['item_name'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'total_price' => (float) $item['qty'] * (float) $item['price'],
                ]);
            }

            $stages = [
                ['stage' => 'perencanaan', 'start' => $this->plan_start, 'end' => $this->plan_end, 'desc' => $this->plan_context],
                ['stage' => 'persiapan', 'start' => $this->prepare_start, 'end' => $this->prepare_end, 'desc' => $this->prepare_context],
                ['stage' => 'pelaksanaan', 'start' => $this->action_start, 'end' => $this->action_end, 'desc' => $this->action_context],
                ['stage' => 'penutupan', 'start' => $this->closing_start, 'end' => $this->closing_end, 'desc' => $this->closing_context],
            ];

            foreach ($stages as $idx => $stg) {
                SubmissionTimeline::create([
                    'submission_id' => $submission->id,
                    'stage' => $stg['stage'],
                    'start_date' => $stg['start'],
                    'end_date' => $stg['end'],
                    'description' => $stg['desc'],
                    'sort_order' => $idx,
                ]);
            }

            foreach ($this->committees as $index => $committee) {
                SubmissionCommittee::create([
                    'submission_id' => $submission->id,
                    'name' => $committee['name'],
                    'structural_role' => $committee['structural_role'] ?? 'Mahasiswa',
                    'committee_role' => $committee['committee_role'] ?? 'Anggota',
                    'description' => $committee['description'] ?? '-',
                    'sort_order' => $index,
                ]);
            }

            foreach ($this->rundowns as $index => $rundown) {
                SubmissionRundown::create([
                    'submission_id' => $submission->id,
                    'day_date' => $rundown['day_date'] ?? null,
                    'time_slot' => $rundown['time_slot'],
                    'event_name' => $rundown['event_name'],
                    'pic' => $rundown['pic'],
                    'note' => $rundown['note'] ?? null,
                    'sort_order' => $index,
                ]);
            }

            if ($status === 'submitted') {
                if (! $submissionService) {
                    throw new \LogicException('Layanan pengajuan harus tersedia saat mengirim pengajuan.');
                }

                if ($this->submission?->status === 'revision') {
                    $submissionService->resubmit($submission, $user);
                } else {
                    $submissionService->submit($submission);
                }
            }
        });

        $this->submission?->documents()
            ->where('type', 'proposal')
            ->get()
            ->each(function (Document $document): void {
                Storage::disk('local')->delete($document->file_path);
                $document->delete();
            });

        session()->flash(
            'success',
            $status === 'submitted'
                ? 'Pengajuan berhasil dikirim untuk persetujuan.'
                : ($this->submission ? 'Perubahan revisi berhasil disimpan.' : 'Draft berhasil disimpan.')
        );
        $this->redirectRoute('sekretaris.pengajuan');
    }

    protected function loadRevisionData(Submission $submission): void
    {
        $this->activity_name = $submission->activity_name;
        $this->activity_theme = $submission->activity_theme ?? '';
        $this->divisi_name = $submission->divisi_name ?? '';
        $this->meeting_date = $submission->meeting_date?->format('Y-m-d');
        $this->circular_letter = $submission->circular_letter ?? '';
        $this->budget_code = $submission->budget_code ?? '';
        $this->chairman_name = $submission->chairman_name ?? '';
        $this->chairman_nim = $submission->chairman_nim ?? '';
        $this->pembina_name = $submission->pembina_name ?? '';
        $this->pembina_nuptk = $submission->pembina_nuptk ?? '';
        $this->kaprodi_name = $submission->kaprodi_name ?? '';
        $this->kaprodi_nuptk = $submission->kaprodi_nuptk ?? '';
        $this->background = $submission->background ?? '';
        $this->objectives = $submission->objectives ?: [''];
        $this->benefits = $submission->benefits ?: [''];
        $this->target_participants = $submission->target_participants ?? '';
        $this->event_day = $submission->event_day ?? '';
        $this->event_date = $submission->event_date?->format('Y-m-d');
        $this->event_start_time = $submission->event_start_time?->format('H:i');
        $this->event_end_time = $submission->event_end_time?->format('H:i');
        $this->event_location = $submission->event_location ?? '';

        $timelines = $submission->timelines->keyBy('stage');
        $this->plan_start = $timelines->get('perencanaan')?->start_date?->format('Y-m-d');
        $this->plan_end = $timelines->get('perencanaan')?->end_date?->format('Y-m-d');
        $this->plan_context = $timelines->get('perencanaan')?->description ?: [''];
        $this->prepare_start = $timelines->get('persiapan')?->start_date?->format('Y-m-d');
        $this->prepare_end = $timelines->get('persiapan')?->end_date?->format('Y-m-d');
        $this->prepare_context = $timelines->get('persiapan')?->description ?: [''];
        $this->action_start = $timelines->get('pelaksanaan')?->start_date?->format('Y-m-d');
        $this->action_end = $timelines->get('pelaksanaan')?->end_date?->format('Y-m-d');
        $this->action_context = $timelines->get('pelaksanaan')?->description ?: [''];
        $this->closing_start = $timelines->get('penutupan')?->start_date?->format('Y-m-d');
        $this->closing_end = $timelines->get('penutupan')?->end_date?->format('Y-m-d');
        $this->closing_context = $timelines->get('penutupan')?->description ?: [''];

        $this->items = $submission->items
            ->map(fn (SubmissionItem $item): array => [
                'item_name' => $item->item_name,
                'qty' => $item->qty,
                'unit' => $item->unit ?? '',
                'price' => $item->price,
            ])
            ->all();
        $this->committees = $submission->committees
            ->map(fn (SubmissionCommittee $committee): array => [
                'name' => $committee->name,
                'structural_role' => $committee->structural_role,
                'committee_role' => $committee->committee_role,
                'description' => $committee->description,
            ])
            ->all();
        $this->rundowns = $submission->rundowns
            ->map(fn (SubmissionRundown $rundown): array => [
                'day_date' => $rundown->day_date ?? '',
                'time_slot' => $rundown->time_slot,
                'event_name' => $rundown->event_name,
                'pic' => $rundown->pic ?? '',
                'note' => $rundown->note ?? '',
            ])
            ->all();
    }

    protected function generateSubmissionNumber(): string
    {
        do {
            $number = 'SUB/'.now()->format('Y').'/'.Str::upper(Str::random(8));
        } while (Submission::where('submission_number', $number)->exists());

        return $number;
    }

    protected function generateNotaNumber(): string
    {
        $romans = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $month = $romans[(int) now()->format('m')];
        $count = Submission::whereYear('created_at', now()->year)->count() + 1;
        $formattedCount = sprintf('%03d', $count);
        $ormawaName = Str::contains(strtoupper($this->ormawa->name), 'BEM') ? 'BEM' : 'HIMA';

        return "ND / {$formattedCount} / {$month} / ".now()->format('Y')." / {$ormawaName}";
    }

    protected function messages(): array
    {
        return [
            'event_date.after_or_equal' => 'Tanggal pelaksanaan kegiatan minimal 1 bulan (30 hari) dari tanggal pengajuan proposal.',
            'proposalUpload.mimes' => 'Format file proposal yang diunggah harus berekstensi .docx atau .pdf.',
            'proposalUpload.max' => 'Ukuran file proposal maksimal 20 MB.',
        ];
    }

    public function render(): View
    {
        return view('livewire.sekretaris.pengajuan.create');
    }
}
