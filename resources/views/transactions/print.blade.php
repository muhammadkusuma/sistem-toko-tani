<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->invoice_no }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .bold {
            font-weight: bold;
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
        <h2 style="margin:0;">TOKO TANI MAKMUR</h2>
        <p style="margin:0;">Jl. Raya Pertanian No. 123</p>
        <p style="margin:5px 0;">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
        <p style="margin:0;">No: {{ $transaction->invoice_no }}</p>
        <p style="margin:0;">Kasir: {{ $transaction->user->name ?? 'Umum' }}</p>
    </div>

    <div class="items">
        @foreach ($transaction->details as $detail)
            <div style="margin-bottom: 2px;">
                <strong>{{ $detail->product->name }}</strong>
            </div>
            <div class="item">
                <span>{{ $detail->qty + 0 }} {{ $detail->unit->unit_name }} x
                    {{ number_format($detail->price, 0) }}</span>
                <span>{{ number_format($detail->subtotal, 0) }}</span>
            </div>
        @endforeach
    </div>

    <div style="border-top: 1px dashed #000; margin: 10px 0;"></div>

    <div class="item bold">
        <span>TOTAL</span>
        <span>{{ number_format($transaction->total_amount, 0) }}</span>
    </div>
    <div class="item">
        <span>TUNAI</span>
        <span>{{ number_format($transaction->cash_amount, 0) }}</span>
    </div>
    <div class="item">
        <span>KEMBALI</span>
        <span>{{ number_format($transaction->change_amount, 0) }}</span>
    </div>

    <div class="footer">
        <p>Terima Kasih & Selamat Menanam!</p>
        <p>Barang yang dibeli tidak dapat ditukar.</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <a href="{{ route('transactions.create') }}"
            style="background: #000; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 5px;">Kembali
            ke Kasir</a>
    </div>

</body>

</html>
