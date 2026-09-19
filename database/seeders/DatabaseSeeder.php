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
            UserSeeder::class,
            AlexRiveraSeeder::class,
            SamChenSeeder::class,
            JordanBlakeSeeder::class,
            MayaPatelSeeder::class,
            ChrisNguyenSeeder::class,
        ]);
    }
}
