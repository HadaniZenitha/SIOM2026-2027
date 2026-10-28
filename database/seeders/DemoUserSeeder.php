<?php

namespace Database\Seeders;

use App\Models\Ormawa;
use App\Models\ProgramStudi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        $superadminRole = Role::where('name', 'superadmin')->first();
        $sekretarisRole = Role::where('name', 'sekretaris_ormawa')->first();
        $pembinaRole = Role::where('name', 'pembina_ormawa')->first();
        $kaprodiRole = Role::where('name', 'kaprodi')->first();
        $kabagRole = Role::where('name', 'kabag_kemahasiswaan')->first();
        $warekRole = Role::where('name', 'warek')->first();

        $himatif = Ormawa::where('code', 'HIMATIF')->first();
        $prodiIF = ProgramStudi::where('code', 'IF')->first();

        $ukmTaekwondo = Ormawa::where('code', 'UKM_TAEKWONDO')->first() ?: Ormawa::where('type', 'ukm')->first();
        $dpm = Ormawa::where('code', 'DPM')->first();
        $period = \App\Models\Period::where('status', 'active')->first();

        // 1. Sekretaris HIMA (HIMATIF)
        User::updateOrCreate(
            ['email' => 'sekretaris@siom.test'],
            [
                'name' => 'Sekretaris HIMATIF',
                'password' => $password,
                'role_id' => $sekretarisRole?->id,
                'ormawa_id' => $himatif?->id,
                'program_studi_id' => $prodiIF?->id,
                'is_active' => true,
            ]
        );

        // 1b. Sekretaris UKM (UKM Taekwondo)
        User::updateOrCreate(
            ['email' => 'sekretaris.ukm@siom.test'],
            [
                'name' => 'Sekretaris UKM Taekwondo',
                'password' => $password,
                'role_id' => $sekretarisRole?->id,
                'ormawa_id' => $ukmTaekwondo?->id,
                'is_active' => true,
            ]
        );

        // 1c. Sekretaris DPM (Dewan Perwakilan Mahasiswa)
        User::updateOrCreate(
            ['email' => 'sekretaris.dpm@siom.test'],
            [
                'name' => 'Sekretaris DPM',
                'password' => $password,
                'role_id' => $sekretarisRole?->id,
                'ormawa_id' => $dpm?->id,
                'is_active' => true,
            ]
        );

        // Pastikan pagu anggaran ormawa tersedia
        if ($period) {
            foreach ([$himatif, $ukmTaekwondo, $dpm] as $org) {
                if ($org) {
                    \App\Models\OrganizationBudget::updateOrCreate(
                        ['period_id' => $period->id, 'ormawa_id' => $org->id],
                        [
                            'budget_amount' => 15000000,
                            'allocated_amount' => 0,
                            'remaining_amount' => 15000000,
                        ]
                    );
                }
            }
        }

        // 2. Pembina Ormawa
        User::updateOrCreate(
            ['email' => 'pembina@siom.test'],
            [
                'name' => 'Dr. Pembina Ormawa, M.Kom',
                'password' => $password,
                'role_id' => $pembinaRole?->id,
                'ormawa_id' => $himatif?->id,
                'nuptk' => '198501012010121001',
                'is_active' => true,
            ]
        );

        // 3. Kaprodi
        User::updateOrCreate(
            ['email' => 'kaprodi@siom.test'],
            [
                'name' => 'Kaprodi Informatika, M.T',
                'password' => $password,
                'role_id' => $kaprodiRole?->id,
                'program_studi_id' => $prodiIF?->id,
                'nuptk' => '198002022005011002',
                'is_active' => true,
            ]
        );

        // 4. Kabag Kemahasiswaan
        User::updateOrCreate(
            ['email' => 'kabag@siom.test'],
            [
                'name' => 'Kepala Bagian Kemahasiswaan',
                'password' => $password,
                'role_id' => $kabagRole?->id,
                'nuptk' => '197503032000031003',
                'is_active' => true,
            ]
        );

        // 5. Warek III
        User::updateOrCreate(
            ['email' => 'warek@siom.test'],
            [
                'name' => 'Wakil Rektor III Kemahasiswaan',
                'password' => $password,
                'role_id' => $warekRole?->id,
                'nuptk' => '197004041995021004',
                'is_active' => true,
            ]
        );
    }
}
