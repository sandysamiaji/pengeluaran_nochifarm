<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\DailyPrice;
use App\Models\FeedProduct;
use App\Models\Coop;
use App\Models\Flock;
use App\Models\Setting;

class WarehouseInventoryService
{
    /**
     * Dapatkan ringkasan lengkap persediaan mengendap di gudang:
     * 1. Stok & Nilai Telur (terkonversi Peti & Kg)
     * 2. Stok & Nilai Pakan (Grower & Layer)
     * 3. Sisa Populasi Ayam Hidup (Per Kloter & Total Farm)
     */
    public static function getInventorySummary(): array
    {
        $telurData = self::calculateEggInventory();
        $pakanData = self::calculateFeedInventory();
        $ayamData = self::calculateChickenPopulation();

        $totalEstimasiAsetGudang = (float) ($telurData['estimasi_nilai'] + $pakanData['total_estimasi_nilai']);

        return [
            'telur' => $telurData,
            'pakan' => $pakanData,
            'ayam' => $ayamData,
            'total_estimasi_aset_gudang' => $totalEstimasiAsetGudang,
        ];
    }

    /**
     * 1. Hitung Stok & Nilai Estimasi Telur di Gudang
     * Mengikuti kalkulasi OutboundIntegrationService / WarehouseController
     */
    private static function calculateEggInventory(): array
    {
        try {
            // A. Penjualan Telur (Peti & Kg) dari database nochifram
            $petiSold = 0.0;
            $kgSold = 0.0;
            if (Schema::hasTable('sale_items') && Schema::hasTable('sales')) {
                $petiSold = (float) DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.category', 'telur')
                    ->where('sale_items.unit', 'Peti')
                    ->sum('sale_items.quantity');

                $kgSold = (float) DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.category', 'telur')
                    ->where('sale_items.unit', 'Kg')
                    ->sum('sale_items.quantity');
            }

            // Normalisasi Penjualan (10 kg = 1 Peti)
            if ($kgSold >= 10) {
                $extraSoldPeti = (int) floor($kgSold / 10);
                $petiSold = (int) round($petiSold + $extraSoldPeti);
                $kgSold = round($kgSold - ($extraSoldPeti * 10), 1);
            } else {
                $petiSold = (int) round($petiSold);
                $kgSold = round($kgSold, 1);
            }

            // B. Produksi Telur Kandang Masuk
            $totalProducedCrates = 0.0;
            $totalProducedWeightKg = 0.0;
            $totalBrokenEggs = 0;
            if (Schema::hasTable('egg_productions')) {
                $totalProducedCrates = (float) DB::table('egg_productions')->sum('crates_count');
                $totalProducedWeightKg = (float) DB::table('egg_productions')->sum('weight_kg');
                $totalBrokenEggs = (int) DB::table('egg_productions')->sum('broken_eggs');
            }

            // Telur rusak (estimasi rata-rata 1 butir = 0.06 kg / 60 gram)
            $brokenEggsToKg = round($totalBrokenEggs * 0.06, 1);

            // C. Mutasi FarmStock Telur (Masuk & Keluar)
            $farmStockMasukPeti = 0.0;
            $farmStockMasukKg = 0.0;
            $manualKeluarPeti = 0.0;
            $manualKeluarKg = 0.0;

            if (Schema::hasTable('farm_stocks')) {
                $farmStockMasukPeti = (float) DB::table('farm_stocks')
                    ->where('category', 'telur')->where('type', 'masuk')
                    ->where(function ($q) { $q->where('unit', 'Peti')->orWhere('unit', 'peti'); })
                    ->sum('quantity');

                $farmStockMasukKg = (float) DB::table('farm_stocks')
                    ->where('category', 'telur')->where('type', 'masuk')
                    ->where(function ($q) { $q->where('unit', 'Kg')->orWhere('unit', 'kg'); })
                    ->sum('quantity');

                $manualKeluarPeti = (float) DB::table('farm_stocks')
                    ->where('category', 'telur')->where('type', 'keluar')
                    ->where(function ($q) { $q->where('unit', 'Peti')->orWhere('unit', 'peti'); })
                    ->sum('quantity');

                $manualKeluarKg = (float) DB::table('farm_stocks')
                    ->where('category', 'telur')->where('type', 'keluar')
                    ->where(function ($q) { $q->where('unit', 'Kg')->orWhere('unit', 'kg'); })
                    ->sum('quantity');
            }

            // D. Normalisasi Telur Rusak (10 kg = 1 Peti)
            $rawBrokenPeti = (float) $manualKeluarPeti;
            $rawBrokenKg = (float) ($manualKeluarKg + $brokenEggsToKg);
            if ($rawBrokenKg >= 10) {
                $extraBrokenPeti = (int) floor($rawBrokenKg / 10);
                $totalBrokenPeti = (int) round($rawBrokenPeti + $extraBrokenPeti);
                $totalBrokenKg = round($rawBrokenKg - ($extraBrokenPeti * 10), 1);
            } else {
                $totalBrokenPeti = (int) round($rawBrokenPeti);
                $totalBrokenKg = round($rawBrokenKg, 1);
            }

            // E. Total Masuk Bersih (10 kg = 1 Peti)
            $rawMasukPeti = (float) ($totalProducedCrates + $farmStockMasukPeti);
            $rawMasukKg = (float) ($totalProducedWeightKg + $farmStockMasukKg);
            if ($rawMasukKg >= 10) {
                $extraMasukPeti = (int) floor($rawMasukKg / 10);
                $totalMasukPeti = (int) round($rawMasukPeti + $extraMasukPeti);
                $totalMasukKg = round($rawMasukKg - ($extraMasukPeti * 10), 1);
            } else {
                $totalMasukPeti = (int) round($rawMasukPeti);
                $totalMasukKg = round($rawMasukKg, 1);
            }

            // F. Total Keluar Bersih (Penjualan + Rusak)
            $rawKeluarPeti = (float) ($petiSold + $totalBrokenPeti);
            $rawKeluarKg = (float) ($kgSold + $totalBrokenKg);
            if ($rawKeluarKg >= 10) {
                $extraKeluarPeti = (int) floor($rawKeluarKg / 10);
                $totalKeluarPeti = (int) round($rawKeluarPeti + $extraKeluarPeti);
                $totalKeluarKg = round($rawKeluarKg - ($extraKeluarPeti * 10), 1);
            } else {
                $totalKeluarPeti = (int) round($rawKeluarPeti);
                $totalKeluarKg = round($rawKeluarKg, 1);
            }

            // G. Hitung Sisa Stok Telur Mengendap
            $netTotalKg = round((($totalMasukPeti * 10) + $totalMasukKg) - (($totalKeluarPeti * 10) + $totalKeluarKg), 1);
            if ($netTotalKg >= 0) {
                $currentStockPeti = (int) floor($netTotalKg / 10);
                $currentStockKg = round($netTotalKg - ($currentStockPeti * 10), 1);
            } else {
                $absNetKg = abs($netTotalKg);
                $currentStockPeti = - (int) floor($absNetKg / 10);
                $currentStockKg = - round($absNetKg - (abs($currentStockPeti) * 10), 1);
            }

            // H. Harga Penjualan Telur dari DataMasterController (daily_prices)
            $hargaPeti = 0.0;
            $hargaKg = 0.0;
            if (Schema::hasTable('daily_prices')) {
                $dailyPrice = DailyPrice::orderBy('date', 'desc')->orderBy('id', 'desc')->first();
                if ($dailyPrice) {
                    $hargaPeti = (float) $dailyPrice->price_peti;
                    $hargaKg = (float) $dailyPrice->price_kg;
                }
            }

            // Fallback harga jika belum ada input daily_price terbaru
            if ($hargaPeti <= 0 && Schema::hasTable('sale_items')) {
                $lastSalePeti = DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.category', 'telur')
                    ->where('sale_items.unit', 'Peti')
                    ->where('sale_items.price', '>', 0)
                    ->orderBy('sales.date', 'desc')
                    ->value('sale_items.price');
                if ($lastSalePeti) $hargaPeti = (float) $lastSalePeti;
            }
            if ($hargaPeti <= 0) $hargaPeti = 280000; // Acuan standar per peti

            if ($hargaKg <= 0 && Schema::hasTable('sale_items')) {
                $lastSaleKg = DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.category', 'telur')
                    ->where('sale_items.unit', 'Kg')
                    ->where('sale_items.price', '>', 0)
                    ->orderBy('sales.date', 'desc')
                    ->value('sale_items.price');
                if ($lastSaleKg) $hargaKg = (float) $lastSaleKg;
            }
            if ($hargaKg <= 0) $hargaKg = round($hargaPeti / 10); // Acuan standar 1 kg

            // I. Estimasi Nilai Stok Telur Mengendap
            $estimasiNilai = max(0, ($currentStockPeti * $hargaPeti) + ($currentStockKg * $hargaKg));

            return [
                'stok_peti' => $currentStockPeti,
                'stok_kg' => $currentStockKg,
                'net_total_kg' => $netTotalKg,
                'harga_peti' => $hargaPeti,
                'harga_kg' => $hargaKg,
                'estimasi_nilai' => $estimasiNilai,
                'total_masuk_peti' => $totalMasukPeti,
                'total_masuk_kg' => $totalMasukKg,
                'total_keluar_peti' => $totalKeluarPeti,
                'total_keluar_kg' => $totalKeluarKg,
            ];
        } catch (\Exception $e) {
            return [
                'stok_peti' => 0,
                'stok_kg' => 0,
                'net_total_kg' => 0,
                'harga_peti' => 280000,
                'harga_kg' => 28000,
                'estimasi_nilai' => 0,
                'total_masuk_peti' => 0,
                'total_masuk_kg' => 0,
                'total_keluar_peti' => 0,
                'total_keluar_kg' => 0,
            ];
        }
    }

