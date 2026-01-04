<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Halaman Kasir (Point of Sales)
     */
    public function create()
    {
        $products = Product::with('units')->where('stock', '>', 0)->get();
        return view('transactions.create', compact('products'));
    }

    /**
     * Proses Simpan Transaksi & Potong Stok
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'cart'        => 'required', // Jangan pakai 'array' karena formatnya JSON String
            'total_amount'=> 'required|numeric',
            'cash_amount' => 'required|numeric|gte:total_amount',
        ]);

        // 2. Decode Data Keranjang dari JSON ke Array PHP
        $cartData = json_decode($request->cart, true);

        // Cek validitas data keranjang
        if (!$cartData || !is_array($cartData) || count($cartData) < 1) {
            return back()->with('error', 'Keranjang belanja kosong atau data error.');
        }

        DB::beginTransaction();
        try {
            // 3. Buat Header Transaksi
            $transaction = Transaction::create([
                'user_id'       => Auth::id() ?? 1,
                'invoice_no'    => 'INV-' . date('YmdHis') . '-' . rand(100, 999),
                'total_amount'  => $request->total_amount,
                'cash_amount'   => $request->cash_amount,
                'change_amount' => $request->cash_amount - $request->total_amount,
            ]);

            // 4. Proses Setiap Item & POTONG STOK
            foreach ($cartData as $item) {
                // Ambil data produk & unit terbaru dari DB untuk keamanan
                $product = Product::find($item['product_id']);
                $unit    = ProductUnit::find($item['unit_id']);

                if (!$product || !$unit) continue;

                // Hitung total quantity dalam satuan dasar (Base Unit)
                // Contoh: Beli 2 Karung (1 karung = 50kg) => 2 * 50 = 100kg
                $qtyDeduction = $item['qty'] * $unit->conversion_factor;

                // Cek Stok Cukup?
                if ($product->stock < $qtyDeduction) {
                    throw new \Exception("Stok {$product->name} tidak cukup! (Sisa: {$product->stock} {$product->base_unit})");
                }

                // Kurangi Stok
                $product->stock = $product->stock - $qtyDeduction;
                $product->save();

                // Simpan Detail Transaksi
                TransactionDetail::create([
                    'transaction_id'      => $transaction->id,
                    'product_id'          => $product->id,
                    'product_unit_id'     => $unit->id,
                    'qty'                 => $item['qty'],
                    'price'               => $unit->price,
                    'conversion_snapshot' => $unit->conversion_factor,
                    'subtotal'            => $unit->price * $item['qty'],
                ]);
            }

            DB::commit();

            // Redirect ke halaman cetak struk
            return redirect()->route('transactions.show', $transaction->id);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Transaksi Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan Struk Belanja
     */
    public function show($id)
    {
        $transaction = Transaction::with(['user', 'details.product', 'details.unit'])->findOrFail($id);
        return view('transactions.print', compact('transaction'));
    }
}