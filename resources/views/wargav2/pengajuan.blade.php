{{-- resources/views/wargav2/pengajuan.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Permohonan Pengajuan Surat Berkas</title>
    <style>
        body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; font-family: 'Segoe UI', sans-serif; padding: 40px 20px; }
        .container { width: 100%; max-width: 650px; margin: auto; }
        .card { background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 30px; }
        input, select, textarea { width: 100%; padding: 10px; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 8px; margin-bottom: 15px; }
        button { width: 100%; padding: 12px; background: #10b981; color: white; border: none; font-weight: 600; border-radius: 8px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <p style="margin-bottom: 15px;"><a href="{{ route('warga.riwayat') }}" style="color:#60a5fa; text-decoration:none;">⬅️ Kembali ke Menu Utama</a></p>

            @if(isset($pengajuan))
                {{-- DETECT MODE: SHOW DETAIL TIMELINE --}}
                <h2>Pelacakan Dokumen #{{ $pengajuan->id }}</h2><br>
                <p>Kategori: <strong>{{ $pengajuan->kategori->nama_kategori ?? '-' }}</strong></p>
                <p>Keperluan: {{ $pengajuan->keperluan }}</p>
                <p>Status: <span style="background: #334155; padding: 3px 8px; border-radius: 4px;">{{ strtoupper($pengajuan->status_terkini) }}</span></p>
                <hr style="border-color:rgba(255,255,255,0.1); margin: 20px 0;">
                <h3>Linimasa Proses Berkas</h3>
                <ul style="padding-left:20px; margin-top:10px; line-height:2;">
                    @foreach($pengajuan->riwayatStatus ?? [] as $log)
                        <li><strong>[{{ $log->status }}]</strong> - {{ $log->keterangan }}</li>
                    @endforeach
                </ul>
                @if($pengajuan->suratKeluar)
                    <br><a href="{{ route('warga.pengajuan.download', $pengajuan->id) }}" style="background: #22c55e; color: white; padding: 10px; display: block; text-align: center; border-radius: 8px; text-decoration: none; font-weight: bold;">⬇️ DOWNLOAD DOKUMEN HASIL VERIFIKASI (PDF)</a>
                @endif
            @else
                {{-- DETECT MODE: CREATE NEW DOKUMEN --}}
                <h2>Formulir Permohonan Surat Online</h2><br>
                <form action="{{ route('warga.pengajuan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label>Pilih Kategori Surat</label>
                    <select name="id_kategori" required>
                        @foreach($kategori ?? [] as $kat) <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option> @endforeach
                    </select>
                    <label>Keperluan Berkas</label>
                    <textarea name="keperluan" rows="3" required></textarea>
                    <label>No Kartu Keluarga (KK)</label>
                    <input type="text" name="no_kk">
                    <hr style="border-color:rgba(255,255,255,0.1); margin:15px 0;">
                    <label>Nama Usaha (Oposional SKU)</label><input type="text" name="nama_usaha">
                    <label>Alamat Usaha (Oposional SKU)</label><input type="text" name="alamat_usaha">
                    <hr style="border-color:rgba(255,255,255,0.1); margin:15px 0;">
                    <label>Upload File Berkas KK</label><input type="file" name="file_kk">
                    <label>Upload File Berkas Pengantar RT</label><input type="file" name="file_pengantar">
                    <button type="submit">SUBMIT SEKARANG</button>
                </form>
            @endif
        </div>
    </div>
</body>
</html>