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
        Schema::table('submissions', function (Blueprint $table) {
            $table->index(['ormawa_id', 'period_id']);
            $table->index(['status']);
            $table->index(['submitted_by']);
            $table->index(['created_at']);
        });

        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->index(['cash_account_id', 'transaction_date']);
            $table->index(['type']);
        });

        Schema::table('organization_members', function (Blueprint $table) {
            $table->index(['ormawa_id', 'period_id']);
            $table->index(['program_studi_id']);
        });

        Schema::table('approval_steps', function (Blueprint $table) {
            $table->index(['submission_id', 'status']);
            $table->index(['role_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex(['ormawa_id', 'period_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['submitted_by']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->dropIndex(['cash_account_id', 'transaction_date']);
            $table->dropIndex(['type']);
        });

        Schema::table('organization_members', function (Blueprint $table) {
            $table->dropIndex(['ormawa_id', 'period_id']);
            $table->dropIndex(['program_studi_id']);
        });

        Schema::table('approval_steps', function (Blueprint $table) {
            $table->dropIndex(['submission_id', 'status']);
            $table->dropIndex(['role_id', 'status']);
        });
    }
};
