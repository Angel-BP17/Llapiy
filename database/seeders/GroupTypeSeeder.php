<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('group_types')->insert([
            ['descripcion' => 'Secretaría', 'abreviacion' => 'SEC', 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'Jefatura', 'abreviacion' => 'JEF', 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'Equipos', 'abreviacion' => 'EQU', 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'Comisiones', 'abreviacion' => 'COM', 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'Especialistas', 'abreviacion' => 'ESP', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
