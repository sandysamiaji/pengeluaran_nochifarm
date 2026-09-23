<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MasterTemplateController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\PermissionController;

/*
|--------------------------------------------------------------------------
| Web Routes - Nochi Farm Pengeluaran & Transaksi Kandang
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. Autentikasi Publik (Guest)
// =========================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// =========================================================================
// 2. Rute Terproteksi (Wajib Login & Punya Izin Akses)
// =========================================================================
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 1. Dashboard Utama: Saldo Realtime, Pemasukan (Omzet), Pengeluaran, & Filter Traveloka
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:menu_dashboard');

    // 2. Detail Transaksi (Modal Pemasukan Kasir & Pengeluaran)
    Route::get('/transaksi/{type}/{id}', [DashboardController::class, 'getTransactionDetail'])
        ->name('transaksi.detail')
        ->middleware('permission:dashboard_view_transaction_detail');

    // 3. Laporan Resmi Investor & Export Data
    Route::get('/laporan/cetak', [DashboardController::class, 'printReport'])
        ->name('report.print')
        ->middleware('permission:dashboard_export_report');

    Route::get('/laporan/export-csv', [DashboardController::class, 'exportCsv'])
        ->name('report.csv')
        ->middleware('permission:dashboard_export_report');

    // 4. Modul Pengeluaran Kandang
    Route::prefix('pengeluaran')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])
            ->name('index')
            ->middleware('permission:menu_expenses_index');

        Route::get('/tambah', [ExpenseController::class, 'create'])
            ->name('create')
            ->middleware('permission:expense_create');

        Route::post('/', [ExpenseController::class, 'store'])
            ->name('store')
            ->middleware('permission:expense_create');

        Route::get('/{id}', [ExpenseController::class, 'show'])
            ->name('show')
            ->middleware('permission:expense_view_detail');

        Route::put('/{id}', [ExpenseController::class, 'update'])
            ->name('update')
            ->middleware('permission:expense_edit');

        Route::delete('/{id}', [ExpenseController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:expense_delete');
    });

    // 5. Master Data: Template Pengeluaran Rutin
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/templates', [MasterTemplateController::class, 'index'])
            ->name('templates.index')
            ->middleware('permission:menu_master_templates');

        Route::post('/templates', [MasterTemplateController::class, 'store'])
            ->name('templates.store')
            ->middleware('permission:template_create');

        Route::put('/templates/{id}', [MasterTemplateController::class, 'update'])
            ->name('templates.update')
            ->middleware('permission:template_edit');

        Route::post('/templates/{id}/toggle', [MasterTemplateController::class, 'toggle'])
            ->name('templates.toggle')
            ->middleware('permission:template_toggle');

        Route::delete('/templates/{id}', [MasterTemplateController::class, 'destroy'])
            ->name('templates.destroy')
            ->middleware('permission:template_delete');

        // 6. Manajemen Hak Akses Pengguna (Khusus Admin)
        Route::get('/hak-akses', [PermissionController::class, 'index'])->name('permissions');
        Route::post('/hak-akses/{userId}/toggle', [PermissionController::class, 'toggle'])->name('permissions.toggle');
        Route::post('/hak-akses/{userId}/bulk', [PermissionController::class, 'bulk'])->name('permissions.bulk');
        Route::post('/users/store', [PermissionController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{id}/update', [PermissionController::class, 'updateUser'])->name('users.update');
        Route::patch('/users/{id}/toggle-active', [PermissionController::class, 'toggleActiveUser'])->name('users.toggle-active');
    });

    // 7. Data Produksi Telur (Sinkronisasi Hasil Input Kandang)
    Route::prefix('produksi')->name('production.')->group(function () {
        Route::get('/', [ProductionController::class, 'index'])
            ->name('index')
            ->middleware('permission:menu_production');

        Route::get('/{id}', [ProductionController::class, 'show'])
            ->name('show')
            ->middleware('permission:production_view_detail');
    });

});
