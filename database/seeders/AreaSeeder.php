<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('areas')->insert([
            ['descripcion' => 'Organo de Control Interno', 'abreviacion' => 'OCI', 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'Área de Gestión Institucional', 'abreviacion' => 'AGI', 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'Área de Gestión Pedagógica', 'abreviacion' => 'AGP', 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'Area de Administración', 'abreviacion' => 'ADR', 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'Dirección', 'abreviacion' => 'DIR', 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'Area de Asesoría Jurídica', 'abreviacion' => 'AAJ', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
