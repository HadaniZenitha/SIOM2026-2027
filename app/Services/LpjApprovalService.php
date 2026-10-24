<?php

namespace App\Services;

use App\Models\Lpj;
use App\Models\LpjApprovalLog;
use App\Models\LpjApprovalStep;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class LpjApprovalService
{
    public function submit(Lpj $lpj): void
    {
        DB::transaction(function () use ($lpj): void {
            if (! in_array($lpj->status, ['draft', 'revision'], true)) {
                throw new RuntimeException('LPJ ini tidak dapat dikirim.');
            }
            if (
                ! $lpj->documents()->whereIn('type', ['nota', 'keuangan'])->exists()
                || ! $lpj->documents()->where('type', 'dokumentasi')->exists()
            ) {
                throw new RuntimeException('Unggah minimal satu scan nota dan satu dokumentasi kegiatan sebelum mengirim.');
            }

            $revisionStep = $lpj->approvalSteps()->where('status', 'revision')->lockForUpdate()->first();
            if ($revisionStep) {
                $revisionStep->update([
                    'status' => 'pending',
                    'approved_by' => null,
                    'approved_at' => null,
                    'note' => null,
                ]);
                $lpj->update(['status' => 'waiting_approval', 'submitted_at' => now()]);
                LpjApprovalLog::create([
                    'lpj_id' => $lpj->id,
                    'lpj_approval_step_id' => $revisionStep->id,
                    'user_id' => $lpj->created_by,
                    'action' => 'resubmitted',
                    'note' => 'LPJ dikirim ulang setelah revisi.',
                ]);

                return;
            }

            if ($lpj->approvalSteps()->exists()) {
                throw new RuntimeException('Alur persetujuan LPJ sudah tersedia.');
            }
            $roleNames = ['pembina_ormawa', 'kaprodi', 'kabag_kemahasiswaan', 'warek'];
            foreach ($roleNames as $index => $roleName) {
                $role = Role::query()->where('name', $roleName)->firstOrFail();
                LpjApprovalStep::create([
                    'lpj_id' => $lpj->id,
                    'role_id' => $role->id,
                    'sequence' => $index + 1,
                    'status' => $index === 0 ? 'pending' : 'waiting',
                ]);
            }
            $lpj->update(['status' => 'waiting_approval', 'submitted_at' => now()]);
        });
    }

    public function decide(Lpj $lpj, User $user, string $action, ?string $note = null): void
    {
        if (! in_array($action, ['approved', 'rejected', 'revision'], true)) {
            throw new RuntimeException('Keputusan persetujuan tidak valid.');
        }

        DB::transaction(function () use ($lpj, $user, $action, $note): void {
            $step = $lpj->approvalSteps()
                ->where('role_id', $user->role_id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();
            if (! $step || ! in_array($lpj->status, ['submitted', 'waiting_approval'], true)) {
                throw new RuntimeException('Anda tidak memiliki tahap approval LPJ yang sedang menunggu.');
            }

            if ($action === 'approved' && (! $user->signature_path || ! Storage::disk('local')->exists($user->signature_path))) {
                throw new RuntimeException('Unggah tanda tangan Anda pada dashboard sebelum menyetujui LPJ.');
            }

            $step->update([
                'status' => $action,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'note' => $note,
                'signature_path' => $action === 'approved' ? $user->signature_path : null,
            ]);
            LpjApprovalLog::create([
                'lpj_id' => $lpj->id,
                'lpj_approval_step_id' => $step->id,
                'user_id' => $user->id,
                'action' => $action,
                'note' => $note,
            ]);

            if ($action !== 'approved') {
                $lpj->update(['status' => $action]);

                return;
            }

            $nextStep = $lpj->approvalSteps()
                ->where('sequence', '>', $step->sequence)->orderBy('sequence')->first();
            if ($nextStep) {
                $nextStep->update(['status' => 'pending']);
                $lpj->update(['status' => 'waiting_approval']);
            } else {
                $lpj->update(['status' => 'approved']);
            }
        });
    }
}