    /**
     * 2. Hitung Stok & Nilai Estimasi Pakan (Grower & Layer) di Gudang
     */
    private static function calculateFeedInventory(): array
    {
        try {
            $kgPerKarung = 50.0;
            if (Schema::hasTable('settings')) {
                $settingVal = Setting::get('kg_per_karung');
                if ($settingVal && is_numeric($settingVal)) {
                    $kgPerKarung = (float) $settingVal;
                }
            }

            // Helper konversi stok pakan ke Kg
            $convertStockToKg = function ($item) use ($kgPerKarung) {
                $qty = (float) ($item->quantity ?? 0);
                $u = strtolower(trim($item->unit ?? ''));
                if (in_array($u, ['karung', 'sak', 'krg'])) {
                    return $qty * $kgPerKarung;
                } elseif ($u === 'ton') {
                    return $qty * 1000;
                }
                return $qty;
            };

            // A. Penjualan Pakan Luar (Total, Grower, Layer)
            $karungSold = 0.0;
            $kgSold = 0.0;
            $karungSoldGrower = 0.0;
            $kgSoldGrower = 0.0;

            if (Schema::hasTable('sale_items') && Schema::hasTable('sales')) {
                $karungSold = (float) DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.category', 'pakan')
                    ->where('sale_items.unit', 'Karung')
                    ->sum('sale_items.quantity');

                $kgSold = (float) DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.category', 'pakan')
                    ->where('sale_items.unit', 'Kg')
                    ->sum('sale_items.quantity');

                $karungSoldGrower = (float) DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.category', 'pakan')->where('sale_items.unit', 'Karung')
                    ->where(function ($q) {
                        $q->where('sale_items.item_name', 'like', '%grower%')
                          ->orWhere('sale_items.item_name', 'like', '%starter%')
                          ->orWhere('sale_items.item_name', 'like', '%pullet%');
                    })
                    ->sum('sale_items.quantity');

                $kgSoldGrower = (float) DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.category', 'pakan')->where('sale_items.unit', 'Kg')
                    ->where(function ($q) {
                        $q->where('sale_items.item_name', 'like', '%grower%')
                          ->orWhere('sale_items.item_name', 'like', '%starter%')
                          ->orWhere('sale_items.item_name', 'like', '%pullet%');
                    })
                    ->sum('sale_items.quantity');
            }

            $soldInKg = ($karungSold * $kgPerKarung) + $kgSold;
            $soldInKgGrower = ($karungSoldGrower * $kgPerKarung) + $kgSoldGrower;
            $soldInKgLayer = max(0, $soldInKg - $soldInKgGrower);
            $karungSoldLayer = max(0, $karungSold - $karungSoldGrower);

            // B. Konsumsi Pakan oleh Ayam di Kandang
            $consumptionKg = 0.0;
            $consumptionKgGrower = 0.0;
            if (Schema::hasTable('feed_consumptions')) {
                $consumptionKg = (float) DB::table('feed_consumptions')->sum('quantity_kg');
                $consumptionKgGrower = (float) DB::table('feed_consumptions')
                    ->where(function ($q) {
                        $q->where('feed_name', 'like', '%grower%')
                          ->orWhere('feed_name', 'like', '%starter%')
                          ->orWhere('feed_name', 'like', '%pullet%');
                    })
                    ->sum('quantity_kg');
            }
            $consumptionKgLayer = max(0, $consumptionKg - $consumptionKgGrower);

            // C. Pembelian Pakan Masuk (FarmStock)
            $purchasedKg = 0.0;
            $purchasedKgGrower = 0.0;
            if (Schema::hasTable('farm_stocks')) {
                $stockMasukList = DB::table('farm_stocks')
                    ->whereRaw('LOWER(category) = ?', ['pakan'])
                    ->whereRaw('LOWER(type) = ?', ['masuk'])
                    ->get();

                foreach ($stockMasukList as $stk) {
                    $kgVal = $convertStockToKg($stk);
                    $purchasedKg += $kgVal;
                    $iName = strtolower($stk->item_name ?? '');
                    if (str_contains($iName, 'grower') || str_contains($iName, 'starter') || str_contains($iName, 'pullet')) {
                        $purchasedKgGrower += $kgVal;
                    }
                }
            }
            $purchasedKgLayer = max(0, $purchasedKg - $purchasedKgGrower);

            // D. Mutasi Manual Keluar FarmStock (di luar auto-konsumsi)
            $manualKeluarKg = 0.0;
            $manualKeluarKgGrower = 0.0;
            if (Schema::hasTable('farm_stocks')) {
                $stockKeluarList = DB::table('farm_stocks')
                    ->whereRaw('LOWER(category) = ?', ['pakan'])
                    ->whereRaw('LOWER(type) = ?', ['keluar'])
                    ->where(function ($q) {
                        $q->whereNull('notes')->orWhere('notes', 'not like', '[AUTO-KONSUMSI]%');
                    })
                    ->get();

                foreach ($stockKeluarList as $stk) {
                    $kgVal = $convertStockToKg($stk);
                    $manualKeluarKg += $kgVal;
                    $iName = strtolower($stk->item_name ?? '');
                    if (str_contains($iName, 'grower') || str_contains($iName, 'starter') || str_contains($iName, 'pullet')) {
                        $manualKeluarKgGrower += $kgVal;
                    }
                }
            }
            $manualKeluarKgLayer = max(0, $manualKeluarKg - $manualKeluarKgGrower);

            // E. Hitung Total Keluar & Sisa Stok Grower vs Layer
            $totalKeluarKgGrower = $consumptionKgGrower + $soldInKgGrower + $manualKeluarKgGrower;
            $currentStockKgGrower = round($purchasedKgGrower - $totalKeluarKgGrower, 1);
            $currentStockKarungGrower = round($currentStockKgGrower / $kgPerKarung, 1);

            $totalKeluarKgLayer = $consumptionKgLayer + $soldInKgLayer + $manualKeluarKgLayer;
            $currentStockKgLayer = round($purchasedKgLayer - $totalKeluarKgLayer, 1);
            $currentStockKarungLayer = round($currentStockKgLayer / $kgPerKarung, 1);

            $totalKeluarKg = $consumptionKg + $soldInKg + $manualKeluarKg;
            $currentStockKg = round($purchasedKg - $totalKeluarKg, 1);
            $currentStockKarung = round($currentStockKg / $kgPerKarung, 1);

            // F. Harga Penjualan Pakan dari DataMasterController (feed_products)
            $hargaLayerPerKarung = 0.0;
            $hargaGrowerPerKarung = 0.0;

            if (Schema::hasTable('feed_products')) {
                $feedProducts = FeedProduct::where('is_active', true)->get();
                foreach ($feedProducts as $fp) {
                    $fName = strtolower($fp->name);
                    if (str_contains($fName, 'grower') || str_contains($fName, 'starter') || str_contains($fName, 'pullet')) {
                        if ($hargaGrowerPerKarung <= 0 && $fp->price_per_karung > 0) {
                            $hargaGrowerPerKarung = (float) $fp->price_per_karung;
                        }
                    } elseif (str_contains($fName, 'layer')) {
                        if ($hargaLayerPerKarung <= 0 && $fp->price_per_karung > 0) {
                            $hargaLayerPerKarung = (float) $fp->price_per_karung;
                        }
                    }
                }
            }

            // Fallback harga dari riwayat penjualan jika belum disetting di FeedProduct
            if ($hargaLayerPerKarung <= 0 && Schema::hasTable('sale_items')) {
                $lastSaleLayer = DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.category', 'pakan')
                    ->where('sale_items.unit', 'Karung')
                    ->where('sale_items.price', '>', 0)
                    ->where('sale_items.item_name', 'like', '%layer%')
                    ->orderBy('sales.date', 'desc')
                    ->value('sale_items.price');
                if ($lastSaleLayer) $hargaLayerPerKarung = (float) $lastSaleLayer;
            }
            if ($hargaLayerPerKarung <= 0) $hargaLayerPerKarung = 385000; // Standar pakan layer per sak

            if ($hargaGrowerPerKarung <= 0 && Schema::hasTable('sale_items')) {
                $lastSaleGrower = DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.category', 'pakan')
                    ->where('sale_items.unit', 'Karung')
                    ->where('sale_items.price', '>', 0)
                    ->where(function ($q) {
                        $q->where('sale_items.item_name', 'like', '%grower%')
                          ->orWhere('sale_items.item_name', 'like', '%starter%')
                          ->orWhere('sale_items.item_name', 'like', '%pullet%');
                    })
                    ->orderBy('sales.date', 'desc')
                    ->value('sale_items.price');
                if ($lastSaleGrower) $hargaGrowerPerKarung = (float) $lastSaleGrower;
            }
            if ($hargaGrowerPerKarung <= 0) $hargaGrowerPerKarung = 395000; // Standar pakan grower per sak

            // G. Estimasi Nilai Stok Pakan Mengendap
            $estimasiNilaiLayer = max(0, $currentStockKarungLayer) * $hargaLayerPerKarung;
            $estimasiNilaiGrower = max(0, $currentStockKarungGrower) * $hargaGrowerPerKarung;
            $totalEstimasiNilai = $estimasiNilaiLayer + $estimasiNilaiGrower;

            return [
                'total_stok_karung' => $currentStockKarung,
                'total_stok_kg' => $currentStockKg,
                'total_estimasi_nilai' => $totalEstimasiNilai,
                'kg_per_karung' => $kgPerKarung,

                // Layer
                'layer_stok_karung' => $currentStockKarungLayer,
                'layer_stok_kg' => $currentStockKgLayer,
                'layer_harga_karung' => $hargaLayerPerKarung,
                'layer_estimasi_nilai' => $estimasiNilaiLayer,

                // Grower
                'grower_stok_karung' => $currentStockKarungGrower,
                'grower_stok_kg' => $currentStockKgGrower,
                'grower_harga_karung' => $hargaGrowerPerKarung,
                'grower_estimasi_nilai' => $estimasiNilaiGrower,
            ];
        } catch (\Exception $e) {
            return [
                'total_stok_karung' => 0,
                'total_stok_kg' => 0,
                'total_estimasi_nilai' => 0,
                'kg_per_karung' => 50.0,
                'layer_stok_karung' => 0,
                'layer_stok_kg' => 0,
                'layer_harga_karung' => 385000,
                'layer_estimasi_nilai' => 0,
                'grower_stok_karung' => 0,
                'grower_stok_kg' => 0,
                'grower_harga_karung' => 395000,
                'grower_estimasi_nilai' => 0,
            ];
        }
    }

