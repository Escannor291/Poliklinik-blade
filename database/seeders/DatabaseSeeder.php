<?php

namespace Database\Seeders;

use App\Models\datapasien;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Memanggil seeder untuk tabel users
        $this->call(UserSeeder::class);
    }
}
