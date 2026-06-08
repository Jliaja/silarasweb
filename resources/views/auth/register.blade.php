{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SILARAS - Registrasi Warga</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --teal-brand: #1a5f7a;
            --teal-dark: #13465a;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: var(--teal-brand); min-height: 100vh; display: flex; flex-direction: column; justify-content: space-between; }
        
        .hero-section { padding: 40px 40px 30px 40px; color: white; max-width: 1200px; width: 100%; margin: 0 auto; }
        .brand-header { display: flex; align-items: center; gap: 12px; margin-bottom: 25px; }
        .brand-logo { width: 35px; height: auto; }
        .brand-title { font-size: 18px; font-weight: 700; letter-spacing: 0.5px; }
        .hero-heading { font-size: 28px; font-weight: 700; margin-bottom: 8px; }
        
        .auth-container { background: white; border-radius: 32px 32px 0 0; padding: 45px 40px; flex-grow: 1; display: flex; justify-content: center; align-items: flex-start; }
        .card-box { width: 100%; max-width: 800px; }
        .form-title { font-size: 22px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px; }
        .form-subtitle { font-size: 14px; color: var(--text-muted); margin-bottom: 25px; }
        
        .alert { padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; font-weight: 500; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .form-group { margin-bottom: 18px; position: relative; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .input-wrapper { position: relative; display: flex; align-items: center; width: 100%; }
        
        /* Icon Base */
        .input-icon { position: absolute; left: 16px; color: var(--text-muted); font-size: 15px; pointer-events: none; z-index: 10; }
        
        /* Input Global */
        input[type="text"], input[type="email"], input[type="password"], textarea {
            width: 100%; padding: 13px 16px 13px 44px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; color: var(--text-dark); font-size: 14px; font-weight: 500; transition: all 0.2s;
        }
        
        /* 💡 FIX UTAMA: Khusus Input Date dipisah pad-nya biar komponen bawaan browser kaga kebejek */
        input[type="date"] {
            width: 100%; padding: 13px 16px 13px 44px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; color: var(--text-dark); font-size: 14px; font-weight: 500; transition: all 0.2s;
            position: relative;
        }
        
        /* Maksa pop-up kalender muncul pas area manapun di dalam input di-klik */
        input[type="date"]::-webkit-calendar-picker-indicator {
            position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: transparent; color: transparent; cursor: pointer;
        }
        
        input:focus, textarea:focus { outline: none; background: white; border-color: var(--teal-brand); box-shadow: 0 0 0 4px rgba(26, 95, 122, 0.1); }
        
        .radio-group { display: flex; gap: 24px; padding: 8px 4px; }
        .radio-label { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; color: var(--text-dark); font-weight: 500; }
        
        .btn-submit { width: 100%; padding: 14px; background: var(--teal-brand); color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-submit:hover { background: var(--teal-dark); }
        .form-footer { text-align: center; margin-top: 25px; font-size: 13px; color: var(--text-muted); font-weight: 500; }
        .form-footer a { color: var(--teal-brand); text-decoration: none; font-weight: 700; }

        @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; gap: 0; } .hero-section { padding: 30px 20px; } .auth-container { padding: 35px 20px; } }
    </style>
</head>
<body>

    <div class="hero-section">
        <div class="brand-header">
            <img src="{{ asset('images/logo-indramayu.png') }}" alt="Logo" class="brand-logo">
            <span class="brand-title">SILARAS</span>
        </div>
        <h1 class="hero-heading">Buat Akun Baru Warga</h1>
    </div>

    <div class="auth-container">
        <div class="card-box">
            
            @if($errors->any())
                <div class="alert">
                    <ul style="padding-left: 15px; margin: 0;">
                        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <h2 class="form-title">Registrasi Data Diri</h2>
            <p class="form-subtitle">Lengkapi formulir di bawah sesuai identitas KTP asli lu brok.</p>
            
            <form action="{{ url('/register') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <div class="input-wrapper"><span class="input-icon">👤</span><input type="text" name="name" value="{{ old('name') }}" placeholder="Nama Lengkap" required></div>
                    </div>
                    <div class="form-group">
                        <div class="input-wrapper"><span class="input-icon">🪪</span><input type="text" name="nik" value="{{ old('nik') }}" placeholder="NIK (16 Digit)" required></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <div class="input-wrapper"><span class="input-icon">✉️</span><input type="email" name="email" value="{{ old('email') }}" placeholder="Alamat Email" required></div>
                    </div>
                    <div class="form-group">
                        <div class="input-wrapper"><span class="input-icon">📞</span><input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Nomor HP" required></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <div class="input-wrapper"><span class="input-icon">📍</span><input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Tempat Lahir" required></div>
                    </div>
                    <div class="form-group">
                        {{-- Field Tanggal Lahir yang udah di-fix jalurnya --}}
                        <div class="input-wrapper">
                            <span class="input-icon">📅</span>
                            <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir') }}" required>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <div class="input-wrapper"><span class="input-icon">🕌</span><input type="text" name="agama" value="{{ old('agama') }}" placeholder="Agama" required></div>
                    </div>
                    <div class="form-group">
                        <div class="input-wrapper"><span class="input-icon">💼</span><input type="text" name="pekerjaan" value="{{ old('pekerjaan') }}" placeholder="Pekerjaan" required></div>
                    </div>
                </div>
                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; display: block;">Jenis Kelamin</label>
                    <div class="radio-group">
                        <label class="radio-label"><input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin') == 'L' ? 'checked' : '' }} required> Laki-laki</label>
                        <label class="radio-label"><input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }} required> Perempuan</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-wrapper"><span class="input-icon">🏠</span><textarea name="alamat" rows="2" placeholder="Alamat Lengkap Rumah Tinggal" required style="padding-left: 44px;">{{ old('alamat') }}</textarea></div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <div class="input-wrapper"><span class="input-icon">🔒</span><input type="password" name="password" placeholder="Kata Sandi (Minimal 6 Karakter)" required></div>
                    </div>
                    <div class="form-group">
                        <div class="input-wrapper"><span class="input-icon">🔒</span><input type="password" name="password_confirmation" placeholder="Konfirmasi Kata Sandi" required></div>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">Daftar Akun Warga</button>
            </form>
            
            <div class="form-footer">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk disini</a>
            </div>
        </div>
    </div>

</body>
</html>