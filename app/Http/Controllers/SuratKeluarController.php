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

        return view(
            'admin.surat-keluar.index',
            compact('suratKeluar')
        );
    }

    // ================= FORM BUAT SURAT =================
    public function create($id_pengajuan)
    {
        $pengajuan = Pengajuan::with([
            'user',
            'kategori'
        ])->findOrFail($id_pengajuan);

        // CEK SUDAH ADA SURAT
        if ($pengajuan->suratKeluar) {

            return redirect()
                ->route('admin.surat-keluar.index')
                ->with(
                    'error',
                    'Surat sudah pernah dibuat'
                );
        }

        // PENOMORAN
        $penomoran = PenomoranSurat::where(
            'id_kategori',
            $pengajuan->id_kategori
        )->first();

        // PEJABAT
        $pejabat = Pejabat::all();

        if (!$penomoran) {

            return redirect()
                ->route('admin.pengajuan.index')
                ->with(
                    'error',
                    'Aturan penomoran belum tersedia'
                );
        }

        return view(
            'admin.surat-keluar.create',
            compact(
                'pengajuan',
                'penomoran',
                'pejabat'
            )
        );
    }

    // ================= SIMPAN SURAT =================
    public function store(Request $request)
    {
        $request->validate([

            'id_pengajuan' =>
                'required',

            'id_penomoran' =>
                'required',

            'id_pejabat' =>
                'required',

            'tgl_surat' =>
                'required|date',
        ]);

        // PENGAJUAN
        $pengajuan = Pengajuan::with(
            'kategori'
        )->findOrFail(
            $request->id_pengajuan
        );

        // PENOMORAN
        $penomoran = PenomoranSurat::findOrFail(
            $request->id_penomoran
        );

        // KODE SURAT
        $kodeSurat =
            $pengajuan
                ->kategori
                ->kode_surat;

        // NOMOR BARU
        $nomorBaru =
            $penomoran
                ->nomor_terakhir + 1;

        // GENERATE NOMOR
        $nomorSurat =
            $this->generateNomorSurat(
                $penomoran,
                $kodeSurat,
                $nomorBaru
            );

        // UPDATE NOMOR TERAKHIR
        $penomoran->update([

            'nomor_terakhir' =>
                $nomorBaru
        ]);

        // SIMPAN SURAT
        $suratKeluar = SuratKeluar::create([

            'id_pengajuan' =>
                $request->id_pengajuan,

            'id_penomoran' =>
                $request->id_penomoran,

            'id_pejabat' =>
                $request->id_pejabat,

            'nomor_surat' =>
                $nomorSurat,

            'tgl_surat' =>
                $request->tgl_surat,
        ]);

        // GENERATE PDF
        $pdf = $this->generatePDF(
            $suratKeluar
        );

        // PATH PDF
        $pdfPath =
            'surat_keluar/surat_' .
            $suratKeluar->id .
            '.pdf';

        // SIMPAN PDF
        Storage::disk('public')->put(
            $pdfPath,
            $pdf->output()
        );

        // UPDATE FILE PATH
        $suratKeluar->update([

            'file_surat_path' =>
                $pdfPath
        ]);

        // UPDATE STATUS
        $pengajuan->update([

            'status_terkini' =>
                'selesai'
        ]);

        // RIWAYAT STATUS
        RiwayatStatus::create([

            'id_pengajuan' =>
                $pengajuan->id,

            'status' =>
                'selesai',

            'keterangan' =>

                'Surat berhasil dibuat dengan nomor: ' .
                $nomorSurat,

            'diubah_oleh' =>
                Auth::id()
        ]);

        return redirect()
            ->route('admin.surat-keluar.index')
            ->with(
                'success',
                'Surat berhasil dibuat'
            );
    }

    // ================= GENERATE NOMOR =================
    private function generateNomorSurat(
        $penomoran,
        $kodeSurat,
        $nomorUrut
    )
    {
        // FORMAT
        $format =
            $penomoran->format_nomor
            ??
            '{kode_surat}/{nomor}/{tahun}';

        // DATA
        $tahun = date('Y');

        $bulan = date('m');

        $bulanRomawi =
            $this->getRomanMonth(
                date('n')
            );

        $nomorFormatted =
            sprintf(
                "%03d",
                $nomorUrut
            );

        // REPLACE
        $nomorSurat = str_replace(
            '{nomor}',
            $nomorFormatted,
            $format
        );

        $nomorSurat = str_replace(
            '{tahun}',
            $tahun,
            $nomorSurat
        );

        $nomorSurat = str_replace(
            '{bulan}',
            $bulan,
            $nomorSurat
        );

        $nomorSurat = str_replace(
            '{bulan_romawi}',
            $bulanRomawi,
            $nomorSurat
        );

        $nomorSurat = str_replace(
            '{kode_surat}',
            $kodeSurat,
            $nomorSurat
        );

        return $nomorSurat;
    }

    // ================= BULAN ROMAWI =================
    private function getRomanMonth($month)
    {
        $romawi = [

            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $romawi[$month];
    }

    // ================= GENERATE PDF =================
    private function generatePDF($suratKeluar)
    {
        $suratKeluar->load([

            'pengajuan.user',
            'pengajuan.kategori',
            'pejabat'
        ]);

        // KODE SURAT
        $kodeSurat = strtolower(

            $suratKeluar
                ->pengajuan
                ->kategori
                ->kode_surat
        );

        /*
            SKU
            SKTM
            SKD
        */
        // TEMPLATE
$template = match ($kodeSurat) {

    'sku'
        => 'admin.surat-keluar.template-sku', // Diubah dari .sku

    'sktm'
        => 'admin.surat-keluar.template-sktm', // Diubah dari .sktm

    'skd'
        => 'admin.surat-keluar.template-skd', // Diubah dari .skd
        

    default
        => 'admin.surat-keluar.template-default',
};

        $data = [

            'surat' =>
                $suratKeluar,

            'tanggal_cetak' =>
                now()->translatedFormat(
                    'd F Y'
                )
        ];

        $pdf = Pdf::loadView(
            $template,
            $data
        );

        $pdf->setPaper(
            'a4',
            'portrait'
        );

        return $pdf;
    }

    // ================= PREVIEW PDF =================
    public function preview($id)
    {
        $suratKeluar = SuratKeluar::with([

            'pengajuan.user',
            'pengajuan.kategori',
            'pejabat'

        ])->findOrFail($id);

        // KODE SURAT
        $kodeSurat = strtolower(

            $suratKeluar
                ->pengajuan
                ->kategori
                ->kode_surat
        );

        // TEMPLATE
        $template = match ($kodeSurat) {

            'sku'
                => 'admin.surat-keluar.sku',

            'sktm'
                => 'admin.surat-keluar.sktm',

            'skd'
                => 'admin.surat-keluar.skd',
                

            default
                => 'admin.surat-keluar.template-default',
        };

        $data = [

            'surat' =>
                $suratKeluar,

            'tanggal_cetak' =>
                now()->translatedFormat(
                    'd F Y'
                )
        ];

        $pdf = Pdf::loadView(
            $template,
            $data
        );

        $pdf->setPaper(
            'a4',
            'portrait'
        );

        return $pdf->stream(

            'surat_' .
            $suratKeluar->id .
            '.pdf'
        );
    }

    // ================= DOWNLOAD =================
    public function download($id)
    {
        $suratKeluar =
            SuratKeluar::findOrFail($id);

        if (

            $suratKeluar->file_surat_path
            &&

            Storage::disk('public')
                ->exists(
                    $suratKeluar->file_surat_path
                )

        ) {

            return Storage::disk('public')
                ->download(
                    $suratKeluar->file_surat_path
                );
        }

        return redirect()
            ->route('admin.surat-keluar.index')
            ->with(
                'error',
                'File surat tidak ditemukan'
            );
    }

    // ================= HAPUS =================
    public function destroy($id)
    {
        $suratKeluar =
            SuratKeluar::findOrFail($id);

        // HAPUS FILE
        if (

            $suratKeluar->file_surat_path
            &&

            Storage::disk('public')
                ->exists(
                    $suratKeluar->file_surat_path
                )

        ) {

            Storage::disk('public')
                ->delete(
                    $suratKeluar->file_surat_path
                );
        }

        // HAPUS DATA
        $suratKeluar->delete();

        return redirect()
            ->route('admin.surat-keluar.index')
            ->with(
                'success',
                'Surat berhasil dihapus'
            );
    }
}