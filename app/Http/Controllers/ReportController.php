<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Tampilkan Halaman Laporan & Grafik
     */
    public function index(Request $request)
    {
        // 1. Filter Tanggal (Default: Bulan Ini)
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate   = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // 2. Query Transaksi Berdasarkan Filter
        $query = Transaction::with('user')
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Hitung Ringkasan
        $totalRevenue      = $query->sum('total_amount');
        $totalTransactions = $query->count();

        // Data Tabel (Paginate)
        // Kita clone query agar tidak merusak perhitungan sum/count di atas
        $transactions = (clone $query)->latest()->paginate(20);

        // 3. Siapkan Data Grafik (Trend Harian)
        // Group by Tanggal -> Sum Total
        $chartData = Transaction::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Format Data untuk Chart.js
        $labels = $chartData->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d M'));
        $data   = $chartData->pluck('total');

        return view('reports.index', compact(
            'transactions',
            'totalRevenue',
            'totalTransactions',
            'startDate',
            'endDate',
            'labels',
            'data'
        ));
    }

    /**
     * Cetak Laporan (Mode Print Browser yang Bersih)
     */
    public function print(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate   = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $transactions = Transaction::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get(); // Ambil semua (tanpa paging)

        $totalRevenue = $transactions->sum('total_amount');

        return view('reports.print', compact('transactions', 'totalRevenue', 'startDate', 'endDate'));
    }
}
