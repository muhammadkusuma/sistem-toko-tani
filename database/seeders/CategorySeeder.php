<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \App\Models\Category::insert([
            ['name' => 'Pupuk & Nutrisi', 'created_at' => now()],
            ['name' => 'Benih & Bibit', 'created_at' => now()],
            ['name' => 'Pestisida & Obat', 'created_at' => now()],
            ['name' => 'Alat Pertanian', 'created_at' => now()],
        ]);
    }
}
