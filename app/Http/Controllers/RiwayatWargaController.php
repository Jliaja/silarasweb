<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;

class RiwayatWargaController extends Controller
{
    /**
     * 💡 FIX UTAMA: Method khusus untuk handle halaman Dashboard Warga
     */
    public function dashboard()
    {
        try {
            // Ambil semua data permohonan milik warga yang sedang login
            $pengajuan = Pengajuan::where('id_user', Auth::id())->get();

            // Lempar variabel $pengajuan secara resmi ke view warga.dashboard
            return view('warga.dashboard', compact('pengajuan'));

        } catch (\Throwable $e) {
            // Jika ada error, kirim collection kosong biar tidak crash eror 500
            return view('warga.dashboard')->with([
                'pengajuan' => collect([]),
                'error' => 'Gagal memuat statistik: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Halaman Riwayat Tabel Berkas
     */
    public function index()
    {
        try {
            $pengajuan = Pengajuan::where('id_user', Auth::id())
                ->with([
                    'kategori',
                    'riwayatStatus',
                    'suratKeluar.pejabat'
                ])
                ->latest()
                ->get();

            return view('warga.riwayat', compact('pengajuan'));

        } catch (\Throwable $e) {
            return redirect()->route('warga.dashboard')
                ->with('error', 'Gagal memuat riwayat pengajuan: ' . $e->getMessage());
        }
    }
}