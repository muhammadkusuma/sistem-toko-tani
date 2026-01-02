<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    /**
     * Menampilkan Halaman Kasir (POS)
     */
    public function create()
    {
        // Ambil semua produk beserta unit-unitnya yang stoknya ada
        $products = Product::with('units')->where('stock', '>', 0)->get();

        return view('transactions.create', compact('products'));
    }

    /**
     * Logic Penyimpanan Transaksi & Pengurangan Stok
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'cart_data'   => 'required', // String JSON dari Frontend
            'cash_amount' => 'required|numeric',
        ]);

        // Decode JSON Cart
        $cartItems = json_decode($request->cart_data, true);

        if (empty($cartItems)) {
            return redirect()->back()->with('error', 'Keranjang belanja kosong!');
        }

        DB::beginTransaction();
        try {
            // 2. Hitung Total Belanja (Validasi Server-Side)
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $totalAmount += $item['price'] * $item['qty'];
            }

            // 3. Buat Header Transaksi
            $transaction = Transaction::create([
                'user_id'       => auth()->id(),
                'invoice_no'    => 'INV/' . date('Ymd') . '/' . strtoupper(Str::random(5)),
                'total_amount'  => $totalAmount,
                'cash_amount'   => $request->cash_amount,
                'change_amount' => $request->cash_amount - $totalAmount,
            ]);

            // 4. Loop Barang Belanjaan
            foreach ($cartItems as $item) {

                // Ambil Data Produk Asli dari DB & Lock
                $product = Product::lockForUpdate()->find($item['product_id']);

                if (! $product) {
                    throw new \Exception("Produk " . $item['product_name'] . " tidak ditemukan/dihapus.");
                }

                // HITUNG PENGURANGAN STOK
                // Rumus: Qty Beli x Faktor Konversi Satuan
                $qtyToDeduct = $item['qty'] * $item['conversion_factor'];

                if ($product->stock < $qtyToDeduct) {
                    throw new \Exception("Stok " . $product->name . " tidak cukup! Sisa: " . $product->stock . " " . $product->base_unit);
                }

                // Kurangi Stok
                $product->decrement('stock', $qtyToDeduct);

                // Simpan Detail Transaksi
                TransactionDetail::create([
                    'transaction_id'      => $transaction->id,
                    'product_id'          => $item['product_id'],
                    'product_unit_id'     => $item['unit_id'],
                    'qty'                 => $item['qty'],
                    'price'               => $item['price'],
                    'conversion_snapshot' => $item['conversion_factor'],
                    'subtotal'            => $item['price'] * $item['qty'],
                ]);
            }

            DB::commit();

            // Redirect ke halaman cetak struk
            return redirect()->route('transactions.show', $transaction->id)->with('success', 'Transaksi Berhasil!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Transaksi Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan Struk
     */
    public function show($id)
    {
        $transaction = Transaction::with(['details.product', 'details.unit'])->findOrFail($id);
        return view('transactions.print', compact('transaction'));
    }
}