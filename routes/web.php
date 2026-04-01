<?php

use Illuminate\Support\Facades\Route;
//public controllers
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DonasiController;
// Auth Controllers
use App\Http\Controllers\Auth\AuthController;
// Admin Controllers
use App\Http\Controllers\Admin\SkemaArisanController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PesertaArisanController;
use App\Http\Controllers\Admin\UserApprovalController;
use App\Http\Controllers\Admin\AdminManageController;
use App\Http\Controllers\Admin\UndianArisanController;
use App\Http\Controllers\Admin\TransaksiAdminController;
use App\Http\Controllers\Admin\KegiatanSosialController;
// Peserta Controllers
use App\Http\Controllers\Peserta\DashboardPesertaController;
use App\Http\Controllers\Peserta\KelompokController;
use App\Http\Controllers\Peserta\UndianController;
use App\Http\Controllers\Peserta\ProfilePesertaController;
use App\Http\Controllers\Peserta\TransaksiPesertaController;
use App\Http\Controllers\Peserta\JadwalController;
use App\Http\Controllers\Peserta\LaporanPesertaController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/agenda-sosial', [WelcomeController::class, 'semuaAgenda'])->name('sosial.semua');
Route::get('/agenda-sosial/{id}', [WelcomeController::class, 'detailAgenda'])->name('sosial.detail');
// Proses Checkout & Callback Midtrans (Harus Bisa Diakses Publik)
Route::post('/donasi/checkout', [DonasiController::class, 'checkout'])->name('donasi.checkout');
Route::post('/donasi/callback', [DonasiController::class, 'callback'])->name('donasi.callback');

// Halaman Redirect setelah bayar dari Midtrans
Route::get('/donasi/finish', [DonasiController::class, 'finish'])->name('donasi.finish');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        // SKEMA ARISAN ROUTES
        Route::resource('skema', SkemaArisanController::class);
        // PESERTA ARISAN ROUTES
        Route::resource('peserta', PesertaArisanController::class);
        // USER APPROVAL ROUTES
        Route::get('/pending', [UserApprovalController::class, 'index'])->name('pending.index');
        Route::post('/pending/{id}/approve', [UserApprovalController::class, 'approve'])->name('pending.approve');
        Route::post('/pending/{id}/reject', [UserApprovalController::class, 'reject'])->name('pending.reject');
        //profile & manage admin
        Route::get('/profile', [AdminManageController::class, 'index'])->name('profile.index');
        Route::get('/manage-admin', [AdminManageController::class, 'manage'])->name('manage.index');
        Route::post('/manage-admin', [AdminManageController::class, 'store'])->name('manage.store');
        Route::put('/manage-admin/{id}', [AdminManageController::class, 'update'])->name('manage.update');
        Route::delete('/manage-admin/{id}', [AdminManageController::class, 'destroy'])->name('manage.destroy');
        // UNDIAN ARISAN ROUTES
        Route::get('/undian', [UndianArisanController::class, 'index'])->name('undian.index');
        Route::post('/undian/proses', [UndianArisanController::class, 'prosesUndian'])->name('undian.proses');
        // TRANSAKSI PEMBAYARAN ROUTES
        Route::get('/transaksi', [TransaksiAdminController::class, 'index'])->name('transaksi.index');
        Route::post('/transaksi/verifikasi/{id}', [TransaksiAdminController::class, 'verifikasiManual'])->name('transaksi.verifikasi');
        Route::post('/transaksi/generate', [TransaksiAdminController::class, 'generateTagihan'])->name('transaksi.generate');
        Route::get('/transaksi/export', [TransaksiAdminController::class, 'exportPDF'])->name('transaksi.export');
        // KEGIATAN SOSIAL ROUTES
        Route::prefix('sosial')->as('sosial.')->group(function () {
            Route::get('/', [KegiatanSosialController::class, 'index'])->name('index');
            Route::post('/store', [KegiatanSosialController::class, 'store'])->name('store');
            Route::post('/kategori', [KegiatanSosialController::class, 'storeKategori'])->name('kategori.store');
            Route::put('/update/{id}', [KegiatanSosialController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [KegiatanSosialController::class, 'destroy'])->name('destroy');
            Route::post('/dana-masuk', [KegiatanSosialController::class, 'storeDanaMasuk'])->name('dana_masuk');
            Route::post('/cairkan/{id}', [KegiatanSosialController::class, 'cairkanDana'])->name('cairkan');
            Route::get('/laporan', [KegiatanSosialController::class, 'laporan'])->name('laporan');
            Route::get('/laporan/pdf/{id}', [KegiatanSosialController::class, 'exportPdf'])->name('laporan.pdf');
            Route::post('/upload-dokumentasi/{id}', [KegiatanSosialController::class, 'uploadDokumentasi'])->name('upload_dokumentasi');
        });
    });

/*
|--------------------------------------------------------------------------
| Peserta Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:peserta'])
    ->prefix('peserta')
    ->as('peserta.')
    ->group(function () {

        Route::get('/dashboard', [DashboardPesertaController::class, 'index'])
            ->name('dashboard');
        // Notifikasi 
        Route::get('/read-undian-notif', function() {
            session(['undian_notif_read' => true]);
            return redirect()->route('peserta.undian.index');
        })->name('mark-undian-read');
        // Profile Routes
        Route::get('/profile', [ProfilePesertaController::class, 'index'])->name('profile.index');
        Route::put('/profile/update', [ProfilePesertaController::class, 'update'])->name('profile.update');
        // Kelompok Arisan Routes
        Route::get('/kelompok', [KelompokController::class, 'index'])->name('kelompok.index');
        Route::post('/kelompok', [KelompokController::class, 'storeAnggota'])->name('kelompok.store');
        Route::put('/kelompok/anggota/{id}', [KelompokController::class, 'updateAnggota'])->name('kelompok.update');
        Route::delete('/kelompok/anggota/{id}', [KelompokController::class, 'destroyAnggota'])->name('kelompok.destroy');
        // Hasil Undian Routes
        Route::get('/undian', [UndianController::class, 'index'])->name('undian.index');
        // Transaksi Pembayaran Routes
        Route::get('/transaksi', [TransaksiPesertaController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/get-token/{id}', [TransaksiPesertaController::class, 'getToken'])->name('transaksi.getToken');
        // Jadwal & Tagihan Routes
        Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
        // Laporan Pembayaran Routes
        Route::get('/laporan', [LaporanPesertaController::class, 'index'])->name('laporan.index');
    });
