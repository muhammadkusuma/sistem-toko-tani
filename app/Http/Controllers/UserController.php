<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan semua user (Hanya Karyawan/Kasir & Admin)
     * Owner tidak perlu diedit di sini untuk keamanan.
     */
    public function index()
    {
        // Ambil semua user kecuali diri sendiri (yang sedang login)
        $users = User::where('id', '!=', auth()->id())->latest()->get();
        return view('users.index', compact('users'));
    }

    /**
     * Form Tambah User
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Simpan User Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,cashier,owner',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password), // Enkripsi password
            'role'     => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Form Edit User
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update User
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'  => 'required|in:admin,cashier,owner',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        // Update password HANYA jika diisi
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data user diperbarui!');
    }

    /**
     * Hapus User
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User telah dihapus.');
    }
}
