<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAffairsInformation extends Model
{
    protected $fillable = [
        'category',
        'title',
        'summary',
        'published_at',
        'document_path',
        'document_name',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function categoryLabel(): string
    {
        return match ($this->category) {
            'surat_edaran' => 'Surat Edaran',
            'surat_keputusan' => 'Surat Keputusan',
            'pengumuman' => 'Pengumuman',
            'informasi_lainnya' => 'Informasi Lainnya',
            default => 'Informasi Kemahasiswaan',
        };
    }
}
