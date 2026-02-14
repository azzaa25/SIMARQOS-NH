<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\SkemaArisanController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PesertaArisanController;
use App\Http\Controllers\Admin\UserApprovalController;
use App\Http\Controllers\Admin\AdminManageController;
use App\Http\Controllers\Admin\UndianArisanController;
use App\Http\Controllers\Peserta\DashboardPesertaController;
use App\Http\Controllers\Peserta\KelompokController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'));

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
        // Kelompok Arisan Routes
        Route::get('/kelompok', [KelompokController::class, 'index'])->name('kelompok.index');
        Route::post('/kelompok', [KelompokController::class, 'storeAnggota'])->name('kelompok.store');
        Route::put('/kelompok/anggota/{id}', [KelompokController::class, 'updateAnggota'])->name('kelompok.update');
        Route::delete('/kelompok/anggota/{id}', [KelompokController::class, 'destroyAnggota'])->name('kelompok.destroy');
    });
