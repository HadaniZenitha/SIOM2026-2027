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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('period_id')
                ->constrained('periods')
                ->cascadeOnDelete();

            $table->foreignId('ormawa_id')
                ->constrained('ormawas')
                ->cascadeOnDelete();

            $table->foreignId('submitted_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('submission_number')->unique();

            $table->string('activity_name');
            $table->string('activity_theme')->nullable();

            $table->date('activity_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->string('location')->nullable();

            $table->text('description')->nullable();
            $table->text('purpose')->nullable();

            $table->string('chairman_name')->nullable();
            $table->string('chairman_nim')->nullable();
            $table->string('pembina_name')->nullable();
            $table->string('pembina_nuptk')->nullable();
            $table->string('kaprodi_name')->nullable();
            $table->string('kaprodi_nuptk')->nullable();

            $table->enum('status', [
                'draft',
                'submitted',
                'revision',
                'waiting_approval',
                'approved',
                'rejected',
                'completed'
            ])->default('draft');

            $table->decimal('total_amount', 15, 2)->default(0);

            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
