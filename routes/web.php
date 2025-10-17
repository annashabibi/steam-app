<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MotorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\HelmTransactionController;
use App\Http\Controllers\FoodController;
use Illuminate\Http\Request;

// Jika belum login, langsung redirect ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Group route yang butuh login
Route::middleware('auth')->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Motors
    Route::resource('motors', MotorController::class);

    // Karyawan
    Route::resource('karyawans', KaryawanController::class)->except(['show']);
    Route::post('/karyawans/{id}/status', [KaryawanController::class, 'status'])->name('karyawans.status');

    // Food
    Route::resource('food', FoodController::class);

    // Transactions
    Route::resource('transactions', TransactionController::class);
    // Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');

    // Pengeluaran
    Route::resource('pengeluaran', PengeluaranController::class);

    // Pendapatan index
    Route::get('/gaji', [GajiController::class, 'index'])->name('gaji.index');
    Route::get('/gaji/filter', [GajiController::class, 'filter'])->name('gaji.filter');
    // Route::post('/gaji/store', [GajiController::class, 'store'])->name('gaji.store');
    Route::get('/gaji/print/{id}', [GajiController::class, 'printGaji'])->name('gaji.print');


    // Report
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/filter', [ReportController::class, 'filter'])->name('report.filter');
    Route::get('/report/print/{type}', [ReportController::class, 'print'])->name('report.print');

    // Midtrans
    Route::get('/payments/{transaction}/pay', [PaymentController::class, 'pay'])->name('midtrans.pay');
    // Route::get('/transactions/{transaction}/pay', [PaymentController::class, 'pay'])->name('pay');
    Route::get('/transactions/{id}/transaction', [TransactionController::class, 'transaction'])->name('transaction');
    Route::get('/payment/{transaction}/status', [PaymentController::class, 'checkStatus'])->name('payment.checkStatus');
    

    // Helm
    Route::resource('helms', HelmTransactionController::class);
    Route::post('/helms/deleteAll', [HelmTransactionController::class, 'deleteAll'])->name('helms.deleteAll');
    Route::get('/helms/{helm_transaction}/pay', [PaymentController::class, 'payHelm'])->name('helms.pay');
    Route::get('/helms/{id}/transaction', [HelmTransactionController::class, 'transaction'])->name('helms.transaction');
});

// Auth routes
require __DIR__.'/auth.php';
