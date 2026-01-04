<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun ADMIN (Segala Bisa)
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@tokotani.com',
            'password' => bcrypt('admin123'),
            'role'     => 'admin',
        ]);

        // 2. Akun KASIR (Hanya Kasir)
        User::create([
            'name'     => 'Kasir Toko',
            'email'    => 'kasir@tokotani.com',
            'password' => bcrypt('kasir123'),
            'role'     => 'cashier',
        ]);

        // 3. Akun PEMILIK (Hanya Lihat Laporan)
        User::create([
            'name'     => 'Pak Bos',
            'email'    => 'owner@tokotani.com',
            'password' => bcrypt('owner123'),
            'role'     => 'owner',
        ]);

        // Panggil Seeder Produk & Kategori
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
