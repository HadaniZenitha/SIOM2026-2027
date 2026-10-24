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
        Schema::create('organization_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('period_id')
                ->constrained('periods')
                ->cascadeOnDelete();

            $table->foreignId('ormawa_id')
                ->constrained('ormawas')
                ->cascadeOnDelete();

            $table->foreignId('program_studi_id')
                ->nullable()
                ->constrained('program_studis')
                ->nullOnDelete();

            $table->foreignId('organization_division_id')
                ->nullable()
                ->constrained('organization_divisions')
                ->nullOnDelete();

            $table->string('position')->nullable();
            $table->unsignedSmallInteger('position_number')->nullable();

            $table->string('name');

            $table->string('birth_place');
            $table->date('birth_date');

            $table->string('nim', 50);

            $table->string('phone', 30);

            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            $table->timestamps();

            $table->unique(['period_id', 'ormawa_id', 'nim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_members');
    }
};
