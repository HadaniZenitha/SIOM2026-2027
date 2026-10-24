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
        Schema::table('lpjs', function (Blueprint $table) {
            $table->unique('submission_id');
        });

        Schema::create('lpj_approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lpj_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('sequence');
            $table->enum('status', ['waiting', 'pending', 'approved', 'revision', 'rejected'])
                ->default('waiting');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['lpj_id', 'sequence']);
        });

        Schema::create('lpj_approval_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lpj_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lpj_approval_step_id')->nullable()
                ->constrained('lpj_approval_steps')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('action');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpj_approval_logs');
        Schema::dropIfExists('lpj_approval_steps');
        Schema::table('lpjs', function (Blueprint $table) {
            $table->dropUnique(['submission_id']);
        });
    }
};
