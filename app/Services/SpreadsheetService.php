<?php

namespace App\Services;

use DateTimeInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpreadsheetService
{
    /**
     * @param  list<string>  $expectedHeaders
     * @return list<array{line: int, values: array<string, string>}>
     */
    public function read(UploadedFile $file, array $expectedHeaders): array
    {
        $reader = new XlsxReader;
        $path = $file->getRealPath();

        if (! is_string($path)) {
            throw ValidationException::withMessages([
                'importFile' => 'File Excel tidak valid. Gunakan template .xlsx yang disediakan.',
            ]);
        }

        $temporaryPath = tempnam(sys_get_temp_dir(), 'siom-xlsx-');

        if ($temporaryPath === false || ! copy($path, $temporaryPath)) {
            if (is_string($temporaryPath) && file_exists($temporaryPath)) {
                unlink($temporaryPath);
            }

            throw new RuntimeException('File Excel tidak dapat disalin untuk diproses.');
        }

        try {
            if (! $reader->canRead($temporaryPath)) {
                throw ValidationException::withMessages([
                    'importFile' => 'File Excel tidak valid. Gunakan template .xlsx yang disediakan.',
                ]);
            }

            $spreadsheet = $reader->load($temporaryPath);
        } catch (ReaderException) {
            throw ValidationException::withMessages([
                'importFile' => 'File Excel tidak dapat dibaca. Pastikan berkas .xlsx tidak rusak.',
            ]);
        } finally {
            unlink($temporaryPath);
        }

        try {
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestDataRow();
            $highestColumn = $worksheet->getHighestDataColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            $headers = [];

            for ($column = 1; $column <= $highestColumnIndex; $column++) {
                $headers[] = mb_strtolower(trim($this->cellValue($worksheet->getCell([$column, 1]), 1)));
            }

            while ($headers !== [] && end($headers) === '') {
                array_pop($headers);
            }

            if (
                count($headers) !== count($expectedHeaders)
                || count(array_unique($headers)) !== count($headers)
                || array_diff($expectedHeaders, $headers) !== []
            ) {
                throw ValidationException::withMessages([
                    'importFile' => 'Header Excel tidak sesuai. Gunakan template impor .xlsx yang disediakan.',
                ]);
            }

            $records = [];

            for ($row = 2; $row <= $highestRow; $row++) {
                $values = [];
                $hasData = false;

                foreach ($headers as $column => $header) {
                    $value = $this->cellValue($worksheet->getCell([$column + 1, $row]), $row);
                    $values[$header] = $value;
                    $hasData = $hasData || $value !== '';
                }

                for ($column = count($headers) + 1; $column <= $highestColumnIndex; $column++) {
                    if ($this->cellValue($worksheet->getCell([$column, $row]), $row) !== '') {
                        throw ValidationException::withMessages([
                            'importFile' => "Jumlah kolom pada baris {$row} tidak sesuai dengan header Excel.",
                        ]);
                    }
                }

                if ($hasData) {
                    $records[] = ['line' => $row, 'values' => $values];
                }
            }

            if ($records === []) {
                throw ValidationException::withMessages([
                    'importFile' => 'File Excel tidak berisi data untuk diimpor.',
                ]);
            }

            return $records;
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }

    /**
     * @param  list<string>  $headers
     * @param  iterable<list<int|float|string|null>>  $rows
     */
    public function download(string $filename, string $sheetTitle, array $headers, iterable $rows): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle($sheetTitle);
        $this->writeRow($worksheet, 1, $headers);

        foreach ($rows as $rowIndex => $row) {
            $this->writeRow($worksheet, $rowIndex + 2, $row);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            try {
                (new XlsxWriter($spreadsheet))->save('php://output');
            } finally {
                $spreadsheet->disconnectWorksheets();
            }
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function cellValue(Cell $cell, int $row): string
    {
        if ($cell->getDataType() === DataType::TYPE_FORMULA) {
            throw ValidationException::withMessages([
                'importFile' => "Rumus Excel tidak diizinkan pada baris {$row}.",
            ]);
        }

        $value = $cell->getValue();

        if ($value === null) {
            return '';
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_numeric($value) && ExcelDate::isDateTime($cell)) {
            return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return trim((string) $value);
    }

    /**
     * @param  list<int|float|string|null>  $values
     */
    private function writeRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet, int $rowIndex, array $values): void
    {
        foreach ($values as $columnIndex => $value) {
            $cell = $worksheet->getCell([$columnIndex + 1, $rowIndex]);

            if (is_string($value) || $value === null) {
                $cell->setValueExplicit($value ?? '', DataType::TYPE_STRING);
            } else {
                $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
            }
        }
    }
}
