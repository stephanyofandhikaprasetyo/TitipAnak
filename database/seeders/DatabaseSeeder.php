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
            RolePermissionSeeder::class,
            PenggunaSeeder::class,
            MasterDataSeeder::class,
            PendaftaranSeeder::class,
            OperasionalSeeder::class,
            JadwalSeeder::class,
            LaporanSeeder::class,
        ]);
    }
}