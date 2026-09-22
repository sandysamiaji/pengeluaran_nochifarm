<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EggProduction;
use App\Models\Coop;
use App\Models\Flock;
use Carbon\Carbon;

class ProductionController extends Controller
{
    /**
     * Tampilkan Riwayat & Ringkasan Produksi Telur Kandang
     */
    public function index(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $coopId = $request->query('coop_id');
        $search = $request->query('q');

        $query = EggProduction::with(['coop.flock', 'flock', 'user'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        // Filter Rentang Tanggal
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('date', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        // Filter Kandang
        if (!empty($coopId) && $coopId !== 'all') {
            $query->where('coop_id', $coopId);
        }

        // Pencarian Bebas
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('coop', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%")
                       ->orWhere('code', 'like', "%{$search}%");
                })
                ->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('username', 'like', "%{$search}%");
                })
                ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Hitung Total Akumulasi Sesuai Filter
        $totalEggs = (int) (clone $query)->sum('total_eggs');
        $totalGoodEggs = (int) (clone $query)->sum('good_eggs');
        $totalBrokenEggs = (int) (clone $query)->sum('broken_eggs');
        $totalCrates = (float) (clone $query)->sum('crates_count');
        $totalWeightKg = (float) (clone $query)->sum('weight_kg');

        // Konversi Peti & Kg: 1 Peti = 10 Kg
        $intPeti = (int) floor($totalCrates);
        $sisaKg = round(($totalCrates - $intPeti) * 10, 1);
        if ($sisaKg >= 10) {
            $intPeti += (int) floor($sisaKg / 10);
            $sisaKg = round($sisaKg - (floor($sisaKg / 10) * 10), 1);
        }

        // Produksi Hari Ini Realtime
        $today = Carbon::today()->toDateString();
        $todayQuery = EggProduction::whereDate('date', $today);
        $todayEggs = (int) (clone $todayQuery)->sum('total_eggs');
        $todayGoodEggs = (int) (clone $todayQuery)->sum('good_eggs');
        $todayBrokenEggs = (int) (clone $todayQuery)->sum('broken_eggs');
        $todayCrates = (float) (clone $todayQuery)->sum('crates_count');
        $todayWeightKg = (float) (clone $todayQuery)->sum('weight_kg');

        $todayIntPeti = (int) floor($todayCrates);
        $todaySisaKg = round(($todayCrates - $todayIntPeti) * 10, 1);

        // Paginate 10 Data per Halaman Sesuai Permintaan
        $productions = $query->paginate(10)->withQueryString();

        // Master Kandang untuk Dropdown Filter
        $coops = Coop::orderBy('name', 'asc')->get();

        return view('production.index', compact(
            'productions',
            'totalEggs',
            'totalGoodEggs',
            'totalBrokenEggs',
            'totalCrates',
            'totalWeightKg',
            'intPeti',
            'sisaKg',
            'todayEggs',
            'todayGoodEggs',
            'todayBrokenEggs',
            'todayCrates',
            'todayWeightKg',
            'todayIntPeti',
            'todaySisaKg',
            'coops',
            'startDate',
            'endDate',
            'coopId',
            'search'
        ));
    }

    /**
     * Detail Produksi Telur (JSON untuk Detail View Modal)
     */
    public function show($id)
    {
        $prod = EggProduction::with(['coop.flock', 'flock', 'user'])->findOrFail($id);

        $crates = (float) $prod->crates_count;
        $wholePeti = (int) floor($crates);
        $remKg = round(($crates - $wholePeti) * 10, 1);

        $username = $prod->user ? ($prod->user->username ?: $prod->user->name) : 'petugas';
        $name = $prod->user ? $prod->user->name : 'Petugas Kandang';

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $prod->id,
                'date' => Carbon::parse($prod->date)->format('Y-m-d'),
                'display_date' => Carbon::parse($prod->date)->translatedFormat('d F Y'),
                'time' => $prod->time ? Carbon::parse($prod->time)->format('H:i') : null,
                'created_at_time' => $prod->created_at ? $prod->created_at->translatedFormat('d M Y, H:i') : null,
                'created_at_diff' => $prod->created_at ? $prod->created_at->diffForHumans() : null,
                'coop_name' => $prod->coop ? $prod->coop->name : 'Kandang Umum',
                'coop_code' => $prod->coop ? $prod->coop->code : '-',
                'flock_name' => $prod->flock ? $prod->flock->name : ($prod->coop && $prod->coop->flock ? $prod->coop->flock->name : 'Kloter'),
                'total_eggs' => (int) $prod->total_eggs,
                'good_eggs' => (int) $prod->good_eggs,
                'broken_eggs' => (int) $prod->broken_eggs,
                'abnormal_eggs' => (int) $prod->abnormal_eggs,
                'crates_count' => $crates,
                'weight_kg' => (float) $prod->weight_kg,
                'converted_str' => "{$wholePeti} Peti + {$remKg} Kg",
                'notes' => $prod->notes ?: '-',
                'penginput_username' => $username,
                'penginput_name' => $name,
            ]
        ]);
    }
}
