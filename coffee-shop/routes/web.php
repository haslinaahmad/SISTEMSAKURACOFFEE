<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Redirect Halaman Utama ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Authentication Routes (Login, Register, Logout)
Auth::routes(); 

// 3. Protected Routes (Hanya bisa diakses jika sudah Login)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS System (Kasir)
    Route::get('/pos', [TransactionController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [TransactionController::class, 'store'])->name('pos.store');

    // Manajemen Produk & Kategori (CRUD Lengkap)
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);

    // Riwayat Transaksi & Cetak Struk
    Route::get('/transactions', [TransactionController::class, 'history'])->name('transactions.index');
    Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');
    
    // --- ROUTE PENTING UNTUK CETAK STRUK ---
    Route::get('/transactions/{id}/print', [TransactionController::class, 'print'])->name('transactions.print');

    // Keuangan (Pemasukan/Pengeluaran)
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::post('/finance', [FinanceController::class, 'store'])->name('finance.store');

    // Hutang & Piutang
    Route::get('/debts', [DebtController::class, 'index'])->name('debts.index');
    Route::post('/debts', [DebtController::class, 'store'])->name('debts.store');
    Route::post('/debts/{debt}/payment', [DebtController::class, 'addPayment'])->name('debts.payment');

    // Akun / Rekening (CRUD Lengkap)
    Route::resource('accounts', AccountController::class)->except(['create', 'edit', 'show']);

    // Laporan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});