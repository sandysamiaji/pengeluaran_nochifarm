<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Expense;
use App\Services\WarehouseInventoryService;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    /**
     * Dashboard Keuangan Nochi Farm
     */
    public function index(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $typeFilter = $request->query('type', 'all'); // all, income, expense
        $search = $request->query('q');

        // Cek apakah filter rentang tanggal gaya Traveloka aktif
        $isFilterActive = !empty($startDate) && !empty($endDate);

        // 1. Query Pemasukan (Sales dari database nochifram)
        $salesQuery = Sale::with(['items', 'user', 'trip.user'])->orderBy('date', 'desc')->orderBy('id', 'desc');
        if ($isFilterActive) {
            $salesQuery->whereBetween('date', [$startDate, $endDate]);
        }

        // 2. Query Pengeluaran (Expenses dari nochifarmpengeluaran)
        $expensesQuery = Expense::with('user')->orderBy('date', 'desc')->orderBy('id', 'desc');
        if ($isFilterActive) {
            $expensesQuery->whereBetween('date', [$startDate, $endDate]);
        }

        // 3. Kalkulasi Ringkasan Keuangan Utama
        $totalPemasukan = (clone $salesQuery)->sum('total_amount');
        $totalPengeluaran = (clone $expensesQuery)->sum('amount');
        $saldoSaatIni = $totalPemasukan - $totalPengeluaran;

        $countSales = (clone $salesQuery)->count();
        $countExpenses = (clone $expensesQuery)->count();
        $totalTransaksi = $countSales + $countExpenses;

        // Metrik Laba Rugi untuk Investor
        $profitMargin = $totalPemasukan > 0 ? round(($saldoSaatIni / $totalPemasukan) * 100, 1) : 0;
        $opexRatio = $totalPemasukan > 0 ? round(($totalPengeluaran / $totalPemasukan) * 100, 1) : 0;

        // Ambil data untuk tabel transaksi gabungan
        $sales = $salesQuery->get();
        $expenses = $expensesQuery->get();

        // Pemisahan Saldo Kas Tunai vs Rekening Bank
        $tunaiPemasukan = $sales->filter(fn($s) => strtolower($s->payment_method) === 'tunai')->sum('total_amount');
        $transferPemasukan = $sales->filter(fn($s) => strtolower($s->payment_method) !== 'tunai')->sum('total_amount');
        $tunaiPengeluaran = $expenses->filter(fn($e) => strtolower($e->payment_method ?? 'kas tunai') === 'kas tunai')->sum('amount');
        $transferPengeluaran = $expenses->filter(fn($e) => strtolower($e->payment_method ?? 'kas tunai') !== 'kas tunai')->sum('amount');

        $saldoKasTunai = $tunaiPemasukan - $tunaiPengeluaran;
        $saldoBankTransfer = $transferPemasukan - $transferPengeluaran;

        // 4. Transformasi menjadi Satu Daftar Transaksi Seragam
        $allTransactions = collect();

        // Map Pemasukan
        if ($typeFilter === 'all' || $typeFilter === 'income') {
            foreach ($sales as $sale) {
                $itemsSummary = $sale->items->map(function ($it) {
                    return $it->item_name . ' (' . (float)$it->quantity . ' ' . $it->unit . ')';
                })->implode(', ');

                $penginputUser = $sale->user;
                $penginputUsername = $penginputUser ? ($penginputUser->username ?: $penginputUser->name) : 'admin';
                $penginputName = $penginputUser ? $penginputUser->name : 'Administrator';

                $trip = $sale->trip;
                $tripUser = $trip ? $trip->user : null;
                $perjalananUsername = $tripUser ? ($tripUser->username ?: $tripUser->name) : ($trip ? 'Driver' : null);
                $perjalananName = $tripUser ? $tripUser->name : ($trip ? 'Petugas Trip' : null);
                $tripCode = $trip ? $trip->trip_code : null;
                $tripRoute = $trip ? $trip->route : null;
                $tripVehicle = $trip ? $trip->vehicle : null;

                $allTransactions->push([
                    'id' => $sale->id,
                    'type' => 'pemasukan',
                    'date' => Carbon::parse($sale->date)->format('Y-m-d'),
                    'datetime' => $sale->created_at ? $sale->created_at->format('Y-m-d H:i') : Carbon::parse($sale->date)->format('Y-m-d 00:00'),
                    'display_date' => Carbon::parse($sale->date)->translatedFormat('d M Y'),
                    'code' => $sale->invoice_no ?: ('INV-' . str_pad($sale->id, 5, '0', STR_PAD_LEFT)),
                    'title' => 'Penjualan' . ($sale->customer_name ? ' - ' . $sale->customer_name : ' Tunai'),
                    'category' => 'Penjualan',
                    'subcategory' => ucfirst($sale->category ?: 'Telur/Pakan'),
                    'description' => $itemsSummary ?: ($sale->notes ?: 'Transaksi Penjualan Nochi Farm'),
                    'payment_method' => $sale->payment_method ?: 'Tunai',
                    'payment_status' => $sale->payment_status ?: 'Lunas',
                    'pemasukan' => (float)$sale->total_amount,
                    'pengeluaran' => 0,
                    'penginput_username' => $penginputUsername,
                    'penginput_name' => $penginputName,
                    'perjalanan_username' => $perjalananUsername,
                    'perjalanan_name' => $perjalananName,
                    'trip_code' => $tripCode,
                    'trip_route' => $tripRoute,
                    'trip_vehicle' => $tripVehicle,
                    'receipt_photo' => null,
                    'raw_model' => $sale,
                ]);
            }
        }

        // Map Pengeluaran
        if ($typeFilter === 'all' || $typeFilter === 'expense') {
            foreach ($expenses as $expense) {
                $penginputUser = $expense->user;
                $penginputUsername = $penginputUser ? ($penginputUser->username ?: $penginputUser->name) : 'admin';
                $penginputName = $penginputUser ? $penginputUser->name : 'Admin Kandang';

                $allTransactions->push([
                    'id' => $expense->id,
                    'type' => 'pengeluaran',
                    'date' => Carbon::parse($expense->date)->format('Y-m-d'),
                    'datetime' => $expense->created_at ? $expense->created_at->format('Y-m-d H:i') : Carbon::parse($expense->date)->format('Y-m-d 00:00'),
                    'display_date' => Carbon::parse($expense->date)->translatedFormat('d M Y'),
                    'code' => $expense->transaction_code,
                    'title' => $expense->purpose,
                    'category' => $expense->category,
                    'subcategory' => $expense->subcategory,
                    'description' => $expense->notes ?: ($expense->category . ' - ' . $expense->subcategory),
                    'payment_method' => $expense->payment_method ?? 'Kas Tunai',
                    'payment_status' => 'Dibayar',
                    'pemasukan' => 0,
                    'pengeluaran' => (float)$expense->amount,
                    'penginput_username' => $penginputUsername,
                    'penginput_name' => $penginputName,
                    'perjalanan_username' => null,
                    'perjalanan_name' => null,
                    'trip_code' => null,
                    'trip_route' => null,
                    'trip_vehicle' => null,
                    'receipt_photo' => $expense->receipt_photo ? asset($expense->receipt_photo) : null,
                    'raw_model' => $expense,
                ]);
            }
        }

        // Pencarian jika ada
        if (!empty($search)) {
            $searchLower = strtolower($search);
            $allTransactions = $allTransactions->filter(function ($item) use ($searchLower) {
                return str_contains(strtolower($item['code']), $searchLower)
                    || str_contains(strtolower($item['title']), $searchLower)
                    || str_contains(strtolower($item['category']), $searchLower)
                    || str_contains(strtolower($item['subcategory']), $searchLower)
                    || str_contains(strtolower($item['description']), $searchLower)
                    || str_contains(strtolower($item['penginput_username'] ?? ''), $searchLower)
                    || str_contains(strtolower($item['penginput_name'] ?? ''), $searchLower)
                    || str_contains(strtolower($item['perjalanan_username'] ?? ''), $searchLower)
                    || str_contains(strtolower($item['trip_code'] ?? ''), $searchLower);
            });
        }

        // Sort kronologis menurun (tanggal terbaru di atas)
        $allTransactions = $allTransactions->sortByDesc(function ($item) {
            return $item['datetime'] . '_' . str_pad($item['id'], 8, '0', STR_PAD_LEFT);
        })->values();

        // Paginate Transaksi 10 per Halaman Sesuai Permintaan
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('page');
        $currentItems = $allTransactions->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedTransactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $allTransactions->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // 5. Data Chart: Rekapitulasi per Kategori Pengeluaran (Donut Chart)
        $categoryBreakdown = $expenses->groupBy('category')->map(function ($items, $cat) use ($totalPengeluaran) {
            $total = $items->sum('amount');
            $pct = $totalPengeluaran > 0 ? round(($total / $totalPengeluaran) * 100, 1) : 0;
            return [
                'category' => $cat,
                'total' => $total,
                'count' => $items->count(),
                'percentage' => $pct,
            ];
        })->sortByDesc('total')->values();

        // 6. Data Chart: Tren Arus Kas Bulanan (Bar/Line Chart)
        $allSalesForChart = Sale::selectRaw('DATE_FORMAT(date, "%Y-%m") as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('total', 'month')
            ->toArray();

        $allExpensesForChart = Expense::selectRaw('DATE_FORMAT(date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('total', 'month')
            ->toArray();

        // Ambil daftar unik semua bulan
        $monthsUnion = array_unique(array_merge(array_keys($allSalesForChart), array_keys($allExpensesForChart)));
        sort($monthsUnion);

        // Jika data kurang dari 3 bulan, tambahkan rentang default
        if (count($monthsUnion) < 3) {
            for ($i = 2; $i >= 0; $i--) {
                $m = Carbon::now()->subMonths($i)->format('Y-m');
                if (!in_array($m, $monthsUnion)) {
                    $monthsUnion[] = $m;
                }
            }
            sort($monthsUnion);
        }

        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];

        foreach ($monthsUnion as $m) {
            $chartLabels[] = Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y');
            $chartIncome[] = (float)($allSalesForChart[$m] ?? 0);
            $chartExpense[] = (float)($allExpensesForChart[$m] ?? 0);
        }

        // 7. Format Pesan WhatsApp Share
        $periodeStr = $isFilterActive
            ? Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y')
            : 'Semua Riwayat s/d ' . Carbon::today()->translatedFormat('d F Y');

        $waText = "*LAPORAN KAS NOCHI FARM*\n";
        $waText .= "*Periode:* " . $periodeStr . "\n";
        $waText .= "------------------------------------\n";
        $waText .= "💰 *Saldo Kas Saat Ini:* Rp " . number_format($saldoSaatIni, 0, ',', '.') . "\n";
        $waText .= "🟢 *Total Omzet Penjualan:* Rp " . number_format($totalPemasukan, 0, ',', '.') . "\n";
        $waText .= "🔴 *Total Biaya Operasional:* Rp " . number_format($totalPengeluaran, 0, ',', '.') . "\n";
        $waText .= "📈 *Profit Margin:* " . $profitMargin . "%\n";
        $waText .= "------------------------------------\n";
        $waText .= "*Pos Pengeluaran Terbesar:*\n";
        foreach ($categoryBreakdown->take(3) as $i => $cb) {
            $waText .= ($i + 1) . ". " . $cb['category'] . ": Rp " . number_format($cb['total'], 0, ',', '.') . " (" . $cb['percentage'] . "%)\n";
        }
        $waText .= "------------------------------------\n";
        $waText .= "_Laporan real-time resmi dari Sistem Nochi Farm_";

        $waUrl = "https://wa.me/?text=" . urlencode($waText);

        // 8. Ringkasan Aset & Stok Mengendap di Gudang (Telur, Pakan Layer/Grower, Populasi Sisa Ayam)
        $inventorySummary = WarehouseInventoryService::getInventorySummary();

        // 9. Master Kategori untuk Edit Modal Pengeluaran
        $categories = ExpenseController::getCategoriesData();

        return view('dashboard', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'saldoSaatIni',
            'countSales',
            'countExpenses',
            'totalTransaksi',
            'allTransactions',
            'paginatedTransactions',
            'startDate',
            'endDate',
            'isFilterActive',
            'typeFilter',
            'search',
            'categoryBreakdown',
            'profitMargin',
            'opexRatio',
            'tunaiPemasukan',
            'transferPemasukan',
            'tunaiPengeluaran',
            'transferPengeluaran',
            'saldoKasTunai',
            'saldoBankTransfer',
            'chartLabels',
            'chartIncome',
            'chartExpense',
            'waUrl',
            'inventorySummary',
            'categories'
        ));
    }

    /**
     * Endpoint API JSON untuk detail transaksi (Pemasukan atau Pengeluaran)
     */
    public function getTransactionDetail(Request $request, $type, $id)
    {
        if ($type === 'pemasukan') {
            $sale = Sale::with(['items', 'user', 'trip.user'])->find($id);
            if (!$sale) {
                return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
            }

            $penginputUser = $sale->user;
            $trip = $sale->trip;
            $tripUser = $trip ? $trip->user : null;

            return response()->json([
                'success' => true,
                'type' => 'pemasukan',
                'data' => [
                    'id' => $sale->id,
                    'invoice_no' => $sale->invoice_no,
                    'date' => Carbon::parse($sale->date)->translatedFormat('d F Y'),
                    'customer_name' => $sale->customer_name ?: 'Pelanggan Umum',
                    'customer_phone' => $sale->customer_phone ?: '-',
                    'payment_method' => $sale->payment_method ?: 'Tunai',
                    'payment_status' => $sale->payment_status ?: 'Lunas',
                    'category' => $sale->category ?: 'Telur/Pakan',
                    'total_amount' => $sale->total_amount,
                    'formatted_amount' => 'Rp ' . number_format($sale->total_amount, 0, ',', '.'),
                    'penginput_username' => $penginputUser ? ($penginputUser->username ?: $penginputUser->name) : 'admin',
                    'penginput_name' => $penginputUser ? $penginputUser->name : 'Administrator',
                    'penginput_role' => $penginputUser ? ($penginputUser->role ?? 'Kasir / Admin') : 'Admin',
                    'perjalanan_username' => $tripUser ? ($tripUser->username ?: $tripUser->name) : ($trip ? 'Driver' : null),
                    'perjalanan_name' => $tripUser ? $tripUser->name : ($trip ? 'Petugas Trip' : null),
                    'trip_code' => $trip ? $trip->trip_code : null,
                    'trip_route' => $trip ? $trip->route : null,
                    'trip_vehicle' => $trip ? $trip->vehicle : null,
                    'notes' => $sale->notes ?: '-',
                    'items' => $sale->items->map(function ($item) {
                        return [
                            'item_name' => $item->item_name,
                            'quantity' => (float)$item->quantity,
                            'unit' => $item->unit,
                            'unit_price' => (float)$item->unit_price,
                            'formatted_unit_price' => 'Rp ' . number_format($item->unit_price, 0, ',', '.'),
                            'total_price' => (float)$item->total_price,
                            'formatted_total_price' => 'Rp ' . number_format($item->total_price, 0, ',', '.'),
                        ];
                    }),
                ],
            ]);
        } elseif ($type === 'pengeluaran') {
            $expense = Expense::with('user')->find($id);
            if (!$expense) {
                return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
            }

            $penginputUser = $expense->user;

            return response()->json([
                'success' => true,
                'type' => 'pengeluaran',
                'data' => [
                    'id' => $expense->id,
                    'transaction_code' => $expense->transaction_code,
                    'date' => Carbon::parse($expense->date)->translatedFormat('d F Y'),
                    'category' => $expense->category,
                    'subcategory' => $expense->subcategory,
                    'purpose' => $expense->purpose,
                    'amount' => $expense->amount,
                    'raw_amount' => (float)$expense->amount,
                    'payment_method' => $expense->payment_method ?? 'Kas Tunai',
                    'formatted_amount' => 'Rp ' . number_format($expense->amount, 0, ',', '.'),
                    'raw_date' => Carbon::parse($expense->date)->format('Y-m-d'),
                    'penginput_username' => $penginputUser ? ($penginputUser->username ?: $penginputUser->name) : 'admin',
                    'penginput_name' => $penginputUser ? $penginputUser->name : 'Admin Kandang',
                    'penginput_role' => $penginputUser ? ($penginputUser->role ?? 'Petugas Input') : 'Petugas Kandang',
                    'perjalanan_username' => null,
                    'perjalanan_name' => null,
                    'trip_code' => null,
                    'notes' => $expense->notes ?: '-',
                    'raw_notes' => $expense->notes ?? '',
                    'receipt_photo' => $expense->receipt_photo ? asset($expense->receipt_photo) : null,
                ],
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Tipe transaksi tidak valid.'], 400);
    }

    /**
     * Halaman Cetak Laporan Keuangan Resmi Investor / Owner (Print / Save as PDF)
     */
    public function printReport(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $isFilterActive = !empty($startDate) && !empty($endDate);

        $salesQuery = Sale::with(['items', 'user', 'trip.user'])->orderBy('date', 'asc');
        $expensesQuery = Expense::with('user')->orderBy('date', 'asc');

        if ($isFilterActive) {
            $salesQuery->whereBetween('date', [$startDate, $endDate]);
            $expensesQuery->whereBetween('date', [$startDate, $endDate]);
        }

        $sales = $salesQuery->get();
        $expenses = $expensesQuery->get();

        $totalPemasukan = $sales->sum('total_amount');
        $totalPengeluaran = $expenses->sum('amount');
        $saldoKas = $totalPemasukan - $totalPengeluaran;

        $categoryBreakdown = $expenses->groupBy('category')->map(function ($items, $cat) use ($totalPengeluaran) {
            $total = $items->sum('amount');
            return [
                'category' => $cat,
                'total' => $total,
                'percentage' => $totalPengeluaran > 0 ? round(($total / $totalPengeluaran) * 100, 1) : 0,
                'count' => $items->count(),
            ];
        })->sortByDesc('total')->values();

        return view('reports.print', compact(
            'sales',
            'expenses',
            'totalPemasukan',
            'totalPengeluaran',
            'saldoKas',
            'categoryBreakdown',
            'startDate',
            'endDate',
            'isFilterActive'
        ));
    }

    /**
     * Export Seluruh Mutasi Transaksi ke Format CSV / Excel
     */
    public function exportCsv(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $isFilterActive = !empty($startDate) && !empty($endDate);

        $salesQuery = Sale::with(['user', 'trip.user'])->orderBy('date', 'asc');
        $expensesQuery = Expense::with('user')->orderBy('date', 'asc');

        if ($isFilterActive) {
            $salesQuery->whereBetween('date', [$startDate, $endDate]);
            $expensesQuery->whereBetween('date', [$startDate, $endDate]);
        }

        $sales = $salesQuery->get();
        $expenses = $expensesQuery->get();

        $allRows = collect();

        foreach ($sales as $s) {
            $penginputStr = $s->user ? ($s->user->username ?: $s->user->name) : 'admin';
            $perjalananStr = ($s->trip && $s->trip->user) ? ($s->trip->user->username ?: $s->trip->user->name) : ($s->trip ? $s->trip->trip_code : '-');

            $allRows->push([
                'date' => Carbon::parse($s->date)->format('Y-m-d'),
                'code' => $s->invoice_no ?: ('INV-' . $s->id),
                'type' => 'PEMASUKAN',
                'category' => 'Penjualan Telur/Pakan',
                'description' => 'Penjualan' . ($s->customer_name ? ' - ' . $s->customer_name : ''),
                'penginput' => $penginputStr,
                'perjalanan' => $perjalananStr,
                'payment_method' => $s->payment_method ?: 'Tunai',
                'income' => (float)$s->total_amount,
                'expense' => 0,
            ]);
        }

        foreach ($expenses as $e) {
            $penginputStr = $e->user ? ($e->user->username ?: $e->user->name) : 'admin';

            $allRows->push([
                'date' => Carbon::parse($e->date)->format('Y-m-d'),
                'code' => $e->transaction_code,
                'type' => 'PENGELUARAN',
                'category' => $e->category . ' (' . $e->subcategory . ')',
                'description' => $e->purpose,
                'penginput' => $penginputStr,
                'perjalanan' => '-',
                'payment_method' => $e->payment_method ?? 'Kas Tunai',
                'income' => 0,
                'expense' => (float)$e->amount,
            ]);
        }

        // Urutkan kronologis
        $allRows = $allRows->sortBy('date')->values();

        $filename = 'Laporan_Kas_NochiFarm_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return new StreamedResponse(function () use ($allRows) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['No', 'Tanggal', 'Kode Transaksi', 'Tipe', 'Kategori', 'Keterangan / Keperluan', 'Petugas Penginput', 'Petugas Perjalanan (Trip)', 'Metode Bayar', 'Pemasukan (Rp)', 'Pengeluaran (Rp)', 'Saldo Kumulatif (Rp)']);

            $runningBalance = 0;
            foreach ($allRows as $idx => $row) {
                $runningBalance += ($row['income'] - $row['expense']);
                fputcsv($handle, [
                    $idx + 1,
                    $row['date'],
                    $row['code'],
                    $row['type'],
                    $row['category'],
                    $row['description'],
                    $row['penginput'],
                    $row['perjalanan'],
                    $row['payment_method'],
                    $row['income'],
                    $row['expense'],
                    $runningBalance,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