    /**
     * 3. Hitung Populasi & Sisa Ayam Hidup di Kandang
     * Dari data MasterController (Coop & Flock)
     */
    private static function calculateChickenPopulation(): array
    {
        try {
            $totalSisaAyam = 0;
            $totalKapasitas = 0;
            $flocksData = [];

            if (Schema::hasTable('coops')) {
                $totalSisaAyam = (int) Coop::where('is_active', true)->sum('active_chickens');
                $totalKapasitas = (int) Coop::where('is_active', true)->sum('capacity');
            }

            if (Schema::hasTable('flocks')) {
                $flocks = Flock::with(['coops' => function ($q) {
                    $q->where('is_active', true);
                }])->where('is_active', true)->get();

                foreach ($flocks as $flock) {
                    $activeInFlock = (int) $flock->coops->sum('active_chickens');
                    $flocksData[] = [
                        'id' => $flock->id,
                        'name' => $flock->name,
                        'breed' => $flock->breed ?: 'Lohmann Brown',
                        'initial_population' => (int) $flock->initial_population,
                        'current_population' => $activeInFlock > 0 ? $activeInFlock : (int) $flock->current_population,
                        'coops_count' => $flock->coops->count(),
                    ];
                }
            }

            $persentaseKeterisian = $totalKapasitas > 0 ? round(($totalSisaAyam / $totalKapasitas) * 100, 1) : 0;

            return [
                'total_sisa_ayam' => $totalSisaAyam,
                'total_kapasitas' => $totalKapasitas,
                'persentase_keterisian' => $persentaseKeterisian,
                'flocks' => $flocksData,
            ];
        } catch (\Exception $e) {
            return [
                'total_sisa_ayam' => 0,
                'total_kapasitas' => 0,
                'persentase_keterisian' => 0,
                'flocks' => [],
            ];
        }
    }
}
