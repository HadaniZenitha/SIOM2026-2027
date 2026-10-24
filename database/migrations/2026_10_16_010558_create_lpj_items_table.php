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
        Schema::create('lpj_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lpj_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submission_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name');
            $table->decimal('qty', 15, 2)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpj_items');
    }
};
