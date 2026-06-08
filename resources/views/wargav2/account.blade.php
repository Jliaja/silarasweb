{{-- resources/views/wargav2/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem Pelayanan Warga</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --card-bg: rgba(30, 41, 59, 0.7);
            --border-color: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: var(--bg-gradient); color: var(--text-main); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { width: 100%; max-width: 480px; margin: auto; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500; }
        .alert-success { background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; color: #4ade80; }
        .alert-error { background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #f87171; }
        .card { background: var(--card-bg); backdrop-filter: blur(16px); border: 1px solid var(--border-color); border-radius: 16px; padding: 40px 35px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4); }
        .card-header { text-align: center; margin-bottom: 30px; }
        .brand-logo { width: 75px; height: auto; margin-bottom: 15px; }
        .card-header h2 { font-size: 22px; font-weight: 700; margin-bottom: 8px; }
        .card-header p { color: var(--text-muted); font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        label { display: block; font-size: 13px; margin-bottom: 6px; color: #cbd5e1; }
        input, textarea { width: 100%; padding: 11px 14px; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-main); font-size: 14px; }
        input:focus, textarea:focus { outline: none; border-color: var(--primary); }
        .radio-group { display: flex; gap: 20px; padding: 8px 0; }
        .radio-label { display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 14px; }
        button { width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 10px; }
        button:hover { background: var(--primary-hover); }
        .form-footer { text-align: center; margin-top: 25px; font-size: 13px; color: var(--text-muted); line-height: 1.6; }
        .form-footer a { color: #60a5fa; text-decoration: none; }
        .auth-form { display: none; }
        .auth-form.active { display: block; }
    </style>
</head>
<body>
    <div class="container">
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-error">{{ session('error') }}</div> @endif
        @if($errors->any())
            <div class="alert alert-error">
                <ul style="padding-left: 15px; margin: 0;">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            {{-- FORM LOGIN --}}
            <div id="form-login" class="auth-form active">
                <div class="card-header">
                    <img src="{{ asset('images/logo-indramayu.png') }}" alt="Logo" class="brand-logo">
                    <h2>Portal Pelayanan Warga</h2>
                    <p>Silakan masuk untuk mengakses layanan digital</p>
                </div>
                <form action="{{ url('/login') }}" method="POST">
                    @csrf
                    <div class="form-group"><label>Alamat Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
                    <div class="form-group"><label>Kata Sandi</label><input type="password" name="password" required></div>
                    <button type="submit">Masuk Aplikasi</button>
                </form>
                <div class="form-footer">
                    Belum punya akun? <a href="#" onclick="switchForm('form-register')">Daftar Sekarang</a><br>
                    <a href="#" onclick="switchForm('form-recovery')" style="display:inline-block; margin-top: 8px; font-size:12px;">Lupa kata sandi?</a>
                </div>
            </div>

            {{-- FORM REGISTER --}}
            <div id="form-register" class="auth-form">
                <div class="card-header">
                    <h2>Registrasi Warga Baru</h2>
                    <p>Lengkapi biodata sesuai KTP asli lu brok</p>
                </div>
                <form action="{{ url('/register') }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group"><label>Nama Lengkap</label><input type="text" name="name" value="{{ old('name') }}" required></div>
                        <div class="form-group"><label>NIK (16 Digit)</label><input type="text" name="nik" value="{{ old('nik') }}" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
                        <div class="form-group"><label>No HP</label><input type="text" name="no_hp" value="{{ old('no_hp') }}" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Tempat Lahir</label><input type="text" name="tempat_lahir" required></div>
                        <div class="form-group"><label>Tanggal Lahir</label><input type="date" name="tgl_lahir" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Agama</label><input type="text" name="agama" required></div>
                        <div class="form-group"><label>Pekerjaan</label><input type="text" name="pekerjaan" required></div>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <div class="radio-group">
                            <label class="radio-label"><input type="radio" name="jenis_kelamin" value="L" required> Laki-laki</label>
                            <label class="radio-label"><input type="radio" name="jenis_kelamin" value="P" required> Perempuan</label>
                        </div>
                    </div>
                    <div class="form-group"><label>Alamat Rumah</label><textarea name="alamat" rows="2" required></textarea></div>
                    <div class="form-row">
                        <div class="form-group"><label>Kata Sandi</label><input type="password" name="password" required></div>
                        <div class="form-group"><label>Konfirmasi Sandi</label><input type="password" name="password_confirmation" required></div>
                    </div>
                    <button type="submit">Kirim Pendaftaran</button>
                </form>
                <div class="form-footer">Sudah punya akun? <a href="#" onclick="switchForm('form-login')">Masuk disini</a></div>
            </div>

            {{-- FORM RECOVERY --}}
            <div id="form-recovery" class="auth-form">
                <div class="card-header">
                    <h2>Pemulihan Kata Sandi</h2>
                    <p>Opsi validasi data dan reset sandi warga</p>
                </div>
                <form action="{{ url('/check-email') }}" method="POST" style="margin-bottom: 15px; border-bottom: 1px dashed rgba(255,255,255,0.1); padding-bottom: 15px;">
                    @csrf
                    <div class="form-group"><label>Validasi Ketersediaan Email</label><input type="email" name="email" required></div>
                    <button type="submit" style="background:#4b5563;">Cek Email</button>
                </form>
                <form action="{{ url('/forgot-password') }}" method="POST" style="margin-bottom: 15px; border-bottom: 1px dashed rgba(255,255,255,0.1); padding-bottom: 15px;">
                    @csrf
                    <div class="form-group"><label>Minta Tautan Reset Via Email</label><input type="email" name="email" required></div>
                    <button type="submit" style="background:#4b5563;">Kirim Link Reset</button>
                </form>
                <form action="{{ url('/direct-reset-password') }}" method="POST">
                    @csrf
                    <div class="form-group"><label>Direct Reset Tanpa Token (Paksa Ubah)</label><input type="email" name="email" required></div>
                    <div class="form-row">
                        <div class="form-group"><label>Sandi Baru</label><input type="password" name="password" required></div>
                        <div class="form-group"><label>Konfirmasi</label><input type="password" name="password_confirmation" required></div>
                    </div>
                    <button type="submit">Paksa Ubah Sandi</button>
                </form>
                <div class="form-footer"><a href="#" onclick="switchForm('form-login')">⬅️ Kembali ke Halaman Login</a></div>
            </div>
        </div>
    </div>
    <script>
        function switchForm(formId) {
            const forms = document.querySelectorAll('.auth-form');
            forms.forEach(form => form.classList.remove('active'));
            document.getElementById(formId).classList.add('active');
            if(event) event.preventDefault();
        }
    </script>
</body>
</html>