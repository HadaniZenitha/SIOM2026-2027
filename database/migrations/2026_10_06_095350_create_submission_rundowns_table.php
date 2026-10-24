<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_rundowns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('submission_id')
                ->constrained('submissions')
                ->cascadeOnDelete();

            $table->string('day_date')->nullable();
            $table->string('time_slot');
            $table->string('event_name');
            $table->string('pic')->nullable();
            $table->text('note')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['submission_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_rundowns');
    }
};