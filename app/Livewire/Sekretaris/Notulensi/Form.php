<?php

namespace App\Livewire\Sekretaris\Notulensi;

use App\Models\Meeting;
use App\Models\MeetingAttendance;
use App\Models\OrganizationMember;
use App\Models\Period;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?int $meetingId = null;

    #[Locked]
    public ?int $periodId = null;

    public string $title = '';

    public ?string $meeting_date = null;

    public ?string $start_time = null;

    public ?string $end_time = null;

    public string $location = '';

    public string $minutes_type = 'text';

    public string $minutes_text = '';

    public array $attendances = [];

    public function mount(?Meeting $meeting = null): void
    {
        $user = Auth::user();
        abort_unless($user?->hasRole('sekretaris_ormawa') && $user->ormawa_id, 403);

        if ($meeting?->exists) {
            $meeting = $this->meetingsQuery()->with('attendances')->findOrFail($meeting->id);
            $this->meetingId = $meeting->id;
            $this->periodId = $meeting->period_id;
            $this->title = $meeting->title;
            $this->meeting_date = $meeting->meeting_date->format('Y-m-d');
            $this->start_time = $meeting->start_time ? substr($meeting->start_time, 0, 5) : null;
            $this->end_time = $meeting->end_time ? substr($meeting->end_time, 0, 5) : null;
            $this->location = $meeting->location ?? '';
            $this->minutes_type = $meeting->minutes_type;
            $this->minutes_text = $meeting->minutes_text ?? '';
            $this->attendances = $meeting->attendances->mapWithKeys(
                fn (MeetingAttendance $attendance): array => [
                    $attendance->member_id => ['status' => $attendance->status, 'note' => $attendance->note ?? ''],
                ]
            )->all();
        } else {
            $this->periodId = Period::query()->where('status', 'active')->value('id')
                ?? Period::query()->latest('start_date')->value('id');
        }

        if ($this->periodId) {
            $memberIds = OrganizationMember::query()
                ->where('period_id', $this->periodId)
                ->where('ormawa_id', $user->ormawa_id)
                ->pluck('id');
            foreach ($memberIds as $memberId) {
                $this->attendances[$memberId] ??= ['status' => 'hadir', 'note' => ''];
            }
        }
    }

    public function save(): void
    {
        $user = Auth::user();
        abort_unless($user?->hasRole('sekretaris_ormawa') && $user->ormawa_id, 403);

        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'meeting_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
            'minutes_type' => ['required', Rule::in(['text'])],
            'minutes_text' => ['required', 'string', 'max:50000'],
            'attendances' => ['nullable', 'array'],
            'attendances.*.status' => ['required', Rule::in(['hadir', 'izin', 'sakit', 'alpa'])],
            'attendances.*.note' => ['nullable', 'string', 'max:1000'],
        ]);

        $period = Period::query()->findOrFail($this->periodId);
        $members = OrganizationMember::query()
            ->where('ormawa_id', $user->ormawa_id)
            ->where('period_id', $period->id)
            ->pluck('id')->all();
        $attendance = collect($data['attendances'] ?? []);
        if ($attendance->keys()->diff($members)->isNotEmpty()) {
            throw ValidationException::withMessages([
                'attendances' => 'Daftar hadir memuat anggota yang tidak terdaftar pada ORMAWA/periode ini.',
            ]);
        }

        DB::transaction(function () use ($data, $user, $period, $attendance): void {
            $meeting = $this->meetingId
                ? $this->meetingsQuery()->findOrFail($this->meetingId)
                : new Meeting;
            $meeting->fill([
                ...$data,
                'period_id' => $period->id,
                'ormawa_id' => $user->ormawa_id,
                'created_by' => $meeting->created_by ?? $user->id,
            ]);
            $meeting->save();
            $meeting->attendances()->delete();
            foreach ($attendance as $memberId => $values) {
                $meeting->attendances()->create([
                    'member_id' => $memberId,
                    'status' => $values['status'],
                    'note' => $values['note'] ?? null,
                ]);
            }
        });

        session()->flash('success', $this->meetingId ? 'Notulensi berhasil diperbarui.' : 'Notulensi berhasil disimpan.');
        $this->redirectRoute('sekretaris.notulensi');
    }

    private function meetingsQuery(): Builder
    {
        return Meeting::query()->where('ormawa_id', Auth::user()->ormawa_id);
    }

    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user?->hasRole('sekretaris_ormawa') && $user->ormawa_id, 403);
        $period = Period::query()->find($this->periodId);

        return view('livewire.sekretaris.notulensi.form', [
            'members' => $period
                ? OrganizationMember::query()->where('period_id', $period->id)
                    ->where('ormawa_id', $user->ormawa_id)->orderBy('name')->get()
                : collect(),
        ]);
    }
}
