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

/*
|--------------------------------------------------------------------------
| ROOT & AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect()->route('login'));

// Login
Route::get('/login', fn() => view('wargav2.login'))->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Register & Password (Mapping ke folder auth/...)
Route::get('/register', fn() => view('auth.regis'))->middleware('guest')->name('register');
Route::post('/register', [AccountController::class, 'register'])->middleware('guest');

Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
Route::post('/check-email', [AccountController::class, 'checkEmail']);
Route::post('/direct-reset-password', [AccountController::class, 'directResetPassword']);

// Utility
Route::get('/cek-surat', function () {
    $path = 'surat_keluar/surat_2.pdf';
    return response()->json([
        'exists' => Storage::disk('public')->exists($path),
        'full_path' => storage_path('app/public/' . $path),
    ]);
});

/*
|--------------------------------------------------------------------------
| ROUTE GROUP - ADMIN
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
| ROUTE GROUP - WARGA
|--------------------------------------------------------------------------
*/
Route::prefix('warga')->middleware(['auth', 'role:warga'])->group(function () {
    Route::get('/dashboard', [RiwayatWargaController::class, 'dashboard'])->name('warga.dashboard');
    Route::get('/riwayat', [RiwayatWargaController::class, 'index'])->name('warga.riwayat');
    Route::get('/pengajuan/create', [PengajuanWargaController::class, 'create'])->name('warga.pengajuan.create');
    Route::post('/pengajuan', [PengajuanWargaController::class, 'store'])->name('warga.pengajuan.store');
    Route::get('/pengajuan/{id}', [PengajuanWargaController::class, 'show'])->name('warga.pengajuan.show');
    Route::get('/pengajuan/{id}/download', [PengajuanWargaController::class, 'download'])->name('warga.pengajuan.download');
    Route::get('/profile/edit', [ProfileWargaController::class, 'edit'])->name('warga.profile.edit');
    Route::patch('/profile/update', [ProfileWargaController::class, 'update'])->name('profile.update');
    Route::post('/change-password', [AccountController::class, 'changePassword'])->name('warga.password.update');
    Route::delete('/profile/destroy', [ProfileWargaController::class, 'destroy'])->name('profile.destroy');
    Route::view('/informasi', 'wargav2.dashboard')->name('warga.informasi'); 
});

/*
|--------------------------------------------------------------------------
| GLOBAL HELPERS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return Auth::user()->role === 'admin' 
            ? redirect()->route('admin.dashboard') 
            : redirect()->route('warga.dashboard');
    })->name('dashboard');
});

Route::get('/surat/{filename}', function ($filename) {
    $path = storage_path('app/public/surat_keluar/' . $filename);
    if (!file_exists($path)) abort(404);
    return response()->file($path);
});