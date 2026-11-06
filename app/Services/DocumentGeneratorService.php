<?php

namespace App\Services;

use App\Models\ApprovalStep;
use App\Models\DocumentTemplate;
use App\Models\Lpj;
use App\Models\LpjApprovalStep;
use App\Models\OrganizationMember;
use App\Models\Ormawa;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;

class DocumentGeneratorService
{
    // -----------------------------------------------------------------------
    // Mapping signature placeholders per template kategori:
    // hima/ukm memakai: signature_pembina_ormawa, signature_kaprodi,
    //                   signature_kabag_kemahasiswaan, signature_warek, signature_ketua_ormawa
    // bem/dpm  memakai: signature_presma, signature_ketua_pelaksana,
    //                   signature_kabag_kemahasiswaan, signature_warek
    // -----------------------------------------------------------------------

    public function __construct()
    {
        $tempDir = storage_path('app/temp');
        if (! is_dir($tempDir)) {
            @mkdir($tempDir, 0777, true);
        }
        \PhpOffice\PhpWord\Settings::setTempDir(realpath($tempDir) ?: $tempDir);
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
    }

    public function generateProposal(Submission $submission, ?string $preferredFileName = null): string
    {
        return $this->generateDocument($submission, 'proposal', null, $preferredFileName);
    }

    public function generateNotaDinas(Submission $submission): string
    {
        $ormawa = $submission->ormawa;
        $isBem = $ormawa && ($ormawa->isBem() || $ormawa->isDpm());
        $preferredFileName = $isBem
            ? 'FORMAT NOTA DINAS PENGAJUAN DANA KEGIATAN BEM.docx'
            : 'FORMAT NOTA DINAS & PROPOSAL PENGAJUAN DANA KEGIATAN ORMAWA.docx';

        return $this->generateDocument($submission, 'proposal', null, $preferredFileName);
    }

    public function generateLpj(Lpj $lpj): string
    {
        $lpj->loadMissing(['items', 'submission', 'documents']);

        $submission = $lpj->submission;

        if (! $submission) {
            throw new RuntimeException('Proposal untuk LPJ tidak ditemukan.');
        }

        if ($lpj->items->isEmpty()) {
            $submission->loadMissing('items');
            $lpj->setRelation('items', $submission->items);
        }

        return $this->generateDocument(
            $submission,
            'lpj',
            $lpj->items,
            null,
            $lpj
        );
    }

    public function generateSignedProposal(Submission $submission): string
    {
        if ($submission->status !== 'approved') {
            throw new RuntimeException('Proposal belum mendapatkan seluruh persetujuan.');
        }

        $proposalSource = $submission->documents()
            ->where('type', 'proposal')
            ->latest('id')
            ->first();

        if ($proposalSource) {
            if (! Storage::disk('local')->exists($proposalSource->file_path)) {
                throw new RuntimeException('File proposal yang diunggah tidak ditemukan.');
            }

            $sourcePath = Storage::disk('local')->path($proposalSource->file_path);
        } else {
            $generatedPath = $this->generateProposal($submission);
            $sourcePath = Storage::disk('public')->path($generatedPath);
        }

        return $this->applyApprovalSignatures(
            $sourcePath,
            $submission->approvalSteps()->with('role')->get(),
            'Proposal_'.Str::slug($submission->activity_name),
            $submission->ormawa,
            $submission
        );
    }

    public function generateSignedLpj(Lpj $lpj): string
    {
        if ($lpj->status !== 'approved') {
            throw new RuntimeException('LPJ belum mendapatkan seluruh persetujuan.');
        }

        $generatedPath = $this->generateLpj($lpj);

        return $this->applyApprovalSignatures(
            Storage::disk('public')->path($generatedPath),
            $lpj->approvalSteps()->with('role')->get(),
            'LPJ_'.Str::slug($lpj->title),
            $lpj->submission?->ormawa,
            $lpj
        );
    }

    /**
     * @param  iterable<int, ApprovalStep|LpjApprovalStep>  $steps
     */
    protected function applyApprovalSignatures(string $sourcePath, iterable $steps, string $filename, $ormawa = null, $context = null): string
    {
        $signatures = [];
        foreach ($steps as $step) {
            $roleName = $step->role?->name;
            if ($step->status !== 'approved' || ! $roleName || ! $step->signature_path) {
                throw new RuntimeException('Belum semua tahap persetujuan memiliki tanda tangan tersimpan.');
            }
            if (! Storage::disk('local')->exists($step->signature_path)) {
                throw new RuntimeException('File tanda tangan salah satu pemberi persetujuan tidak ditemukan.');
            }

            $signatures['signature_'.$roleName] = Storage::disk('local')->path($step->signature_path);
        }

        if (count($signatures) < 3) {
            throw new RuntimeException('Alur persetujuan belum lengkap untuk membuat dokumen final.');
        }

        $processor = $this->createTemplateProcessor($sourcePath);
        $variables = $processor->getVariables();

        // Inject tanda tangan approver
        foreach ($signatures as $placeholder => $imagePath) {
            if (collect($variables)->contains(fn (string $variable): bool => $variable === $placeholder || str_starts_with($variable, $placeholder.':'))) {
                $processor->setImageValue($placeholder, [
                    'path'   => $imagePath,
                    'width'  => 195,
                    'height' => 85,
                    'ratio'  => true,
                ]);
            }
        }

        // Inject tanda tangan ketua pelaksana jika tersimpan di submission atau profil submitter
        $chairmanSigPath = null;
        if ($context instanceof Submission && $context->chairman_signature_path) {
            $chairmanSigPath = $this->resolveSignatureFilePath($context->chairman_signature_path);
        } elseif ($context instanceof Lpj && $context->submission?->chairman_signature_path) {
            $chairmanSigPath = $this->resolveSignatureFilePath($context->submission->chairman_signature_path);
        }

        $submitterUser = null;
        if ($context instanceof Submission) {
            $submitterUser = $context->submitter;
        } elseif ($context instanceof Lpj) {
            $submitterUser = $context->creator;
        }

        $finalLeaderSigPath = $chairmanSigPath;
        if (! $finalLeaderSigPath && $submitterUser?->signature_path && Storage::disk('local')->exists($submitterUser->signature_path)) {
            $finalLeaderSigPath = Storage::disk('local')->path($submitterUser->signature_path);
        }

        if ($finalLeaderSigPath && file_exists($finalLeaderSigPath)) {
            foreach (['signature_ketua_pelaksana', 'signature_ketua_ormawa', 'signature_presma'] as $userPlaceholder) {
                if (in_array($userPlaceholder, $variables, true) && ! isset($signatures[$userPlaceholder])) {
                    $processor->setImageValue($userPlaceholder, [
                        'path'   => $finalLeaderSigPath,
                        'width'  => 195,
                        'height' => 85,
                        'ratio'  => true,
                    ]);
                    $signatures[$userPlaceholder] = $finalLeaderSigPath;
                }
            }
        }

        // Tambahkan alias: jika template BEM menggunakan signature_presma/signature_ketua_pelaksana
        // dari role pembina_ormawa/sekretaris_ormawa, petakan secara dinamis
        $isBem = $ormawa && ($ormawa->isBem() || $ormawa->isDpm());
        if ($isBem) {
            // BEM: presma = pemimpin ormawa (pembina_ormawa step) -> jika ada
            foreach ($variables as $variable) {
                if ($variable === 'signature_presma' && isset($signatures['signature_pembina_ormawa'])) {
                    $processor->setImageValue('signature_presma', [
                        'path'   => $signatures['signature_pembina_ormawa'],
                        'width'  => 195,
                        'height' => 85,
                        'ratio'  => true,
                    ]);
                }
                if ($variable === 'signature_ketua_pelaksana' && isset($signatures['signature_kaprodi'])) {
                    $processor->setImageValue('signature_ketua_pelaksana', [
                        'path'   => $signatures['signature_kaprodi'],
                        'width'  => 195,
                        'height' => 85,
                        'ratio'  => true,
                    ]);
                }
            }
        }

        // Bersihkan placeholder tanda tangan yang tersisa dan tidak terisi (misal signature_kaprodi pada UKM/BEM)
        foreach ($variables as $variable) {
            if (str_starts_with($variable, 'signature_') && ! isset($signatures[$variable])) {
                $processor->setValue($variable, '');
            }
        }

        $directory = 'generated_documents/signed';
        Storage::disk('local')->makeDirectory($directory);
        $path = $directory.'/'.$filename.'_'.Str::uuid().'.docx';
        $fullPath = Storage::disk('local')->path($path);
        $processor->saveAs($fullPath);
        $this->ensureDocxImagesCentered($fullPath);

        return $path;
    }

