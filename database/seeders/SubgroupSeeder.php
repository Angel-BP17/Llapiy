<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubgroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subgroups')->insert([
            ['group_id' => 2, 'descripcion' => 'Subgrupo de Equipo 1 de AGI', 'abreviacion' => 'SUB1_AGI', 'created_at' => now(), 'updated_at' => now()],
            ['group_id' => 3, 'descripcion' => 'Subgrupo de Equipo 2 de AGI', 'abreviacion' => 'SUB2_AGI', 'created_at' => now(), 'updated_at' => now()],
            ['group_id' => 9, 'descripcion' => 'Subgrupo de Equipo 1 de OCI', 'abreviacion' => 'SUB1_OCI', 'created_at' => now(), 'updated_at' => now()],
            ['group_id' => 10, 'descripcion' => 'Subgrupo de Equipo 2 de OCI', 'abreviacion' => 'SUB2_OCI', 'created_at' => now(), 'updated_at' => now()],
            ['group_id' => 11, 'descripcion' => 'Subgrupo de Equipo 1 de AGP', 'abreviacion' => 'SUB1_AGP', 'created_at' => now(), 'updated_at' => now()],
            ['group_id' => 12, 'descripcion' => 'Subgrupo de Equipo 2 de AGP', 'abreviacion' => 'SUB2_AGP', 'created_at' => now(), 'updated_at' => now()],
            ['group_id' => 20, 'descripcion' => 'Subgrupo de Especialista ADR', 'abreviacion' => 'SUB_ESP_ADR', 'created_at' => now(), 'updated_at' => now()],
            ['group_id' => 21, 'descripcion' => 'Subgrupo de Especialista AAJ', 'abreviacion' => 'SUB_ESP_AAJ', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
