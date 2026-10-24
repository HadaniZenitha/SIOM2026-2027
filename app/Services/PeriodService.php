<?php

namespace App\Services;

use App\Models\CashAccount;
use App\Models\OrganizationBudget;
use App\Models\Ormawa;
use App\Models\Period;
use Illuminate\Support\Facades\DB;

class PeriodService
{
    public function createPeriod(
        array $data,
        float $defaultBudget = 0
    ): Period {

        return DB::transaction(function () use (
            $data,
            $defaultBudget
        ) {

            $period = Period::create($data);

            $previousPeriod = Period::where('id', '<>', $period->id)
                ->orderByDesc('end_date')
                ->first();

            $ormawas = Ormawa::where('is_active', true)->get();

            foreach ($ormawas as $ormawa) {

                /*
                 * PAGU BARU
                 *
                 * Tidak mengambil sisa pagu periode sebelumnya.
                 */
                OrganizationBudget::create([
                    'period_id' => $period->id,
                    'ormawa_id' => $ormawa->id,
                    'budget_amount' => $defaultBudget,
                    'allocated_amount' => 0,
                    'remaining_amount' => $defaultBudget,
                ]);

                /*
                 * KAS
                 *
                 * Hanya kas yang dibawa ke periode baru.
                 */
                $carryOver = 0;

                if ($previousPeriod) {
                    $previousCash = CashAccount::where(
                        'period_id',
                        $previousPeriod->id
                    )
                        ->where('ormawa_id', $ormawa->id)
                        ->first();

                    $carryOver = $previousCash?->current_balance ?? 0;
                }

                CashAccount::create([
                    'period_id' => $period->id,
                    'ormawa_id' => $ormawa->id,
                    'carry_over_amount' => $carryOver,
                    'opening_balance' => $carryOver,
                    'current_balance' => $carryOver,
                ]);
            }

            return $period;
        });
    }
}