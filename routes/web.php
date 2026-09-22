<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;

/*
|--------------------------------------------------------------------------
| Web Routes - Nochi Farm Pengeluaran & Dashboard Transaksi
|--------------------------------------------------------------------------
*/

// 1. Dashboard Utama: Saldo Realtime, Pemasukan (Omzet), Pengeluaran, & Filter Traveloka
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// 2. Detail Transaksi (Modal Pemasukan & Pengeluaran)
Route::get('/transaksi/{type}/{id}', [DashboardController::class, 'getTransactionDetail'])->name('transaksi.detail');

// 3. Modul Pengeluaran Kandang Sesuai Mockup 5-Layar
Route::get('/pengeluaran', [ExpenseController::class, 'index'])->name('expenses.index');
Route::get('/pengeluaran/tambah', [ExpenseController::class, 'create'])->name('expenses.create');
Route::post('/pengeluaran', [ExpenseController::class, 'store'])->name('expenses.store');
Route::delete('/pengeluaran/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

// 4. Laporan Resmi Investor & Export Data
Route::get('/laporan/cetak', [DashboardController::class, 'printReport'])->name('report.print');
Route::get('/laporan/export-csv', [DashboardController::class, 'exportCsv'])->name('report.csv');

// 5. Master Data: Template Pengeluaran Rutin
use App\Http\Controllers\MasterTemplateController;
Route::get('/master/templates', [MasterTemplateController::class, 'index'])->name('master.templates.index');
Route::post('/master/templates', [MasterTemplateController::class, 'store'])->name('master.templates.store');
Route::put('/master/templates/{id}', [MasterTemplateController::class, 'update'])->name('master.templates.update');
Route::post('/master/templates/{id}/toggle', [MasterTemplateController::class, 'toggle'])->name('master.templates.toggle');
Route::delete('/master/templates/{id}', [MasterTemplateController::class, 'destroy'])->name('master.templates.destroy');


