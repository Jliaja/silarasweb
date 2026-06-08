<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    // ================= REGISTER =================
    public function register(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'nik'           => 'required|digits:16|unique:users,nik',
            'email'         => 'required|email|unique:users,email',
            'no_hp'         => 'required|min:10|max:15',
            'alamat'        => 'required',
            'tempat_lahir'  => 'required',
            'tgl_lahir'     => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama'         => 'required',
            'pekerjaan'     => 'required',
            'password'      => 'required|min:6|confirmed', // Gua tambahin confirmed biar standar form web aman
        ], [
            'nik.digits'   => 'NIK harus berupa 16 digit angka.',
            'nik.unique'   => 'NIK sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        try {
            $user = User::create([
                'name'          => $request->name,
                'nik'           => $request->nik,
                'email'         => $request->email,
                'no_hp'         => $request->no_hp,
                'alamat'        => $request->alamat,
                'tempat_lahir'  => $request->tempat_lahir,
                'tgl_lahir'     => $request->tgl_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama'         => $request->agama,
                'pekerjaan'     => $request->pekerjaan,
                'password'      => Hash::make($request->password),
                'role'          => 'warga',
            ]);

            event(new Registered($user));

            // Otomatis loginin user setelah register khas aplikasi web
            Auth::login($user);

            return redirect()->route('warga.dashboard')->with('success', 'Register berhasil! Selamat datang brok.');

        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal register: ' . $e->getMessage());
        }
    }

    // ================= VERIFY EMAIL =================
    public function verifyEmail(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('info', 'Email sudah diverifikasi sebelumnya.');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi!');
    }

    // ================= CHANGE PASSWORD =================
    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed'
        ], [
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.'
        ]);

        try {
            $user = $request->user();
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return redirect()->back()->with('success', 'Password berhasil diubah!');
            
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengubah password: ' . $e->getMessage());
        }
    }

    // ================= FORGOT PASSWORD =================
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return redirect()->back()->with('success', __($status));
            }

            return redirect()->back()->withInput()->with('error', __($status));

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Sistem error: ' . $e->getMessage());
        }
    }

    // ================= RESET PASSWORD =================
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'token'    => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            $status = Password::reset(
                $request->only('email', 'password', 'token'),
                function ($user, $password) {
                    $user->update([
                        'password' => Hash::make($password)
                    ]);
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return redirect()->route('login')->with('success', __($status));
            }

            return redirect()->back()->withInput()->with('error', __($status));

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal reset password: ' . $e->getMessage());
        }
    }

    // ================= CHECK EMAIL (DIPERBAIKIN BIAR PINDAH FORM) =================
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Email tidak ditemukan di sistem kami.');
        }

        // Trick Sakti: Mengirim flag 'step_dua' dan nyimpan data email terverifikasi ke session flash
        return redirect()->back()->with([
            'success'        => 'Email terdaftar! Silakan isi sandi baru lu dibawah brok.',
            'step_dua'       => true,
            'verified_email' => $request->email
        ]);
    }

    // ================= DIRECT RESET PASSWORD =================
    public function directResetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return redirect()->back()->withInput()->with('error', 'Email tidak ditemukan.');
            }

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return redirect()->route('login')->with('success', 'Password berhasil diubah secara langsung!');

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal eksekusi direct reset: ' . $e->getMessage());
        }
    }
}