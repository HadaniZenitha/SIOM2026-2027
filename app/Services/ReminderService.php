<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReminderService
{
    /**
     * @return list<array{type: string, title: string, message: string, url: string, date: string, severity: string}>
     */
    public function forUser(User $user): array
    {
        if (! $user->hasRole('sekretaris_ormawa') || ! $user->ormawa_id) {
            return [];
        }

        $today = today();
        $reminders = [];

        $unapprovedSubmissions = Submission::query()
            ->where('ormawa_id', $user->ormawa_id)
            ->whereIn('status', ['submitted', 'revision', 'waiting_approval'])
            ->whereNotNull('activity_date')
            ->whereDate('activity_date', '>=', $today->toDateString())
            ->whereDate('activity_date', '<=', $today->copy()->addDays(14)->toDateString())
            ->orderBy('activity_date')
            ->limit(50)
            ->get(['id', 'activity_name', 'activity_date']);

        foreach ($unapprovedSubmissions as $submission) {
            $daysUntilActivity = (int) $today->diffInDays($submission->activity_date);
            $reminders[] = [
                'type' => 'proposal_approval',
                'title' => 'Proposal belum disetujui',
                'message' => $submission->activity_name.' belum mendapatkan persetujuan. Pelaksanaan tinggal '.$daysUntilActivity.' hari lagi.',
                'url' => route('sekretaris.pengajuan'),
                'date' => $submission->activity_date->format('d/m/Y'),
                'severity' => $daysUntilActivity <= 7 ? 'urgent' : 'warning',
            ];
        }

        $completedActivities = Submission::query()
            ->with(['timelines' => fn (HasMany $query) => $query
                ->where('stage', 'pelaksanaan')
                ->orderByDesc('end_date')])
            ->where('ormawa_id', $user->ormawa_id)
            ->whereIn('status', ['approved', 'completed'])
            ->whereDoesntHave('lpj')
            ->where(function (Builder $query) use ($today): void {
                $query->whereHas('timelines', fn (Builder $timelineQuery) => $timelineQuery
                    ->where('stage', 'pelaksanaan')
                    ->whereDate('end_date', '<=', $today->toDateString()))
                    ->orWhere(fn (Builder $fallbackQuery) => $fallbackQuery
                        ->whereDoesntHave('timelines', fn (Builder $timelineQuery) => $timelineQuery
                            ->where('stage', 'pelaksanaan'))
                        ->whereNotNull('activity_date')
                        ->whereDate('activity_date', '<=', $today->toDateString()));
            })
            ->orderByDesc('activity_date')
            ->limit(100)
            ->get(['id', 'activity_name', 'activity_date']);

        foreach ($completedActivities as $submission) {
            $executionEnd = $submission->timelines->first()?->end_date ?? $submission->activity_date;
            if (! $executionEnd instanceof CarbonInterface) {
                continue;
            }

            $daysSinceExecution = (int) $executionEnd->diffInDays($today);
            $deadline = $executionEnd->copy()->addWeek();
            $isOverdue = $daysSinceExecution > 7;

            $reminders[] = [
                'type' => $isOverdue ? 'lpj_overdue' : 'lpj_due',
                'title' => $isOverdue ? 'LPJ melewati batas waktu' : 'Waktunya membuat LPJ',
                'message' => $isOverdue
                    ? $submission->activity_name.' telah selesai lebih dari satu pekan lalu dan belum memiliki LPJ.'
                    : $submission->activity_name.' telah selesai. Buat LPJ paling lambat '.$deadline->format('d/m/Y').'.',
                'url' => route('sekretaris.lpj'),
                'date' => $executionEnd->format('d/m/Y'),
                'severity' => $isOverdue ? 'urgent' : 'warning',
            ];
        }

        return $reminders;
    }
}
