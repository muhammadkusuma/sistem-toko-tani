<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->invoice_no }}</title>
    <style>
        /* Pengaturan Kertas (PENTING) */
        @page {
            size: 58mm auto; /* Lebar 58mm, Tinggi menyesuaikan */
            margin: 0;       /* Hapus margin browser */
        }

        /* Tampilan Dasar */
        body {
            font-family: 'Courier New', Courier, monospace; /* Font struk jadul */
            font-size: 10px;    /* Ukuran font standar struk */
            width: 58mm;        /* Paksa lebar body */
            margin: 0;          /* Reset margin */
            padding: 5px;       /* Sedikit jarak aman */
            background: #fff;
            color: #000;
        }

        /* Reset Heading */
        h2, p, h3 {
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }

        /* Garis Pemisah Putus-putus */
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 5px 0;
            width: 100%;
        }

        /* Layout Flexbox untuk Rata Kanan-Kiri */
        .flex-row {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        /* Helper Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mt-1 { margin-top: 5px; }
        .mb-1 { margin-bottom: 5px; }

        /* Sembunyikan elemen layar saat diprint */
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; width: 100%; }
        }
    </style>
</head>
<body>

    <div class="text-center mb-1">
        <h2 style="font-size: 14px; font-weight: bold;">TOKO TANI MAKMUR</h2>
        <p>Jl. Raya Pertanian No. 123</p>
        <p>Telp: 0812-3456-7890</p>
    </div>

    <div class="dashed-line"></div>

    <div>
        <div class="flex-row">
            <span>Tgl:</span>
            <span>{{ $transaction->created_at->format('d/m/y H:i') }}</span>
        </div>
        <div class="flex-row">
            <span>No:</span>
            <span>#{{ substr($transaction->invoice_no, -6) }}</span> </div>
        <div class="flex-row">
            <span>Kasir:</span>
            <span>{{ substr($transaction->user->name ?? 'Umum', 0, 10) }}</span>
        </div>
    </div>

    <div class="dashed-line"></div>

    @foreach($transaction->details as $detail)
        <div class="mb-1">
            <div class="font-bold">{{ $detail->product->name }}</div>
            <div class="flex-row">
                <span>{{ $detail->qty + 0 }} {{ $detail->unit->unit_name ?? '' }} x {{ number_format($detail->price, 0, ',', '.') }}</span>
                <span>{{ number_format($detail->subtotal, 0, ',', '.') }}</span>
            </div>
        </div>
    @endforeach

    <div class="dashed-line"></div>

    <div class="flex-row font-bold" style="font-size: 12px;">
        <span>TOTAL</span>
        <span>{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
    </div>
    <div class="flex-row">
        <span>TUNAI</span>
        <span>{{ number_format($transaction->cash_amount, 0, ',', '.') }}</span>
    </div>
    <div class="flex-row">
        <span>KEMBALI</span>
        <span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
    </div>

    <div class="dashed-line"></div>

    <div class="text-center mt-1" style="font-size: 9px;">
        <p>Terima Kasih & Selamat Menanam!</p>
        <p>Barang yg dibeli tdk dpt ditukar</p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center; border-top: 2px solid #ddd; padding-top: 10px;">
        <p class="mb-1 text-gray-500">Tips: Pilih kertas "58mm" di pengaturan print & matikan "Header/Footer".</p>
        
        <button onclick="window.print()" style="padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 5px; cursor: pointer;">
            🖨️ Print Struk
        </button>
        <br><br>
        <a href="{{ route('transactions.create') }}" style="text-decoration: none; color: #666;">
            ← Kembali ke Kasir
        </a>
    </div>

    <script>
        // Auto Print saat halaman dimuat
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500); // Delay sedikit biar CSS load dulu
        }
    </script>
</body>
</html>