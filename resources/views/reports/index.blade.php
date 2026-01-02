@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-2xl font-bold text-gray-700 mb-6">Laporan Penjualan</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-emerald-500">
            <div class="text-gray-500 text-sm font-medium">Total Omset</div>
            <div class="text-3xl font-bold text-gray-800">Rp {{ number_format($totalOmset, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500">
            <div class="text-gray-500 text-sm font-medium">Total Transaksi</div>
            <div class="text-3xl font-bold text-gray-800">{{ $totalTransaksi }}</div>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form action="{{ route('reports.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="border rounded px-3 py-2 w-full">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="border rounded px-3 py-2 w-full">
            </div>
            <button type="submit" class="bg-emerald-600 text-white px-6 py-2 rounded hover:bg-emerald-700 transition h-10">
                <i class="fa-solid fa-filter"></i> Tampilkan
            </button>
            
            <button type="button" onclick="window.print()" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition h-10">
                <i class="fa-solid fa-print"></i> Print PDF
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100 text-gray-600 uppercase text-sm">
                <tr>
                    <th class="py-3 px-6">Tanggal & Jam</th>
                    <th class="py-3 px-6">No Invoice</th>
                    <th class="py-3 px-6">Kasir</th>
                    <th class="py-3 px-6 text-right">Total Belanja</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm">
                @forelse($transactions as $trx)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-6">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                    <td class="py-3 px-6 font-medium text-emerald-600">{{ $trx->invoice_no }}</td>
                    <td class="py-3 px-6">{{ $trx->user->name ?? 'Admin' }}</td>
                    <td class="py-3 px-6 text-right font-bold">
                        Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                    </td>
                    <td class="py-3 px-6 text-center">
                        <a href="{{ route('transactions.show', $trx->id) }}" target="_blank" class="text-blue-500 hover:text-blue-700 underline text-xs">
                            Lihat Struk
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-gray-400">Tidak ada transaksi pada tanggal ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    @media print {
        /* Sembunyikan elemen yang tidak perlu saat print laporan */
        nav, footer, form, .no-print {
            display: none !important;
        }
        body { margin: 0; padding: 0; background: white; }
        .container { width: 100%; max-width: 100%; }
        /* Pastikan tabel tercetak rapi */
        table { width: 100%; border: 1px solid #ddd; }
        th, td { border: 1px solid #ddd; padding: 8px; }
    }
</style>
@endsection