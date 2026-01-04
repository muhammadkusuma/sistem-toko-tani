@extends('layouts.app')

@section('content')
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-emerald-500">
            <h3 class="text-gray-500 text-sm">Pendapatan Hari Ini</h3>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
            <h3 class="text-gray-500 text-sm">Total Pendapatan (Semua)</h3>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-orange-500">
            <h3 class="text-gray-500 text-sm">Total Transaksi</h3>
            <p class="text-2xl font-bold text-gray-800">{{ $totalTransactions }} Transaksi</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-700 mb-4">Riwayat Transaksi</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal">
                        <th class="py-3 px-6">No Invoice</th>
                        <th class="py-3 px-6">Tanggal</th>
                        <th class="py-3 px-6">Kasir</th>
                        <th class="py-3 px-6 text-right">Total Belanja</th>
                        <th class="py-3 px-6 text-right">Tunai</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                    @forelse($transactions as $trx)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-6 font-medium text-emerald-600">{{ $trx->invoice_no }}</td>
                            <td class="py-3 px-6">{{ $trx->created_at->format('d M Y H:i') }}</td>
                            <td class="py-3 px-6">{{ $trx->user->name ?? 'Umum' }}</td>
                            <td class="py-3 px-6 text-right font-bold">Rp
                                {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-6 text-right">Rp {{ number_format($trx->cash_amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-6 text-center">
                                <a href="{{ route('transactions.show', $trx->id) }}" target="_blank"
                                    class="bg-gray-200 text-gray-700 py-1 px-3 rounded text-xs hover:bg-gray-300">
                                    <i class="fa-solid fa-print"></i> Struk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-400">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
