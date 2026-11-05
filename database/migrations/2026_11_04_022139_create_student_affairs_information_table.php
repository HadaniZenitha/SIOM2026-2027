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
        Schema::create('student_affairs_information', function (Blueprint $table) {
            $table->id();
            $table->string('category', 40);
            $table->string('title', 200);
            $table->text('summary');
            $table->date('published_at')->nullable();
            $table->string('document_path')->nullable();
            $table->string('document_name')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_affairs_information');
    }
};
