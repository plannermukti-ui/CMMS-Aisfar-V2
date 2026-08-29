<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            UserSeeder::class,
            ReffEquipSeeder::class,
            ReffComponentSeeder::class,
            EquipmentSeeder::class,
            ScmSeeder::class,
            WorkOrderSeeder::class,
        ]);
    }
}
