<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use App\Models\Pengajuan;
use App\Models\PenomoranSurat;
use App\Models\Pejabat;
use App\Models\RiwayatStatus;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratKeluarController extends Controller
{
    // ================= DAFTAR SURAT =================
    public function index()
    {
        $suratKeluar = SuratKeluar::with([
            'pengajuan.user',
            'pengajuan.kategori',
            'pejabat'
        ])
        ->latest()
        ->get();

        return view('admin.surat-keluar.index', compact('suratKeluar'));
    }

    // ================= FORM BUAT SURAT =================
    public function create($id_pengajuan)
    {
        $pengajuan = Pengajuan::with(['user', 'kategori'])->findOrFail($id_pengajuan);

        // CEK SUDAH ADA SURAT
        if ($pengajuan->suratKeluar) {
            return redirect()
                ->route('admin.surat-keluar.index')
                ->with('error', 'Surat sudah pernah dibuat');
        }

        // PENOMORAN
        $penomoran = PenomoranSurat::where('id_kategori', $pengajuan->id_kategori)->first();

        // PEJABAT
        $pejabat = Pejabat::all();

        if (!$penomoran) {
            return redirect()
                ->route('admin.pengajuan.index')
                ->with('error', 'Aturan penomoran belum tersedia');
        }

        return view('admin.surat-keluar.create', compact('pengajuan', 'penomoran', 'pejabat'));
    }

    // ================= SIMPAN SURAT =================
    public function store(Request $request)
    {
        $request->validate([
            'id_pengajuan' => 'required',
            'id_penomoran' => 'required',
            'id_pejabat' => 'required',
            'tgl_surat' => 'required|date',
        ]);

        $pengajuan = Pengajuan::with('kategori')->findOrFail($request->id_pengajuan);
        $penomoran = PenomoranSurat::findOrFail($request->id_penomoran);
        
        $kodeSurat = $pengajuan->kategori->kode_surat;
        $nomorBaru = $penomoran->nomor_terakhir + 1;

        $nomorSurat = $this->generateNomorSurat($penomoran, $kodeSurat, $nomorBaru);

        $penomoran->update([
            'nomor_terakhir' => $nomorBaru
        ]);

        // 1. SIMPAN DATA SURAT KELUAR
        $suratKeluar = SuratKeluar::create([
            'id_pengajuan' => $request->id_pengajuan,
            'id_penomoran' => $request->id_penomoran,
            'id_pejabat' => $request->id_pejabat,
            'nomor_surat' => $nomorSurat,
            'tgl_surat' => $request->tgl_surat,
        ]);

        // 2. GENERATE PDF MENGGUNAKAN DOMPDF
        $pdf = $this->generatePDF($suratKeluar);

        // 3. KONSEP PENYIMPANAN YANG DISAMAKAN
        // Bikin nama file unik & path mengarah ke folder surat_keluar di dalam disk public
        $fileName = 'surat_' . $suratKeluar->id . '.pdf';
        $pdfPath = 'surat_keluar/' . $fileName;

        // Pastikan directory 'surat_keluar' udah otomatis dibuat kalau belum ada
        if (!Storage::disk('public')->exists('surat_keluar')) {
            Storage::disk('public')->makeDirectory('surat_keluar');
        }

        // Simpan file pdf hasil render ke disk public railway
        Storage::disk('public')->put($pdfPath, $pdf->output());

        // 4. UPDATE FILE PATH DI DATABASE
        $suratKeluar->update([
            'file_surat_path' => $pdfPath
        ]);

        $pengajuan->update([
            'status_terkini' => 'selesai'
        ]);

        RiwayatStatus::create([
            'id_pengajuan' => $pengajuan->id,
            'status' => 'selesai',
            'keterangan' => 'Surat berhasil dibuat dengan nomor: ' . $nomorSurat,
            'diubah_oleh' => Auth::id()
        ]);

        return redirect()
            ->route('admin.surat-keluar.index')
            ->with('success', 'Surat berhasil dibuat');
    }

    // ================= GENERATE NOMOR =================
    private function generateNomorSurat($penomoran, $kodeSurat, $nomorUrut)
    {
        $format = $penomoran->format_nomor ?? '{kode_surat}/{nomor}/{tahun}';

        $tahun = date('Y');
        $bulan = date('m');
        $bulanRomawi = $this->getRomanMonth(date('n'));
        $nomorFormatted = sprintf("%03d", $nomorUrut);

        $nomorSurat = str_replace('{nomor}', $nomorFormatted, $format);
        $nomorSurat = str_replace('{tahun}', $tahun, $nomorSurat);
        $nomorSurat = str_replace('{bulan}', $bulan, $nomorSurat);
        $nomorSurat = str_replace('{bulan_romawi}', $bulanRomawi, $nomorSurat);
        $nomorSurat = str_replace('{kode_surat}', $kodeSurat, $nomorSurat);

        return $nomorSurat;
    }

    // ================= BULAN ROMAWI =================
    private function getRomanMonth($month)
    {
        $romawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $romawi[$month];
    }

    // ================= GENERATE PDF =================
    private function generatePDF($suratKeluar)
    {
        $suratKeluar->load(['pengajuan.user', 'pengajuan.kategori', 'pejabat']);
        $kodeSurat = strtolower($suratKeluar->pengajuan->kategori->kode_surat);

        $template = match ($kodeSurat) {
            'sku'   => 'admin.surat-keluar.template-sku',
            'sktm'  => 'admin.surat-keluar.template-sktm',
            'skd'   => 'admin.surat-keluar.template-skd',
            default => 'admin.surat-keluar.template-default',
        };

        $pdf = Pdf::loadView($template, [
            'surat' => $suratKeluar,
            'tanggal_cetak' => now()->translatedFormat('d F Y')
        ]);
        
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    // ================= PREVIEW PDF =================
    public function preview($id)
    {
        $suratKeluar = SuratKeluar::with(['pengajuan.user', 'pengajuan.kategori', 'pejabat'])->findOrFail($id);
        $kodeSurat = strtolower($suratKeluar->pengajuan->kategori->kode_surat);

        $template = match ($kodeSurat) {
            'sku'   => 'admin.surat-keluar.template-sku',
            'sktm'  => 'admin.surat-keluar.template-sktm',
            'skd'   => 'admin.surat-keluar.template-skd',
            default => 'admin.surat-keluar.template-default',
        };

        $pdf = Pdf::loadView($template, [
            'surat' => $suratKeluar,
            'tanggal_cetak' => now()->translatedFormat('d F Y')
        ]);
        
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('surat_' . $suratKeluar->id . '.pdf');
    }

    // ================= DOWNLOAD =================
    public function download($id)
    {
        $suratKeluar = SuratKeluar::findOrFail($id);

        if ($suratKeluar->file_surat_path && Storage::disk('public')->exists($suratKeluar->file_surat_path)) {
            return Storage::disk('public')->download($suratKeluar->file_surat_path);
        }

        return redirect()
            ->route('admin.surat-keluar.index')
            ->with('error', 'File surat tidak ditemukan');
    }

    // ================= HAPUS =================
    public function destroy($id)
    {
        $suratKeluar = SuratKeluar::findOrFail($id);

        if ($suratKeluar->file_surat_path && Storage::disk('public')->exists($suratKeluar->file_surat_path)) {
            Storage::disk('public')->delete($suratKeluar->file_surat_path);
        }

        $suratKeluar->delete();

        return redirect()
            ->route('admin.surat-keluar.index')
            ->with('success', 'Surat berhasil dihapus');
    }
}