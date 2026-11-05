<?php

namespace App\Livewire\Laporan;

use App\Models\Lpj;
use App\Models\LpjItem;
use App\Models\OrganizationBudget;
use App\Models\Ormawa;
use App\Models\Period;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $activePeriod = Period::query()
            ->where('status', 'active')
            ->orderByDesc('start_date')
            ->first();
        $reportType = 'none';
        $budget = null;
        $budgetActivities = collect();
        $lpjs = collect();
        $proposals = collect();
        $lpjStatusCounts = collect();
        $proposalStatusCounts = collect();
        $ormawaActivity = collect();
        $activeOrmawaCount = 0;
        $totalProposalCount = 0;
        $totalLpjCount = 0;
        $maximumProposalCount = 1;
        $maximumLpjCount = 1;

        if ($user->hasRole('superadmin')) {
            $reportType = 'superadmin';

            if ($activePeriod) {
                $proposalCounts = Submission::query()
                    ->where('period_id', $activePeriod->id)
                    ->selectRaw('ormawa_id, COUNT(*) as total')
                    ->groupBy('ormawa_id')
                    ->pluck('total', 'ormawa_id');
                $lpjCounts = Lpj::query()
                    ->where('period_id', $activePeriod->id)
                    ->selectRaw('ormawa_id, COUNT(*) as total')
                    ->groupBy('ormawa_id')
                    ->pluck('total', 'ormawa_id');

                $ormawaActivity = Ormawa::query()
                    ->orderBy('name')
                    ->get(['id', 'name', 'code'])
                    ->map(function (Ormawa $ormawa) use ($proposalCounts, $lpjCounts): array {
                        $proposals = (int) ($proposalCounts[$ormawa->id] ?? 0);
                        $lpjs = (int) ($lpjCounts[$ormawa->id] ?? 0);

                        return [
                            'id' => $ormawa->id,
                            'name' => $ormawa->name,
                            'code' => $ormawa->code,
                            'proposals' => $proposals,
                            'lpjs' => $lpjs,
                            'is_active' => $proposals + $lpjs > 0,
                        ];
                    });
                $totalProposalCount = (int) $proposalCounts->sum();
                $totalLpjCount = (int) $lpjCounts->sum();
                $activeOrmawaCount = $ormawaActivity->where('is_active', true)->count();
                $maximumProposalCount = max(1, (int) $ormawaActivity->max('proposals'));
                $maximumLpjCount = max(1, (int) $ormawaActivity->max('lpjs'));
            }
        } elseif ($user->hasAnyRole(['sekretaris_ormawa', 'pembina_ormawa'])) {
            $reportType = 'budget';

            if ($activePeriod && $user->ormawa_id) {
                $budget = OrganizationBudget::query()
                    ->where('period_id', $activePeriod->id)
                    ->where('ormawa_id', $user->ormawa_id)
                    ->first();

                $budgetActivities = Submission::query()
                    ->with(['lpj.items'])
                    ->where('period_id', $activePeriod->id)
                    ->where('ormawa_id', $user->ormawa_id)
                    ->where('status', 'approved')
                    ->latest('activity_date')
                    ->limit(50)
                    ->get();

                $lpjScope = Lpj::query()
                    ->where('period_id', $activePeriod->id)
                    ->where('ormawa_id', $user->ormawa_id);

                $lpjStatusCounts = $this->statusCounts(clone $lpjScope);
                $lpjs = $lpjScope
                    ->with(['submission', 'items'])
                    ->latest()
                    ->limit(50)
                    ->get();
            }
        } elseif ($user->hasRole('kaprodi')) {
            $reportType = 'kaprodi';

            if ($activePeriod && $user->program_studi_id) {
                $himaOrmawaIds = Ormawa::query()
                    ->where('program_studi_id', $user->program_studi_id)
                    ->where(function (Builder $query): void {
                        $query->where('type', 'hima')
                            ->orWhere('code', 'like', 'HIMA%')
                            ->orWhere('name', 'like', '%Himpunan Mahasiswa%');
                    })
                    ->pluck('id');

                $proposalScope = Submission::query()
                    ->where('period_id', $activePeriod->id)
                    ->whereIn('ormawa_id', $himaOrmawaIds);

                $proposalStatusCounts = $this->statusCounts(clone $proposalScope);
                $proposals = $proposalScope
                    ->with('ormawa')
                    ->latest()
                    ->limit(50)
                    ->get();

                $lpjScope = Lpj::query()
                    ->where('period_id', $activePeriod->id)
                    ->whereIn('ormawa_id', $himaOrmawaIds);

                $lpjStatusCounts = $this->statusCounts(clone $lpjScope);
                $lpjs = $lpjScope
                    ->with(['ormawa', 'submission'])
                    ->latest()
                    ->limit(50)
                    ->get();
            }
        }

        $realizedAmount = 0.0;
        if ($reportType === 'budget' && $activePeriod && $user->ormawa_id) {
            $realizedAmount = (float) LpjItem::query()
                ->whereHas('lpj', fn (Builder $query) => $query
                    ->where('period_id', $activePeriod->id)
                    ->where('ormawa_id', $user->ormawa_id)
                    ->where('status', 'approved'))
                ->sum('total_price');
        }

        return view('livewire.laporan.index', [
            'user' => $user,
            'activePeriod' => $activePeriod,
            'reportType' => $reportType,
            'budget' => $budget,
            'budgetActivities' => $budgetActivities,
            'realizedAmount' => $realizedAmount,
            'lpjs' => $lpjs,
            'proposals' => $proposals,
            'lpjStatusCounts' => $lpjStatusCounts,
            'proposalStatusCounts' => $proposalStatusCounts,
            'ormawaActivity' => $ormawaActivity,
            'activeOrmawaCount' => $activeOrmawaCount,
            'totalProposalCount' => $totalProposalCount,
            'totalLpjCount' => $totalLpjCount,
            'maximumProposalCount' => $maximumProposalCount,
            'maximumLpjCount' => $maximumLpjCount,
        ]);
    }

    /**
     * @return Collection<string, int>
     */
    private function statusCounts(Builder $query): Collection
    {
        return $query
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');
    }
}
