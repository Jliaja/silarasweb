{{-- resources/views/wargav2/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SILARAS - Masuk Aplikasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --teal-brand: #1a5f7a;
            --teal-dark: #13465a;
            --bg-light: #f1f5f9;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: var(--teal-brand);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Top Hero Section (Sesuai Screenshot Mockup) */
        .hero-section {
            padding: 40px 40px 60px 40px;
            color: white;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 50px;
        }

        .brand-logo {
            width: 35px;
            height: auto;
        }

        .brand-title {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .hero-heading {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .hero-subheading {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            max-width: 500px;
            line-height: 1.5;
        }

        /* Container Card Melengkung Putih (Sesuai Mockup Lu) */
        .auth-container {
            background: white;
            border-radius: 32px 32px 0 0;
            padding: 50px 40px;
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .card-box {
            width: 100%;
            max-width: 480px; /* Ukuran box dikecilkan biar makin presisi & padat pas login doang */
        }

        .form-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .form-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        /* Alert Center */
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        /* Form Grouping & Field Box */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: var(--text-muted);
            font-size: 16px;
            pointer-events: none;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px 14px 46px; /* Space ekstra di kiri buat narok icon */
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            color: var(--text-dark);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        input:focus {
            outline: none;
            background: white;
            border-color: var(--teal-brand);
            box-shadow: 0 0 0 4px rgba(26, 95, 122, 0.1);
        }

        /* Form Utilities Row (Remember Me & Lupa Password) */
        .form-utils {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 13px;
            font-weight: 500;
        }

        .remember-box {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-dark);
            cursor: pointer;
        }

        .forgot-link {
            color: var(--teal-brand);
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        /* Button Action Sakti */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--teal-brand);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s ease;
        }

        .btn-submit:hover {
            background: var(--teal-dark);
        }

        /* Footer Switching Link */
        .form-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .form-footer a {
            color: var(--teal-brand);
            text-decoration: none;
            font-weight: 700;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .hero-section { padding: 30px 20px; }
            .auth-container { padding: 40px 20px; }
        }
    </style>
</head>
<body>

    {{-- Top Section (Sesuai Mockup Teal Lu) --}}
    <div class="hero-section">
        <div class="brand-header">
            <img src="{{ asset('images/logo-indramayu.png') }}" alt="Logo" class="brand-logo">
            <span class="brand-title">SILARAS</span>
        </div>
        <h1 class="hero-heading">Selamat Datang 👋</h1>
        <p class="hero-subheading">Masuk untuk mengakses layanan surat desa digital dengan lebih cepat dan praktis.</p>
    </div>

    {{-- Bottom White Container --}}
    <div class="auth-container">
        <div class="card-box">
            
            {{-- Notification Global Center --}}
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="alert alert-error">{{ session('error') }}</div> @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="padding-left: 15px; margin: 0; list-style-type: none;">
                        @foreach($errors->all() as $error) <li>⚠️ {{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            {{-- ================= FORM INTERFACE: LOGIN ONLY ================= --}}
            <div id="form-login">
                <h2 class="form-title">Masuk Akun</h2>
                <p class="form-subtitle">Silakan login menggunakan email dan password Anda.</p>
                
                <form action="{{ url('/login') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <div class="input-wrapper">
                            <span class="input-icon">✉️</span>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                    </div>
                    
                    <div class="form-utils">
                        <label class="remember-box">
                            <input type="checkbox" name="remember"> Remember Me
                        </label>
                        {{-- Menggunakan rute resmi bawaan auth.php --}}
                        <a href="{{ route('password.request') }}" class="forgot-link">Lupa Password</a>
                    </div>
                    
                    <button type="submit" class="btn-submit">➔ Masuk</button>
                </form>
                <div class="form-footer">
                    {{-- Menggunakan rute resmi bawaan auth.php --}}
                    Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
                </div>
            </div>

        </div>
    </div>

</body>
</html>