{{-- resources/views/auth/forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SILARAS - Pemulihan Sandi</title>
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
        
        .hero-section { padding: 40px 40px 50px 40px; color: white; max-width: 1200px; width: 100%; margin: 0 auto; }
        .brand-header { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; }
        .brand-logo { width: 35px; height: auto; }
        .brand-title { font-size: 18px; font-weight: 700; letter-spacing: 0.5px; }
        .hero-heading { font-size: 30px; font-weight: 700; margin-bottom: 10px; }
        
        .auth-container { background: white; border-radius: 32px 32px 0 0; padding: 50px 40px; flex-grow: 1; display: flex; justify-content: center; align-items: flex-start; }
        .card-box { width: 100%; max-width: 480px; }
        .form-title { font-size: 24px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px; }
        .form-subtitle { font-size: 14px; color: var(--text-muted); margin-bottom: 30px; line-height: 1.5; }
        
        .alert { padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; font-weight: 500; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        
        .form-group { margin-bottom: 20px; }
        .input-wrapper { position: relative; display: flex; align-items: center; }
        .input-icon { position: absolute; left: 16px; color: var(--text-muted); font-size: 16px; pointer-events: none; }
        
        input[type="email"], input[type="password"] { width: 100%; padding: 14px 16px 14px 46px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; color: var(--text-dark); font-size: 14px; font-weight: 500; }
        input:focus { outline: none; border-color: var(--teal-brand); box-shadow: 0 0 0 4px rgba(26, 95, 122, 0.1); }
        
        .btn-submit { width: 100%; padding: 14px; background: var(--teal-brand); color: white; border: none; border-radius: 14px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-submit:hover { background: var(--teal-dark); }
        .form-footer { text-align: center; margin-top: 25px; font-size: 13px; color: var(--text-muted); }
        .form-footer a { color: var(--teal-brand); text-decoration: none; font-weight: 700; }

        /* Step Switcher Utilities */
        .step-form { display: none; }
        .step-form.active { display: block; }
    </style>
</head>
<body>

    <div class="hero-section">
        <div class="brand-header">
            <img src="{{ asset('images/logo-indramayu.png') }}" alt="Logo" class="brand-logo">
            <span class="brand-title">SILARAS</span>
        </div>
        <h1 class="hero-heading">Pemulihan Akun</h1>
    </div>

    <div class="auth-container">
        <div class="card-box">
            
            {{-- Notification Flash --}}
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="alert alert-error">{{ session('error') }}</div> @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="padding-left: 15px; margin: 0; list-style-type: none;">
                        @foreach($errors->all() as $error) <li>⚠️ {{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            {{-- ================= STEP 1: CEK EMAIL (DEFAULT ACTIVE) ================= --}}
            {{-- Form ini aktif kalo session 'step_dua' kaga dikirim dari controller --}}
            <div id="step-cek-email" class="step-form {{ session('step_dua') ? '' : 'active' }}">
                <h2 class="form-title">Tahap 1: Verifikasi Email</h2>
                <p class="form-subtitle">Masukkan alamat email aktif Anda untuk memvalidasi kepemilikan akun di database SILARAS.</p>
                
                <form action="{{ url('/check-email') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <div class="input-wrapper">
                            <span class="input-icon">✉️</span>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan Email Terdaftar Anda" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit">Periksa Email Warga</button>
                </form>
            </div>

            {{-- ================= STEP 2: FORM DIRECT GANTI PASSWORD ================= --}}
            {{-- Form ini otomatis kebuka kalo email terbukti terdaftar di sistem --}}
            <div id="step-ganti-password" class="step-form {{ session('step_dua') ? 'active' : '' }}">
                <h2 class="form-title">Tahap 2: Atur Sandi Baru</h2>
                <p class="form-subtitle">Email lu valid brok! Sekarang silakan ketik kata sandi baru buat akun lu.</p>
                
                <form action="{{ url('/direct-reset-password') }}" method="POST">
                    @csrf
                    {{-- Melempar kembali data email dari session simpanan controller --}}
                    <input type="hidden" name="email" value="{{ session('verified_email') ?? old('email') }}">

                    <div class="form-group">
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input type="password" name="password" placeholder="Kata Sandi Baru" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input type="password" name="password_confirmation" placeholder="Konfirmasi Kata Sandi Baru" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit">Eksekusi Perubahan Sandi</button>
                </form>
            </div>
            
            <div class="form-footer">
                <a href="{{ route('login') }}">⬅️ Kembali ke Halaman Login</a>
            </div>
        </div>
    </div>

</body>
</html>