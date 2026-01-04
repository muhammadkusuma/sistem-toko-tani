@extends('layouts.app')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-gray-700">Laporan Penjualan</h2>
        
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap gap-2 items-end">
            <div>
                <label class="text-xs font-bold text-gray-500">Dari</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="block border rounded px-2 py-1 text-sm">
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500">Sampai</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="block border rounded px-2 py-1 text-sm">
            </div>
            <button type="submit" class="bg-emerald-600 text-white px-3 py-1.5 rounded text-sm hover:bg-emerald-700">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            
            <a href="{{ route('reports.print', request()->all()) }}" target="_blank" class="bg-gray-700 text-white px-3 py-1.5 rounded text-sm hover:bg-gray-800 ml-2">
                <i class="fa-solid fa-print"></i> Cetak PDF
            </a>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-emerald-500">
            <h3 class="text-gray-500 text-sm">Total Omzet (Periode Ini)</h3>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
            <h3 class="text-gray-500 text-sm">Total Transaksi</h3>
            <p class="text-2xl font-bold text-gray-800">{{ $totalTransactions }} Transaksi</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Grafik Tren Penjualan</h3>
        <div class="h-64">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="font-bold text-gray-700 mb-4">Rincian Transaksi</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal">
                        <th class="py-3 px-4">No Invoice</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Kasir</th>
                        <th class="py-3 px-4 text-right">Total</th>
                        <th class="py-3 px-4 text-center">Opsi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                    @forelse($transactions as $trx)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-2 px-4 font-medium text-emerald-600">{{ $trx->invoice_no }}</td>
                            <td class="py-2 px-4">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2 px-4">{{ $trx->user->name ?? 'Umum' }}</td>
                            <td class="py-2 px-4 text-right font-bold">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                            <td class="py-2 px-4 text-center">
                                <a href="{{ route('transactions.show', $trx->id) }}" target="_blank" class="text-blue-500 hover:text-blue-700 text-xs">
                                    Lihat Struk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-400">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $transactions->withQueryString()->links() }}
        </div>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line', // Jenis Grafik: Garis
            data: {
                labels: {!! $labels !!}, // Data Tanggal dari Controller
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! $data !!}, // Data Uang dari Controller
                    borderColor: '#10b981', // Warna Emerald
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3 // Garis agak melengkung
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection