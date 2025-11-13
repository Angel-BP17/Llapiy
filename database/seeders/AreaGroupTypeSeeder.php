<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaGroupTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('area_group_types')->insert([
            ['area_id' => 1, 'group_type_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 1, 'group_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 1, 'group_type_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 1, 'group_type_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 1, 'group_type_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 2, 'group_type_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 2, 'group_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 2, 'group_type_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 2, 'group_type_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 2, 'group_type_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 3, 'group_type_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 3, 'group_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 3, 'group_type_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 3, 'group_type_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 3, 'group_type_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 4, 'group_type_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 4, 'group_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 4, 'group_type_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 4, 'group_type_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 4, 'group_type_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 5, 'group_type_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 5, 'group_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 5, 'group_type_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 5, 'group_type_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 5, 'group_type_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 6, 'group_type_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 6, 'group_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 6, 'group_type_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 6, 'group_type_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['area_id' => 6, 'group_type_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
