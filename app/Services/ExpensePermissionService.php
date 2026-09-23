<?php

namespace App\Services;

use App\Models\User;
use App\Models\ExpenseUserPermission;

class ExpensePermissionService
{
    /**
     * Master daftar menu dan seluruh fitur yang dapat diatur hak aksesnya (Toggle Switch)
     * Mencakup izin login, navigasi menu, dashboard, pengeluaran (lihat/tambah/edit/hapus),
     * master templates, dan data produksi telur.
     */
    public static function getAllPermissions(): array
    {
        return [
            // 0. AKSES APLIKASI
            'app_access' => [
                'label' => '0. Akses Sistem & Login Pengeluaran',
                'icon' => 'shield-alert',
                'items' => [
                    'app_login_access' => [
                        'label' => 'Izin Login ke Sistem Pengeluaran',
                        'desc' => 'Wajib aktif agar pengguna dapat masuk/login ke dalam aplikasi Nochi Farm Pengeluaran. Jika nonaktif, pengguna otomatis ditolak saat login.',
                        'default' => false,
                    ],
                ],
            ],

            // 1. NAVIGATOR MENU & TAB (NAVBAR, DRAWER & BOTTOM BAR)
            'navigator' => [
                'label' => '1. Navigator Menu & Tab Navigasi',
                'icon' => 'layout-grid',
                'items' => [
                    'menu_dashboard' => [
                        'label' => 'Menu Beranda & Dashboard',
                        'desc' => 'Menampilkan link navigasi dan membuka halaman Beranda & Dashboard Transaksi',
                        'default' => false,
                    ],
                    'menu_expenses_create' => [
                        'label' => 'Menu Catat Pengeluaran Baru',
                        'desc' => 'Menampilkan tombol (+) pada navbar, mobile drawer, dan floating bottom bar',
                        'default' => false,
                    ],
                    'menu_expenses_index' => [
                        'label' => 'Menu Riwayat Pengeluaran',
                        'desc' => 'Menampilkan link navigasi dan membuka halaman Daftar Riwayat Pengeluaran Kandang',
                        'default' => false,
                    ],
                    'menu_production' => [
                        'label' => 'Menu Produksi Telur',
                        'desc' => 'Menampilkan link navigasi dan membuka halaman Data Produksi Telur Farm',
                        'default' => false,
                    ],
                    'menu_master_templates' => [
                        'label' => 'Menu Master Template Pengeluaran',
                        'desc' => 'Menampilkan link navigasi dan membuka halaman Master Template Pengeluaran Rutin',
                        'default' => false,
                    ],
                ],
            ],

            // 2. BERANDA & DASHBOARD KEUANGAN
            'dashboard' => [
                'label' => '2. Beranda & Dashboard Keuangan Transaksi',
                'icon' => 'layout-dashboard',
                'items' => [
                    'dashboard_view' => [
                        'label' => 'Akses Halaman Dashboard',
                        'desc' => 'Membuka dan melihat tampilan dashboard utama sistem pengeluaran',
                        'default' => false,
                    ],
                    'dashboard_filter_date' => [
                        'label' => 'Filter Tanggal & Kalender (Traveloka)',
                        'desc' => 'Izin memilih rentang tanggal kalender dan filter periode keuangan di dashboard',
                        'default' => false,
                    ],
                    'dashboard_view_balance' => [
                        'label' => 'Kartu Saldo, Pemasukan & Pengeluaran',
                        'desc' => 'Melihat angka ringkasan kartu Saldo Realtime, Pemasukan Kasir (Omzet), dan Total Pengeluaran',
                        'default' => false,
                    ],
                    'dashboard_view_charts' => [
                        'label' => 'Grafik Tren Keuangan & Kategori',
                        'desc' => 'Melihat visualisasi grafik Chart.js aliran kas harian dan rincian alokasi biaya',
                        'default' => false,
                    ],
                    'dashboard_view_transaction_detail' => [
                        'label' => 'Modal Detail Transaksi',
                        'desc' => 'Izin mengklik transaksi pada timeline aktivitas untuk membuka modal rincian transaksi',
                        'default' => false,
                    ],
                    'dashboard_export_report' => [
                        'label' => 'Cetak Laporan Investor & Export CSV',
                        'desc' => 'Akses tombol cetak laporan keuangan resmi investor dan download data format CSV',
                        'default' => false,
                    ],
                ],
            ],

            // 3. MODUL PENGELUARAN KANDANG
            'expenses' => [
                'label' => '3. Modul Pengeluaran Kandang (Aksi Operasional)',
                'icon' => 'receipt',
                'items' => [
                    'expense_view' => [
                        'label' => 'Lihat Tabel Riwayat Pengeluaran',
                        'desc' => 'Membuka dan melihat seluruh data catatan pengeluaran operasional kandang',
                        'default' => false,
                    ],
                    'expense_view_detail' => [
                        'label' => 'Lihat Detail Pengeluaran & Nota',
                        'desc' => 'Membuka modal detail pengeluaran dan melihat lampiran foto nota transaksi',
                        'default' => false,
                    ],
                    'expense_create' => [
                        'label' => 'Catat / Tambah Pengeluaran Baru',
                        'desc' => 'Mengakses formulir input pengeluaran, upload foto nota, dan menyimpan transaksi baru',
                        'default' => false,
                    ],
                    'expense_edit' => [
                        'label' => 'Ubah / Edit Catatan Pengeluaran',
                        'desc' => 'Menampilkan tombol edit dan izin mengubah nominal, kategori, tanggal, atau nota',
                        'default' => false,
                    ],
                    'expense_delete' => [
                        'label' => 'Hapus Catatan Pengeluaran',
                        'desc' => 'Menampilkan tombol hapus dan izin membatalkan/menghapus transaksi pengeluaran',
                        'default' => false,
                    ],
                    'expense_filter' => [
                        'label' => 'Filter Kategori & Pencarian Pengeluaran',
                        'desc' => 'Akses menggunakan filter kategori, rentang tanggal, dan search bar riwayat pengeluaran',
                        'default' => false,
                    ],
                ],
            ],

            // 4. MASTER TEMPLATE PENGELUARAN RUTIN
            'master_templates' => [
                'label' => '4. Master Template Pengeluaran Rutin',
                'icon' => 'zap',
                'items' => [
                    'template_view' => [
                        'label' => 'Lihat Daftar Master Template',
                        'desc' => 'Melihat daftar template pengeluaran rutin 1-klik di halaman master',
                        'default' => false,
                    ],
                    'template_create' => [
                        'label' => 'Tambah Template Baru',
                        'desc' => 'Menampilkan tombol dan formulir penambahan template pengeluaran baru',
                        'default' => false,
                    ],
                    'template_edit' => [
                        'label' => 'Ubah / Edit Template',
                        'desc' => 'Menampilkan tombol dan formulir perbaikan/perubahan data template pengeluaran',
                        'default' => false,
                    ],
                    'template_toggle' => [
                        'label' => 'Ubah Status Aktif / Nonaktif Template',
                        'desc' => 'Izin mengklik badge status aktif/nonaktif template pengeluaran',
                        'default' => false,
                    ],
                    'template_delete' => [
                        'label' => 'Hapus Template Pengeluaran',
                        'desc' => 'Menampilkan tombol dan izin menghapus template dari database',
                        'default' => false,
                    ],
                ],
            ],

            // 5. DATA PRODUKSI TELUR
            'production' => [
                'label' => '5. Data Produksi Telur (Sinkronisasi Kandang)',
                'icon' => 'egg',
                'items' => [
                    'production_view' => [
                        'label' => 'Lihat Data Produksi Telur',
                        'desc' => 'Melihat tabel riwayat panen telur hasil sinkronisasi inputan kandang',
                        'default' => false,
                    ],
                    'production_view_detail' => [
                        'label' => 'Lihat Detail Produksi Telur',
                        'desc' => 'Melihat rincian panen per blok (peti, butir, abnormal/rusak)',
                        'default' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * Periksa apakah pengguna memiliki hak akses ke fitur/menu tertentu
     */
    public static function canAccess(?User $user, string $permissionKey): bool
    {
        if (!$user) {
            return false;
        }

        // Pengguna non-aktif tidak memiliki akses sama sekali
        if (!$user->is_active) {
            return false;
        }

        // Role 'admin' selalu memiliki akses penuh tanpa batas (Full Access)
        if ($user->role === 'admin') {
            return true;
        }

        // Cari pengaturan spesifik user di tabel expense_user_permissions
        try {
            $record = ExpenseUserPermission::where('user_id', $user->id)
                ->where('permission_key', $permissionKey)
                ->first();

            if ($record !== null) {
                return (bool) $record->is_enabled;
            }
        } catch (\Throwable $e) {
            // Fallback ke default registry jika tabel/DB terjadi error
        }

        // Jika belum diset di tabel, gunakan default dari master registry (umumnya false untuk non-admin)
        foreach (self::getAllPermissions() as $category) {
            if (isset($category['items'][$permissionKey])) {
                return (bool) $category['items'][$permissionKey]['default'];
            }
        }

        return false;
    }

    /**
     * Helper untuk memeriksa apakah pengguna boleh login ke sistem pengeluaran
     */
    public static function canLogin(?User $user): bool
    {
        if (!$user || !$user->is_active) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        return self::canAccess($user, 'app_login_access');
    }
}
