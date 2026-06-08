{{-- resources/views/warga/dashboard.blade.php --}}
@extends('layouts.warga')

@section('content')
<style>
    /* Styling lokal khusus internal card dashboard biar tetep presisi */
    .warga-profile-summary {
        background: linear-gradient(135deg, #1a5f7a 0%, #13465a 100%);
        color: white; border-radius: 16px; padding: 20px; margin-top: 20px;
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;
    }
    .info-meta h3 { font-size: 18px; margin-bottom: 4px; font-weight: 600; color: white; }
    .info-meta p { font-size: 13px; color: rgba(255, 255, 255, 0.8); }

    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 25px; }
    .stats-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px; text-align: center; }
    .stats-count { font-size: 24px; font-weight: 700; color: #1e3a5f; margin-bottom: 2px; }
    .stats-label { font-size: 12px; color: #64748b; font-weight: 500; }

    .section-title { font-size: 16px; font-weight: 700; color: #1e3a5f; margin: 30px 0 15px 0; display: flex; align-items: center; gap: 8px; }
    .tombol-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .tombol-kotak {
        background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 25px 20px;
        text-decoration: none; text-align: center; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; align-items: center; justify-content: center;
    }
    .tombol-kotak:hover {
        transform: translateY(-4px); box-shadow: 0 12px 24px rgba(26, 95, 122, 0.08); border-color: #1a5f7a;
    }
    .tombol-icon { font-size: 36px; margin-bottom: 12px; transition: transform 0.2s; }
    .tombol-kotak:hover .tombol-icon { transform: scale(1.1); }
    .tombol-text { font-size: 14px; font-weight: 600; color: #1e3a5f; }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; gap: 10px; }
        .tombol-grid { grid-template-columns: 1fr; gap: 15px; }
    }
</style>

<div class="container" style="max-width: 1000px; margin: 10px auto; padding: 0;">
    
    {{-- Notifikasi Flash --}}
    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 12px 18px; border-radius: 14px; margin-bottom: 20px; font-weight: 500; font-size: 14px; border: 1px solid #a7f3d0;">
            {{ session('success') }}
        </div>
    @endif

    {{-- MAIN CARD HERO --}}
    <div class="card" style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #e2e8f0;">
        <h1 style="color: #1e3a5f; font-size: 24px; font-weight: 700; margin-bottom: 6px;">Selamat Datang di SILARAS</h1>
        <p style="color: #64748b; font-size: 14px; line-height: 1.5;">Sistem Layanan Surat Administrasi Desa Mandiri. Monitor dan ajukan permohonan surat keterangan lu dengan praktis tanpa perlu antre di balai desa brok.</p>
        
        {{-- DATA DIRI MINI CARD --}}
        <div class="warga-profile-summary">
            <div class="info-meta">
                <h3>{{ Auth::user()->name }}</h3>
                <p>NIK: {{ Auth::user()->nik ?? '-' }} • Email: {{ Auth::user()->email }}</p>
            </div>
            <div style="background: rgba(255,255,255,0.15); padding: 6px 14px; border-radius: 30px; font-size: 12px; font-weight: 600; letter-spacing: 0.3px; color: white;">
                STATUS: WARGA AKTIF
            </div>
        </div>

        {{-- LIVE STATISTICS COUNTER REAL TIME --}}
        <div class="stats-grid">
            <div class="stats-card">
                {{-- 💡 1. TOTAL PERMOHONAN: Menghitung semua record berkas milik si warga --}}
                <div class="stats-count">{{ isset($pengajuan) ? $pengajuan->count() : 0 }}</div>
                <div class="stats-label">Total Permohonan</div>
            </div>
            <div class="stats-card" style="border-left: 3px solid #fbbf24;">
                {{-- 💡 2. SEDANG DIPROSES: Sinkronisasi status string dari file riwayat (menunggu, diverifikasi, diproses) --}}
                <div class="stats-count" style="color: #d97706;">
                    {{ isset($pengajuan) ? $pengajuan->whereIn('status_terkini', ['menunggu', 'diverifikasi', 'diproses'])->count() : 0 }}
                </div>
                <div class="stats-label">Sedang Diproses</div>
            </div>
            <div class="stats-card" style="border-left: 3px solid #10b981;">
                {{-- 💡 3. SURAT SELESAI / SIAP AMBIL: Sinkronisasi murni status string 'selesai' --}}
                <div class="stats-count" style="color: #059669;">
                    {{ isset($pengajuan) ? $pengajuan->where('status_terkini', 'selesai')->count() : 0 }}
                </div>
                <div class="stats-label">Surat Selesai / Siap Ambil</div>
            </div>
        </div>
    </div>

    {{-- SECTION NAVIGATION --}}
    <div class="section-title">
        <span>⚙️</span> Menu Layanan Administrasi
    </div>
    
    <div class="tombol-grid">
        <a href="{{ route('warga.pengajuan.create') }}" class="tombol-kotak">
            <div class="tombol-icon">✍️</div>
            <div class="tombol-text">Ajukan Surat Baru</div>
        </a>
        <a href="{{ route('warga.riwayat') }}" class="tombol-kotak">
            <div class="tombol-icon">📜</div>
            <div class="tombol-text">Riwayat & Status</div>
        </a>
        <a href="{{ route('warga.profile.edit') }}" class="tombol-kotak">
            <div class="tombol-icon">👤</div>
            <div class="tombol-text">Pengaturan Profil</div>
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ANTI-BACK LOOP FIX: Mengunci state history biar pas user klik back ga stuck di loop dashboard/login
        if (window.history && window.history.pushState) {
            window.history.pushState('dashboard', null, window.location.href);
            window.addEventListener('popstate', function () {
                window.history.pushState('dashboard', null, window.location.href);
            });
        }
    });
</script>
@endsection