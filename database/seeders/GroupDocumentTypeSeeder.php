<?php

namespace Database\Seeders;

use DB;
use Dompdf\FrameDecorator\Table;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupDocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('group_document_types')->insert([
            [
                'group_id' => 1,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'group_id' => 2,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 3,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 4,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 5,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 6,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 7,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 8,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 9,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 10,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 11,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 12,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 13,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 14,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 15,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 16,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 17,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 18,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 19,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 20,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => 21,
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]

        ]);
    }
}