    protected function generateDocument(
        Submission $submission,
        string $type,
        ?Collection $budgetItems = null,
        ?string $preferredFileName = null,
        ?Lpj $lpj = null
    ): string {
        $submission->load([
            'period',
            'ormawa',
            'submitter.programStudi',
            'items',
            'timelines',
            'committees',
            'rundowns',
            'approvalSteps.role',
        ]);

        if ($lpj) {
            $lpj->loadMissing('approvalSteps.role');
        }

        $ormawa = $submission->ormawa;
        $isBem  = $ormawa && ($ormawa->isBem() || $ormawa->isDpm());

        // Tentukan kategori ormawa untuk pemilihan template (BEM & DPM = 'bem', HIMA & lainnya = 'hima')
        $ormawaCategory = null;
        if ($ormawa) {
            $ormawaCategory = $isBem ? 'bem' : 'hima';
        }

        // Cari template yang paling tepat sesuai spesifikasi nama dokumen:
        // - BEM & DPM:
        //     Proposal -> FORMAT PROPOSAL KEGIATAN BEM (default) atau FORMAT NOTA DINAS PENGAJUAN DANA KEGIATAN BEM
        //     LPJ      -> FORMAT LPJ REALISASI KEGIATAN BEM
        // - HIMA keseluruhan:
        //     Proposal -> FORMAT NOTA DINAS & PROPOSAL PENGAJUAN DANA KEGIATAN ORMAWA (sebagai pengajuan dana / proposal)
        //     LPJ      -> FORMAT LPJ REALISASI KEGIATAN ORMAWA
        $query = DocumentTemplate::query()
            ->where('type', $type)
            ->where('is_active', true)
            ->where(fn ($q) => $q
                ->whereNull('period_id')
                ->orWhere('period_id', $submission->period_id))
            ->where(fn ($q) => $q
                ->whereNull('ormawa_category')
                ->orWhere('ormawa_category', $ormawaCategory));

        if ($preferredFileName) {
            $query->orderByRaw('CASE WHEN file_name = ? OR name = ? THEN 0 ELSE 1 END', [$preferredFileName, $preferredFileName]);
        } else {
            if ($ormawaCategory === 'bem') {
                if ($type === 'proposal') {
                    $query->orderByRaw("CASE WHEN file_name LIKE '%PROPOSAL KEGIATAN BEM%' THEN 0 ELSE 1 END");
                } else {
                    $query->orderByRaw("CASE WHEN file_name LIKE '%LPJ REALISASI KEGIATAN BEM%' THEN 0 ELSE 1 END");
                }
            } else {
                if ($type === 'proposal') {
                    $query->orderByRaw("CASE WHEN file_name LIKE '%NOTA DINAS & PROPOSAL%' THEN 0 ELSE 1 END");
                } else {
                    $query->orderByRaw("CASE WHEN file_name LIKE '%LPJ REALISASI KEGIATAN ORMAWA%' THEN 0 ELSE 1 END");
                }
            }
        }

        $template = $query
            ->orderByRaw('CASE WHEN period_id = ? THEN 0 ELSE 1 END', [$submission->period_id])
            ->orderByRaw('CASE WHEN ormawa_category = ? THEN 0 ELSE 1 END', [$ormawaCategory])
            ->orderByDesc('version')
            ->first();

        if (! $template) {
            throw new RuntimeException(
                $type === 'lpj'
                    ? 'Template LPJ aktif belum tersedia. Minta admin mengunggah template LPJ .docx.'
                    : 'Template aktif proposal yang berisi Nota Dinas belum tersedia.'
            );
        }

        $templatePath = Storage::disk('public')->path($template->file_path);
        if (! Storage::disk('public')->exists($template->file_path)) {
            throw new RuntimeException(
                $type === 'lpj'
                    ? 'File template LPJ tidak ditemukan.'
                    : 'File template proposal tidak ditemukan.'
            );
        }

        $processor = $this->createTemplateProcessor($templatePath);
        $budgetItems ??= $submission->items;
        $totalAmount = $type === 'lpj'
            ? $budgetItems->sum(fn ($item): float => (float) $item->total_price)
            : (float) $submission->total_amount;

        /* 1. DATA UTAMA */
        $executionTime = ($submission->event_start_time ? $submission->event_start_time->format('H:i') : '08:00')
            .' - '
            .($submission->event_end_time ? $submission->event_end_time->format('H:i') : 'Selesai').' WIB';

        $ormawaRawName = $submission->ormawa?->name ?? '-';
        $ormawaUpperName = mb_strtoupper($ormawaRawName);

        $processor->setValues([
            'nomor_nota'         => $submission->nota_number ?? '-',
            'activity_name'      => $submission->activity_name ?? '-',
            'activity_theme'     => $submission->activity_theme ?? '-',
            'divisi_name'        => $submission->divisi_name ?? '-',
            'ormawa_name'        => $ormawaUpperName,
            'ormawa_name_upper'  => $ormawaUpperName,
            'ORMAWA_NAME'        => $ormawaUpperName,
            'prodi_name'         => ($ormawa && ($ormawa->isUkm() || $ormawa->isBem() || $ormawa->isDpm())) ? 'Unit Kegiatan Mahasiswa' : ($submission->submitter?->programStudi?->name ?? $ormawa?->programStudi?->name ?? '-'),
            'period_name'        => $submission->period?->name ?? '-',
            'year'               => $submission->period?->start_date ? $submission->period->start_date->format('Y') : now()->format('Y'),
            'date'               => now()->translatedFormat('d F Y'),

            // Dasar Nota Dinas
            'meeting_date'       => $submission->meeting_date ? $submission->meeting_date->translatedFormat('d F Y') : '-',
            'circular_letter'    => $submission->circular_letter ?? '-',
            'budget_code'        => $submission->budget_code ?? '-',

            // Detail Proposal
            'background'         => $submission->background ?? '-',
            'target_participants' => $submission->target_participants ?? '-',

            // Waktu & Tempat
            'execution_day'      => $submission->event_day ?? '-',
            'execution_date'     => $submission->event_date ? $submission->event_date->translatedFormat('d F Y') : '-',
            'execution_time'     => $executionTime,
            'execution_location' => $submission->event_location ?? '-',

            // Anggaran
            'total_amount'       => number_format($totalAmount, 0, ',', '.'),
            'total_amount_words' => $this->terbilang($totalAmount).' Rupiah',
        ]);

        $this->replaceWithNumberedList($processor, 'objectives', $submission->objectives);
        $this->replaceWithNumberedList($processor, 'benefits', $submission->benefits);

        /* 1.1 LOGO ORMAWA COVER */
        $this->injectOrmawaLogo($processor, $ormawa);

        /* 2. TIMELINE 4 TAHAP */
        $timelines = $submission->timelines;
        $plan    = $timelines->where('stage', 'perencanaan')->first();
        $prepare = $timelines->where('stage', 'persiapan')->first();
        $action  = $timelines->where('stage', 'pelaksanaan')->first();
        $closing = $timelines->where('stage', 'penutupan')->first();

        $processor->setValues([
            'plan_start'    => $plan?->start_date ? $plan->start_date->format('d/m/Y') : '-',
            'plan_end'      => $plan?->end_date ? $plan->end_date->format('d/m/Y') : '-',
            'prepare_start' => $prepare?->start_date ? $prepare->start_date->format('d/m/Y') : '-',
            'prepare_end'   => $prepare?->end_date ? $prepare->end_date->format('d/m/Y') : '-',
            'action_start'  => $action?->start_date ? $action->start_date->format('d/m/Y') : '-',
            'action_end'    => $action?->end_date ? $action->end_date->format('d/m/Y') : '-',
            'closing_start' => $closing?->start_date ? $closing->start_date->format('d/m/Y') : '-',
            'closing_end'   => $closing?->end_date ? $closing->end_date->format('d/m/Y') : '-',
        ]);

        $this->replaceWithTimelineContext($processor, 'plan_context', $plan?->description);
        $this->replaceWithTimelineContext($processor, 'prepare_context', $prepare?->description);
        $this->replaceWithTimelineContext($processor, 'action_context', $action?->description);
        $this->replaceWithTimelineContext($processor, 'closing_context', $closing?->description);

        /* 3. TABEL DINAMIS RAB */
        if ($budgetItems->isNotEmpty()) {
            $rabRows  = [];
            $rab2Rows = [];
            foreach ($budgetItems as $index => $item) {
                $qtyDisplay = $this->formatQuantity($item->qty, $item->unit);

                $rabRows[] = [
                    'no'       => $index + 1,
                    'item_name' => $item->item_name ?? '-',
                    'qty'      => $qtyDisplay,
                    'price'    => 'Rp '.number_format((float) $item->price, 0, ',', '.'),
                    'subtotal' => 'Rp '.number_format((float) $item->total_price, 0, ',', '.'),
                ];
                $rab2Rows[] = [
                    'rab2_no'        => $index + 1,
                    'rab2_item_name' => $item->item_name ?? '-',
                    'rab2_qty'       => $qtyDisplay,
                    'rab2_price'     => 'Rp '.number_format((float) $item->price, 0, ',', '.'),
                    'rab2_subtotal'  => 'Rp '.number_format((float) $item->total_price, 0, ',', '.'),
                ];
            }

            $this->safeCloneRowAndSetValues($processor, 'item_name',      $rabRows);
            $this->safeCloneRowAndSetValues($processor, 'rab2_item_name', $rab2Rows);
        }

        /* 4. TABEL DINAMIS RUNDOWN */
        if ($submission->rundowns->count() > 0) {
            $rundownRows = [];
            foreach ($submission->rundowns as $index => $rundown) {
                $rundownRows[] = [
                    'rundown_no' => $index + 1,
                    'no'         => $index + 1,
                    'urut'       => $index + 1,
                    'day_date'   => $rundown->day_date ?? 'Hari Pertama',
                    'time_slot'  => $rundown->time_slot ?? '-',
                    'event_name' => $rundown->event_name ?? '-',
                    'pic'        => $rundown->pic ?? '-',
                    'note'       => $rundown->note ?? '-',
                ];
            }
            $this->safeCloneRowAndSetValues($processor, 'event_name', $rundownRows);
        }

        // Pastikan tidak ada residual ${day_date} yang tersisa di template manapun
        try {
            $processor->setValue('day_date', '');
        } catch (\Throwable $e) {
        }

        /* 5. TABEL DINAMIS PANITIA */
        if ($submission->committees->count() > 0) {
            $isBemProposal = $isBem && $type === 'proposal';
            $committeeStartNo = $isBemProposal ? 4 : 6;

            $committeeRows = [];
            foreach ($submission->committees as $index => $committee) {
                $committeeNo = $committeeStartNo + $index;
                $committeeRows[] = [
                    'committee_no'    => $committeeNo,
                    'no'              => $committeeNo,
                    'urut'            => $committeeNo,
                    'committee_name'  => $committee->name ?? '-',
                    'structural_role' => $committee->structural_role ?? 'Mahasiswa',
                    'committee_role'  => $committee->committee_role ?? '-',
                    'description'     => $committee->description ?? '-',
                ];
            }
            $this->safeCloneRowAndSetValues($processor, 'committee_name', $committeeRows);
        }

        // Pastikan tidak ada residual ${no} yang tertinggal di dokumen
        try {
            $processor->setValue('no', '');
        } catch (\Throwable $e) {
        }

        /* 6. PEJABAT & PENANDATANGAN — sesuai kategori ormawa */
        $this->injectOfficialSignatures($processor, $submission, $lpj);

        /* 7. PLACEHOLDER LPJ KHUSUS (foto/kwitansi) — isi dengan teks kosong jika tidak ada */
        $variables = $processor->getVariables();

        /* SCAN NOTA KEGIATAN LPJ */
        if ($type === 'lpj' && $lpj) {

            $receipts = $lpj->documents()
                ->where('type', 'nota')
                ->get()
                ->filter(function ($document) {

                    $extension = strtolower(
                        pathinfo($document->file_name, PATHINFO_EXTENSION)
                    );

                    return in_array(
                        $extension,
                        ['jpg', 'jpeg', 'png'],
                        true
                    ) && Storage::disk('local')->exists($document->file_path);
                })
                ->values();

            // Kelompokkan dua nota dalam satu baris
            $receiptRows = $receipts->chunk(2);

            if ($receiptRows->isNotEmpty()) {

                // Clone baris tabel berdasarkan jumlah kelompok nota
                $processor->cloneRow(
                    'receipt_scan_left',
                    $receiptRows->count()
                );

                foreach ($receiptRows as $index => $row) {

                    $number = $index + 1;

                    $left = $row->get(0);
                    $right = $row->get(1);

                    // NOTA KIRI
                    if ($left) {

                        $processor->setImageValue(
                            "receipt_scan_left#{$number}",
                            [
                                'path' => Storage::disk('local')->path(
                                    $left->file_path
                                ),
                                'width' => 220,
                                'height' => 165,
                                'ratio' => true,
                            ]
                        );

                        $processor->setValue(
                            "receipt_amount_left#{$number}",
                            '-'
                        );

                        $processor->setValue(
                            "receipt_caption_left#{$number}",
                            $left->file_name
                        );
                    } else {

                        $processor->setValue(
                            "receipt_scan_left#{$number}",
                            ''
                        );

                        $processor->setValue(
                            "receipt_amount_left#{$number}",
                            ''
                        );

                        $processor->setValue(
                            "receipt_caption_left#{$number}",
                            ''
                        );
                    }

                    // NOTA KANAN
                    if ($right) {

                        $processor->setImageValue(
                            "receipt_scan_right#{$number}",
                            [
                                'path' => Storage::disk('local')->path(
                                    $right->file_path
                                ),
                                'width' => 220,
                                'height' => 165,
                                'ratio' => true,
                            ]
                        );

                        $processor->setValue(
                            "receipt_amount_right#{$number}",
                            '-'
                        );

                        $processor->setValue(
                            "receipt_caption_right#{$number}",
                            $right->file_name
                        );
                    } else {

                        $processor->setValue(
                            "receipt_scan_right#{$number}",
                            ''
                        );

                        $processor->setValue(
                            "receipt_amount_right#{$number}",
                            ''
                        );

                        $processor->setValue(
                            "receipt_caption_right#{$number}",
                            ''
                        );
                    }
                }
            } else {

                // Jika tidak ada nota gambar
                $processor->setValue('receipt_scan_left', '');
                $processor->setValue('receipt_scan_right', '');

                $processor->setValue('receipt_amount_left', '');
                $processor->setValue('receipt_amount_right', '');

                $processor->setValue('receipt_caption_left', '');
                $processor->setValue('receipt_caption_right', '');
            }
        }

        if ($type === 'lpj' && $lpj) {

            $documentation = $lpj->documents()
                ->where('type', 'dokumentasi')
                ->get()
                ->filter(function ($document) {
                    $extension = strtolower(
                        pathinfo($document->file_name, PATHINFO_EXTENSION)
                    );

                    return in_array($extension, ['jpg', 'jpeg', 'png'], true)
                        && Storage::disk('local')->exists($document->file_path);
                })
                ->values();

            // Kelompokkan dua foto untuk setiap baris
            $rows = $documentation->chunk(2);

            if ($rows->isNotEmpty()) {

                $processor->cloneRow(
                    'documentation_left',
                    $rows->count()
                );

                foreach ($rows as $index => $images) {

                    $rowNumber = $index + 1;

                    $left = $images->get(0);
                    $right = $images->get(1);

                    // Foto kiri
                    if ($left) {
                        $processor->setImageValue(
                            "documentation_left#{$rowNumber}",
                            [
                                'path' => Storage::disk('local')->path(
                                    $left->file_path
                                ),
                                'width' => 220,
                                'height' => 165,
                                'ratio' => true,
                            ]
                        );
                    } else {
                        $processor->setValue(
                            "documentation_left#{$rowNumber}",
                            ''
                        );
                    }

                    // Foto kanan
                    if ($right) {
                        $processor->setImageValue(
                            "documentation_right#{$rowNumber}",
                            [
                                'path' => Storage::disk('local')->path(
                                    $right->file_path
                                ),
                                'width' => 220,
                                'height' => 165,
                                'ratio' => true,
                            ]
                        );
                    } else {
                        $processor->setValue(
                            "documentation_right#{$rowNumber}",
                            ''
                        );
                    }
                }
            } else {
                $processor->setValue('documentation_left', '');
                $processor->setValue('documentation_right', '');
            }
        }

        /* 8. OUTPUT FILE */
        $directory = $type === 'lpj'
            ? 'generated_documents/lpjs'
            : 'generated_documents/proposals';
        Storage::disk('public')->makeDirectory($directory);

        $documentType = $type === 'lpj' ? 'LPJ' : 'Proposal';
        $filename = $documentType.'_'.Str::slug($submission->activity_name).'_'.time().'.docx';
        $outputPath = Storage::disk('public')->path($directory.'/'.$filename);

        $processor->saveAs($outputPath);
        $this->ensureDocxImagesCentered($outputPath);

        return $directory.'/'.$filename;
    }

