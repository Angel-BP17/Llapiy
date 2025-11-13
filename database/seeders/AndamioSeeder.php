<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AndamioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('andamios')->insert([
            [
                'n_andamio' => 1,
                'descripcion' => 'andamio 1',
                'section_id' => 1,
            ],
        ]);
    }
}
