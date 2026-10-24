<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name', 'superadmin')->firstOrFail();

        User::updateOrCreate(
            ['email' => 'admin@siom.test'],
            [
                'name' => 'Superadmin',
                'password' => Hash::make('password'),
                'role_id' => $role->id,
                'phone' => null,
                'is_active' => true,
            ]
        );
    }
}
