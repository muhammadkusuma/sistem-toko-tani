<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->invoice_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            /* Font struk */
            background-color: #f3f4f6;
        }

        .ticket {
            width: 300px;
            /* Lebar standar kertas thermal 80mm */
            margin: 20px auto;
            background-color: white;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* CSS Khusus saat Print */
        @media print {
            body {
                background-color: white;
            }

            .ticket {
                box-shadow: none;
                margin: 0;
                width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="ticket">
        <div class="text-center mb-4">
            <h1 class="text-xl font-bold">TOKO TANI MAKMUR</h1>
            <p class="text-xs">Jl. Raya Pertanian No. 123</p>
            <p class="text-xs">Telp: 0812-3456-7890</p>
        </div>

        <div class="border-b-2 border-dashed border-gray-400 mb-2"></div>

        <div class="text-xs mb-2">
            <div class="flex justify-between">
                <span>No: {{ $transaction->invoice_no }}</span>
                <span>{{ date('d/m/Y H:i', strtotime($transaction->created_at)) }}</span>
            </div>
            <div>Kasir: {{ $transaction->user->name ?? 'Admin' }}</div>
        </div>

        <div class="border-b-2 border-dashed border-gray-400 mb-2"></div>

        <div class="text-xs mb-4 space-y-2">
            @foreach ($transaction->details as $detail)
                <div>
                    <div class="font-bold">{{ $detail->product->name }}</div>
                    <div class="flex justify-between">
                        <span>
                            {{ $detail->qty + 0 }} {{ $detail->unit->unit_name }} x
                            {{ number_format($detail->price, 0, ',', '.') }}
                        </span>
                        <span class="font-bold">
                            {{ number_format($detail->subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="border-b-2 border-dashed border-gray-400 mb-2"></div>

        <div class="text-sm font-bold">
            <div class="flex justify-between mb-1">
                <span>Total</span>
                <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between mb-1 text-xs font-normal">
                <span>Tunai</span>
                <span>Rp {{ number_format($transaction->cash_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between mb-1 text-xs font-normal">
                <span>Kembali</span>
                <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="border-b-2 border-dashed border-gray-400 my-4"></div>

        <div class="text-center text-xs mb-6">
            <p>Terima Kasih</p>
            <p>Barang yang dibeli tidak dapat ditukar/dikembalikan</p>
        </div>

        <div class="no-print flex flex-col space-y-2">
            <button onclick="window.print()" class="bg-gray-800 text-white py-2 rounded hover:bg-black transition">
                Cetak Struk
            </button>
            <a href="{{ route('transactions.create') }}"
                class="block text-center bg-emerald-600 text-white py-2 rounded hover:bg-emerald-700 transition">
                Transaksi Baru
            </a>
            <a href="{{ route('products.index') }}"
                class="block text-center text-gray-500 text-xs hover:underline mt-2">
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Uncomment baris di bawah jika ingin otomatis print saat halaman dibuka
            // window.print();
        }
    </script>
</body>

</html>
