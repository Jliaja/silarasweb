<?php

namespace App\Http\Controllers;

use App\Models\KategoriSurat;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengajuanWargaController extends Controller
{
    public function create()
    {
        $kategori = KategoriSurat::all();
        return view('warga.pengajuan.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        // 1. VALIDASI DATA (SINKRON 100% SAMA ATRIBUT BARU LU BROK)
        $request->validate([
            'id_kategori'     => 'required|exists:kategori_surat,id',
            'keperluan'       => 'required|string',
            'no_kk'           => 'nullable|string|max:16',
            'nama_usaha'      => 'nullable|string|max:255',
            'jenis_usaha'     => 'nullable|string|max:255',
            'alamat_usaha'    => 'nullable|string',
            'tahun_berdiri'   => 'nullable|numeric',
            'file_kk'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_pengantar'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_foto_depan' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',
            'file_foto_dalam' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'id_kategori.required' => 'Kategori surat wajib dipilih.',
            'keperluan.required'   => 'Tujuan keperluan surat wajib diisi.',
            'file_kk.max'          => 'Ukuran berkas scan KK maksimal 2MB.',
            'file_pengantar.max'   => 'Ukuran berkas surat pengantar RT maksimal 2MB.',
        ]);

        try {
            // 2. KELOMPOKKAN DATA TAMBAHAN KE JSON
            $dataPengajuan = [];
            if ($request->filled('no_kk'))         $dataPengajuan['no_kk'] = $request->no_kk;
            if ($request->filled('nama_usaha'))    $dataPengajuan['nama_usaha'] = $request->nama_usaha;
            if ($request->filled('jenis_usaha'))   $dataPengajuan['jenis_usaha'] = $request->jenis_usaha;
            if ($request->filled('alamat_usaha'))  $dataPengajuan['alamat_usaha'] = $request->alamat_usaha;
            if ($request->filled('tahun_berdiri')) $dataPengajuan['tahun_berdiri'] = $request->tahun_berdiri;

            // 3. INJEKSI PENGAJUAN AWAL
            $pengajuan = new Pengajuan([
                'id_user'        => Auth::id(),
                'id_kategori'    => $request->id_kategori,
                'keperluan'      => $request->keperluan,
                'data_pengajuan' => $dataPengajuan,
                'tgl_pengajuan'  => now()->format('Y-m-d'),
                'status_terkini' => 'menunggu'
            ]);

            // 4. PROSES MULTIPART FILE UPLOAD
            if ($request->hasFile('file_kk')) {
                $file = $request->file('file_kk');
                $filename = time() . '_kk_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $pengajuan->file_kk = $file->storeAs('pengajuan', $filename, 'public');
            }

            if ($request->hasFile('file_pengantar')) {
                $file = $request->file('file_pengantar');
                $filename = time() . '_pengantar_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $pengajuan->file_pengantar = $file->storeAs('pengajuan', $filename, 'public');
            }

            if ($request->hasFile('file_foto_depan')) {
                $file = $request->file('file_foto_depan');
                $filename = time() . '_foto_depan_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $pengajuan->file_foto_depan = $file->storeAs('pengajuan', $filename, 'public');
            }

            if ($request->hasFile('file_foto_dalam')) {
                $file = $request->file('file_foto_dalam');
                $filename = time() . '_foto_dalam_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $pengajuan->file_foto_dalam = $file->storeAs('pengajuan', $filename, 'public');
            }

            $pengajuan->save();

            // 5. UPDATE AUTOMATIC BIODATA PROFILE WARGA
            $user = Auth::user();
            if ($request->filled('tempat_lahir'))   $user->tempat_lahir = $request->tempat_lahir;
            if ($request->filled('tgl_lahir'))      $user->tgl_lahir = $request->tgl_lahir;
            if ($request->filled('jenis_kelamin'))  $user->jenis_kelamin = $request->jenis_kelamin;
            if ($request->filled('agama'))          $user->agama = $request->agama;
            if ($request->filled('pekerjaan'))      $user->pekerjaan = $request->pekerjaan;
            if ($request->filled('alamat'))         $user->alamat = $request->alamat;
            $user->save();

            // 6. CREATED LOG TIMELINE STATUS
            RiwayatStatus::create([
                'id_pengajuan' => $pengajuan->id,
                'status'       => 'menunggu',
                'keterangan'   => 'Pengajuan baru berhasil dikirim oleh warga via Web SILARAS',
                'diubah_oleh'  => Auth::id()
            ]);

            return redirect()->route('warga.riwayat')->with('success', 'Pengajuan berhasil dikirim!');

        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Ada masalah pas nyimpen: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $pengajuan = Pengajuan::where('id_user', Auth::id())
            ->with(['kategori', 'suratKeluar', 'riwayatStatus'])
            ->findOrFail($id);
        
        return view('warga.pengajuan.detail', compact('pengajuan'));
    }

    public function download($id)
    {
        $pengajuan = Pengajuan::where('id_user', Auth::id())
            ->with('suratKeluar')
            ->findOrFail($id);
        
        if (!$pengajuan->suratKeluar || !$pengajuan->suratKeluar->file_surat_path) {
            return redirect()->back()->with('error', 'Surat belum tersedia atau belum ditandatangani admin.');
        }

        $path = storage_path('app/public/' . $pengajuan->suratKeluar->file_surat_path);

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File fisik surat tidak ditemukan di server.');
        }
        
        return response()->download($path);
    }
}