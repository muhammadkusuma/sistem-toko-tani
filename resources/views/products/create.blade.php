@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-700">Tambah Produk Baru</h2>
            <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="{{ route('products.store') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
            @csrf

            <h3 class="text-lg font-semibold text-emerald-700 border-b pb-2 mb-4">Informasi Dasar</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Kode Barcode / SKU</label>
                    <input type="text" name="code"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required placeholder="Contoh: 899123456">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Produk</label>
                    <input type="text" name="name"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required placeholder="Contoh: Pupuk NPK Mutiara">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-emerald-700 border-b pb-2 mb-4">Stok & Harga Dasar</h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="md:col-span-1">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Satuan Terkecil</label>
                    <input type="text" name="base_unit" id="base_unit_input"
                        class="w-full border border-gray-300 rounded-md px-3 py-2" required placeholder="kg/pcs/ltr">
                    <p class="text-xs text-gray-500 mt-1">*Satuan dasar stok</p>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Stok Awal</label>
                    <input type="number" step="0.01" name="stock"
                        class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Harga Beli (HPP)</label>
                    <input type="number" name="buy_price" class="w-full border border-gray-300 rounded-md px-3 py-2"
                        required>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Harga Jual (Ecer)</label>
                    <input type="number" name="sell_price" class="w-full border border-gray-300 rounded-md px-3 py-2"
                        required>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                <h3 class="text-lg font-semibold text-emerald-700 mb-2">Satuan Lain (Opsional)</h3>
                <p class="text-sm text-gray-600 mb-4">Tambahkan jika barang ini dijual dalam satuan lebih besar (Contoh:
                    Karung, Dus).</p>

                <div id="unit-container">
                </div>

                <button type="button" onclick="addUnitRow()"
                    class="mt-2 text-sm bg-emerald-100 text-emerald-700 px-3 py-2 rounded hover:bg-emerald-200">
                    <i class="fa-solid fa-plus-circle"></i> Tambah Satuan Lain
                </button>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="bg-emerald-600 text-white font-bold py-2 px-6 rounded hover:bg-emerald-700 shadow-lg transform hover:-translate-y-0.5 transition">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>

    <script>
        let rowCount = 0;

        function addUnitRow() {
            const container = document.getElementById('unit-container');
            const baseUnit = document.getElementById('base_unit_input').value || 'Satuan Dasar';

            const html = `
        <div class="grid grid-cols-1 md:grid-cols-7 gap-4 mb-3 items-end p-3 bg-white border rounded shadow-sm" id="row-${rowCount}">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">Nama Satuan Besar</label>
                <input type="text" name="more_units[${rowCount}][name]" placeholder="Cth: Karung" class="w-full border rounded px-2 py-1 text-sm" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">Isi Berapa ${baseUnit}?</label>
                <input type="number" step="0.01" name="more_units[${rowCount}][factor]" placeholder="Cth: 50" class="w-full border rounded px-2 py-1 text-sm" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">Harga Jual Satuan Ini</label>
                <input type="number" name="more_units[${rowCount}][price]" placeholder="Rp..." class="w-full border rounded px-2 py-1 text-sm" required>
            </div>
            <div class="md:col-span-1 text-right">
                <button type="button" onclick="removeRow(${rowCount})" class="text-red-500 hover:text-red-700 bg-red-50 p-2 rounded">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
        `;

            // Insert HTML sebelum akhir container
            container.insertAdjacentHTML('beforeend', html);
            rowCount++;
        }

        function removeRow(id) {
            document.getElementById(`row-${id}`).remove();
        }

        // Update label saat user mengetik satuan dasar
        document.getElementById('base_unit_input').addEventListener('input', function(e) {
            // Logika sederhana untuk update teks placeholder realtime (opsional)
        });
    </script>
@endsection
