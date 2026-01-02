<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Default tanggal: Hari ini
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));

        // Query Transaksi berdasarkan tanggal
        $transactions = Transaction::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->with('user') // Load relasi user/kasir
            ->latest()
            ->get();

        // Hitung Ringkasan
        $totalOmset = $transactions->sum('total_amount');
        $totalTransaksi = $transactions->count();

        return view('reports.index', compact('transactions', 'startDate', 'endDate', 'totalOmset', 'totalTransaksi'));
    }
}