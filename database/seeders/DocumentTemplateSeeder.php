<?php

namespace Database\Seeders;

use App\Models\DocumentTemplate;
use App\Models\Period;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        Storage::disk('public')->makeDirectory('document-templates');
        $admin = User::first();
        $period = Period::where('status', 'active')->first();

        // 1. Template Proposal Default
        $proposalPath = 'document-templates/template_proposal_default.docx';
        $fullProposalPath = Storage::disk('public')->path($proposalPath);

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Header / Kop
        $section->addText('NOTA DINAS PENGAJUAN KEGIATAN MAHASISWA', ['bold' => true, 'size' => 14], ['alignment' => Jc::CENTER]);
        $section->addText('Nomor: ${nomor_nota}', ['size' => 11], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(1);

        // Metadata
        $section->addText('Organisasi: ${ormawa_name}', ['bold' => true]);
        $section->addText('Program Studi: ${prodi_name}');
        $section->addText('Nama Kegiatan: ${activity_name}', ['bold' => true]);
        $section->addText('Tema: ${activity_theme}');
        $section->addText('Tanggal Kegiatan: ${execution_day}, ${execution_date} (${execution_time})');
        $section->addText('Lokasi: ${execution_location}');
        $section->addText('Sasaran: ${target_participants}');
        $section->addText('Total Anggaran: Rp ${total_amount} (${total_amount_words})');
        $section->addTextBreak(1);

        // Latar Belakang & Tujuan
        $section->addText('A. Latar Belakang', ['bold' => true, 'size' => 12]);
        $section->addText('${background}');
        $section->addTextBreak(1);

        $section->addText('B. Tujuan Kegiatan', ['bold' => true, 'size' => 12]);
        $section->addText('${objectives}');
        $section->addTextBreak(1);

        $section->addText('C. Manfaat Kegiatan', ['bold' => true, 'size' => 12]);
        $section->addText('${benefits}');
        $section->addTextBreak(1);

        // Timeline
        $section->addText('D. Jadwal dan Tahapan Kegiatan', ['bold' => true, 'size' => 12]);
        $section->addText('1. Perencanaan: ${plan_start} s/d ${plan_end}');
        $section->addText('${plan_context}');
        $section->addText('2. Persiapan: ${prepare_start} s/d ${prepare_end}');
        $section->addText('${prepare_context}');
        $section->addText('3. Pelaksanaan: ${action_start} s/d ${action_end}');
        $section->addText('${action_context}');
        $section->addText('4. Penutupan: ${closing_start} s/d ${closing_end}');
        $section->addText('${closing_context}');
        $section->addTextBreak(1);

        // Susunan Panitia
        $section->addText('E. Susunan Kepanitiaan', ['bold' => true, 'size' => 12]);
        $panitiaTable = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
        $panitiaTable->addRow();
        $panitiaTable->addCell(3000)->addText('Nama', ['bold' => true]);
        $panitiaTable->addCell(3000)->addText('Jabatan Struktural', ['bold' => true]);
        $panitiaTable->addCell(3000)->addText('Tugas Panitia', ['bold' => true]);
        $panitiaTable->addRow();
        $panitiaTable->addCell(3000)->addText('${committee_name}');
        $panitiaTable->addCell(3000)->addText('${structural_role}');
        $panitiaTable->addCell(3000)->addText('${committee_role}');
        $section->addTextBreak(1);

        // Rundown Acara
        $section->addText('F. Rundown Acara', ['bold' => true, 'size' => 12]);
        $rundownTable = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
        $rundownTable->addRow();
        $rundownTable->addCell(2000)->addText('Hari/Tanggal', ['bold' => true]);
        $rundownTable->addCell(2000)->addText('Waktu', ['bold' => true]);
        $rundownTable->addCell(3500)->addText('Agenda Kegiatan', ['bold' => true]);
        $rundownTable->addCell(1500)->addText('PIC', ['bold' => true]);
        $rundownTable->addRow();
        $rundownTable->addCell(2000)->addText('${day_date}');
        $rundownTable->addCell(2000)->addText('${time_slot}');
        $rundownTable->addCell(3500)->addText('${event_name}');
        $rundownTable->addCell(1500)->addText('${pic}');
        $section->addTextBreak(1);

        // RAB Anggaran
        $section->addText('G. Rencana Anggaran Biaya (RAB)', ['bold' => true, 'size' => 12]);
        $rabTable = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
        $rabTable->addRow();
        $rabTable->addCell(800)->addText('No', ['bold' => true]);
        $rabTable->addCell(4000)->addText('Nama Kebutuhan', ['bold' => true]);
        $rabTable->addCell(1500)->addText('Vol', ['bold' => true]);
        $rabTable->addCell(2000)->addText('Harga Satuan', ['bold' => true]);
        $rabTable->addCell(2000)->addText('Total', ['bold' => true]);
        $rabTable->addRow();
        $rabTable->addCell(800)->addText('${no}');
        $rabTable->addCell(4000)->addText('${item_name}');
        $rabTable->addCell(1500)->addText('${qty}');
        $rabTable->addCell(2000)->addText('${price}');
        $rabTable->addCell(2000)->addText('${subtotal}');
        $section->addTextBreak(1);

        // Pengesahan / Tanda Tangan
        $section->addText('H. Lembar Pengesahan', ['bold' => true, 'size' => 12]);
        $section->addText('Ditetapkan di: ${execution_location}, Tanggal: ${date}');
        $ttdTable = $section->addTable();
        $ttdTable->addRow();
        $ttdTable->addCell(4500)->addText('Ketua Pelaksana,'."\n\n\n".'${chairman_name}'."\nNIM: ".'${chairman_nim}');
        $ttdTable->addCell(4500)->addText('Ketua Ormawa,'."\n\n\n".'${ormawa_leader_name}'."\nNIM: ".'${ormawa_leader_nim}');
        $ttdTable->addRow();
        $ttdTable->addCell(4500)->addText('Pembina Ormawa,'."\n".'${signature_pembina_ormawa}'."\n\n".'${pembina_name}'."\nNUPTK: ".'${pembina_nuptk}');
        $ttdTable->addCell(4500)->addText('Kaprodi,'."\n".'${signature_kaprodi}'."\n\n".'${kaprodi_name}'."\nNUPTK: ".'${kaprodi_nuptk}');
        $ttdTable->addRow();
        $ttdTable->addCell(4500)->addText('Kabag Kemahasiswaan,'."\n".'${signature_kabag_kemahasiswaan}'."\n\n".'${kabag_name}'."\nNUPTK: ".'${kabag_nuptk}');
        $ttdTable->addCell(4500)->addText('Wakil Rektor III,'."\n".'${signature_warek}'."\n\n".'${warek_name}'."\nNUPTK: ".'${warek_nuptk}');

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($fullProposalPath);

        DocumentTemplate::updateOrCreate(
            ['name' => 'Template Proposal Standar (Default)'],
            [
                'type' => 'proposal',
                'ormawa_category' => null,
                'file_name' => 'template_proposal_default.docx',
                'file_path' => $proposalPath,
                'period_id' => $period?->id,
                'created_by' => $admin?->id,
                'version' => 1,
                'is_active' => false,
            ]
        );

        // 2. Template LPJ Default
        $lpjPath = 'document-templates/template_lpj_default.docx';
        $fullLpjPath = Storage::disk('public')->path($lpjPath);

        $phpWordLpj = new PhpWord();
        $lpjSection = $phpWordLpj->addSection();

        $lpjSection->addText('LAPORAN PERTANGGUNGJAWABAN (LPJ) KEGIATAN', ['bold' => true, 'size' => 14], ['alignment' => Jc::CENTER]);
        $lpjSection->addText('Organisasi: ${ormawa_name}', ['bold' => true], ['alignment' => Jc::CENTER]);
        $lpjSection->addTextBreak(1);

        $lpjSection->addText('Nama Kegiatan: ${activity_name}', ['bold' => true]);
        $lpjSection->addText('Tanggal Terlaksana: ${execution_day}, ${execution_date}');
        $lpjSection->addText('Lokasi: ${execution_location}');
        $lpjSection->addText('Realisasi Anggaran: Rp ${total_amount} (${total_amount_words})');
        $lpjSection->addTextBreak(1);

        $lpjSection->addText('A. Ringkasan Ketercapaian Kegiatan', ['bold' => true, 'size' => 12]);
        $lpjSection->addText('${background}');
        $lpjSection->addTextBreak(1);

        $lpjSection->addText('B. Realisasi Anggaran Pengeluaran', ['bold' => true, 'size' => 12]);
        $lpjRabTable = $lpjSection->addTable(['borderSize' => 6, 'borderColor' => '999999']);
        $lpjRabTable->addRow();
        $lpjRabTable->addCell(800)->addText('No', ['bold' => true]);
        $lpjRabTable->addCell(4000)->addText('Uraian Pengeluaran', ['bold' => true]);
        $lpjRabTable->addCell(1500)->addText('Vol', ['bold' => true]);
        $lpjRabTable->addCell(2000)->addText('Harga Satuan', ['bold' => true]);
        $lpjRabTable->addCell(2000)->addText('Total', ['bold' => true]);
        $lpjRabTable->addRow();
        $lpjRabTable->addCell(800)->addText('${no}');
        $lpjRabTable->addCell(4000)->addText('${item_name}');
        $lpjRabTable->addCell(1500)->addText('${qty}');
        $lpjRabTable->addCell(2000)->addText('${price}');
        $lpjRabTable->addCell(2000)->addText('${subtotal}');
        $lpjSection->addTextBreak(1);

        $lpjSection->addText('C. Lembar Pengesahan LPJ', ['bold' => true, 'size' => 12]);
        $lpjTtd = $lpjSection->addTable();
        $lpjTtd->addRow();
        $lpjTtd->addCell(4500)->addText('Ketua Pelaksana,'."\n\n\n".'${chairman_name}');
        $lpjTtd->addCell(4500)->addText('Ketua Ormawa,'."\n\n\n".'${ormawa_leader_name}');
        $lpjTtd->addRow();
        $lpjTtd->addCell(4500)->addText('Pembina Ormawa,'."\n".'${signature_pembina_ormawa}'."\n\n".'${pembina_name}');
        $lpjTtd->addCell(4500)->addText('Kaprodi,'."\n".'${signature_kaprodi}'."\n\n".'${kaprodi_name}');
        $lpjTtd->addRow();
        $lpjTtd->addCell(4500)->addText('Kabag Kemahasiswaan,'."\n".'${signature_kabag_kemahasiswaan}'."\n\n".'${kabag_name}');
        $lpjTtd->addCell(4500)->addText('Wakil Rektor III,'."\n".'${signature_warek}'."\n\n".'${warek_name}');

        $objWriterLpj = IOFactory::createWriter($phpWordLpj, 'Word2007');
        $objWriterLpj->save($fullLpjPath);

        DocumentTemplate::updateOrCreate(
            ['name' => 'Template LPJ Standar (Default)'],
            [
                'type' => 'lpj',
                'ormawa_category' => null,
                'file_name' => 'template_lpj_default.docx',
                'file_path' => $lpjPath,
                'period_id' => $period?->id,
                'created_by' => $admin?->id,
                'version' => 1,
                'is_active' => false,
            ]
        );

        // 3. Register Official Campus Templates sesuai spesifikasi:
        // - BEM & DPM:
        //     FORMAT LPJ REALISASI KEGIATAN BEM
        //     FORMAT NOTA DINAS PENGAJUAN DANA KEGIATAN BEM (sebagai nota dinas pengajuan dana)
        //     FORMAT PROPOSAL KEGIATAN BEM (proposal kegiatan)
        // - HIMA keseluruhan:
        //     FORMAT LPJ REALISASI KEGIATAN ORMAWA
        //     FORMAT NOTA DINAS & PROPOSAL PENGAJUAN DANA KEGIATAN ORMAWA (sebagai pengajuan dana / proposal)
        $officialTemplates = [
            [
                'name' => 'FORMAT NOTA DINAS & PROPOSAL PENGAJUAN DANA KEGIATAN ORMAWA',
                'type' => 'proposal',
                'ormawa_category' => 'hima',
                'file_name' => 'FORMAT NOTA DINAS & PROPOSAL PENGAJUAN DANA KEGIATAN ORMAWA.docx',
                'version' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'FORMAT PROPOSAL KEGIATAN BEM',
                'type' => 'proposal',
                'ormawa_category' => 'bem',
                'file_name' => 'FORMAT PROPOSAL KEGIATAN BEM.docx',
                'version' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'FORMAT NOTA DINAS PENGAJUAN DANA KEGIATAN BEM',
                'type' => 'proposal',
                'ormawa_category' => 'bem',
                'file_name' => 'FORMAT NOTA DINAS PENGAJUAN DANA KEGIATAN BEM.docx',
                'version' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'FORMAT LPJ REALISASI KEGIATAN ORMAWA',
                'type' => 'lpj',
                'ormawa_category' => 'hima',
                'file_name' => 'FORMAT LPJ REALISASI KEGIATAN ORMAWA.docx',
                'version' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'FORMAT LPJ REALISASI KEGIATAN BEM',
                'type' => 'lpj',
                'ormawa_category' => 'bem',
                'file_name' => 'FORMAT LPJ REALISASI KEGIATAN BEM.docx',
                'version' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'FORMAT PROPOSAL PENGAJUAN DANA KEGIATAN ORMAWA',
                'type' => 'proposal',
                'ormawa_category' => 'hima',
                'file_name' => 'FORMAT PROPOSAL PENGAJUAN DANA KEGIATAN ORMAWA.docx',
                'version' => 1,
                'is_active' => false,
            ],
            [
                'name' => 'FORMAT NOTA DINAS PENGAJUAN DANA KEGIATAN ORMAWA',
                'type' => 'proposal',
                'ormawa_category' => 'hima',
                'file_name' => 'FORMAT NOTA DINAS PENGAJUAN DANA KEGIATAN ORMAWA.docx',
                'version' => 1,
                'is_active' => false,
            ],
            [
                'name' => 'Template Peminjaman Ruangan HIMA Dan UKM',
                'type' => 'proposal',
                'ormawa_category' => 'hima',
                'file_name' => 'Template Peminjaman Ruangan HIMA Dan UKM.docx',
                'version' => 1,
                'is_active' => false,
            ],
        ];

        foreach ($officialTemplates as $tpl) {
            $tplPath = 'document-templates/'.$tpl['file_name'];
            if (Storage::disk('public')->exists($tplPath)) {
                DocumentTemplate::updateOrCreate(
                    ['file_name' => $tpl['file_name']],
                    [
                        'name' => $tpl['name'],
                        'type' => $tpl['type'],
                        'ormawa_category' => $tpl['ormawa_category'] ?? null,
                        'file_path' => $tplPath,
                        'period_id' => $period?->id,
                        'created_by' => $admin?->id,
                        'version' => $tpl['version'] ?? 1,
                        'is_active' => $tpl['is_active'] ?? true,
                    ]
                );
            }
        }
    }
}
