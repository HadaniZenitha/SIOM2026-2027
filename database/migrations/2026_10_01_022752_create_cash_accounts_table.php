<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('period_id')
                ->constrained('periods')
                ->cascadeOnDelete();

            $table->foreignId('ormawa_id')
                ->constrained('ormawas')
                ->cascadeOnDelete();

            // Saldo kas yang dibawa dari periode sebelumnya.
            $table->decimal('carry_over_amount', 15, 2)
                ->default(0);

            // Saldo awal periode.
            $table->decimal('opening_balance', 15, 2)
                ->default(0);

            // Saldo kas saat ini.
            $table->decimal('current_balance', 15, 2)
                ->default(0);

            $table->timestamps();

            $table->unique([
                'period_id',
                'ormawa_id'
            ]);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_accounts');
    }
};
