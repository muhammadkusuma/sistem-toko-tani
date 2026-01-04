<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Tampilkan Form Login
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Proses Login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect Cerdas: Arahkan sesuai peran masing-masing
            $role = Auth::user()->role;

            if ($role === 'cashier') {
                return redirect()->route('transactions.create'); // Kasir -> Langsung Jualan
            } elseif ($role === 'owner') {
                return redirect()->route('reports.index'); // Bos -> Langsung Laporan
            }

            // Default (Admin) -> Ke Data Produk
            return redirect()->route('products.index');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
