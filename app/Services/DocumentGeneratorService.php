<?php

namespace App\Services;

use App\Models\ApprovalStep;
use App\Models\DocumentTemplate;
use App\Models\Lpj;
use App\Models\LpjApprovalStep;
use App\Models\OrganizationMember;
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
    public function generateProposal(Submission $submission): string
    {
        return $this->generateDocument($submission, 'proposal');
    }

    public function generateLpj(Lpj $lpj): string
    {
        $lpj->loadMissing(['items', 'submission']);
        $submission = $lpj->submission;

        if (! $submission) {
            throw new RuntimeException('Proposal untuk LPJ tidak ditemukan.');
        }

        if ($lpj->items->isEmpty()) {
            $submission->loadMissing('items');
            $lpj->setRelation('items', $submission->items);
        }

        return $this->generateDocument($submission, 'lpj', $lpj->items);
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
            'Proposal_'.Str::slug($submission->activity_name)
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
            'LPJ_'.Str::slug($lpj->title)
        );
    }

    /**
     * @param  iterable<int, ApprovalStep|LpjApprovalStep>  $steps
     */
    protected function applyApprovalSignatures(string $sourcePath, iterable $steps, string $filename): string
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

        if (count($signatures) !== 4) {
            throw new RuntimeException('Alur persetujuan belum lengkap untuk membuat dokumen final.');
        }

        $processor = new TemplateProcessor($sourcePath);
        $variables = $processor->getVariables();
        foreach ($signatures as $placeholder => $imagePath) {
            if (! collect($variables)->contains(fn (string $variable): bool => $variable === $placeholder || str_starts_with($variable, $placeholder.':'))) {
                throw new RuntimeException("Placeholder \${$placeholder} tidak tersedia pada dokumen. Tambahkan placeholder tanda tangan di template DOCX.");
            }

            $processor->setImageValue($placeholder, [
                'path' => $imagePath,
                'width' => 150,
                'height' => 60,
                'ratio' => true,
            ]);
        }

        $directory = 'generated_documents/signed';
        Storage::disk('local')->makeDirectory($directory);
        $path = $directory.'/'.$filename.'_'.Str::uuid().'.docx';
        $processor->saveAs(Storage::disk('local')->path($path));

        return $path;
    }

    protected function generateDocument(
        Submission $submission,
        string $type,
        ?Collection $budgetItems = null
    ): string {
        $submission->load([
            'period',
            'ormawa',
            'submitter.programStudi',
            'items',
            'timelines',
            'committees',
            'rundowns',
        ]);

        $template = DocumentTemplate::query()
            ->where('type', $type)
            ->where('is_active', true)
            ->where(fn ($query) => $query
                ->whereNull('period_id')
                ->orWhere('period_id', $submission->period_id))
            ->orderByRaw('CASE WHEN period_id = ? THEN 0 ELSE 1 END', [$submission->period_id])
            ->latest('version')
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

        $processor = new TemplateProcessor($templatePath);
        $budgetItems ??= $submission->items;
        $totalAmount = $type === 'lpj'
            ? $budgetItems->sum(fn ($item): float => (float) $item->total_price)
            : (float) $submission->total_amount;

        /* 1. DATA UTAMA */
        $executionTime = ($submission->event_start_time ? $submission->event_start_time->format('H:i') : '08:00')
            .' - '
            .($submission->event_end_time ? $submission->event_end_time->format('H:i') : 'Selesai').' WIB';

        $processor->setValues([
            'nomor_nota' => $submission->nota_number ?? '-',
            'activity_name' => $submission->activity_name ?? '-',
            'activity_theme' => $submission->activity_theme ?? '-',
            'divisi_name' => $submission->divisi_name ?? '-',
            'ormawa_name' => $submission->ormawa?->name ?? '-',
            'prodi_name' => $submission->submitter?->programStudi?->name ?? '-',
            'period_name' => $submission->period?->name ?? '-',
            'year' => $submission->period?->start_date ? $submission->period->start_date->format('Y') : now()->format('Y'),
            'date' => now()->translatedFormat('d F Y'),

            // Dasar Nota Dinas
            'meeting_date' => $submission->meeting_date ? $submission->meeting_date->translatedFormat('d F Y') : '-',
            'circular_letter' => $submission->circular_letter ?? '-',
            'budget_code' => $submission->budget_code ?? '-',

            // Detail Proposal
            'background' => $submission->background ?? '-',
            'target_participants' => $submission->target_participants ?? '-',

            // Waktu & Tempat
            'execution_day' => $submission->event_day ?? '-',
            'execution_date' => $submission->event_date ? $submission->event_date->translatedFormat('d F Y') : '-',
            'execution_time' => $executionTime,
            'execution_location' => $submission->event_location ?? '-',

            // Anggaran
            'total_amount' => number_format($totalAmount, 0, ',', '.'),
            'total_amount_words' => $this->terbilang($totalAmount).' Rupiah',

        ]);

        $processor->setComplexBlock(
            'objectives',
            $this->makeWordList($submission->objectives)
        );

        $processor->setComplexBlock(
            'benefits',
            $this->makeWordList($submission->benefits)
        );

        /* 2. TIMELINE 4 TAHAP */
        $timelines = $submission->timelines;
        $plan = $timelines->where('stage', 'perencanaan')->first();
        $prepare = $timelines->where('stage', 'persiapan')->first();
        $action = $timelines->where('stage', 'pelaksanaan')->first();
        $closing = $timelines->where('stage', 'penutupan')->first();

        $processor->setValues([
            'plan_start' => $plan?->start_date ? $plan->start_date->format('d/m/Y') : '-',
            'plan_end' => $plan?->end_date ? $plan->end_date->format('d/m/Y') : '-',
            'prepare_start' => $prepare?->start_date ? $prepare->start_date->format('d/m/Y') : '-',
            'prepare_end' => $prepare?->end_date ? $prepare->end_date->format('d/m/Y') : '-',

            'action_start' => $action?->start_date ? $action->start_date->format('d/m/Y') : '-',
            'action_end' => $action?->end_date ? $action->end_date->format('d/m/Y') : '-',

            'closing_start' => $closing?->start_date ? $closing->start_date->format('d/m/Y') : '-',
            'closing_end' => $closing?->end_date ? $closing->end_date->format('d/m/Y') : '-',
        ]);

        $processor->setComplexBlock(
            'plan_context',
            $this->makeWordList($plan?->description)
        );

        $processor->setComplexBlock(
            'prepare_context',
            $this->makeWordList($prepare?->description)
        );

        $processor->setComplexBlock(
            'action_context',
            $this->makeWordList($action?->description)
        );

        $processor->setComplexBlock(
            'closing_context',
            $this->makeWordList($closing?->description)
        );

        /* 3. TABEL DINAMIS RAB */
        if ($budgetItems->isNotEmpty()) {
            $rabRows = [];
            $rab2Rows = [];
            foreach ($budgetItems as $index => $item) {
                $rabRows[] = [
                    'no' => $index + 1,
                    'item_name' => $item->item_name ?? '-',
                    'qty' => $item->qty.' '.($item->unit ?? 'pcs'),
                    'price' => 'Rp '.number_format((float) $item->price, 0, ',', '.'),
                    'subtotal' => 'Rp '.number_format((float) $item->total_price, 0, ',', '.'),
                ];
                $rab2Rows[] = [
                    'rab2_no' => $index + 1,
                    'rab2_item_name' => $item->item_name ?? '-',
                    'rab2_qty' => $item->qty.' '.($item->unit ?? 'pcs'),
                    'rab2_price' => 'Rp '.number_format((float) $item->price, 0, ',', '.'),
                    'rab2_subtotal' => 'Rp '.number_format((float) $item->total_price, 0, ',', '.'),
                ];
            }

            $processor->cloneRowAndSetValues('item_name', $rabRows);
            $processor->cloneRowAndSetValues('rab2_item_name', $rab2Rows);
        }

        /* 4. TABEL DINAMIS RUNDOWN */
        if ($submission->rundowns->count() > 0) {
            $rundownRows = [];
            foreach ($submission->rundowns as $index => $rundown) {
                $rundownRows[] = [
                    'day_date' => $rundown->day_date ?? 'Hari Pertama',
                    'time_slot' => $rundown->time_slot ?? '-',
                    'event_name' => $rundown->event_name ?? '-',
                    'pic' => $rundown->pic ?? '-',
                    'note' => $rundown->note ?? '-',
                ];
            }
            $processor->cloneRowAndSetValues('event_name', $rundownRows);
        }

        /* 5. TABEL DINAMIS PANITIA */
        if ($submission->committees->count() > 0) {
            $committeeRows = [];
            foreach ($submission->committees as $index => $committee) {
                $committeeRows[] = [
                    'committee_name' => $committee->name ?? '-',
                    'structural_role' => $committee->structural_role ?? 'Mahasiswa',
                    'committee_role' => $committee->committee_role ?? '-',
                    'description' => $committee->description ?? '-',
                ];
            }
            $processor->cloneRowAndSetValues('committee_name', $committeeRows);
        }

        /* 6. PEJABAT & PENANDATANGAN */
        $this->injectOfficialSignatures($processor, $submission);

        /* 7. OUTPUT FILE */
        $directory = $type === 'lpj'
            ? 'generated_documents/lpjs'
            : 'generated_documents/proposals';
        Storage::disk('public')->makeDirectory($directory);

        $documentType = $type === 'lpj' ? 'LPJ' : 'Proposal';
        $filename = $documentType.'_'.Str::slug($submission->activity_name).'_'.time().'.docx';
        $outputPath = Storage::disk('public')->path($directory.'/'.$filename);

        $processor->saveAs($outputPath);

        return $directory.'/'.$filename;
    }

    protected function injectOfficialSignatures(TemplateProcessor $processor, Submission $submission): void
    {
        $submitter = $submission->submitter;
        $ormawa = $submission->ormawa;
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
        $leaderName = $organizationLeader?->name
            ?: ($submission->chairman_name ?: '(Nama '.$leaderTitle.')');
        $leaderNim = $organizationLeader?->nim ?: ($submission->chairman_nim ?: '-');
        $pembina = User::query()
            ->whereHas('role', fn ($query) => $query->where('name', 'pembina_ormawa'))
            ->where('ormawa_id', $submission->ormawa_id)
            ->first();
        $kaprodi = User::query()
            ->whereHas('role', fn ($query) => $query->where('name', 'kaprodi'))
            ->where('program_studi_id', $submitter?->program_studi_id)
            ->first();

        $processor->setValues([
            'chairman_name' => $submission->chairman_name ?: '(Nama Ketua Pelaksana)',
            'chairman_nim' => $submission->chairman_nim ?: '-',
            'leader_title' => $leaderTitle,
            'leader_name' => $leaderName,
            'leader_nim' => $leaderNim,
            'org_leader_title' => $leaderTitle,
            'org_leader_name' => $leaderName,
            'org_leader_nim' => $leaderNim,
            'kahim_title' => $leaderTitle,
            'kahim_name' => $leaderName,
            'kahim_nim' => $leaderNim,

            'pembina_name' => $submission->pembina_name ?: ($pembina?->name ?? '(Nama Pembina)'),
            'pembina_nuptk' => $submission->pembina_nuptk ?: ($pembina?->nuptk ?? '-'),

            'kaprodi_name' => $submission->kaprodi_name ?: ($kaprodi?->name ?? '(Nama Kaprodi)'),
            'kaprodi_nuptk' => $submission->kaprodi_nuptk ?: ($kaprodi?->nuptk ?? '-'),

            'kabag_name' => 'Ns. Alfunnafi’ Fahrul Rizzal, M.Kep., Sp. Kep.J',
            'kabag_nuptk' => '4043771672130263',

            'warek_name' => 'Amin Zakaria S.Kep,Ners, M.Kes',
            'warek_nuptk' => '2035754655130113',
        ]);
    }

    protected function terbilang($angka): string
    {
        $angka = abs($angka);
        $baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
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
}
