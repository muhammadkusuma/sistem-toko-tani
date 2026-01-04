@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-700 mb-6">Tambah User Baru</h2>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2 focus:ring-emerald-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" class="w-full border rounded px-3 py-2 focus:ring-emerald-500"
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2 focus:ring-emerald-500"
                    required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Role (Jabatan)</label>
                <select name="role" class="w-full border rounded px-3 py-2 bg-white">
                    <option value="cashier">Kasir (Hanya Transaksi)</option>
                    <option value="admin">Admin (Kelola Produk)</option>
                    <option value="owner">Owner (Akses Penuh)</option>
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('users.index') }}" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</a>
                <button type="submit"
                    class="bg-emerald-600 text-white px-6 py-2 rounded hover:bg-emerald-700 font-bold">Simpan</button>
            </div>
        </form>
    </div>
@endsection
