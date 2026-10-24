<?php

namespace App\Livewire\Sekretaris\Notulensi;

use App\Models\Meeting;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function delete(int $id): void
    {
        $this->meetingsQuery()->findOrFail($id)->delete();
        session()->flash('success', 'Notulensi berhasil dihapus.');
    }

    public function render(): View
    {
        abort_unless(Auth::user()?->hasRole('sekretaris_ormawa') && Auth::user()->ormawa_id, 403);

        return view('livewire.sekretaris.notulensi.index', [
            'meetings' => $this->meetingsQuery()->with('creator')
                ->when($this->search !== '', fn ($query) => $query->where('title', 'like', '%'.$this->search.'%'))
                ->latest('meeting_date')->paginate(10),
        ]);
    }

    private function meetingsQuery(): Builder
    {
        return Meeting::query()->where('ormawa_id', Auth::user()->ormawa_id);
    }
}
