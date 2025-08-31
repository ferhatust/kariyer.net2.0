<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            IsverenSeeder::class,
            KullaniciSeeder::class,
            IsIlaniSeeder::class,
            BasvuruSeeder::class,
        ]);
    }
}
