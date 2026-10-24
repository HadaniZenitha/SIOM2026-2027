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
        Schema::create('organization_budgets', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('period_id')
                ->constrained('periods')
                ->cascadeOnDelete();

            $table->foreignId('ormawa_id')
                ->constrained('ormawas')
                ->cascadeOnDelete();

            // Nominal pagu yang ditetapkan Superadmin
            $table->decimal('budget_amount', 15, 2)
                ->default(0);

            // Total dana yang telah dialokasikan
            // melalui pengajuan yang telah disetujui.
            $table->decimal('allocated_amount', 15, 2)
                ->default(0);

            // Sisa dana pagu.
            $table->decimal('remaining_amount', 15, 2)
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
        Schema::dropIfExists('organization_budgets');
    }
};