    protected function injectOfficialSignatures(
        TemplateProcessor $processor,
        Submission $submission,
        ?Lpj $lpj = null
    ): void {
        $submitter = $submission->submitter;
        $ormawa    = $submission->ormawa;

        $isBem = $ormawa && ($ormawa->isBem() || $ormawa->isDpm());

        // Cari ketua / pemimpin ormawa dari tabel members
        $organizationLeader = $ormawa
            ? OrganizationMember::query()
                ->where('period_id', $submission->period_id)
                ->where('ormawa_id', $submission->ormawa_id)
                ->where('status', 'active')
                ->where('position', $ormawa->leadershipPosition())
                ->oldest('id')
                ->first()
            : null;

        $leaderTitle = $ormawa?->leadershipTitle() ?? 'Ketua Himpunan';
        $leaderName  = $organizationLeader?->name
            ?: ($submission->chairman_name ?: '(Nama '.$leaderTitle.')');
        $leaderNim   = $organizationLeader?->nim ?: ($submission->chairman_nim ?: '-');

        $pembina = User::query()
            ->whereHas('role', fn ($query) => $query->where('name', 'pembina_ormawa'))
            ->where('ormawa_id', $submission->ormawa_id)
            ->first();

        $prodiId = $submitter?->program_studi_id ?: $ormawa?->program_studi_id;
        $kaprodi = $prodiId
            ? User::query()
                ->whereHas('role', fn ($query) => $query->where('name', 'kaprodi'))
                ->where('program_studi_id', $prodiId)
                ->first()
            : null;

        // Nilai bersama
        $values = [
            // Ketua Pelaksana
            'chairman_name'    => $submission->chairman_name ?: '(Nama Ketua Pelaksana)',
            'chairman_nim'     => $submission->chairman_nim ?: '-',
            'ketupel_name'     => $submission->chairman_name ?: '(Nama Ketua Pelaksana)',
            'ketupel_nim'      => $submission->chairman_nim ?: '-',

            // Pemimpin Ormawa (generik)
            'leader_title'     => $leaderTitle,
            'leader_name'      => $leaderName,
            'leader_nim'       => $leaderNim,
            'org_leader_title' => $leaderTitle,
            'org_leader_name'  => $leaderName,
            'org_leader_nim'   => $leaderNim,

            // Alias khusus HIMA/UKM
            'kahim_title'      => $leaderTitle,
            'kahim_name'       => $leaderName,
            'kahim_nim'        => $leaderNim,

            // Alias khusus BEM/DPM
            'presma_title'     => $isBem ? $leaderTitle : 'Presiden Mahasiswa',
            'presma_name'      => $leaderName,
            'presma_nim'       => $leaderNim,

            // Pembina
            'pembina_name'     => $submission->pembina_name ?: ($pembina?->name ?? '(Nama Pembina)'),
            'pembina_nuptk'    => $submission->pembina_nuptk ?: ($pembina?->nuptk ?? '-'),

            // Kaprodi (organisasi tingkat universitas seperti UKM dan BEM/DPM tidak melewati kaprodi kecuali ditentukan eksplisit)
            'kaprodi_name'     => $submission->kaprodi_name ?: (($isBem || ($ormawa && $ormawa->isUkm())) ? '-' : ($kaprodi?->name ?? '(Nama Kaprodi)')),
            'kaprodi_nuptk'    => $submission->kaprodi_nuptk ?: (($isBem || ($ormawa && $ormawa->isUkm())) ? '-' : ($kaprodi?->nuptk ?? '-')),

            // Kabag Kemahasiswaan (tetap)
            'kabag_name'       => 'Ns. Alfunnafi\' Fahrul Rizzal, M.Kep., Sp. Kep.J',
            'kabag_nuptk'      => '4043771672130263',

            // Warek III (tetap)
            'warek_name'       => 'Amin Zakaria S.Kep,Ners, M.Kes',
            'warek_nuptk'      => '2035754655130113',
        ];

        $processor->setValues($values);

        /* 6.1 SISIPKAN TANDA TANGAN CANVAS SESUAI ROLE */
        $this->injectCanvasSignatures($processor, $submission, $isBem, $ormawa, $pembina, $kaprodi, $submitter, $lpj);
    }

