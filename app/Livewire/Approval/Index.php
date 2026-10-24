<?php

namespace App\Livewire\Approval;

use App\Models\Lpj;
use App\Models\Submission;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public function render(): View
    {
        $submissions = Submission::query()
            ->with(['period', 'ormawa', 'submitter', 'approvalSteps.role'])
            ->whereIn('status', ['submitted', 'waiting_approval'])
            ->whereHas('approvalSteps', function ($query): void {
                $query
                    ->where('role_id', Auth::user()->role_id)
                    ->where('status', 'pending');
            })
            ->latest('submitted_at')
            ->paginate(15);

        $lpjs = Lpj::query()
            ->with(['period', 'ormawa', 'creator', 'approvalSteps.role'])
            ->whereIn('status', ['submitted', 'waiting_approval'])
            ->whereHas('approvalSteps', function ($query): void {
                $query->where('role_id', Auth::user()->role_id)->where('status', 'pending');
            })
            ->latest('submitted_at')
            ->paginate(15, ['*'], 'lpjPage');

        return view('livewire.approval.index', [
            'submissions' => $submissions,
            'lpjs' => $lpjs,
        ]);
    }
}
