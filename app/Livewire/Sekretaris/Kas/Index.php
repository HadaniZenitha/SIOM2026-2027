<?php

namespace App\Livewire\Sekretaris\Kas;

use App\Models\CashAccount;
use App\Models\CashTransaction;
use App\Models\Period;
use App\Services\SpreadsheetService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public mixed $importFile = null;

    public function exportExcel(SpreadsheetService $spreadsheet): StreamedResponse
    {
        $this->authorizeSecretary();
        $transactions = $this->transactionsQuery()
            ->with('cashAccount.period')
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();
        $rows = $transactions->map(fn (CashTransaction $transaction): array => [
            $transaction->type,
            $transaction->category,
            (float) $transaction->amount,
            $transaction->description ?? '',
            $transaction->transaction_date->format('Y-m-d'),
        ])->all();

        return $spreadsheet->download('kas-ormawa.xlsx', 'Kas', [
            'type', 'category', 'amount', 'description', 'transaction_date',
        ], $rows);
    }

    public function downloadImportTemplate(SpreadsheetService $spreadsheet): StreamedResponse
    {
        $this->authorizeSecretary();

        return $spreadsheet->download('template-impor-kas.xlsx', 'Kas', [
            'type', 'category', 'amount', 'description', 'transaction_date',
        ], []);
    }

    public function importExcel(SpreadsheetService $spreadsheet): void
    {
        $this->authorizeSecretary();
        $this->validate([
            'importFile' => ['required', 'file', 'mimes:xlsx', 'extensions:xlsx', 'max:5120'],
        ]);

        $records = $spreadsheet->read($this->importFile, [
            'type', 'category', 'amount', 'description', 'transaction_date',
        ]);
        $transactions = [];

        foreach ($records as $record) {
            $validator = validator($record['values'], [
                'type' => ['required', Rule::in(['income', 'expense'])],
                'category' => ['required', 'string', 'max:255'],
                'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
                'description' => ['nullable', 'string', 'max:5000'],
                'transaction_date' => ['required', 'date'],
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages([
                    'importFile' => 'Baris '.$record['line'].': '.$validator->errors()->first(),
                ]);
            }

            $transactions[] = [
                ...$validator->validated(),
                'line' => $record['line'],
            ];
        }

        $user = Auth::user();
        $period = Period::query()->where('status', 'active')->first()
            ?? Period::query()->latest('start_date')->firstOrFail();

        DB::transaction(function () use ($transactions, $period, $user): void {
            $account = CashAccount::query()->firstOrCreate(
                ['period_id' => $period->id, 'ormawa_id' => $user->ormawa_id],
                ['opening_balance' => 0, 'current_balance' => 0, 'carry_over_amount' => 0]
            );
            $account = CashAccount::query()->whereKey($account->id)->lockForUpdate()->firstOrFail();
            $balance = (float) $account->current_balance;

            foreach ($transactions as $transaction) {
                $balance += $transaction['type'] === 'income'
                    ? (float) $transaction['amount']
                    : -(float) $transaction['amount'];

                if ($balance < 0) {
                    throw ValidationException::withMessages([
                        'importFile' => 'Baris '.$transaction['line'].': saldo kas tidak mencukupi untuk pengeluaran ini.',
                    ]);
                }
            }

            foreach ($transactions as $transaction) {
                CashTransaction::query()->create([
                    'cash_account_id' => $account->id,
                    'created_by' => $user->id,
                    'type' => $transaction['type'],
                    'category' => $transaction['category'],
                    'amount' => $transaction['amount'],
                    'description' => $transaction['description'] ?: null,
                    'transaction_date' => $transaction['transaction_date'],
                ]);
            }

            $account->update(['current_balance' => $balance]);
        });

        $this->reset('importFile');
        session()->flash('success', count($transactions).' transaksi kas berhasil diimpor.');
    }

    public function delete(int $id): void
    {
        $this->authorizeSecretary();
        $transaction = $this->transactionsQuery()->findOrFail($id);
        $deleted = false;

        DB::transaction(function () use ($transaction, &$deleted): void {
            $account = CashAccount::query()->whereKey($transaction->cash_account_id)->lockForUpdate()->firstOrFail();
            $balance = (float) $account->current_balance
                + ($transaction->type === 'income' ? -(float) $transaction->amount : (float) $transaction->amount);
            if ($balance < 0) {
                session()->flash('error', 'Transaksi tidak dapat dihapus karena saldo kas telah terpakai.');

                return;
            }

            $transaction->delete();
            $account->update(['current_balance' => $balance]);
            $deleted = true;
        });

        if ($deleted) {
            session()->flash('success', 'Transaksi kas berhasil dihapus.');
        }
    }

    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user?->hasRole('sekretaris_ormawa') && $user->ormawa_id, 403);
        $period = Period::query()->where('status', 'active')->first()
            ?? Period::query()->latest('start_date')->first();
        $account = $period
            ? CashAccount::query()->firstOrCreate(
                ['period_id' => $period->id, 'ormawa_id' => $user->ormawa_id],
                ['opening_balance' => 0, 'current_balance' => 0, 'carry_over_amount' => 0]
            )
            : null;

        return view('livewire.sekretaris.kas.index', [
            'account' => $account?->load('period'),
            'transactions' => $account?->transactions()->with('creator')->latest('transaction_date')->paginate(10),
        ]);
    }

    private function transactionsQuery(): Builder
    {
        return CashTransaction::query()
            ->whereHas('cashAccount', fn ($query) => $query
                ->where('ormawa_id', Auth::user()->ormawa_id)
                ->whereHas('period', fn ($period) => $period->where('status', 'active')));
    }

    private function authorizeSecretary(): void
    {
        abort_unless(Auth::user()?->hasRole('sekretaris_ormawa') && Auth::user()->ormawa_id, 403);
    }

}