    /**
     * Sisipkan tanda tangan digital (canvas) yang HANYA muncul ketika role yang bersangkutan
     * sudah menyetujui (ACC / status = 'approved') pengajuan proposal atau LPJ tersebut.
     */
    protected function injectCanvasSignatures(
        TemplateProcessor $processor,
        Submission $submission,
        bool $isBem,
        ?Ormawa $ormawa,
        ?User $pembina,
        ?User $kaprodi,
        ?User $submitter,
        ?Lpj $lpj = null
    ): void {
        $variables = $processor->getVariables();
        $sigPlaceholders = array_filter($variables, fn ($v) => str_starts_with($v, 'signature_'));

        if (empty($sigPlaceholders)) {
            return;
        }

        $signatures = [];

        // Ambil tahapan approval dari LPJ (jika dokumen LPJ) atau dari Submission (jika dokumen proposal)
        $steps = $lpj
            ? ($lpj->relationLoaded('approvalSteps') ? $lpj->approvalSteps : $lpj->approvalSteps()->with('role')->get())
            : ($submission->relationLoaded('approvalSteps') ? $submission->approvalSteps : $submission->approvalSteps()->with('role')->get());

        // 1. Pembina Ormawa (Hanya muncul jika sudah ACC / approved)
        $pembinaSig = $steps
            ->filter(fn ($s) => $s->role?->name === 'pembina_ormawa'
                && (($s->status instanceof \BackedEnum ? $s->status->value : (string) $s->status) === 'approved')
                && ! empty($s->signature_path))
            ->last()
            ?->signature_path;

        if ($pembinaPath = $this->resolveSignatureFilePath($pembinaSig)) {
            $signatures['signature_pembina_ormawa'] = $pembinaPath;
        }

        // 2. Kaprodi (Hanya muncul jika sudah ACC / approved)
        $kaprodiSig = $steps
            ->filter(fn ($s) => $s->role?->name === 'kaprodi'
                && (($s->status instanceof \BackedEnum ? $s->status->value : (string) $s->status) === 'approved')
                && ! empty($s->signature_path))
            ->last()
            ?->signature_path;

        if ($kaprodiPath = $this->resolveSignatureFilePath($kaprodiSig)) {
            $signatures['signature_kaprodi'] = $kaprodiPath;
        }

        // 3. Kabag Kemahasiswaan (Hanya muncul jika sudah ACC / approved)
        $kabagSig = $steps
            ->filter(fn ($s) => $s->role?->name === 'kabag_kemahasiswaan'
                && (($s->status instanceof \BackedEnum ? $s->status->value : (string) $s->status) === 'approved')
                && ! empty($s->signature_path))
            ->last()
            ?->signature_path;

        if ($kabagPath = $this->resolveSignatureFilePath($kabagSig)) {
            $signatures['signature_kabag_kemahasiswaan'] = $kabagPath;
        }

        // 4. Warek III (Hanya muncul jika sudah ACC / approved)
        $warekSig = $steps
            ->filter(fn ($s) => $s->role?->name === 'warek'
                && (($s->status instanceof \BackedEnum ? $s->status->value : (string) $s->status) === 'approved')
                && ! empty($s->signature_path))
            ->last()
            ?->signature_path;

        if ($warekPath = $this->resolveSignatureFilePath($warekSig)) {
            $signatures['signature_warek'] = $warekPath;
        }

        // 5. Ketua Pelaksana / Ketua Ormawa / Presma (Penandatangan Pengaju Kegiatan)
        $chairmanSig = $submission->chairman_signature_path;
        if (! $chairmanSig && $lpj) {
            $chairmanSig = $lpj->submission?->chairman_signature_path;
        }
        $chairmanPath = $this->resolveSignatureFilePath($chairmanSig);

        $leaderSig = $chairmanSig ?: ($lpj?->creator?->signature_path ?: $submitter?->signature_path);
        if (! $leaderSig && $submission->ormawa_id) {
            $leaderSig = User::query()
                ->where('ormawa_id', $submission->ormawa_id)
                ->whereNotNull('signature_path')
                ->value('signature_path');
        }
        if (! $leaderSig) {
            $leaderSig = User::query()
                ->whereHas('role', fn ($q) => $q->whereIn('name', ['sekretaris_ormawa', 'superadmin']))
                ->whereNotNull('signature_path')
                ->value('signature_path');
        }
        if ($leaderPath = $this->resolveSignatureFilePath($leaderSig)) {
            $signatures['signature_ketua_ormawa'] = $chairmanPath ?: $leaderPath;
            $signatures['signature_ketua_pelaksana'] = $chairmanPath ?: $leaderPath;
            $signatures['signature_presma'] = $leaderPath;
        }

        // Terapkan ke template processor
        foreach ($sigPlaceholders as $placeholder) {
            if (isset($signatures[$placeholder]) && file_exists($signatures[$placeholder])) {
                try {
                    $processor->setImageValue($placeholder, [
                        'path'   => $signatures[$placeholder],
                        'width'  => 195,
                        'height' => 85,
                        'ratio'  => true,
                    ]);
                } catch (\Throwable $e) {
                    $processor->setValue($placeholder, '');
                }
            } else {
                // Bersihkan placeholder agar teks ${...} tidak tampil pada dokumen
                try {
                    $processor->setValue($placeholder, '');
                } catch (\Throwable $e) {
                }
            }
        }
    }

