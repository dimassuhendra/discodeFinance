<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SumberDanaController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/quick-pengeluaran', [DashboardController::class, 'storeQuickPengeluaran'])->name('quick-pengeluaran.store');

Route::prefix('transactions')->name('transactions.')->group(function () {
    Route::get('/', [TransactionController::class, 'index'])->name('index');
    Route::post('/pemasukkan', [TransactionController::class, 'storePemasukkan'])->name('store.pemasukkan');
    Route::post('/pengeluaran', [TransactionController::class, 'storePengeluaran'])->name('store.pengeluaran');
    Route::post('/mutasi', [TransactionController::class, 'storeMutasi'])->name('store.mutasi');
    Route::delete('/{type}/{id}', [TransactionController::class, 'destroy'])->name('destroy');
});

Route::prefix('sumber-dana')->name('sumber-dana.')->group(function () {
    Route::get('/', [SumberDanaController::class, 'index'])->name('index');
    Route::post('/', [SumberDanaController::class, 'store'])->name('store');
    Route::put('/{tipe}/{id}', [SumberDanaController::class, 'update'])->name('update');
    Route::delete('/{tipe}/{id}', [SumberDanaController::class, 'destroy'])->name('destroy');
});
