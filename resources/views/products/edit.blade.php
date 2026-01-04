@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-700">Edit Produk</h2>
            <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="{{ route('products.update', $product->id) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
            @csrf
            @method('PUT')

            <h3 class="text-lg font-semibold text-emerald-700 border-b pb-2 mb-4">Informasi Dasar</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Kode Barcode / SKU</label>
                    <input type="text" value="{{ $product->code }}" disabled
                        class="w-full border border-gray-200 bg-gray-100 rounded-md px-3 py-2 text-gray-500">
                    <p class="text-xs text-gray-400 mt-1">*Kode tidak bisa diubah</p>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Produk</label>
                    <input type="text" name="name" value="{{ $product->name }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-emerald-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-emerald-700 border-b pb-2 mb-4">Stok & Harga Dasar
                ({{ $product->base_unit }})</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Stok Saat Ini
                        ({{ $product->base_unit }})</label>
                    <input type="number" step="0.01" name="stock" value="{{ $product->stock + 0 }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Harga Beli (HPP)</label>
                    <input type="number" name="buy_price" value="{{ $product->buy_price + 0 }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Harga Jual
                        ({{ $product->base_unit }})</label>
                    <input type="number" name="sell_price"
                        value="{{ $product->units->where('is_base', 1)->first()->price ?? 0 }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                <h3 class="text-lg font-semibold text-emerald-700 mb-2">Satuan Lain</h3>
                <div id="unit-container">
                    {{-- Loop unit yang BUKAN base --}}
                    @foreach ($product->units->where('is_base', 0) as $index => $unit)
                        <div class="grid grid-cols-1 md:grid-cols-7 gap-4 mb-3 items-end p-3 bg-white border rounded shadow-sm"
                            id="row-old-{{ $index }}">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 mb-1">Nama Satuan</label>
                                <input type="text" name="more_units[{{ $index }}][name]"
                                    value="{{ $unit->unit_name }}" class="w-full border rounded px-2 py-1 text-sm"
                                    required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 mb-1">Isi
                                    ({{ $product->base_unit }})</label>
                                <input type="number" step="0.01" name="more_units[{{ $index }}][factor]"
                                    value="{{ $unit->conversion_factor + 0 }}"
                                    class="w-full border rounded px-2 py-1 text-sm" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 mb-1">Harga Jual</label>
                                <input type="number" name="more_units[{{ $index }}][price]"
                                    value="{{ $unit->price + 0 }}" class="w-full border rounded px-2 py-1 text-sm"
                                    required>
                            </div>
                            <div class="md:col-span-1 text-right">
                                <button type="button"
                                    onclick="document.getElementById('row-old-{{ $index }}').remove()"
                                    class="text-red-500 hover:text-red-700 bg-red-50 p-2 rounded">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" onclick="addUnitRow()"
                    class="mt-2 text-sm bg-emerald-100 text-emerald-700 px-3 py-2 rounded hover:bg-emerald-200">
                    <i class="fa-solid fa-plus-circle"></i> Tambah Satuan
                </button>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="bg-emerald-600 text-white font-bold py-2 px-6 rounded hover:bg-emerald-700 shadow-lg">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        // Mulai counter dari angka besar supaya tidak bentrok dengan index loop PHP
        let rowCount = 1000;

        function addUnitRow() {
            const container = document.getElementById('unit-container');
            const baseUnit = "{{ $product->base_unit }}";

            const html = `
            <div class="grid grid-cols-1 md:grid-cols-7 gap-4 mb-3 items-end p-3 bg-white border rounded shadow-sm" id="row-${rowCount}">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1">Nama Satuan Baru</label>
                    <input type="text" name="more_units[${rowCount}][name]" placeholder="Cth: Dus" class="w-full border rounded px-2 py-1 text-sm" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1">Isi Berapa ${baseUnit}?</label>
                    <input type="number" step="0.01" name="more_units[${rowCount}][factor]" placeholder="10" class="w-full border rounded px-2 py-1 text-sm" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1">Harga Jual</label>
                    <input type="number" name="more_units[${rowCount}][price]" placeholder="Rp..." class="w-full border rounded px-2 py-1 text-sm" required>
                </div>
                <div class="md:col-span-1 text-right">
                    <button type="button" onclick="document.getElementById('row-${rowCount}').remove()" class="text-red-500 hover:text-red-700 bg-red-50 p-2 rounded">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            rowCount++;
        }
    </script>
@endsection
