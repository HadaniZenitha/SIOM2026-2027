<?php

namespace Database\Seeders;

use App\Models\Ormawa;
use App\Models\Period;
use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Periode Akademik
        |--------------------------------------------------------------------------
        */

        Period::updateOrCreate(
            ['name' => '2026/2027'],
            [
                'start_date' => '2026-08-01',
                'end_date' => '2027-07-31',
                'status' => 'active',
                'description' => 'Periode akademik 2026/2027',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Program Studi (ITSK RS dr. Soepraoen Malang)
        |--------------------------------------------------------------------------
        */

        $programStudi = [
            [
                'code' => 'IF',
                'name' => 'S1 Informatika',
                'fakultas' => 'Teknologi Kesehatan',
            ],
            [
                'code' => 'S1_FISIO',
                'name' => 'S1 Fisioterapi',
                'fakultas' => 'Ilmu Kesehatan',
            ],
            [
                'code' => 'S1_FAR',
                'name' => 'S1 Farmasi',
                'fakultas' => 'Farmasi',
            ],
            [
                'code' => 'S1_KG',
                'name' => 'S1 Kedokteran Gigi',
                'fakultas' => 'Kedokteran Gigi',
            ],
            [
                'code' => 'D3_FAR',
                'name' => 'D-III Farmasi',
                'fakultas' => 'Farmasi',
            ],
            [
                'code' => 'D3_KEP',
                'name' => 'D-III Keperawatan',
                'fakultas' => 'Keperawatan',
            ],
            [
                'code' => 'D3_RMIK',
                'name' => 'D-III Rekam Medis dan Informasi Kesehatan',
                'fakultas' => 'Ilmu Kesehatan',
            ],
            [
                'code' => 'KEBIDANAN',
                'name' => 'Kebidanan',
                'fakultas' => 'Kebidanan',
            ],
            [
                'code' => 'ANESTESI',
                'name' => 'Anestesiologi',
                'fakultas' => 'Ilmu Kesehatan',
            ],
            [
                'code' => 'AKUPUNKTUR',
                'name' => 'Akupunktur',
                'fakultas' => 'Ilmu Kesehatan',
            ],
        ];

        foreach ($programStudi as $prodi) {
            ProgramStudi::updateOrCreate(
                ['code' => $prodi['code']],
                [
                    'name' => $prodi['name'],
                    'fakultas' => $prodi['fakultas'] ?? null,
                    'is_active' => true,
                ]
            );
        }

        $prodiMap = ProgramStudi::pluck('id', 'code');

        /*
        |--------------------------------------------------------------------------
        | ORMAWA (BEM, DPM, HIMA, UKM)
        |--------------------------------------------------------------------------
        */

        $ormawas = [
            // Lembaga Mahasiswa Tingkat Universitas
            [
                'code' => 'DPM',
                'name' => 'Dewan Perwakilan Mahasiswa',
                'type' => 'dpm',
                'program_studi_id' => null,
                'description' => 'Lembaga legislatif mahasiswa tingkat universitas',
            ],
            [
                'code' => 'BEM',
                'name' => 'Badan Eksekutif Mahasiswa',
                'type' => 'bem',
                'program_studi_id' => null,
                'description' => 'Organisasi eksekutif mahasiswa tingkat universitas',
            ],

            // 10 Himpunan Mahasiswa (HIMA) Program Studi ITSK RS dr. Soepraoen Malang
            [
                'code' => 'HIMATIF',
                'name' => 'HIMA S1 Informatika',
                'type' => 'hima',
                'program_studi_id' => $prodiMap['IF'] ?? null,
                'description' => 'Mewadahi mahasiswa Program Studi S1 Informatika dengan fokus teknologi dan kesehatan (health technology).',
            ],
            [
                'code' => 'HIMA_S1_FISIO',
                'name' => 'HIMA S1 Fisioterapi',
                'type' => 'hima',
                'program_studi_id' => $prodiMap['S1_FISIO'] ?? null,
                'description' => 'Mewadahi mahasiswa Program Studi S1 Fisioterapi.',
            ],
            [
                'code' => 'HIMA_S1_FAR',
                'name' => 'HIMA S1 Farmasi',
                'type' => 'hima',
                'program_studi_id' => $prodiMap['S1_FAR'] ?? null,
                'description' => 'Mewadahi mahasiswa Program Studi S1 Farmasi.',
            ],
            [
                'code' => 'HIMA_S1_KG',
                'name' => 'HIMA S1 Kedokteran Gigi',
                'type' => 'hima',
                'program_studi_id' => $prodiMap['S1_KG'] ?? null,
                'description' => 'Mewadahi mahasiswa Program Studi S1 Kedokteran Gigi.',
            ],
            [
                'code' => 'HIMA_D3_FAR',
                'name' => 'HIMA D-III Farmasi',
                'type' => 'hima',
                'program_studi_id' => $prodiMap['D3_FAR'] ?? null,
                'description' => 'Mewadahi mahasiswa Program Studi D-III Farmasi.',
            ],
            [
                'code' => 'HIMA_D3_KEP',
                'name' => 'HIMA D-III Keperawatan',
                'type' => 'hima',
                'program_studi_id' => $prodiMap['D3_KEP'] ?? null,
                'description' => 'Mewadahi mahasiswa Program Studi D-III Keperawatan.',
            ],
            [
                'code' => 'HIMA_D3_RMIK',
                'name' => 'HIMA D-III RMIK',
                'type' => 'hima',
                'program_studi_id' => $prodiMap['D3_RMIK'] ?? null,
                'description' => 'Mewadahi mahasiswa Program Studi D-III RMIK (Rekam Medis dan Informasi Kesehatan).',
            ],
            [
                'code' => 'HIMA_KEBIDANAN',
                'name' => 'HIMA Kebidanan',
                'type' => 'hima',
                'program_studi_id' => $prodiMap['KEBIDANAN'] ?? null,
                'description' => 'Mewadahi mahasiswa Program Studi Kebidanan.',
            ],
            [
                'code' => 'HIMA_ANESTESI',
                'name' => 'HIMA Anestesiologi',
                'type' => 'hima',
                'program_studi_id' => $prodiMap['ANESTESI'] ?? null,
                'description' => 'Mewadahi mahasiswa Program Studi Anestesiologi.',
            ],
            [
                'code' => 'HIMAKU',
                'name' => 'HIMAKU (Himpunan Mahasiswa Akupunktur)',
                'type' => 'hima',
                'program_studi_id' => $prodiMap['AKUPUNKTUR'] ?? null,
                'description' => 'Mewadahi mahasiswa Program Studi Akupunktur.',
            ],

            // 11 Unit Kegiatan Mahasiswa (UKM) ITSK RS dr. Soepraoen Malang
            [
                'code' => 'UKM_TAEKWONDO',
                'name' => 'UKM Taekwondo',
                'type' => 'ukm',
                'program_studi_id' => null,
                'description' => 'Wadah olahraga bela diri',
            ],
            [
                'code' => 'UKM_MUSIK_BAND',
                'name' => 'UKM Musik Band',
                'type' => 'ukm',
                'program_studi_id' => null,
                'description' => 'Wadah bagi mahasiswa yang gemar bermusik dan tampil di berbagai acara kampus',
            ],
            [
                'code' => 'UKM_TEATER_SARAF',
                'name' => 'UKM Teather Saraf',
                'type' => 'ukm',
                'program_studi_id' => null,
                'description' => 'UKM seni peran dan teater',
            ],
            [
                'code' => 'UKM_AMICI_ET_FIDEI',
                'name' => 'UKM Kerohanian Amici Et Fidei',
                'type' => 'ukm',
                'program_studi_id' => null,
                'description' => 'UKM kerohanian/spiritual',
            ],
            [
                'code' => 'UKM_ROHIS_BANJARI',
                'name' => 'UKM Al Banjari dan Rohani Islam',
                'type' => 'ukm',
                'program_studi_id' => null,
                'description' => 'UKM bidang keagamaan Islam dan seni al-banjari',
            ],
            [
                'code' => 'UKM_TENIS_MEJA',
                'name' => 'UKM Tenis Meja',
                'type' => 'ukm',
                'program_studi_id' => null,
                'description' => 'UKM bidang olahraga cabang tenis meja',
            ],
            [
                'code' => 'UKM_GITA_CAHYA',
                'name' => 'UKM Gita Cahya Swara',
                'type' => 'ukm',
                'program_studi_id' => null,
                'description' => 'UKM paduan suara kampus',
            ],
            [
                'code' => 'UKM_TARI_SPECTRA',
                'name' => 'UKM Tari (Spectra)',
                'type' => 'ukm',
                'program_studi_id' => null,
                'description' => 'UKM seni tari tradisional maupun modern',
            ],
            [
                'code' => 'UKM_LKIM',
                'name' => 'UKM LKIM (Lembaga Karya Ilmiah Mahasiswa)',
                'type' => 'ukm',
                'program_studi_id' => null,
                'description' => 'Wadah penalaran, riset, dan penulisan karya ilmiah',
            ],
            [
                'code' => 'UKM_KSR_PMI',
                'name' => 'UKM Korps Sukarela (KSR) PMI',
                'type' => 'ukm',
                'program_studi_id' => null,
                'description' => 'UKM bidang kemanusiaan dan kepalangmerahan',
            ],
            [
                'code' => 'UKM_MODELLING',
                'name' => 'UKM Modelling',
                'type' => 'ukm',
                'program_studi_id' => null,
                'description' => 'UKM bidang peragaan busana',
            ],
        ];

        // Hapus contoh data lama yang sudah digantikan
        Ormawa::whereIn('code', ['UKM_OLAHRAGA', 'UKM_SENI', 'HIMANERS'])->delete();

        foreach ($ormawas as $ormawa) {
            Ormawa::updateOrCreate(
                ['code' => $ormawa['code']],
                [
                    'name' => $ormawa['name'],
                    'type' => $ormawa['type'],
                    'program_studi_id' => $ormawa['program_studi_id'],
                    'description' => $ormawa['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
