<?php
namespace App\Http\Controllers;

use App\Models\Transaction;

class ReportController extends Controller
{
    public function index()
    {
        // Ambil data transaksi urut dari yang terbaru
        $transactions = Transaction::with('user')->latest()->paginate(20);

        // Hitung Ringkasan Pendapatan
        $totalRevenue      = Transaction::sum('total_amount');
        $todayRevenue      = Transaction::whereDate('created_at', today())->sum('total_amount');
        $totalTransactions = Transaction::count();

        return view('reports.index', compact('transactions', 'totalRevenue', 'todayRevenue', 'totalTransactions'));
    }
}
