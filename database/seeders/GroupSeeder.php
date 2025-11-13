<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('groups')->insert([
            ['area_group_type_id' => 1, 'descripcion' => 'Secretaria de OCI', 'abreviacion' => 'SEC_OCI', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 8, 'descripcion' => 'Equipo 1 de AGI', 'abreviacion' => 'EQU1_AGI', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 8, 'descripcion' => 'Equipo 2 de AGI', 'abreviacion' => 'EQU2_AGI', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 6, 'descripcion' => 'Secretaria de AGI', 'abreviacion' => 'SEC_AGI', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 11, 'descripcion' => 'Secretaria de AGP', 'abreviacion' => 'SEC_AGP', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 16, 'descripcion' => 'Secretaria de ADR', 'abreviacion' => 'SEC_ADR', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 21, 'descripcion' => 'Secretaria de DIR', 'abreviacion' => 'SEC_DIR', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 26, 'descripcion' => 'Secretaria de AAJ', 'abreviacion' => 'SEC_AAJ', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 3, 'descripcion' => 'Equipo 1 de OCI', 'abreviacion' => 'EQU1_OCI', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 3, 'descripcion' => 'Equipo 2 de OCI', 'abreviacion' => 'EQU2_OCI', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 13, 'descripcion' => 'Equipo 1 de AGP', 'abreviacion' => 'EQU1_AGP', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 13, 'descripcion' => 'Equipo 2 de AGP', 'abreviacion' => 'EQU2_AGP', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 18, 'descripcion' => 'Equipo 1 de ADR', 'abreviacion' => 'EQU1_ADR', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 18, 'descripcion' => 'Equipo 2 de ADR', 'abreviacion' => 'EQU2_ADR', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 28, 'descripcion' => 'Equipo 1 de AAJ', 'abreviacion' => 'EQU1_AAJ', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 28, 'descripcion' => 'Equipo 2 de AAJ', 'abreviacion' => 'EQU2_AAJ', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 5, 'descripcion' => 'Especialista 1 de OCI', 'abreviacion' => 'ESP_OCI', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 10, 'descripcion' => 'Especialista 1 de AGI', 'abreviacion' => 'ESP_AGI', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 15, 'descripcion' => 'Especialista 1 de AGP', 'abreviacion' => 'ESP_AGP', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 20, 'descripcion' => 'Especialista 1 de ADR', 'abreviacion' => 'ESP_ADR', 'created_at' => now(), 'updated_at' => now()],
            ['area_group_type_id' => 30, 'descripcion' => 'Especialista 1 de AAJ', 'abreviacion' => 'ESP_AAJ', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
