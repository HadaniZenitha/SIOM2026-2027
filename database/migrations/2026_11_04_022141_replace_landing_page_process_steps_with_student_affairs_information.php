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
        Schema::dropIfExists('landing_page_process_steps');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('landing_page_process_steps', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->text('description');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }
};
