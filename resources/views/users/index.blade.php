@extends('layouts.app')

@section('content')
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-700">Manajemen Pengguna</h2>
            <a href="{{ route('users.create') }}"
                class="bg-emerald-600 text-white px-4 py-2 rounded-md text-sm hover:bg-emerald-700">
                <i class="fa-solid fa-user-plus mr-2"></i> Tambah User
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <th class="py-3 px-4">Nama</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                    @foreach ($users as $user)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-4 font-bold">{{ $user->name }}</td>
                            <td class="py-3 px-4">{{ $user->email }}</td>
                            <td class="py-3 px-4">
                                <span
                                    class="px-2 py-1 rounded text-xs font-bold
                            {{ $user->role == 'admin'
                                ? 'bg-blue-100 text-blue-700'
                                : ($user->role == 'owner'
                                    ? 'bg-purple-100 text-purple-700'
                                    : 'bg-emerald-100 text-emerald-700') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}"
                                        class="text-yellow-500 hover:text-yellow-700">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus user ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
