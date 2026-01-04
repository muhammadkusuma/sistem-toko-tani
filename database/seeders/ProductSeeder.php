<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Pastikan kategori sudah ada (di-seed oleh CategorySeeder)
        $pupuk = Category::where('name', 'Pupuk & Nutrisi')->first();

        // Cek agar tidak error jika dijalankan berulang (optional, tapi good practice)
        if (! $pupuk) {
            return;
        }

        // --- PRODUK 1: PUPUK NPK (Ada satuan Eceran & Karung) ---
        $npk = Product::create([
            'category_id' => $pupuk->id,
            'code'        => 'P001',
            'name'        => 'Pupuk NPK Mutiara 16-16-16',
            'base_unit'   => 'kg',
            'buy_price'   => 15000, // Modal per kg
            'stock'       => 500,   // Total stok dalam kg
        ]);

        // Input Satuannya
        $npk->units()->createMany([
            [
                'unit_name'         => 'Eceran (kg)',
                'conversion_factor' => 1,     // 1 kg = 1 base unit
                'price'             => 18000, // Harga jual ecer
                'is_base'           => true,
            ],
            [
                'unit_name'         => 'Karung (50kg)',
                'conversion_factor' => 50,     // 1 karung = 50 base unit
                'price'             => 850000, // Harga jual grosir
                'is_base'           => false,
            ],
        ]);

        // --- PRODUK 2: OBAT PERTANIAN (Satuan Tunggal) ---
        $obat = Category::where('name', 'Pestisida & Obat')->first();
        if ($obat) {
            $roundup = Product::create([
                'category_id' => $obat->id,
                'code'        => 'O001',
                'name'        => 'Herbisida Roundup 1L',
                'base_unit'   => 'btl',
                'buy_price'   => 85000,
                'stock'       => 20,
            ]);

            $roundup->units()->create([
                'unit_name'         => 'Botol',
                'conversion_factor' => 1,
                'price'             => 95000,
                'is_base'           => true,
            ]);
        }
    }
}
