<?php

namespace App\Services;

use App\Models\ApprovalLog;
use App\Models\OrganizationBudget;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SubmissionService
{
    public function __construct(
        protected ApprovalService $approvalService
    ) {}

    public function submit(Submission $submission): void
    {
        DB::transaction(function () use ($submission) {
            if ($submission->status !== 'draft' || $submission->approvalSteps()->exists()) {
                throw new RuntimeException('Pengajuan ini tidak dapat dikirim ulang.');
            }

            $budget = OrganizationBudget::where(
                'period_id',
                $submission->period_id
            )
                ->where('ormawa_id', $submission->ormawa_id)
                ->lockForUpdate()
                ->first();

            if (! $budget) {
                throw new RuntimeException(
                    'Pagu ORMAWA untuk periode ini belum tersedia.'
                );
            }

            if ($submission->total_amount > $budget->remaining_amount) {
                throw new RuntimeException(
                    'Total pengajuan melebihi sisa pagu.'
                );
            }

            $submission->update([
                'status' => 'waiting_approval',
                'submitted_at' => now(),
            ]);

            $this->approvalService
                ->createApprovalFlow($submission);
        });
    }

    public function resubmit(Submission $submission, User $user): void
    {
        DB::transaction(function () use ($submission, $user): void {
            if ($submission->submitted_by !== $user->id || $submission->status !== 'revision') {
                throw new RuntimeException('Pengajuan ini tidak dapat dikirim ulang oleh akun Anda.');
            }

            $step = $submission->approvalSteps()
                ->where('status', 'revision')
                ->lockForUpdate()
                ->first();

            if (! $step) {
                throw new RuntimeException('Tahap revisi pengajuan tidak ditemukan.');
            }

            $budget = OrganizationBudget::where('period_id', $submission->period_id)
                ->where('ormawa_id', $submission->ormawa_id)
                ->lockForUpdate()
                ->first();

            if (! $budget) {
                throw new RuntimeException('Pagu ORMAWA untuk periode ini belum tersedia.');
            }

            if ($submission->total_amount > $budget->remaining_amount) {
                throw new RuntimeException('Total pengajuan melebihi sisa pagu.');
            }

            $step->update([
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'note' => null,
            ]);

            $submission->update([
                'status' => 'waiting_approval',
                'submitted_at' => now(),
            ]);

            ApprovalLog::create([
                'submission_id' => $submission->id,
                'approval_step_id' => $step->id,
                'user_id' => $user->id,
                'action' => 'resubmitted',
                'note' => 'Pengajuan dikirim ulang setelah revisi.',
            ]);
        });
    }
}