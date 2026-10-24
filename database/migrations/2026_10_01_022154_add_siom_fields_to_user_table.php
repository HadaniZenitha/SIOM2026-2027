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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->nullable()
                ->after('id')
                ->constrained('roles')
                ->nullOnDelete();

            $table->foreignId('ormawa_id')
                ->nullable()
                ->after('role_id')
                ->constrained('ormawas')
                ->nullOnDelete();

            $table->foreignId('program_studi_id')
                ->nullable()
                ->after('ormawa_id')
                ->constrained('program_studis')
                ->nullOnDelete();

            $table->string('phone', 30)
                ->nullable()
                ->after('email');
            
            $table->string('nuptk')
                ->nullable()
                ->after('phone');

            $table->boolean('is_active')
                ->default(true)
                ->after('nuptk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropForeign(['role_id']);
            $table->dropForeign(['ormawa_id']);
            $table->dropForeign(['program_studi_id']);

            $table->dropColumn([
                'role_id',
                'ormawa_id',
                'program_studi_id',
                'phone',
                'is_active',
            ]);
            
        });
    }
};
