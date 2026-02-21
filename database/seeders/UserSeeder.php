<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR']);

        $adminUser = User::updateOrCreate(
            ['user_name' => 'ADMIN'],
            [
                'name' => 'ADMIN',
                'last_name' => 'ADMIN',
                'dni' => '00000000',
                'email' => 'admin@admin.com',
                'foto_perfil' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'group_id' => null,
                'subgroup_id' => null,
            ]
        );
        $adminUser->syncRoles([$adminRole->name]);
    }
}
