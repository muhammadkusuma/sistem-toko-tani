@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-700 mb-6">Edit User: {{ $user->name }}</h2>

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ $user->name }}"
                    class="w-full border rounded px-3 py-2 focus:ring-emerald-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" value="{{ $user->email }}"
                    class="w-full border rounded px-3 py-2 focus:ring-emerald-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password Baru <span
                        class="text-gray-400 font-normal">(Kosongkan jika tidak ingin ubah)</span></label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2 focus:ring-emerald-500">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Role (Jabatan)</label>
                <select name="role" class="w-full border rounded px-3 py-2 bg-white">
                    <option value="cashier" {{ $user->role == 'cashier' ? 'selected' : '' }}>Kasir</option>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="owner" {{ $user->role == 'owner' ? 'selected' : '' }}>Owner</option>
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('users.index') }}" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</a>
                <button type="submit"
                    class="bg-emerald-600 text-white px-6 py-2 rounded hover:bg-emerald-700 font-bold">Update</button>
            </div>
        </form>
    </div>
@endsection
