<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileWargaController extends Controller
{
    /**
     * Tampilkan halaman edit profil warga
     * Pengganti method me() di API, karena data user langsung ditarik via Auth::user() di view
     */
    public function edit()
    {
        $user = Auth::user();
        return view('warga.profile.edit', compact('user'));
    }

    /**
     * Update data profil warga (Gabungan Web & API)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. VALIDASI GABUNGAN (Pake rules ketat dari API + kelengkapan field dari Web)
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'nik'           => 'required|string|digits:16|unique:users,nik,' . $user->id,
            'email'         => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_hp'         => 'required|regex:/^[0-9]{10,15}$/',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tgl_lahir'     => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'agama'         => 'nullable|string|max:20',
            'pekerjaan'     => 'nullable|string|max:100',
            'alamat'        => 'required|string|max:500',
            'password'      => 'nullable|string|min:8|confirmed',
        ], [
            // Custom Message warisan dari API lu biar user friendly
            'name.required'        => 'Nama lengkap wajib diisi.',
            'nik.required'         => 'NIK wajib diisi.',
            'nik.digits'           => 'NIK harus tepat 16 digit angka.',
            'nik.unique'           => 'NIK sudah terdaftar di sistem.',
            'email.required'       => 'Email wajib diisi.',
            'email.email'          => 'Format email tidak valid.',
            'email.unique'         => 'Email sudah digunakan oleh akun lain.',
            'email.max'            => 'Email terlalu panjang.',
            'no_hp.required'       => 'Nomor HP wajib diisi.',
            'no_hp.regex'          => 'Nomor HP harus berupa angka 10-15 digit.',
            'alamat.required'      => 'Alamat wajib diisi.',
            'alamat.max'           => 'Alamat terlalu panjang (maks 500 karakter).',
            'password.min'         => 'Password baru minimal berukuran 8 karakter.',
            'password.confirmed'   => 'Konfirmasi password baru tidak cocok.',
        ]);

        try {
            // 2. MAPPING DATA YANG DIUPDATE
            $user->name          = $request->name;
            $user->nik           = $request->nik;
            $user->email         = $request->email;
            $user->no_hp         = $request->no_hp;
            $user->tempat_lahir  = $request->tempat_lahir;
            $user->tgl_lahir     = $request->tgl_lahir;
            $user->jenis_kelamin = $request->jenis_kelamin;
            $user->agama         = $request->agama;
            $user->pekerjaan     = $request->pekerjaan;
            $user->alamat        = $request->alamat;

            // 3. HANDLE UPDATE PASSWORD (Hanya jika form password diisi)
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // 4. REDIRECT KHAS WEB (Bawa session flash success)
            return redirect()->route('warga.profile.edit')
                ->with('success', 'Profil lu berhasil diperbarui brok!');

        } catch (\Throwable $e) {
            // Safe guard kalo ada kendala di database query
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }
}