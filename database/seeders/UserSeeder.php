<?php

namespace Database\Seeders;

use DB;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'ADMIN',
                'last_name' => 'ADMIN',
                'user_name' => 'ADMIN',
                'dni' => '00000000',
                'email' => 'admin@admin.com',
                'foto_perfil' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'user_type_id' => 1,
                'group_id' => null,
                'subgroup_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
