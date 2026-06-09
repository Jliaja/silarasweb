<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KategoriSuratController;
use App\Http\Controllers\PejabatController;
use App\Http\Controllers\PenomoranSuratController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\PengajuanWargaController;
use App\Http\Controllers\RiwayatWargaController;
use App\Http\Controllers\ProfileAdminController;
use App\Http\Controllers\ProfileWargaController;
use App\Http\Controllers\DownloadController;


/*
|--------------------------------------------------------------------------
| ROOT & UTILITY ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/login', function () {
    return view('wargav2.login'); 
})->middleware('guest')->name('login');
Route::get('/cek-surat', function () {
    $path = 'surat_keluar/surat_2.pdf';
    return response()->json([
        'exists' => Storage::disk('public')->exists($path),
        'full_path' => storage_path('app/public/' . $path),
    ]);
});

// Rute untuk nge-proses login (nembak ke AuthController lu)
Route::post('/login', [AuthController::class, 'login']);
/*
|--------------------------------------------------------------------------
| GUEST ROUTES - PROSES RECOVERY & REGISTRASI (DI LUAR MIDDLEWARE AUTH)
|--------------------------------------------------------------------------
*/
Route::post('/check-email', [AccountController::class, 'checkEmail']);
Route::post('/direct-reset-password', [AccountController::class, 'directResetPassword']);
Route::post('/register', [AccountController::class, 'register'])->middleware('guest');

/*
|--------------------------------------------------------------------------
| ROUTE GROUP - ADMIN (WEB SESSION)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::resource('kategori', KategoriSuratController::class)->names('admin.kategori');
    Route::resource('pejabat', PejabatController::class)->names('admin.pejabat');
    Route::resource('penomoran', PenomoranSuratController::class)->names('admin.penomoran');

    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('admin.pengajuan.index');
    Route::get('/pengajuan/{id}', [PengajuanController::class, 'show'])->name('admin.pengajuan.show');
    Route::post('/pengajuan/{id}/verify', [PengajuanController::class, 'verify'])->name('admin.pengajuan.verify');
    Route::delete('/pengajuan/{id}', [PengajuanController::class, 'destroy'])->name('admin.pengajuan.destroy');

    Route::get('/surat-keluar', [SuratKeluarController::class, 'index'])->name('admin.surat-keluar.index');
    Route::get('/surat-keluar/create/{id_pengajuan}', [SuratKeluarController::class, 'create'])->name('admin.surat-keluar.create');
    Route::post('/surat-keluar', [SuratKeluarController::class, 'store'])->name('admin.surat-keluar.store');
    Route::get('/surat-keluar/preview/{id}', [SuratKeluarController::class, 'preview'])->name('admin.surat-keluar.preview');
    Route::get('/surat-keluar/download/{id}', [SuratKeluarController::class, 'download'])->name('admin.surat-keluar.download');
    Route::delete('/surat-keluar/{id}', [SuratKeluarController::class, 'destroy'])->name('admin.surat-keluar.destroy');

    Route::get('/profile/edit', [ProfileAdminController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/profile/update', [ProfileAdminController::class, 'update'])->name('admin.profile.update');
});

/*
|--------------------------------------------------------------------------
| ROUTE GROUP - WARGA (MURNI AREA SETELAH LOGIN)
|--------------------------------------------------------------------------
*/
Route::prefix('warga')->middleware(['auth', 'role:warga'])->group(function () {

    // 2. Rute Riwayat (Tabel arsip data pengajuan berkas)
    Route::get('/dashboard', [RiwayatWargaController::class, 'dashboard'])->name('warga.dashboard');
    Route::get('/riwayat', [RiwayatWargaController::class, 'index'])->name('warga.riwayat');

    // 3. Rute Pengajuan (Formulir surat & timeline pelacakan berkas)
    Route::get('/pengajuan/create', [PengajuanWargaController::class, 'create'])->name('warga.pengajuan.create');
    
    // 💡 FIX SAKTI: Kata "Push" ghaib di baris ini udah dibabat habis brok!
    Route::post('/pengajuan', [PengajuanWargaController::class, 'store'])->name('warga.pengajuan.store');
    
    Route::get('/pengajuan/{id}', [PengajuanWargaController::class, 'show'])->name('warga.pengajuan.show');
    Route::get('/pengajuan/{id}/download', [PengajuanWargaController::class, 'download'])->name('warga.pengajuan.download');

    // 4. ⚙️ MANAJEMEN PROFILE WARGA (Sinkron ke profile/edit.blade.php)
    Route::get('/profile/edit', [ProfileWargaController::class, 'edit'])->name('warga.profile.edit');
    Route::patch('/profile/update', [ProfileWargaController::class, 'update'])->name('profile.update');
    Route::post('/change-password', [AccountController::class, 'changePassword'])->name('warga.password.update');
    Route::delete('/profile/destroy', [ProfileWargaController::class, 'destroy'])->name('profile.destroy');

    // Utilitas Tambahan
    Route::view('/informasi', 'wargav2.dashboard')->name('warga.informasi'); 
});

/*
|--------------------------------------------------------------------------
| GLOBAL WEB AUTHENTICATION HELPERS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return Auth::user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('warga.dashboard');
    })->name('dashboard');

    Route::get('/kategori', function () {
        return \App\Models\KategoriSurat::all();
    });
});

// Logout Web
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');

// Streaming file fisik surat
Route::get('/surat/{filename}', function ($filename) {
    $path = storage_path('app/public/surat_keluar/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path);
});

Route::get('/debug-route', function() {
    return "Route aman, Laravel jalan!";
});