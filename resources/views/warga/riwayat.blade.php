{{-- resources/views/warga/riwayat.blade.php --}}
@extends('layouts.warga')

@section('content')
<div class="card" style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
    <h3 style="margin-bottom: 20px; font-size: 22px; color: #1e3a5f; font-weight: 700;">Riwayat Pengajuan Surat</h3>
    
    @if($pengajuan->isEmpty())
        <div style="text-align: center; padding: 40px 20px;">
            <p style="color: #64748b; margin-bottom: 20px; font-size: 14px;">Belum ada riwayat permohonan surat keterangan digital.</p>
            <a href="{{ route('warga.pengajuan.create') }}" class="btn-primary">Ajukan Surat Sekarang</a>
        </div>
    @else
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 14px; text-align: left; color: #1e3a5f; font-weight: 600; font-size: 14px;">Tanggal</th>
                        <th style="padding: 14px; text-align: left; color: #1e3a5f; font-weight: 600; font-size: 14px;">Jenis Surat</th>
                        <th style="padding: 14px; text-align: left; color: #1e3a5f; font-weight: 600; font-size: 14px;">Keperluan</th>
                        <th style="padding: 14px; text-align: center; color: #1e3a5f; font-weight: 600; font-size: 14px;">Status</th>
                        <th style="padding: 14px; text-align: center; color: #1e3a5f; font-weight: 600; font-size: 14px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengajuan as $item)
                    <tr style="border-bottom: 1px solid #e2e8f0; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                        <td style="padding: 14px; font-size: 14px;">
                            <strong>{{ \Carbon\Carbon::parse($item->tgl_pengajuan)->translatedFormat('d M Y') }}</strong><br>
                            <small style="color: #94a3b8;">{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('H:i') }} WIB</small>
                        </td>
                        <td style="padding: 14px; font-size: 14px;">
                            <span style="font-weight: 600; color: #1e3a5f;">{{ $item->kategori->nama_kategori ?? '-' }}</span><br>
                            <small style="color: #2c7cb6; font-weight: 500;">{{ $item->kategori->kode_surat ?? '-' }}</small>
                        </td>
                        <td style="padding: 14px; font-size: 14px; color: #475569;">
                            {{ Str::limit($item->keperluan, 50) }}
                        </td>
                        <td style="padding: 14px; text-align: center;">
                            @if($item->status_terkini == 'menunggu')
                                <span class="badge badge-warning">⏳ Menunggu</span>
                            @elseif($item->status_terkini == 'diverifikasi')
                                <span class="badge badge-info">✅ Diverifikasi</span>
                            @elseif($item->status_terkini == 'diproses')
                                <span class="badge badge-info">🔄 Diproses</span>
                            @elseif($item->status_terkini == 'selesai')
                                <span class="badge badge-success">📑 Selesai</span>
                            @elseif($item->status_terkini == 'ditolak')
                                <span class="badge badge-danger">❌ Ditolak</span>
                            @endif
                        </td>
                        <td style="padding: 14px; text-align: center;">
                            <a href="{{ route('warga.pengajuan.show', $item->id) }}" class="btn-detail">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div style="margin-top: 25px; text-align: center;">
    <a href="{{ route('warga.dashboard') }}" class="btn-primary">← Kembali ke Dashboard</a>
</div>

<style>
    .btn-primary {
        background: #2c7cb6; color: white; padding: 10px 24px; border-radius: 40px;
        text-decoration: none; display: inline-block; font-weight: 600; font-size: 14px; transition: all 0.2s; border: none; cursor: pointer;
    }
    .btn-primary:hover { background: #1e5a8a; transform: translateY(-2px); }
    
    .btn-detail {
        background: #2c7cb6; color: white; padding: 6px 16px; border-radius: 30px;
        text-decoration: none; font-size: 12px; font-weight: 500; transition: all 0.2s; display: inline-block;
    }
    .btn-detail:hover { background: #1e5a8a; transform: translateY(-1px); }
    
    .badge { padding: 5px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-block; }
    .badge-warning { background: #fef3c7; color: #92400e; }
    .badge-info { background: #dbeafe; color: #1e40af; }
    .badge-success { background: #d1fae5; color: #065f46; }
    .badge-danger { background: #fee2e2; color: #991b1b; }
</style>
@endsection