@extends('layouts.warga')

@section('content')
<div class="card">
    {{-- 💡 UPGRADE HEADER: Ditambahin flex container buat narok tombol kembali di pojok kanan atas --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <h3 style="color: #1e3a5f; font-size: 22px; font-weight: 700;">Form Pengajuan Surat</h3>
        <a href="{{ route('warga.dashboard') }}" class="btn-back">← Kembali</a>
    </div>

    <form method="POST" action="{{ route('warga.pengajuan.store') }}" enctype="multipart/form-data" id="formPengajuan">
        @csrf

        <div class="form-group">
            <label class="form-label">Jenis Surat</label>
            <select name="id_kategori" id="jenisSurat" class="form-control" required>
                <option value="">Pilih Jenis Surat</option>
                @foreach($kategori as $item)
                    <option value="{{ $item->id }}" data-kode="{{ $item->kode_surat }}">{{ $item->nama_kategori }} ({{ $item->kode_surat }})</option>
                @endforeach
            </select>
        </div>

        {{-- CONTAINER INPUT UTAMA (Otomatis muncul setelah pilih jenis surat) --}}
        <div id="wrapper-biodata-warga" style="display: none;">
            <h4>Data Diri Pemohon</h4>
            <div class="form-row">
                <div class="form-group"><label class="form-label">NIK</label><input type="text" name="nik" class="form-control" value="{{ Auth::user()->nik }}" readonly></div>
                <div class="form-group"><label class="form-label">Nama Lengkap</label><input type="text" name="nama" class="form-control" value="{{ Auth::user()->name }}" readonly></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label class="form-label">Tempat Lahir</label><input type="text" name="tempat_lahir" class="form-control" value="{{ Auth::user()->tempat_lahir }}"></div>
                <div class="form-group"><label class="form-label">Tanggal Lahir</label><input type="date" name="tgl_lahir" class="form-control" value="{{ Auth::user()->tgl_lahir ? \Carbon\Carbon::parse(Auth::user()->tgl_lahir)->format('Y-m-d') : '' }}"></div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control">
                        <option value="L" {{ Auth::user()->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ Auth::user()->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Agama</label>
                    <input type="text" name="agama" class="form-control" value="{{ Auth::user()->agama }}">
                </div>
            </div>
            <div class="form-group"><label class="form-label">Pekerjaan</label><input type="text" name="pekerjaan" class="form-control" value="{{ Auth::user()->pekerjaan }}"></div>
            <div class="form-group"><label class="form-label">Alamat Rumah KTP</label><textarea name="alamat" class="form-control" rows="2">{{ Auth::user()->alamat }}</textarea></div>
        </div>

        <div id="form-sktm" class="dynamic-form" style="display: none;">
            <h4 style="border-left-color: #fbbf24;">Atribut Khusus SKTM</h4>
            <div class="form-group">
                <label class="form-label">No. Kartu Keluarga (KK)</label>
                <input type="text" name="no_kk" class="form-control" placeholder="Masukkan 16 Digit Nomor KK">
            </div>
        </div>

        <div id="form-sku" class="dynamic-form" style="display: none;">
            <h4 style="border-left-color: #10b981;">Atribut Legalitas Usaha (SKU)</h4>
            <div class="form-row">
                <div class="form-group"><label class="form-label">Nama Usaha</label><input type="text" name="nama_usaha" class="form-control" placeholder="Contoh: Warung Sembako Berkah"></div>
                <div class="form-group"><label class="form-label">Jenis / Bidang Usaha</label><input type="text" name="jenis_usaha" class="form-control" placeholder="Contoh: Perdagangan Kuliner"></div>
            </div>
            <div class="form-group"><label class="form-label">Alamat Lengkap Tempat Usaha</label><textarea name="alamat_usaha" class="form-control" rows="2" placeholder="Tulis alamat lokasi usaha dijalankan..."></textarea></div>
            <div class="form-group"><label class="form-label">Tahun Berdiri Usaha</label><input type="number" name="tahun_berdiri" class="form-control" placeholder="Contoh: 2021"></div>
            
            <h4 style="border-left-color: #10b981;">Lampiran Berkas Foto Tempat Usaha</h4>
            <div class="form-row">
                <div class="form-group"><label class="form-label">Foto Depan Tempat Usaha</label><input type="file" name="file_foto_depan" class="form-control" accept="image/*"></div>
                <div class="form-group"><label class="form-label">Foto Bagian Dalam Usaha</label><input type="file" name="file_foto_dalam" class="form-control" accept="image/*"></div>
            </div>
        </div>

        <div id="form-domisili" class="dynamic-form" style="display: none;">
            {{-- Menggunakan template input utama di atas biar kaga mubazir kode brok --}}
        </div>

        <div id="wrapper-dokumen-global" style="display: none; margin-top: 25px; padding-top: 15px; border-top: 1px dashed #cbd5e1;">
            <h4>Upload Dokumen Persyaratan (Maks 2MB)</h4>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Upload Scan Kartu Keluarga (KK)</label>
                    <input type="file" name="file_kk" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="form-group">
                    <label class="form-label">Upload Surat Pengantar RT / RW</label>
                    <input type="file" name="file_pengantar" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <div class="form-group" style="margin-top: 15px;">
                <label class="form-label">Tujuan / Keperluan Pembuatan Surat</label>
                <textarea name="keperluan" class="form-control" rows="3" placeholder="Contoh: Syarat pengajuan Beasiswa Kuliah / Klaim BPJS Kesehatan / Pendaftaran Kredit Bank" required></textarea>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">Kirim Berkas Pengajuan</button>
                <a href="{{ route('warga.dashboard') }}" class="btn btn-outline">Batal</a>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('jenisSurat').addEventListener('change', function() {
        // Reset view box dinamis
        document.getElementById('form-sktm').style.display = 'none';
        document.getElementById('form-sku').style.display = 'none';
        document.getElementById('form-domisili').style.display = 'none';
        
        var kode = this.options[this.selectedIndex]?.getAttribute('data-kode');
        
        if (kode) {
            document.getElementById('wrapper-biodata-warga').style.display = 'block';
            document.getElementById('wrapper-dokumen-global').style.display = 'block';
            
            if (kode === 'SKTM') {
                document.getElementById('form-sktm').style.display = 'block';
            } else if (kode === 'SKU') {
                document.getElementById('form-sku').style.display = 'block';
            } else if (kode === 'Domisili') {
                document.getElementById('form-domisili').style.display = 'block';
            }
        } else {
            document.getElementById('wrapper-biodata-warga').style.display = 'none';
            document.getElementById('wrapper-dokumen-global').style.display = 'none';
        }
    });
</script>

<style>
    .form-group { margin-bottom: 20px; width: 100%; }
    .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #1e3a5f; font-size: 14px; }
    .form-control { width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 14px; background: #f8fafc; }
    .form-control:focus { outline: none; border-color: #2c7cb6; background: white; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .btn-primary { background: #2c7cb6; color: white; padding: 13px 28px; border: none; border-radius: 40px; cursor: pointer; font-weight: 600; font-size: 14px; }
    .btn-primary:hover { background: #1e5a8a; }
    .btn-outline { background: #f1f5f9; padding: 13px 28px; border-radius: 40px; text-decoration: none; color: #1e293b; margin-left: 10px; display: inline-block; font-weight: 600; font-size: 14px; }
    
    /* 💡 STYLING TOMBOL BACK ATAS */
    .btn-back {
        background: #f1f5f9; padding: 8px 18px; border-radius: 30px; text-decoration: none;
        color: #1e3a5f; font-weight: 600; font-size: 13px; transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
    }
    .btn-back:hover { background: #e2e8f0; transform: translateX(-2px); }

    h4 { color: #1e3a5f; border-left: 4px solid #2c7cb6; padding-left: 12px; margin: 25px 0 15px; font-size: 15px; font-weight: 700; }
    @media (max-width: 640px) { .form-row { grid-template-columns: 1fr; gap: 0; } }
</style>
@endsection