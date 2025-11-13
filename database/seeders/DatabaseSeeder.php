<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserTypeSeeder::class,
            AreaSeeder::class,
            GroupTypeSeeder::class,
            AreaGroupTypeSeeder::class,
            GroupSeeder::class,
            SubgroupSeeder::class,
            UserSeeder::class,
            DocumentTypeSeeder::class,
            SectionSeeder::class,
            AndamioSeeder::class,
            BoxSeeder::class,
            GroupDocumentTypeSeeder::class,
            SubgroupDocumentTypeSeeder::class,
        ]);
    }
}
