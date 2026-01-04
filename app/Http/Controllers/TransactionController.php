<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Halaman Kasir (Point of Sales)
     */
    public function create()
    {
        // Ambil semua produk beserta satuannya untuk dipilih di kasir
        // Kita load 'units' agar kasir bisa memilih beli Eceran atau Grosir
        $products = Product::with('units')->where('stock', '>', 0)->get();

        return view('transactions.create', compact('products'));
    }

    /**
     * Proses Simpan Transaksi & Potong Stok
     */
    public function store(Request $request)
    {
        $request->validate([
            'cart'         => 'required|array', // Data keranjang dikirim sebagai array JSON
            'total_amount' => 'required|numeric',
            'cash_amount'  => 'required|numeric|gte:total_amount', // Uang tunai >= Total
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat Header Transaksi
            $transaction = Transaction::create([
                'user_id'       => Auth::id() ?? 1, // Jika belum login pakai user ID 1 (dummy)
                'invoice_no'    => 'INV-' . date('YmdHis') . '-' . rand(100, 999),
                'total_amount'  => $request->total_amount,
                'cash_amount'   => $request->cash_amount,
                'change_amount' => $request->cash_amount - $request->total_amount,
            ]);

            // 2. Proses Setiap Item di Keranjang
            foreach ($request->cart as $item) {
                // Ambil data unit yang dipilih
                $unit    = ProductUnit::find($item['unit_id']);
                $product = Product::find($item['product_id']);

                if (! $unit || ! $product) {
                    continue;
                }

                // Hitung Subtotal
                $subtotal = $unit->price * $item['qty'];

                // Simpan Detail Transaksi
                TransactionDetail::create([
                    'transaction_id'      => $transaction->id,
                    'product_id'          => $product->id,
                    'product_unit_id'     => $unit->id,
                    'qty'                 => $item['qty'],
                    'price'               => $unit->price,
                    'conversion_snapshot' => $unit->conversion_factor,
                    'subtotal'            => $subtotal,
                ]);

                // 3. POTONG STOK
                // Rumus: Stok Berkurang = Jumlah Beli * Faktor Konversi Unit
                // Contoh: Beli 2 Karung (1 karung = 50kg). Maka stok berkurang 100kg.
                $qtyDeduction   = $item['qty'] * $unit->conversion_factor;
                $product->stock = $product->stock - $qtyDeduction;
                $product->save();
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
