<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .right {
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <h2>LAPORAN PENJUALAN TOKO TANI</h2>
        <p>Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 20%">No Invoice</th>
                <th style="width: 15%">Kasir</th>
                <th style="width: 25%">Total Belanja</th>
                <th style="width: 20%">Tunai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $index => $trx)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $trx->invoice_no }}</td>
                    <td>{{ $trx->user->name ?? 'Umum' }}</td>
                    <td class="right">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($trx->cash_amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="right">TOTAL PENDAPATAN</th>
                <th class="right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; text-align: right;">
        <p>Dicetak pada: {{ date('d/m/Y H:i') }}</p>
        <p>Oleh: {{ auth()->user()->name }}</p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Cetak Laporan</button>
    </div>

</body>

</html>
