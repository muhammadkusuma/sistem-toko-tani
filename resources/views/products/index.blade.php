@extends('layouts.app')

@section('content')
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-700">Daftar Produk</h2>

            <div class="flex space-x-2 mt-4 md:mt-0">
                <form action="{{ route('products.index') }}" method="GET" class="flex">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="border border-gray-300 rounded-l-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        placeholder="Cari Kode / Nama...">
                    <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-r-md hover:bg-emerald-700">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </form>

                <a href="{{ route('products.create') }}"
                    class="bg-emerald-600 text-white px-4 py-2 rounded-md hover:bg-emerald-700 transition">
                    <i class="fa-solid fa-plus"></i> Tambah
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6">Kode</th>
                        <th class="py-3 px-6">Nama Produk</th>
                        <th class="py-3 px-6">Kategori</th>
                        <th class="py-3 px-6 text-center">Stok (Base)</th>
                        <th class="py-3 px-6 text-right">Harga Dasar</th>
                        <th class="py-3 px-6 text-center">Satuan Lain</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                    @forelse($products as $product)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-6 font-medium">{{ $product->code }}</td>
                            <td class="py-3 px-6">{{ $product->name }}</td>
                            <td class="py-3 px-6">
                                <span class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs">
                                    {{ $product->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center font-bold">
                                {{ number_format($product->stock + 0, 2) }} {{ $product->base_unit }}
                            </td>
                            <td class="py-3 px-6 text-right">
                                Rp
                                {{ number_format($product->units->where('is_base', 1)->first()->price ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                <span class="text-xs text-gray-500">
                                    {{ $product->units->count() - 1 }} Varian
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center space-x-2">
                                    <a href="{{ route('products.edit', $product->id) }}"
                                        class="text-blue-500 hover:text-blue-700">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-400">Belum ada data produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
@endsection
