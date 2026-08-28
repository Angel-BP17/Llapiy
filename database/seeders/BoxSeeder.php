<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('boxes')->insert([
            [
                'n_box' => 1,
                'andamio_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
