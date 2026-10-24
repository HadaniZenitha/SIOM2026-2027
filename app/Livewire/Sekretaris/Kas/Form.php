<?php

namespace App\Livewire\Sekretaris\Kas;

use App\Models\CashAccount;
use App\Models\CashTransaction;
use App\Models\Period;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?int $transactionId = null;

    public string $type = 'income';

    public string $category = '';

    public string $amount = '';

    public string $description = '';

    public ?string $transaction_date = null;

    public function mount(?CashTransaction $transaction = null): void
    {
        if ($transaction?->exists) {
            $transaction = $this->transactionsQuery()->findOrFail($transaction->id);
            $this->transactionId = $transaction->id;
            $this->type = $transaction->type;
            $this->category = $transaction->category;
            $this->amount = (string) $transaction->amount;
            $this->description = $transaction->description ?? '';
            $this->transaction_date = $transaction->transaction_date->format('Y-m-d');
        } else {
            $this->transaction_date = now()->toDateString();
        }
    }

    public function save(): void
    {
        $user = Auth::user();
        abort_unless($user?->hasRole('sekretaris_ormawa') && $user->ormawa_id, 403);

        $data = $this->validate([
            'type' => ['required', Rule::in(['income', 'expense'])],
            'category' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['nullable', 'string', 'max:5000'],
            'transaction_date' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($data, $user): void {
            $period = Period::query()->where('status', 'active')->first()
                ?? Period::query()->latest('start_date')->firstOrFail();
            $account = CashAccount::query()->firstOrCreate(
                ['period_id' => $period->id, 'ormawa_id' => $user->ormawa_id],
                ['opening_balance' => 0, 'current_balance' => 0, 'carry_over_amount' => 0]
            );
            $account = CashAccount::query()->whereKey($account->id)->lockForUpdate()->firstOrFail();
            $previous = $this->transactionId
                ? $this->transactionsQuery()->lockForUpdate()->findOrFail($this->transactionId)
                : null;

            $balance = (float) $account->current_balance;
            if ($previous) {
                $balance += $previous->type === 'income' ? -(float) $previous->amount : (float) $previous->amount;
            }
            $newBalance = $balance + ($data['type'] === 'income' ? 1 : -1) * (float) $data['amount'];
            if ($newBalance < 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Saldo kas tidak mencukupi untuk pengeluaran ini.',
                ]);
            }

            $transaction = $previous ?? new CashTransaction;
            $transaction->fill([
                ...$data,
                'cash_account_id' => $account->id,
                'created_by' => $previous?->created_by ?? $user->id,
            ]);
            $transaction->save();
            $account->update(['current_balance' => $newBalance]);
        });

        session()->flash('success', $this->transactionId ? 'Transaksi kas diperbarui.' : 'Transaksi kas disimpan.');
        $this->redirectRoute('sekretaris.kas');
    }

    private function transactionsQuery(): Builder
    {
        return CashTransaction::query()
            ->whereHas('cashAccount', fn ($query) => $query
                ->where('ormawa_id', Auth::user()->ormawa_id)
                ->whereHas('period', fn ($period) => $period->where('status', 'active')));
    }

    public function render(): View
    {
        abort_unless(Auth::user()?->hasRole('sekretaris_ormawa'), 403);

        return view('livewire.sekretaris.kas.form');
    }
}