    /**
     * Resolusi path file fisik dari string signature_path (mendukung disk local, public, atau path absolut).
     */
    protected function resolveSignatureFilePath(?string $signaturePath): ?string
    {
        if (! $signaturePath) {
            return null;
        }

        if (file_exists($signaturePath)) {
            return $signaturePath;
        }

        if (Storage::disk('local')->exists($signaturePath)) {
            return Storage::disk('local')->path($signaturePath);
        }

        if (Storage::disk('public')->exists($signaturePath)) {
            return Storage::disk('public')->path($signaturePath);
        }

        return null;
    }

    /**
     * Konversi DOCX ke PDF menggunakan docx2pdf (Python, requires MS Word atau LibreOffice).
     * Mengembalikan path file PDF di disk yang sama, atau null jika gagal.
     */
    public function convertToPdf(string $docxAbsolutePath): ?string
    {
        $pdfPath = Str::beforeLast($docxAbsolutePath, '.docx').'.pdf';

        // Cek executable di PATH atau lokasi user-install Python313
        $execCandidates = [
            'docx2pdf',
            'C:\\Users\\Mohthars-HP\\AppData\\Roaming\\Python\\Python313\\Scripts\\docx2pdf.exe',
            'python -m docx2pdf',
        ];

        $cmd = null;
        foreach ($execCandidates as $candidate) {
            $testOutput = @shell_exec('"'.escapeshellcmd($candidate).'" --help 2>&1');
            if ($testOutput !== null && $testOutput !== '') {
                $cmd = $candidate;
                break;
            }
        }

        if (! $cmd) {
            $cmd = 'python -m docx2pdf';
        }

        $escapedDocx = escapeshellarg($docxAbsolutePath);
        $escapedPdf  = escapeshellarg($pdfPath);
        $command     = "$cmd $escapedDocx $escapedPdf 2>&1";

        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($pdfPath)) {
            return $pdfPath;
        }

