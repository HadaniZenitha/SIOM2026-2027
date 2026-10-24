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
        Schema::create('ormawas', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });

        Schema::create('organization_divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ormawa_id')->constrained('ormawas')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->unique(['ormawa_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_divisions');
        Schema::dropIfExists('ormawas');
    }
};
