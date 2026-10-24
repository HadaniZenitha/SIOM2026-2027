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
        | Contoh ORMAWA
        |--------------------------------------------------------------------------
        |
        | Nanti data ini dapat dikelola melalui halaman Superadmin.
        |
        */

        $ormawas = [
            [
                'code' => 'BEM',
                'name' => 'Badan Eksekutif Mahasiswa',
                'description' => 'Organisasi mahasiswa tingkat universitas',
            ],
            [
                'code' => 'HIMATIF',
                'name' => 'Himpunan Mahasiswa S1 Informatika',
                'description' => 'Organisasi mahasiswa tingkat program studi S1 Informatika',
            ],
            [
                'code' => 'HIMANERS',
                'name' => 'Himpunan Mahasiswa S1 Keperawatan',
                'description' => 'Organisasi mahasiswa tingkat program studi S1 Keperawatan',
            ],
        ];

        foreach ($ormawas as $ormawa) {
            Ormawa::updateOrCreate(
                ['code' => $ormawa['code']],
                [
                    'name' => $ormawa['name'],
                    'description' => $ormawa['description'],
                    'is_active' => true,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Program Studi
        |--------------------------------------------------------------------------
        |
        | Untuk sementara gunakan contoh data.
        | Nanti dapat diganti sesuai data kampus.
        |
        */

        $programStudi = [
            [
                'code' => 'IF',
                'name' => 'S1 Informatika',
            ],
            [
                'code' => 'NERS',
                'name' => 'S1 Keperawatan',
            ],
        ];

        foreach ($programStudi as $prodi) {
            ProgramStudi::updateOrCreate(
                ['code' => $prodi['code']],
                [
                    'name' => $prodi['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
