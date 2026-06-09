{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.warga')

@section('content')
<style>
    /* Styling lokal khusus internal card form profile agar tetep rapi */
    .card-title { font-size: 18px; font-weight: 700; color: #1e3a5f; margin-bottom: 6px; }
    .card-subtitle { font-size: 13px; color: #64748b; margin-bottom: 25px; }
    .form-group { margin-bottom: 20px; width: 100%; }
    .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #1e3a5f; font-size: 14px; }
    .form-control { width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 14px; transition: all 0.2s; background: #f8fafc; color: #1e293b; }
    .form-control:focus { outline: none; border-color: #2c7cb6; background: white; box-shadow: 0 0 0 3px rgba(44,124,182,0.1); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .radio-group { display: flex; gap: 24px; padding: 10px 4px; }
    .radio-label { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; color: #1e293b; font-weight: 500; }
    .btn { display: inline-block; padding: 12px 24px; background: #2c7cb6; color: white; text-decoration: none; border-radius: 12px; border: none; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.2s; }
    .btn:hover { background: #1e5a8a; }
    .btn-danger { background: #ef4444; }
    .btn-danger:hover { background: #b91c1c; }

    input[type="date"] { position: relative; }
    input[type="date"]::-webkit-calendar-picker-indicator { position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: transparent; color: transparent; cursor: pointer; }

    @media (max-width: 640px) {
        .form-row { grid-template-columns: 1fr; gap: 0; }
    }
</style>

<div class="container" style="max-width: 850px; margin: 10px auto; padding: 0;">
    
    {{-- Flash Notification Center --}}
    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 12px 18px; border-radius: 14px; margin-bottom: 20px; font-weight: 500; font-size: 14px; border: 1px solid #a7f3d0;">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 12px 18px; border-radius: 14px; margin-bottom: 20px; font-size: 14px; border: 1px solid #fca5a5;">
            <ul style="padding-left: 15px; margin: 0;">
                @foreach($errors->all() as $error) <li>⚠️ {{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- ================= FORM KOTAK 1: UPDATE PROFILE INFORMATION ================= --}}
    <div class="card" style="background: white; border-radius: 20px; padding: 30px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <h3 style="color: #1e3a5f; font-size: 22px; font-weight: 700;">Form Pengajuan Surat</h3>
        <a href="{{ route('warga.dashboard') }}" class="btn-back">← Kembali</a>
    </div>
        <h3 class="card-title">Informasi Profil Warga</h3>
        <p class="card-subtitle">Perbarui data biodata akun dan alamat domisili terdaftar Anda sesuai KTP asli.</p>
        
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('patch')
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">NIK (Sesuai KTP)</label>
                    <input type="text" name="nik" value="{{ old('nik', Auth::user()->nik) }}" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Email Aktif</label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', Auth::user()->no_hp) }}" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', Auth::user()->tempat_lahir) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir', Auth::user()->tgl_lahir ? (is_string(Auth::user()->tgl_lahir) ? \Carbon\Carbon::parse(Auth::user()->tgl_lahir)->format('Y-m-d') : Auth::user()->tgl_lahir->format('Y-m-d')) : '') }}" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Agama</label>
                    <input type="text" name="agama" value="{{ old('agama', Auth::user()->agama) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Pekerjaan</label>
                    <input type="text" name="pekerjaan" value="{{ old('pekerjaan', Auth::user()->pekerjaan) }}" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Jenis Kelamin</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin', Auth::user()->jenis_kelamin) == 'L' ? 'checked' : '' }}> Laki-laki
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin', Auth::user()->jenis_kelamin) == 'P' ? 'checked' : '' }}> Perempuan
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Rumah Domisili</label>
                <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat', Auth::user()->alamat) }}</textarea>
            </div>

            <button type="submit" class="btn">Simpan Perubahan Data</button>
        </form>
    </div>

    {{-- ================= FORM KOTAK 2: UPDATE PASSWORD ================= --}}
    <div class="card" style="background: white; border-radius: 20px; padding: 30px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #e2e8f0;">
        <h3 class="card-title">Perbarui Kata Sandi</h3>
        <p class="card-subtitle">Pastikan akun Anda menggunakan kata sandi acak yang panjang agar tetap aman brok.</p>
        
        <form action="{{ url('/warga/change-password') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Kata Sandi Baru</label>
                <input type="password" name="password" placeholder="Minimal 6 karakter" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi baru" class="form-control" required>
            </div>

            <button type="submit" class="btn">Perbarui Sandi</button>
        </form>
    </div>

    {{-- ================= FORM KOTAK 3: DELETE ACCOUNT ================= --}}
    <div class="card" style="background: white; border-radius: 20px; padding: 30px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; border-top: 4px solid #ef4444;">
        <h3 class="card-title" style="color: #ef4444;">Hapus Akun Warga</h3>
        <p class="card-subtitle">Setelah akun Anda dihapus, semua sumber daya dan data pengajuan berkas arsip digital di dalam sistem SILARAS akan dihapus secara permanen.</p>
        
        <form action="{{ route('profile.destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda benar-benar yakin ingin menghapus akun ini secara permanen, brok?');">
            @csrf
            @method('delete')
            
            <div class="form-group" style="max-width: 400px; margin-bottom: 15px;">
                <label class="form-label" style="color: #ef4444;">Ketik Password Anda Untuk Konfirmasi</label>
                <input type="password" name="password" placeholder="Masukkan password saat ini" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-danger">Eliminasi Akun Permanen</button>
        </form>
    </div>
</div>

@endsection