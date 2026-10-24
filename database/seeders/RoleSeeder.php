<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'superadmin',
                'display_name' => 'Superadmin',
            ],
            [
                'name' => 'sekretaris_ormawa',
                'display_name' => 'Sekretaris ORMAWA',
            ],
            [
                'name' => 'pembina_ormawa',
                'display_name' => 'Pembina ORMAWA',
            ],
            [
                'name' => 'kaprodi',
                'display_name' => 'Kepala Program Studi',
            ],
            [
                'name' => 'kabag_kemahasiswaan',
                'display_name' => 'Kepala Bagian Kemahasiswaan',
            ],
            [
                'name' => 'warek',
                'display_name' => 'Wakil Rektor III',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
