<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return redirect()->route('kasir.login');
});

Route::get('/kasir/login', [AuthController::class, 'loginKasirForm'])->name('kasir.login');
Route::post('/kasir/login', [AuthController::class, 'loginKasir']);
Route::get('/admin/login', [AuthController::class, 'loginAdminForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'loginAdmin']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth:kasir'])->prefix('kasir')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('kasir.index');
    Route::post('/search-product', [PosController::class, 'searchProduct'])->name('kasir.search');
    Route::post('/transaction', [PosController::class, 'storeTransaction'])->name('kasir.transaction');
});

Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::resource('products', \App\Http\Controllers\ProductController::class);
    Route::resource('reports', \App\Http\Controllers\ReportController::class)->only(['index']);

    // Cashier Management
    Route::resource('cashiers', \App\Http\Controllers\CashierController::class);
    Route::patch('/cashiers/{id}/toggle-status', [\App\Http\Controllers\CashierController::class, 'toggleStatus'])->name('cashiers.toggle-status');
    Route::patch('/cashiers/{id}/reset-pin', [\App\Http\Controllers\CashierController::class, 'resetPin'])->name('cashiers.reset-pin');

    // Bon Management
    Route::get('/bon', [\App\Http\Controllers\BonController::class, 'index'])->name('bon.index');
    Route::post('/bon/{id}/settle', [\App\Http\Controllers\BonController::class, 'settle'])->name('bon.settle');
});
