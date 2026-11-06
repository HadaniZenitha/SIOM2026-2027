<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('landing_page_process_steps', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->text('description');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        $timestamp = now();

        DB::table('landing_page_process_steps')->insert([
            [
                'title' => 'Penyusunan proposal dan RAB',
                'description' => 'Sekretaris ORMAWA menyiapkan proposal dan RAB. Tanggal kegiatan wajib minimal 30 hari kalender setelah pengajuan. Dokumen dapat dibuat otomatis atau diunggah dalam format DOCX/PDF.',
                'sort_order' => 1,
                'is_published' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'title' => 'Review dan persetujuan pembina serta Kaprodi',
                'description' => 'Proposal HIMA ditinjau oleh Pembina dan Kaprodi. Proposal UKM, DPM, dan BEM ditinjau oleh Pembina unit sesuai alur persetujuan.',
                'sort_order' => 2,
                'is_published' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'title' => 'Validasi anggaran dan pagu',
                'description' => 'Bagian Kemahasiswaan memeriksa ketersediaan pagu dan kelayakan anggaran kegiatan.',
                'sort_order' => 3,
                'is_published' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'title' => 'Persetujuan akhir dan tanda tangan elektronik',
                'description' => 'Wakil Rektor III memberikan persetujuan akhir dan otorisasi digital sebelum pencairan dana kegiatan.',
                'sort_order' => 4,
                'is_published' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'title' => 'Pelaksanaan kegiatan dan penyusunan LPJ',
                'description' => 'Setelah kegiatan terlaksana, ORMAWA mengunggah laporan pertanggungjawaban, bukti transaksi, dan dokumentasi kegiatan.',
                'sort_order' => 5,
                'is_published' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_process_steps');
    }
};
