<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubgroupDocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subgroup_document_types')->insert([
            [
                'subgroup_id' => 1,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'subgroup_id' => 2,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subgroup_id' => 3,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subgroup_id' => 4,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subgroup_id' => 5,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subgroup_id' => 6,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subgroup_id' => 7,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subgroup_id' => 8,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]

        ]);
    }
}
