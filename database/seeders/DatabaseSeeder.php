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
            AreaSeeder::class,
            GroupTypeSeeder::class,
            AreaGroupTypeSeeder::class,
            GroupSeeder::class,
            SubgroupSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            SectionSeeder::class,
            AndamioSeeder::class,
            BoxSeeder::class,
        ]);
    }
}
