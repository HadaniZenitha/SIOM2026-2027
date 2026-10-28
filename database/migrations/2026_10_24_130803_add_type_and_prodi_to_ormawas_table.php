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
        Schema::table('ormawas', function (Blueprint $table) {
            $table->string('type', 20)->default('hima')->after('code'); // hima, ukm, dpm, bem
            $table->foreignId('program_studi_id')->nullable()->after('type')->constrained('program_studis')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ormawas', function (Blueprint $table) {
            $table->dropForeign(['program_studi_id']);
            $table->dropColumn(['type', 'program_studi_id']);
        });
    }
};
