<?php

namespace App\Services;

use App\Models\ApprovalLog;
use App\Models\ApprovalStep;
use App\Models\OrganizationBudget;
use App\Models\Role;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ApprovalService
{
    public function createApprovalFlow(Submission $submission): void
    {
        $roles = [
            1 => 'pembina_ormawa',
            2 => 'kaprodi',
            3 => 'kabag_kemahasiswaan',
            4 => 'warek',
        ];

        foreach ($roles as $sequence => $roleName) {

            $role = Role::where('name', $roleName)->firstOrFail();

            ApprovalStep::create([
                'submission_id' => $submission->id,
                'role_id' => $role->id,
                'sequence' => $sequence,
                'status' => $sequence === 1 ? 'pending' : 'waiting',
            ]);
        }
    }

    public function approve(
        Submission $submission,
        User $user,
        ?string $note = null
    ): void {

        DB::transaction(function () use ($submission, $user, $note) {
            $step = $submission->approvalSteps()
                ->whereHas('role', function ($query) use ($user) {
                    $query->where('id', $user->role_id);
                })
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (! $step) {
                throw new RuntimeException(
                    'Anda tidak memiliki tahap approval yang sedang menunggu.'
                );
            }

            if (! $user->signature_path || ! Storage::disk('local')->exists($user->signature_path)) {
                throw new RuntimeException('Unggah tanda tangan Anda pada dashboard sebelum menyetujui proposal.');
            }

            $step->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'note' => $note,
                'signature_path' => $user->signature_path,
            ]);

            ApprovalLog::create([
                'submission_id' => $submission->id,
                'approval_step_id' => $step->id,
                'user_id' => $user->id,
                'action' => 'approved',
                'note' => $note,
            ]);

            $nextStep = $submission->approvalSteps()
                ->where('sequence', '>', $step->sequence)
                ->orderBy('sequence')
                ->first();

            if ($nextStep) {

                $nextStep->update([
                    'status' => 'pending',
                ]);

                $submission->update([
                    'status' => 'waiting_approval',
                ]);

            } else {

                $budget = OrganizationBudget::where(
                    'period_id',
                    $submission->period_id
                )
                    ->where('ormawa_id', $submission->ormawa_id)
                    ->lockForUpdate()
                    ->first();

                if (! $budget) {
                    throw new RuntimeException(
                        'Pagu ORMAWA tidak ditemukan.'
                    );
                }

                if ($budget->remaining_amount < $submission->total_amount) {
                    throw new RuntimeException(
                        'Sisa pagu tidak mencukupi.'
                    );
                }

                $budget->allocated_amount += $submission->total_amount;
                $budget->remaining_amount =
                    $budget->budget_amount - $budget->allocated_amount;

                $budget->save();

                $submission->update([
                    'status' => 'approved',
                ]);
            }
        });
    }

    public function reject(
        Submission $submission,
        User $user,
        ?string $note = null
    ): void {

        DB::transaction(function () use ($submission, $user, $note) {

            $step = $submission->approvalSteps()
                ->whereHas('role', function ($query) use ($user) {
                    $query->where('id', $user->role_id);
                })
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (! $step) {
                throw new RuntimeException(
                    'Anda tidak memiliki tahap approval yang sedang menunggu.'
                );
            }

            $step->update([
                'status' => 'rejected',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'note' => $note,
            ]);

            $submission->update([
                'status' => 'rejected',
            ]);

            ApprovalLog::create([
                'submission_id' => $submission->id,
                'approval_step_id' => $step->id,
                'user_id' => $user->id,
                'action' => 'rejected',
                'note' => $note,
            ]);
        });
    }

    public function requestRevision(
        Submission $submission,
        User $user,
        ?string $note = null
    ): void {

        DB::transaction(function () use ($submission, $user, $note) {

            $step = $submission->approvalSteps()
                ->whereHas('role', function ($query) use ($user) {
                    $query->where('id', $user->role_id);
                })
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (! $step) {
                throw new RuntimeException(
                    'Approval tidak ditemukan.'
                );
            }

            $step->update([
                'status' => 'revision',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'note' => $note,
            ]);

            $submission->update([
                'status' => 'revision',
            ]);

            ApprovalLog::create([
                'submission_id' => $submission->id,
                'approval_step_id' => $step->id,
                'user_id' => $user->id,
                'action' => 'revision',
                'note' => $note,
            ]);
        });
    }
}
