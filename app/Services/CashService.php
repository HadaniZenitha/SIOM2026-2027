<?php

namespace App\Services;

use App\Models\CashAccount;
use App\Models\CashTransaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CashService
{
    public function income(
        CashAccount $account,
        float $amount,
        string $category,
        ?string $description = null,
        $date = null,
        $userId = null
    ): CashTransaction {

        return DB::transaction(function () use (
            $account,
            $amount,
            $category,
            $description,
            $date,
            $userId
        ) {

            if ($amount <= 0) {
                throw new RuntimeException(
                    'Jumlah pemasukan harus lebih dari 0.'
                );
            }

            $account->lockForUpdate();

            $transaction = CashTransaction::create([
                'cash_account_id' => $account->id,
                'type' => 'income',
                'category' => $category,
                'amount' => $amount,
                'description' => $description,
                'transaction_date' => $date ?? now(),
                'created_by' => $userId,
            ]);

            $account->current_balance += $amount;
            $account->save();

            return $transaction;
        });
    }

    public function expense(
        CashAccount $account,
        float $amount,
        string $category,
        ?string $description = null,
        $date = null,
        $userId = null
    ): CashTransaction {

        return DB::transaction(function () use (
            $account,
            $amount,
            $category,
            $description,
            $date,
            $userId
        ) {

            if ($amount <= 0) {
                throw new RuntimeException(
                    'Jumlah pengeluaran harus lebih dari 0.'
                );
            }

            $account->lockForUpdate();

            if ($amount > $account->current_balance) {
                throw new RuntimeException(
                    'Saldo kas tidak mencukupi.'
                );
            }

            $transaction = CashTransaction::create([
                'cash_account_id' => $account->id,
                'type' => 'expense',
                'category' => $category,
                'amount' => $amount,
                'description' => $description,
                'transaction_date' => $date ?? now(),
                'created_by' => $userId,
            ]);

            $account->current_balance -= $amount;
            $account->save();

            return $transaction;
        });
    }
}