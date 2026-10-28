<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tambah kolom ormawa_category agar template bisa dibedakan antara BEM/DPM vs HIMA/UKM.
     * Nilai: null = semua kategori, 'bem' = BEM/DPM, 'hima' = HIMA/UKM.
     */
    public function up(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            // Nullable string: null = semua ormawa, 'bem' = khusus BEM/DPM, 'hima' = HIMA/UKM
            $table->string('ormawa_category')->nullable()->after('type')->comment('null=all, bem=BEM/DPM, hima=HIMA/UKM');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropColumn('ormawa_category');
        });
    }
};
