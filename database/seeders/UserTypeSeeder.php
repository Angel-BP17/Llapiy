<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_types')->insert([
            ['name' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Colaborador', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Revisor/Aprobador', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