        return null;
    }

    protected function terbilang($angka): string
    {
        $angka = abs($angka);
        $baca  = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        if ($angka < 12) {
            return ' '.$baca[$angka];
        }
        if ($angka < 20) {
            return $this->terbilang($angka - 10).' Belas';
        }
        if ($angka < 100) {
            return $this->terbilang((int) ($angka / 10)).' Puluh'.$this->terbilang($angka % 10);
        }
        if ($angka < 200) {
            return ' Seratus'.$this->terbilang($angka - 100);
        }
        if ($angka < 1000) {
            return $this->terbilang((int) ($angka / 100)).' Ratus'.$this->terbilang($angka % 100);
        }
        if ($angka < 2000) {
            return ' Seribu'.$this->terbilang($angka - 1000);
        }
        if ($angka < 1000000) {
            return $this->terbilang((int) ($angka / 1000)).' Ribu'.$this->terbilang($angka % 1000);
        }
        if ($angka < 1000000000) {
            return $this->terbilang((int) ($angka / 1000000)).' Juta'.$this->terbilang($angka % 1000000);
        }

        return (string) $angka;
    }

    protected function makeWordList(?array $items): TextRun
    {
        $run = new TextRun;

        $items = array_values(array_filter(
            $items ?? [],
            fn ($item) => trim((string) $item) !== ''
        ));

        if (empty($items)) {
            $run->addText('-');

            return $run;
        }

        foreach ($items as $index => $item) {
            if ($index > 0) {
                $run->addTextBreak();
            }

            $run->addText(($index + 1).'. '.$item);
        }

        return $run;
    }

    /**
     * Gantikan macro dokumen dengan baris daftar bernomor (1., 2., 3., dst.)
     * Setiap item dijadikan elemen <w:p> mandiri dengan indentasi gantung (hanging indent),
     * sehingga konsisten berjajar rapi ke bawah dan terformat sempurna baik di Word maupun web preview (docx-preview).
     */
    protected function replaceWithNumberedList(TemplateProcessor $processor, string $macro, ?array $items): void
    {
        $filtered = array_values(array_filter(
            $items ?? [],
            fn ($item) => trim((string) $item) !== ''
        ));

        if (empty($filtered)) {
            $emptyXml = '<w:p w:rsidR="006E2067" w:rsidP="00AE2104">'
                      . '<w:pPr>'
                      . '<w:spacing w:after="0" w:line="360" w:lineRule="auto"/>'
                      . '<w:ind w:left="709" w:hanging="425"/>'
                      . '<w:jc w:val="both"/>'
                      . '<w:rPr>'
                      . '<w:rFonts w:ascii="Arial" w:eastAsia="Arial" w:hAnsi="Arial" w:cs="Arial"/>'
                      . '<w:color w:val="000000"/>'
                      . '<w:sz w:val="24"/><w:szCs w:val="24"/>'
                      . '</w:rPr>'
                      . '</w:pPr>'
                      . '<w:r>'
                      . '<w:rPr>'
                      . '<w:rFonts w:ascii="Arial" w:eastAsia="Arial" w:hAnsi="Arial" w:cs="Arial"/>'
                      . '<w:color w:val="000000"/>'
                      . '<w:sz w:val="24"/><w:szCs w:val="24"/>'
                      . '</w:rPr>'
                      . '<w:t>-</w:t>'
                      . '</w:r>'
                      . '</w:p>';

            $processor->replaceXmlBlock($macro, $emptyXml);

            return;
        }

        $xml = '';
        foreach ($filtered as $index => $item) {
            $num = ($index + 1).'. ';
            $text = trim((string) $item);
            // Bersihkan jika user sudah mengetik penomoran di awal input
            $cleanText = preg_replace('/^\d+[\.\)]\s*/', '', $text);
            $displayText = $num.$cleanText;
            $escaped = htmlspecialchars($displayText, ENT_QUOTES | ENT_XML1, 'UTF-8');

            $xml .= '<w:p w:rsidR="006E2067" w:rsidP="00AE2104">'
                  . '<w:pPr>'
                  . '<w:pStyle w:val="ListParagraph"/>'
                  . '<w:spacing w:after="60" w:line="360" w:lineRule="auto"/>'
                  . '<w:ind w:left="709" w:hanging="425"/>'
                  . '<w:jc w:val="both"/>'
                  . '<w:rPr>'
                  . '<w:rFonts w:ascii="Arial" w:eastAsia="Arial" w:hAnsi="Arial" w:cs="Arial"/>'
                  . '<w:color w:val="000000"/>'
                  . '<w:sz w:val="24"/><w:szCs w:val="24"/>'
                  . '</w:rPr>'
                  . '</w:pPr>'
                  . '<w:r>'
                  . '<w:rPr>'
                  . '<w:rFonts w:ascii="Arial" w:eastAsia="Arial" w:hAnsi="Arial" w:cs="Arial"/>'
                  . '<w:color w:val="000000"/>'
                  . '<w:sz w:val="24"/><w:szCs w:val="24"/>'
                  . '</w:rPr>'
                  . '<w:t xml:space="preserve">'.$escaped.'</w:t>'
                  . '</w:r>'
                  . '</w:p>';
        }

        $processor->replaceXmlBlock($macro, $xml);
    }

    /**
     * Gantikan macro konteks timeline (plan_context, dsb.) dengan format terindentasi rapi.
     * Tidak keluar ke tepi kiri kolom 0, melainkan konsisten lurus sejajar di bawah teks tahap kegiatan.
     */
    protected function replaceWithTimelineContext(TemplateProcessor $processor, string $macro, ?array $items): void
    {
        $filtered = array_values(array_filter(
            $items ?? [],
            fn ($item) => trim((string) $item) !== ''
        ));

        if (empty($filtered)) {
            $processor->replaceXmlBlock($macro, '');

            return;
        }

        $xml = '';
        $multiple = count($filtered) > 1;

        foreach ($filtered as $index => $item) {
            $text = trim((string) $item);
            $cleanText = preg_replace('/^[-•*]\s*|^\d+[\.\)]\s*/', '', $text);
            $displayText = $multiple ? '- '.$cleanText : $cleanText;
            $escaped = htmlspecialchars($displayText, ENT_QUOTES | ENT_XML1, 'UTF-8');

            $xml .= '<w:p w:rsidR="006E2067" w:rsidP="00AE2104">'
                  . '<w:pPr>'
                  . '<w:pStyle w:val="ListParagraph"/>'
                  . '<w:spacing w:after="60" w:line="360" w:lineRule="auto"/>'
                  . '<w:ind w:left="992" w:hanging="283"/>'
                  . '<w:jc w:val="both"/>'
                  . '<w:rPr>'
                  . '<w:rFonts w:ascii="Arial" w:eastAsia="Arial" w:hAnsi="Arial" w:cs="Arial"/>'
                  . '<w:color w:val="000000"/>'
                  . '<w:sz w:val="24"/><w:szCs w:val="24"/>'
                  . '</w:rPr>'
                  . '</w:pPr>'
                  . '<w:r>'
                  . '<w:rPr>'
                  . '<w:rFonts w:ascii="Arial" w:eastAsia="Arial" w:hAnsi="Arial" w:cs="Arial"/>'
                  . '<w:color w:val="000000"/>'
                  . '<w:sz w:val="24"/><w:szCs w:val="24"/>'
                  . '</w:rPr>'
                  . '<w:t xml:space="preserve">'.$escaped.'</w:t>'
                  . '</w:r>'
                  . '</w:p>';
        }

        $processor->replaceXmlBlock($macro, $xml);
    }

    protected function createTemplateProcessor(string $documentPath): TemplateProcessor
    {
        $previousHandler = set_error_handler(function ($errno, $errstr) use (&$previousHandler) {
            if (str_contains($errstr, 'tempnam()')) {
                return true;
            }

            return $previousHandler ? $previousHandler(...func_get_args()) : false;
        });

        try {
            return new TemplateProcessor($documentPath);
        } finally {
            restore_error_handler();
        }
    }

    protected function safeCloneRowAndSetValues(TemplateProcessor $processor, string $search, array $values): void
    {
        try {
            $processor->cloneRowAndSetValues($search, $values);
        } catch (\Throwable $e) {
            // Variable is not in template or format does not support row clone
        }
    }

    /**
     * Format kuantitas tanpa .00 di belakang jika bilangan bulat (contoh: 5.00 -> 5).
     * Disertai satuan jika ada (contoh: 5 pcs, 50 kotak).
     */
    protected function formatQuantity(mixed $qty, ?string $unit = null): string
    {
        if ($qty === null || $qty === '') {
            $formattedQty = '0';
        } else {
            $qtyNumber = (float) $qty;
            $formattedQty = ($qtyNumber == (int) $qtyNumber)
                ? (string) (int) $qtyNumber
                : rtrim(rtrim(number_format($qtyNumber, 4, '.', ''), '0'), '.');
        }

        $unitStr = trim((string) ($unit ?? ''));

        return $unitStr !== '' ? "{$formattedQty} {$unitStr}" : $formattedQty;
    }

    /**
     * Sisipkan logo ORMAWA ke placeholder cover ${ormawa_logo} secara proporsional.
     */
    protected function injectOrmawaLogo(TemplateProcessor $processor, ?Ormawa $ormawa): void
    {
        $variables = $processor->getVariables();
        if (! in_array('ormawa_logo', $variables, true)) {
            return;
        }

        $logoPath = null;
        if ($ormawa && $ormawa->hasCustomLogo()) {
            $candidate = Storage::disk('public')->path($ormawa->logo);
            if (file_exists($candidate)) {
                $logoPath = $candidate;
            }
        }

        if (! $logoPath) {
            $defaultLogo = public_path('images/default-ormawa-logo.png');
            if (file_exists($defaultLogo)) {
                $logoPath = $defaultLogo;
            }
        }

        if ($logoPath && file_exists($logoPath)) {
            try {
                $processor->setImageValue('ormawa_logo', [
                    'path'   => $logoPath,
                    'width'  => 130,
                    'height' => 130,
                    'ratio'  => true,
                ]);

                return;
            } catch (\Throwable $e) {
                // Jangan gagalkan pembuatan dokumen jika terjadi kendala pada gambar
            }
        }

        try {
            $processor->setValue('ormawa_logo', '');
        } catch (\Throwable $e) {
        }
    }

    /**
     * Memastikan gambar/logo pada paragraf yang terpusat (center) dalam dokumen DOCX
     * dikonversi ke DrawingML standar dan/atau memiliki properti perataan tengah presisi,
     * sehingga selalu berada tepat di tengah halaman saat diunduh maupun dipratinjau.
     */
    public function ensureDocxImagesCentered(string $docxFilePath): void
    {
        if (! file_exists($docxFilePath) || ! class_exists(\ZipArchive::class)) {
            return;
        }

        $zip = new \ZipArchive();
        if ($zip->open($docxFilePath) !== true) {
            return;
        }

        $xml = $zip->getFromName('word/document.xml');
        if (! $xml) {
            $zip->close();

            return;
        }

        $modified = false;
        $imageCounter = 0;

        // 1. Tangani paragraf yang memiliki perataan tengah (<w:jc w:val="center"/>) dan memuat <w:pict>
        // Konversi legacy VML <w:pict> ke DrawingML <w:drawing><wp:inline> standar modern
        $pattern = '/<w:p(?:\s+[^>]*)?>(?:(?!<\/w:p>).)*?<w:jc[^>]*w:val=[\'"]center[\'"][^>]*\/?>(?:(?!<\/w:p>).)*?<w:pict[^>]*>.*?<v:imagedata[^>]*r:id=[\'"]([^\'"]+)[\'"][^>]*>.*?<\/w:pict>(?:(?!<\/w:p>).)*?<\/w:p>/s';

        $converted = preg_replace_callback($pattern, function (array $matches) use (&$modified, &$imageCounter): string {
            $pXml = $matches[0];
            if (preg_match('/<w:pict[^>]*>.*?<v:shape[^>]*style=[\'"]([^\'"]*)[\'"][^>]*>.*?<v:imagedata[^>]*r:id=[\'"]([^\'"]+)[\'"][^>]*>.*?<\/w:pict>/s', $pXml, $imgMatch)) {
                $style = $imgMatch[1];
                $rId = $imgMatch[2];
                $imageCounter++;

                // Deteksi dimensi gambar jika tersedia pada style v:shape (default 130px)
                $cx = 1238250;
                $cy = 1238250;
                if (preg_match('/width:\s*(\d+(?:\.\d+)?)(px|pt)?/i', $style, $wMatch)) {
                    $val = (float) $wMatch[1];
                    $unit = strtolower($wMatch[2] ?? 'px');
                    $cx = (int) ($unit === 'pt' ? $val * 12700 : $val * 9525);
                }
                if (preg_match('/height:\s*(\d+(?:\.\d+)?)(px|pt)?/i', $style, $hMatch)) {
                    $val = (float) $hMatch[1];
                    $unit = strtolower($hMatch[2] ?? 'px');
                    $cy = (int) ($unit === 'pt' ? $val * 12700 : $val * 9525);
                }

                $modified = true;
                $drawingId = 999 + $imageCounter;
                $drawingName = ($imageCounter === 1) ? 'Logo Ormawa' : 'Tanda Tangan ' . $drawingId;
                $drawingXml = '<w:drawing><wp:inline distT="0" distB="0" distL="0" distR="0" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"><wp:extent cx="' . $cx . '" cy="' . $cy . '"/><wp:effectExtent l="0" t="0" r="0" b="0"/><wp:docPr id="' . $drawingId . '" name="' . $drawingName . '"/><wp:cNvGraphicFramePr><a:graphicFrameLocks xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" noChangeAspect="1"/></wp:cNvGraphicFramePr><a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture"><pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture"><pic:nvPicPr><pic:cNvPr id="' . $drawingId . '" name="' . $drawingName . '"/><pic:cNvPicPr/></pic:nvPicPr><pic:blipFill><a:blip xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" r:embed="' . $rId . '"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill><pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="' . $cx . '" cy="' . $cy . '"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr></pic:pic></a:graphicData></a:graphic></wp:inline></w:drawing>';

                return str_replace($imgMatch[0], $drawingXml, $pXml);
            }

            return $pXml;
        }, $xml);

        // 2. Jika masih ada <v:shape> yang tersisa dalam dokumen, pastikan stylenya memuat perataan tengah otomatis
        if (preg_match('/<v:shape[^>]+style=[\'"]([^\'"]*)[\'"]/', $converted)) {
            $converted = preg_replace_callback('/(<v:shape[^>]+style=[\'"])([^\'"]*)([\'"][^>]*>)/', function (array $m): string {
                $style = $m[2];
                if (! str_contains($style, 'margin-left:auto')) {
                    $style .= ';margin-left:auto;margin-right:auto;text-align:center;position:relative;';
                }

                return $m[1] . $style . $m[3];
            }, $converted);
            $modified = true;
        }

        if ($modified) {
            $zip->addFromString('word/document.xml', $converted);
        }

        $zip->close();
    }
}
