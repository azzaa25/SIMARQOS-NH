<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TransaksiAdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rute ini akan memiliki prefix 'api', jadi URL-nya nanti: domain.com/api/midtrans-callback
Route::post('/midtrans-callback', [TransaksiAdminController::class, 'callback']);