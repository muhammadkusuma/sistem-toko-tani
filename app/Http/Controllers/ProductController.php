<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar barang
     */
    public function index(Request $request)
    {
        // Fitur Pencarian Sederhana
        $query = Product::with('category', 'units');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        }

        $products = $query->latest()->paginate(10);

        return view('products.index', compact('products'));
    }

    /**
     * Form tambah barang
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Proses simpan ke database (LOGIC UTAMA)
     */
    public function store(Request $request)
    {
        $request->validate([
            'code'        => 'required|unique:products,code',
            'name'        => 'required',
            'category_id' => 'required',
            'base_unit'   => 'required', // Contoh: kg, pcs
            'buy_price'   => 'required|numeric',
            'sell_price'  => 'required|numeric', // Harga jual satuan dasar
            'stock'       => 'required|numeric',
        ]);

        // Gunakan DB Transaction agar aman
        DB::beginTransaction();
        try {
            // 1. Simpan Produk Utama
            $product = Product::create([
                'code'        => $request->code,
                'name'        => $request->name,
                'category_id' => $request->category_id,
                'base_unit'   => $request->base_unit,
                'buy_price'   => $request->buy_price,
                'stock'       => $request->stock,
            ]);

            // 2. OTOMATIS Buat Satuan Dasar di tabel product_units
            // Ini penting agar di Kasir nanti semua logika ambil dari tabel units
            ProductUnit::create([
                'product_id'        => $product->id,
                'unit_name'         => $request->base_unit, // Misal: kg
                'conversion_factor' => 1,                   // 1 kg = 1 kg base
                'price'             => $request->sell_price,
                'is_base'           => true,
            ]);

            // 3. Simpan Satuan Tambahan (Jika ada input dynamic dari form)
            if ($request->has('more_units')) {
                foreach ($request->more_units as $unit) {
                    if ($unit['name'] && $unit['factor'] && $unit['price']) {
                        ProductUnit::create([
                            'product_id'        => $product->id,
                            'unit_name'         => $unit['name'],   // Misal: Karung
                            'conversion_factor' => $unit['factor'], // Misal: 50
                            'price'             => $unit['price'],
                            'is_base'           => false,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Form edit barang
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        // Load relasi units agar muncul di form edit
        $product->load('units');
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update barang
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required',
            'category_id' => 'required',
            'stock'       => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            // Update Data Induk
            $product->update([
                'name'        => $request->name,
                'category_id' => $request->category_id,
                'stock'       => $request->stock,
                'buy_price'   => $request->buy_price,
            ]);

            // Update atau Tambah Unit Baru bisa dikembangkan di sini
            // Untuk versi simpel, kita asumsikan user mengedit unit via menu terpisah atau logic replace all
            // Di sini saya skip logic update complex unit agar kode tidak terlalu panjang di awal

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Hapus barang (Soft Delete)
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk dihapus sementara (Arsip).');
    }
}
