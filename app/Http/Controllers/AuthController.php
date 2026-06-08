<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Handle proses login via Web Session & Routing berdasarkan Role Middleware
     */
    public function login(Request $request)
    {
        // 1. Validasi input form login dari view web
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email wajib diisi brok!',
            'email.email'       => 'Format email kagak valid.',
            'password.required' => 'Password-nya jangan lupa diisi.',
        ]);

        // 2. Proses Autentikasi menggunakan Session (menggantikan createToken API)
        if (!Auth::attempt($credentials, $request->filled('remember'))) {
            // Kalo gagal, balikin ke halaman login bawa error flash session
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->with('error', 'Login gagal! Email atau password lu salah.');
        }

        // 3. Jika sukses, amankan session dengan regenerate id
        $request->session()->regenerate();

        // 4. AMBIL DATA USER & AKALIN REDIRECT BERDASARKAN ROLE
        // Ini bakal nge-trigger rute yang udah dijagain sama RoleMiddleware lu
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Langsung lempar ke rute dashboard admin (bakal dicek oleh middleware 'role:admin')
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang kembali Admin Gacor!');
        } 
        
        if ($user->role === 'warga') {
            // Langsung lempar ke rute dashboard warga (bakal dicek oleh middleware 'role:warga')
            return redirect()->intended(route('warga.dashboard'))
                ->with('success', 'Login berhasil! Halo Warga.');
        }

        // Antisipasi kalo ada role antah berantah di database lu
        Auth::logout();
        return redirect()->route('login')->with('error', 'Role user lu kagak dikenali sistem.');
    }

    /**
     * Handle proses logout via Web Session
     */
    public function logout(Request $request)
    {
        // Logout dari guard session web bawaan laravel
        Auth::logout();

        // Bersihin dan hancurin session cookie di browser
        $request->session()->invalidate();

        // Bikin ulang token CSRF baru biar form login aman dari serangan
        $request->session()->regenerateToken();

        // Balikin ke root login bawa pesan sukses
        return redirect('/')
            ->with('success', 'Logout berhasil, ditunggu baliknya lagi brok!');
    }
}