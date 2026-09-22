@extends('layouts.app')

@section('title', 'Dashboard Keuangan & Transaksi - NOCHI FARM')

@section('content')
<div class="space-y-6">

    <!-- Top Greetings & Realtime Status -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-slate-100 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Database Realtime Terhubung</span>
                </span>
                <span class="text-xs text-slate-400">&bull;</span>
                <span class="text-xs text-slate-500 font-medium">Nochi Fram & Pengeluaran</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight mt-1">Dashboard Arus Kas & Transaksi</h1>
        </div>

        <!-- Quick Traveloka Date Filter Trigger Bar -->
        <div class="flex items-center gap-2">
            <button type="button" onclick="openTravelokaFilterModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border {{ $isFilterActive ? 'bg-orange-50 border-orange-300 text-nochi-orange shadow-xs' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-700' }} text-xs sm:text-sm font-bold transition-all">
                <i data-lucide="calendar-range" class="w-4 h-4 {{ $isFilterActive ? 'text-nochi-orange' : 'text-slate-500' }}"></i>
                <span id="activeFilterLabel">
                    @if($isFilterActive)
                        {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                    @else
                        Semua Riwayat (Tanpa Filter)
                    @endif
                </span>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
            </button>

            @if($isFilterActive)
                <a href="{{ route('dashboard') }}" class="p-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-bold transition-all" title="Hapus Filter (Tampilkan Semua)">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            @endif
        </div>
    </div>

    <!-- Active Filter Banner Alert (If Active) -->
    @if($isFilterActive)
        <div class="bg-amber-50/80 border border-amber-200/80 rounded-2xl p-3 sm:p-4 flex items-center justify-between gap-3 text-xs sm:text-sm">
            <div class="flex items-center gap-2.5 text-amber-900">
                <i data-lucide="filter" class="w-4 h-4 text-amber-600 shrink-0"></i>
                <span>Menampilkan transaksi periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</strong></span>
            </div>
            <a href="{{ route('dashboard') }}" class="text-xs font-extrabold text-amber-800 hover:text-amber-950 underline shrink-0">
                Tampilkan Semua Riwayat
            </a>
        </div>
    @endif

    <!-- Financial Metric Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- 1. Saldo Kas Saat Ini -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                    <span class="text-slate-700">Saldo Kas Saat Ini</span>
                    <div class="w-7 h-7 rounded-lg bg-orange-50 text-nochi-orange flex items-center justify-center">
                        <i data-lucide="wallet" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2.5">
                    <span class="text-xl sm:text-2xl font-black tracking-tight block {{ $saldoSaatIni >= 0 ? 'text-slate-900' : 'text-rose-600' }}">
                        Rp {{ number_format($saldoSaatIni, 0, ',', '.') }}
                    </span>
                </div>
                <!-- Status Surplus/Defisit & Margin -->
                <div class="mt-2.5 flex flex-wrap items-center gap-1.5 text-[11px] font-semibold">
                    <span class="{{ $saldoSaatIni >= 0 ? 'text-emerald-800 bg-emerald-50 border-emerald-200/70' : 'text-rose-800 bg-rose-50 border-rose-200/70' }} px-2 py-0.5 rounded-md border">
                        {{ $saldoSaatIni >= 0 ? '✅ Surplus (+)' : '⚠️ Defisit (-)' }}
                    </span>
                    <span class="text-slate-600 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200/70">
                        Margin: {{ $profitMargin }}%
                    </span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>(Pemasukan - Pengeluaran)</span>
                <span class="font-bold {{ $saldoSaatIni >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $saldoSaatIni >= 0 ? 'Kas Sehat' : 'Defisit' }}
                </span>
            </div>
        </div>

        <!-- 2. Total Pemasukan (Omzet Nochi Fram) -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                    <span class="text-emerald-700">Total Pemasukan</span>
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2.5">
                    <span class="text-xl sm:text-2xl font-black text-emerald-600 tracking-tight block">
                        + Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                    </span>
                </div>
                <!-- Pemecahan Tunai vs Transfer -->
                <div class="mt-2.5 flex flex-wrap items-center gap-1.5 text-[11px] font-semibold">
                    <span class="text-amber-800 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/70" title="Pemasukan Tunai / Cash">
                        💵 Tunai: Rp {{ number_format($tunaiPemasukan, 0, ',', '.') }}
                    </span>
                    <span class="text-sky-800 bg-sky-50 px-2 py-0.5 rounded-md border border-sky-200/70" title="Pemasukan Transfer Bank">
                        📱 Transfer: Rp {{ number_format($transferPemasukan, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Omzet Penjualan (Nochi Fram)</span>
                <span class="font-bold text-slate-700">{{ $countSales }} Nota</span>
            </div>
        </div>

        <!-- 3. Total Pengeluaran (Nochi Farm Pengeluaran) -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                    <span class="text-rose-700">Total Pengeluaran</span>
                    <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                        <i data-lucide="trending-down" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2.5">
                    <span class="text-xl sm:text-2xl font-black text-rose-600 tracking-tight block">
                        - Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </span>
                </div>
                <!-- Pemecahan Tunai vs Transfer -->
                <div class="mt-2.5 flex flex-wrap items-center gap-1.5 text-[11px] font-semibold">
                    <span class="text-amber-800 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/70" title="Pengeluaran Kas Tunai">
                        💵 Tunai: Rp {{ number_format($tunaiPengeluaran, 0, ',', '.') }}
                    </span>
                    <span class="text-sky-800 bg-sky-50 px-2 py-0.5 rounded-md border border-sky-200/70" title="Pengeluaran Transfer Bank">
                        📱 Transfer: Rp {{ number_format($transferPengeluaran, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Belanja Operasional Kandang</span>
                <span class="font-bold text-slate-700">{{ $countExpenses }} Transaksi</span>
            </div>
        </div>

        <!-- 4. Total Transaksi Terdata -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                    <span class="text-slate-600">Total Transaksi</span>
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                        <i data-lucide="receipt-text" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2.5">
                    <span class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight block">
                        {{ $totalTransaksi }} <span class="text-sm font-semibold text-slate-400">Data</span>
                    </span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Status Mutasi</span>
                <span class="font-bold text-nochi-orange">{{ $isFilterActive ? 'Terfilter' : 'Realtime Lengkap' }}</span>
            </div>
        </div>

    </div>

    <!-- Inventory & Stock Asset Cards (Barang Mengendap di Gudang & Populasi Ternak) -->
    <div class="space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-1">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></div>
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 tracking-tight uppercase">
                    Stok Barang Mengendap & Estimasi Nilai Aset Gudang
                </h3>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                    Realtime Gudang & Master
                </span>
            </div>
            <div class="text-xs font-bold text-slate-500">
                Total Estimasi Aset Gudang (Telur + Pakan): 
                <span class="text-slate-900 font-black text-sm">
                    Rp {{ number_format($inventorySummary['total_estimasi_aset_gudang'] ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1. Stok Telur Mengendap -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between hover:border-amber-200 transition-colors">
                <div>
                    <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                        <span class="text-amber-700">Stok Telur Gudang</span>
                        <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i data-lucide="egg" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="mt-2.5">
                        <div class="text-xl sm:text-2xl font-black {{ ($inventorySummary['telur']['is_defisit'] ?? false) ? 'text-rose-600' : 'text-slate-900' }} tracking-tight flex flex-wrap items-baseline gap-1.5">
                            <span>{{ number_format($inventorySummary['telur']['stok_peti'] ?? 0, 0, ',', '.') }} <span class="text-base font-bold text-amber-700">Peti</span></span>
                            @if(($inventorySummary['telur']['stok_kg'] ?? 0) != 0)
                                <span class="text-sm font-bold {{ ($inventorySummary['telur']['stok_kg'] ?? 0) < 0 ? 'text-rose-500' : 'text-slate-600' }}">
                                    + {{ ($inventorySummary['telur']['stok_kg'] ?? 0) == floor($inventorySummary['telur']['stok_kg'] ?? 0) ? number_format($inventorySummary['telur']['stok_kg'] ?? 0, 0, ',', '.') : number_format($inventorySummary['telur']['stok_kg'] ?? 0, 1, ',', '.') }} Kg
                                </span>
                            @endif
                        </div>
                    </div>
                    <!-- Estimasi Nilai Penjualan Telur: (x Peti + x Kg) -->
                    <div class="mt-2.5 flex flex-wrap items-center gap-1.5 text-[11px] font-semibold">
                        @if(($inventorySummary['telur']['is_defisit'] ?? false))
                            <span class="text-rose-800 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200 font-bold" title="{{ $inventorySummary['telur']['formula_text'] ?? '' }}">
                                Est: -Rp {{ number_format($inventorySummary['telur']['estimasi_nilai_abs'] ?? 0, 0, ',', '.') }} <span class="font-normal text-[10px] text-rose-600">(Defisit)</span>
                            </span>
                        @else
                            <span class="text-amber-900 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/70 font-bold" title="{{ $inventorySummary['telur']['formula_text'] ?? '' }}">
                                Est: Rp {{ number_format($inventorySummary['telur']['estimasi_nilai'] ?? 0, 0, ',', '.') }}
                            </span>
                        @endif
                        <span class="text-slate-500 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200/70" title="Total Bobot Riil di Gudang">
                            {{ number_format($inventorySummary['telur']['net_total_kg'] ?? 0, 1, ',', '.') }} Kg Telur
                        </span>
                    </div>

                    <!-- Formula Perhitungan: (X Peti × Harga Peti) + (Y Kg × Harga Kg) -->
                    <div class="mt-2 p-1.5 rounded-lg bg-amber-50/60 border border-amber-100 text-[10px] text-amber-950/80 leading-snug">
                        <span class="font-bold text-amber-900">Hitungan:</span> 
                        ({{ $inventorySummary['telur']['stok_peti'] ?? 0 }} Peti &times; {{ number_format($inventorySummary['telur']['harga_peti'] ?? 0, 0, ',', '.') }}) + 
                        ({{ $inventorySummary['telur']['stok_kg'] ?? 0 }} Kg &times; {{ number_format($inventorySummary['telur']['harga_kg'] ?? 0, 0, ',', '.') }})
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span>Harga Master Harian</span>
                    <span class="font-bold text-slate-700 text-right">
                        Rp {{ number_format($inventorySummary['telur']['harga_peti'] ?? 0, 0, ',', '.') }}/Peti 
                        <span class="text-amber-700 font-semibold">+ Rp {{ number_format($inventorySummary['telur']['harga_kg'] ?? 0, 0, ',', '.') }}/Kg</span>
                    </span>
                </div>
            </div>

            <!-- 2. Stok Pakan Layer (Ayam Petelur) -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between hover:border-emerald-200 transition-colors">
                <div>
                    <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                        <span class="text-emerald-700">Pakan Layer (Petelur)</span>
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i data-lucide="boxes" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="mt-2.5">
                        <div class="text-xl sm:text-2xl font-black {{ ($inventorySummary['pakan']['layer_is_defisit'] ?? false) ? 'text-rose-600' : 'text-slate-900' }} tracking-tight flex flex-wrap items-baseline gap-1.5">
                            <span>{{ number_format($inventorySummary['pakan']['layer_karung_bulat'] ?? 0, 0, ',', '.') }} <span class="text-base font-bold text-emerald-700">Krg</span></span>
                            @if(($inventorySummary['pakan']['layer_sisa_kg'] ?? 0) != 0)
                                <span class="text-sm font-bold {{ ($inventorySummary['pakan']['layer_sisa_kg'] ?? 0) < 0 ? 'text-rose-500' : 'text-slate-600' }}">
                                    + {{ ($inventorySummary['pakan']['layer_sisa_kg'] ?? 0) == floor($inventorySummary['pakan']['layer_sisa_kg'] ?? 0) ? number_format($inventorySummary['pakan']['layer_sisa_kg'] ?? 0, 0, ',', '.') : number_format($inventorySummary['pakan']['layer_sisa_kg'] ?? 0, 1, ',', '.') }} Kg
                                </span>
                            @endif
                        </div>
                    </div>
                    <!-- Estimasi Nilai Pakan Layer: (x Krg + x Kg) -->
                    <div class="mt-2.5 flex flex-wrap items-center gap-1.5 text-[11px] font-semibold">
                        @if(($inventorySummary['pakan']['layer_is_defisit'] ?? false))
                            <span class="text-rose-800 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200 font-bold" title="{{ $inventorySummary['pakan']['layer_formula_text'] ?? '' }}">
                                Est: -Rp {{ number_format($inventorySummary['pakan']['layer_estimasi_nilai_abs'] ?? 0, 0, ',', '.') }} <span class="font-normal text-[10px] text-rose-600">(Defisit)</span>
                            </span>
                        @else
                            <span class="text-emerald-900 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/70 font-bold" title="{{ $inventorySummary['pakan']['layer_formula_text'] ?? '' }}">
                                Est: Rp {{ number_format($inventorySummary['pakan']['layer_estimasi_nilai'] ?? 0, 0, ',', '.') }}
                            </span>
                        @endif
                        <span class="text-slate-500 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200/70" title="Total Bobot Pakan Layer">
                            {{ number_format($inventorySummary['pakan']['layer_stok_kg'] ?? 0, 0, ',', '.') }} Kg ({{ $inventorySummary['pakan']['kg_per_karung'] ?? 50 }} Kg/Krg)
                        </span>
                    </div>

                    <!-- Formula Perhitungan: (X Krg × Harga Krg) + (Y Kg × Harga Kg) -->
                    <div class="mt-2 p-1.5 rounded-lg bg-emerald-50/60 border border-emerald-100 text-[10px] text-emerald-950/80 leading-snug">
                        <span class="font-bold text-emerald-900">Hitungan:</span> 
                        ({{ $inventorySummary['pakan']['layer_karung_bulat'] ?? 0 }} Krg &times; {{ number_format($inventorySummary['pakan']['layer_harga_karung'] ?? 0, 0, ',', '.') }}) + 
                        ({{ $inventorySummary['pakan']['layer_sisa_kg'] ?? 0 }} Kg &times; {{ number_format($inventorySummary['pakan']['layer_harga_kg'] ?? 0, 0, ',', '.') }})
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span>Harga Jual Pakan</span>
                    <span class="font-bold text-slate-700 text-right">
                        Rp {{ number_format($inventorySummary['pakan']['layer_harga_karung'] ?? 0, 0, ',', '.') }}/Krg
                        <span class="text-emerald-700 font-semibold">(+ Rp {{ number_format($inventorySummary['pakan']['layer_harga_kg'] ?? 0, 0, ',', '.') }}/Kg)</span>
                    </span>
                </div>
            </div>

            <!-- 3. Stok Pakan Grower / Starter -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between hover:border-sky-200 transition-colors">
                <div>
                    <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                        <span class="text-sky-700">Pakan Grower / Starter</span>
                        <div class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                            <i data-lucide="wheat" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="mt-2.5">
                        <div class="text-xl sm:text-2xl font-black {{ ($inventorySummary['pakan']['grower_is_defisit'] ?? false) ? 'text-rose-600' : 'text-slate-900' }} tracking-tight flex flex-wrap items-baseline gap-1.5">
                            <span>{{ number_format($inventorySummary['pakan']['grower_karung_bulat'] ?? 0, 0, ',', '.') }} <span class="text-base font-bold text-sky-700">Krg</span></span>
                            @if(($inventorySummary['pakan']['grower_sisa_kg'] ?? 0) != 0)
                                <span class="text-sm font-bold {{ ($inventorySummary['pakan']['grower_sisa_kg'] ?? 0) < 0 ? 'text-rose-500' : 'text-slate-600' }}">
                                    + {{ ($inventorySummary['pakan']['grower_sisa_kg'] ?? 0) == floor($inventorySummary['pakan']['grower_sisa_kg'] ?? 0) ? number_format($inventorySummary['pakan']['grower_sisa_kg'] ?? 0, 0, ',', '.') : number_format($inventorySummary['pakan']['grower_sisa_kg'] ?? 0, 1, ',', '.') }} Kg
                                </span>
                            @endif
                        </div>
                    </div>
                    <!-- Estimasi Nilai Pakan Grower: (x Krg + x Kg) -->
                    <div class="mt-2.5 flex flex-wrap items-center gap-1.5 text-[11px] font-semibold">
                        @if(($inventorySummary['pakan']['grower_is_defisit'] ?? false))
                            <span class="text-rose-800 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200 font-bold" title="{{ $inventorySummary['pakan']['grower_formula_text'] ?? '' }}">
                                Est: -Rp {{ number_format($inventorySummary['pakan']['grower_estimasi_nilai_abs'] ?? 0, 0, ',', '.') }} <span class="font-normal text-[10px] text-rose-600">(Defisit)</span>
                            </span>
                        @else
                            <span class="text-sky-900 bg-sky-50 px-2 py-0.5 rounded-md border border-sky-200/70 font-bold" title="{{ $inventorySummary['pakan']['grower_formula_text'] ?? '' }}">
                                Est: Rp {{ number_format($inventorySummary['pakan']['grower_estimasi_nilai'] ?? 0, 0, ',', '.') }}
                            </span>
                        @endif
                        <span class="text-slate-500 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200/70" title="Total Bobot Pakan Grower">
                            {{ number_format($inventorySummary['pakan']['grower_stok_kg'] ?? 0, 0, ',', '.') }} Kg ({{ $inventorySummary['pakan']['kg_per_karung'] ?? 50 }} Kg/Krg)
                        </span>
                    </div>

                    <!-- Formula Perhitungan: (X Krg × Harga Krg) + (Y Kg × Harga Kg) -->
                    <div class="mt-2 p-1.5 rounded-lg bg-sky-50/60 border border-sky-100 text-[10px] text-sky-950/80 leading-snug">
                        <span class="font-bold text-sky-900">Hitungan:</span> 
                        ({{ $inventorySummary['pakan']['grower_karung_bulat'] ?? 0 }} Krg &times; {{ number_format($inventorySummary['pakan']['grower_harga_karung'] ?? 0, 0, ',', '.') }}) + 
                        ({{ $inventorySummary['pakan']['grower_sisa_kg'] ?? 0 }} Kg &times; {{ number_format($inventorySummary['pakan']['grower_harga_kg'] ?? 0, 0, ',', '.') }})
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span>Harga Jual Pakan</span>
                    <span class="font-bold text-slate-700 text-right">
                        Rp {{ number_format($inventorySummary['pakan']['grower_harga_karung'] ?? 0, 0, ',', '.') }}/Krg
                        <span class="text-sky-700 font-semibold">(+ Rp {{ number_format($inventorySummary['pakan']['grower_harga_kg'] ?? 0, 0, ',', '.') }}/Kg)</span>
                    </span>
                </div>
            </div>

            <!-- 4. Populasi Ayam Farm (Total Sisa Ayam) -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between hover:border-violet-200 transition-colors">
                <div>
                    <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                        <span class="text-violet-700">Total Sisa Ayam</span>
                        <div class="w-7 h-7 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center">
                            <i data-lucide="feather" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="mt-2.5">
                        <span class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight block">
                            {{ number_format($inventorySummary['ayam']['total_sisa_ayam'] ?? 0, 0, ',', '.') }} <span class="text-base font-bold text-violet-700">Ekor</span>
                        </span>
                    </div>
                    <!-- Keterisian Kandang & Info Kloter -->
                    <div class="mt-2.5 flex flex-wrap items-center gap-1.5 text-[11px] font-semibold">
                        <span class="text-violet-900 bg-violet-50 px-2 py-0.5 rounded-md border border-violet-200/70 font-bold" title="Keterisian Kandang">
                            🏠 {{ $inventorySummary['ayam']['persentase_keterisian'] ?? 0 }}% Terisi
                        </span>
                        <span class="text-slate-500 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200/70" title="Kapasitas Kandang">
                            Kapasitas: {{ number_format($inventorySummary['ayam']['total_kapasitas'] ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span>Rincian Kloter Aktif</span>
                    <span class="font-bold text-slate-700 truncate max-w-[55%]" title="@foreach($inventorySummary['ayam']['flocks'] ?? [] as $fl){{ $fl['name'] }}: {{ number_format($fl['current_population'], 0, ',', '.') }} ekor | @endforeach">
                        @if(!empty($inventorySummary['ayam']['flocks']))
                            {{ count($inventorySummary['ayam']['flocks']) }} Kloter Aktif
                        @else
                            Semua Kandang
                        @endif
                    </span>
                </div>
            </div>

        </div>
    </div>

    <!-- Executive Investor Insights & Export Toolbar -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-4 sm:p-5 text-white shadow-sm flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 border border-slate-800">
        
        <!-- 4 Clean Executive Metric Cards (Margin, OPEX, Kas Fisik, Saldo Bank) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 sm:gap-3.5 flex-1">
            
            <!-- 1. Net Profit Margin -->
            <div onclick="openFinancialFormulaModal('margin')" 
                class="bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/60 hover:border-emerald-500/50 rounded-xl p-3 sm:p-3.5 cursor-pointer transition-all duration-200 group flex items-center gap-3 relative shadow-xs"
                title="Klik untuk melihat rumus & hitungan realtime">
                <div class="w-9 h-9 rounded-lg bg-emerald-500/20 text-emerald-400 ring-1 ring-emerald-500/30 flex items-center justify-center font-black text-sm shrink-0 group-hover:scale-105 transition-transform">
                    %
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider truncate">Net Profit Margin</span>
                        <div class="w-4 h-4 rounded-full bg-slate-700 flex items-center justify-center text-slate-400 group-hover:text-emerald-400 group-hover:bg-emerald-500/20 transition-colors" title="Cara Hitung">
                            <i data-lucide="help-circle" class="w-3 h-3"></i>
                        </div>
                    </div>
                    <div class="text-sm sm:text-base font-black text-emerald-400 tracking-tight leading-tight mt-0.5">
                        {{ $profitMargin }}%
                    </div>
                    <span class="text-[10px] text-slate-400 block truncate mt-0.5">Laba Bersih / Omzet</span>
                </div>
            </div>

            <!-- 2. OPEX Ratio (Beban) -->
            <div onclick="openFinancialFormulaModal('opex')" 
                class="bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/60 hover:border-rose-500/50 rounded-xl p-3 sm:p-3.5 cursor-pointer transition-all duration-200 group flex items-center gap-3 relative shadow-xs"
                title="Klik untuk melihat rumus & hitungan realtime">
                <div class="w-9 h-9 rounded-lg bg-rose-500/20 text-rose-400 ring-1 ring-rose-500/30 flex items-center justify-center font-black text-xs shrink-0 group-hover:scale-105 transition-transform">
                    OP
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider truncate">OPEX Ratio</span>
                        <div class="w-4 h-4 rounded-full bg-slate-700 flex items-center justify-center text-slate-400 group-hover:text-rose-400 group-hover:bg-rose-500/20 transition-colors" title="Cara Hitung">
                            <i data-lucide="help-circle" class="w-3 h-3"></i>
                        </div>
                    </div>
                    <div class="text-sm sm:text-base font-black text-rose-300 tracking-tight leading-tight mt-0.5">
                        {{ $opexRatio }}%
                    </div>
                    <span class="text-[10px] text-slate-400 block truncate mt-0.5">Beban Operasional</span>
                </div>
            </div>

            <!-- 3. Total Pengeluaran (Beban Operasional) -->
            <div onclick="openFinancialFormulaModal('opex')" 
                class="bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/60 hover:border-rose-500/50 rounded-xl p-3 sm:p-3.5 cursor-pointer transition-all duration-200 group flex items-center gap-3 relative shadow-xs"
                title="Klik untuk melihat detail beban operasional (Total Pengeluaran: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }})">
                <div class="w-9 h-9 rounded-lg bg-rose-500/20 text-rose-400 ring-1 ring-rose-500/30 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <i data-lucide="trending-down" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider truncate">Total Pengeluaran</span>
                        <div class="w-4 h-4 rounded-full bg-slate-700 flex items-center justify-center text-slate-400 group-hover:text-rose-400 group-hover:bg-rose-500/20 transition-colors" title="Cara Hitung Beban">
                            <i data-lucide="help-circle" class="w-3 h-3"></i>
                        </div>
                    </div>
                    <div class="text-xs sm:text-sm font-extrabold text-rose-300 tracking-tight leading-tight mt-0.5 truncate">
                        - Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </div>
                    <span class="text-[10px] text-slate-400 block truncate mt-0.5">Beban Operasional Kandang</span>
                </div>
            </div>

            <!-- 4. Total Pemasukan (Omzet Penjualan) -->
            <div onclick="openFinancialFormulaModal('margin')" 
                class="bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/60 hover:border-emerald-500/50 rounded-xl p-3 sm:p-3.5 cursor-pointer transition-all duration-200 group flex items-center gap-3 relative shadow-xs"
                title="Klik untuk melihat detail omzet pemasukan (Total Pemasukan: Rp {{ number_format($totalPemasukan, 0, ',', '.') }})">
                <div class="w-9 h-9 rounded-lg bg-emerald-500/20 text-emerald-400 ring-1 ring-emerald-500/30 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider truncate">Total Pemasukan</span>
                        <div class="w-4 h-4 rounded-full bg-slate-700 flex items-center justify-center text-slate-400 group-hover:text-emerald-400 group-hover:bg-emerald-500/20 transition-colors" title="Cara Hitung Omzet">
                            <i data-lucide="help-circle" class="w-3 h-3"></i>
                        </div>
                    </div>
                    <div class="text-xs sm:text-sm font-extrabold text-emerald-400 tracking-tight leading-tight mt-0.5 truncate">
                        + Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                    </div>
                    <span class="text-[10px] text-slate-400 block truncate mt-0.5">Omzet Penjualan Nochi Farm</span>
                </div>
            </div>

        </div>

        <!-- Action Buttons (Share WA, Excel CSV, Cetak PDF) -->
        <div class="flex items-center justify-end gap-2 shrink-0">
            <!-- Share WhatsApp -->
            <a href="{{ $waUrl }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-xs transition-all"
                title="Kirim ringkasan kas ke WhatsApp Investor">
                <i data-lucide="share-2" class="w-3.5 h-3.5"></i>
                <span class="hidden sm:inline">Share WA</span>
            </a>

            <!-- Export CSV -->
            <a href="{{ route('report.csv', request()->query()) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-slate-700 hover:bg-slate-600 active:scale-95 text-white rounded-xl text-xs font-bold shadow-xs transition-all"
                title="Unduh data Excel / CSV">
                <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                <span class="hidden sm:inline">Excel (CSV)</span>
            </a>

            <!-- Cetak Resmi Investor -->
            <a href="{{ route('report.print', request()->query()) }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-rose-800 hover:bg-rose-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-xs transition-all"
                title="Buka lembar cetak laporan resmi investor">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Cetak Laporan</span>
            </a>
        </div>

    </div>

    <!-- Visual Analytics: Charts Section (Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        <!-- Chart 1: Cashflow Trend (Pemasukan vs Pengeluaran) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i data-lucide="bar-chart-2" class="w-4 h-4 text-nochi-orange"></i>
                        <span>Tren Arus Kas (Pemasukan vs Pengeluaran)</span>
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Perbandingan omzet penjualan telur/pakan terhadap biaya operasional bulanan</p>
                </div>
                <div class="flex items-center gap-3 text-[11px] font-bold">
                    <span class="flex items-center gap-1.5 text-emerald-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Pemasukan
                    </span>
                    <span class="flex items-center gap-1.5 text-rose-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Pengeluaran
                    </span>
                </div>
            </div>
            <div class="relative h-64 sm:h-72 w-full">
                <canvas id="cashflowTrendChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Category Breakdown (Doughnut Chart) -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2 mb-1">
                    <i data-lucide="pie-chart" class="w-4 h-4 text-maroon-800"></i>
                    <span>Komposisi Pengeluaran (%)</span>
                </h3>
                <p class="text-[11px] text-slate-400 mb-3">Distribusi pos biaya operasional kandang</p>
            </div>
            
            <div class="relative h-48 w-full flex items-center justify-center my-1">
                <canvas id="expenseCategoryChart"></canvas>
            </div>

            <!-- Top Expense Categories List -->
            <div class="mt-3 divide-y divide-slate-100 text-[11px] max-h-36 overflow-y-auto">
                @forelse($categoryBreakdown->take(4) as $cb)
                <div class="py-1.5 flex items-center justify-between">
                    <span class="font-semibold text-slate-700 truncate max-w-[55%]">{{ $cb['category'] }}</span>
                    <span class="font-bold text-slate-800">Rp {{ number_format($cb['total'], 0, ',', '.') }} <span class="text-slate-400 font-normal">({{ $cb['percentage'] }}%)</span></span>
                </div>
                @empty
                <div class="py-2 text-center text-slate-400">Belum ada data pengeluaran</div>
                @endforelse
            </div>
        </div>

    </div>


    <!-- Quick Action Banner & Search / Filter Controls -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-xs space-y-4">
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-base sm:text-lg font-black text-slate-800">Semua Data Transaksi</h2>
                <p class="text-xs text-slate-400 mt-0.5">Gabungan mutasi omzet penjualan dan biaya pengeluaran kandang.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Filter Tabs: Semua / Pemasukan / Pengeluaran -->
                <div class="bg-slate-100 p-1 rounded-xl flex items-center text-xs font-bold">
                    <a href="{{ request()->fullUrlWithQuery(['type' => 'all']) }}"
                        class="px-3 py-1.5 rounded-lg transition-all {{ $typeFilter === 'all' ? 'bg-white text-slate-800 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                        Semua
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['type' => 'income']) }}"
                        class="px-3 py-1.5 rounded-lg transition-all {{ $typeFilter === 'income' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-500 hover:text-emerald-700' }}">
                        Pemasukan Saja
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['type' => 'expense']) }}"
                        class="px-3 py-1.5 rounded-lg transition-all {{ $typeFilter === 'expense' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-500 hover:text-rose-700' }}">
                        Pengeluaran Saja
                    </a>
                </div>

                <!-- Tombol Catat Pengeluaran Baru -->
                <a href="{{ route('expenses.create') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-nochi-orange hover:bg-nochi-orangeDark text-white rounded-xl text-xs font-bold shadow-xs transition-all active:scale-95">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>+ Catat Pengeluaran</span>
                </a>
            </div>
        </div>

        <!-- Search Input Bar -->
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
            @if($startDate && $endDate)
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
            @endif
            @if($typeFilter !== 'all')
                <input type="hidden" name="type" value="{{ $typeFilter }}">
            @endif
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari invoice, keperluan, @username penginput, @driver trip..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-maroon-800">
            </div>
            <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs sm:text-sm font-bold transition-all">
                Cari
            </button>
            @if(!empty($search))
                <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Reset
                </a>
            @endif
        </form>

    </div>

    <!-- ========================================================================= -->
    <!-- Table Sesuai Permintaan: nomor | semua transaksi | pemasukan | pengeluaran | aksi -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-4 text-center w-14">Nomor</th>
                        <th class="py-3.5 px-4">Semua Transaksi</th>
                        <th class="py-3.5 px-4 text-right w-44">Pemasukan</th>
                        <th class="py-3.5 px-4 text-right w-44">Pengeluaran</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                    @forelse($allTransactions as $idx => $trx)
                    <tr class="hover:bg-slate-50/70 transition-colors group">
                        
                        <!-- 1. Nomor -->
                        <td class="py-4 px-4 text-center text-slate-400 font-bold text-xs">
                            {{ $idx + 1 }}
                        </td>

                        <!-- 2. Semua Transaksi -->
                        <td class="py-4 px-4">
                            <div class="flex items-start gap-3">
                                
                                <!-- Icon Jenis Transaksi -->
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5 {{ $trx['type'] === 'pemasukan' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }}">
                                    @if($trx['type'] === 'pemasukan')
                                        <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                                    @else
                                        <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0 space-y-1">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <!-- Badge Jenis -->
                                        @if($trx['type'] === 'pemasukan')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                                PEMASUKAN
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-100 text-rose-800">
                                                PENGELUARAN
                                            </span>
                                        @endif

                                        <span class="text-[11px] text-slate-400 font-medium">&bull; {{ $trx['display_date'] }}</span>
                                        <span class="text-[11px] text-slate-400 font-mono">#{{ $trx['code'] }}</span>

                                        @if($trx['receipt_photo'])
                                            <span class="inline-flex items-center gap-1 text-[10px] bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded font-bold" title="Ada bukti foto nota">
                                                <i data-lucide="paperclip" class="w-3 h-3"></i> Nota
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Judul / Keperluan -->
                                    <h4 class="font-extrabold text-slate-800 text-sm sm:text-base leading-snug truncate">
                                        {{ $trx['title'] }}
                                    </h4>

                                    <!-- Kategori & Keterangan Rincian -->
                                    <div class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
                                        <span class="font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded text-[11px]">
                                            {{ $trx['category'] }} @if($trx['subcategory']) &bull; {{ $trx['subcategory'] }} @endif
                                        </span>
                                        @if($trx['description'])
                                            <span class="text-slate-400 truncate max-w-xs sm:max-w-md" title="{{ $trx['description'] }}">
                                                &mdash; {{ $trx['description'] }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- USERNAME PENGINPUT & USERNAME PERJALANAN (TRIP) -->
                                    <div class="flex flex-wrap items-center gap-2 pt-1.5 mt-1 border-t border-slate-100">
                                        <!-- Username Penginput -->
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-900 border border-amber-200/80 text-[11px] font-medium" title="Diinput oleh: {{ $trx['penginput_name'] }}">
                                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-amber-600"></i>
                                            <span class="text-amber-700 font-bold">Input:</span>
                                            <span class="font-black text-amber-950 font-mono">@<span>{{ $trx['penginput_username'] }}</span></span>
                                        </span>

                                        <!-- Username Perjalanan (Trip) -->
                                        @if(!empty($trx['perjalanan_username']))
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-sky-50 text-sky-900 border border-sky-200/80 text-[11px] font-medium" title="Petugas Perjalanan: {{ $trx['perjalanan_name'] }} | Rute: {{ $trx['trip_route'] }}">
                                                <i data-lucide="truck" class="w-3.5 h-3.5 text-sky-600"></i>
                                                <span class="text-sky-700 font-bold">Trip:</span>
                                                <span class="font-black text-sky-950 font-mono">@<span>{{ $trx['perjalanan_username'] }}</span></span>
                                                @if($trx['trip_code'])
                                                    <span class="text-sky-600 text-[10px] font-mono">({{ $trx['trip_code'] }})</span>
                                                @endif
                                            </span>
                                        @elseif($trx['type'] === 'pemasukan')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100/70 text-slate-400 border border-slate-200/50 text-[10px]" title="Penjualan Langsung (Non-Trip)">
                                                <i data-lucide="map-pin" class="w-3 h-3"></i> Non-Trip
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100/70 text-slate-400 border border-slate-200/50 text-[10px]" title="Operasional Kandang">
                                                <i data-lucide="home" class="w-3 h-3"></i> Kandang
                                            </span>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </td>

                        <!-- 3. Pemasukan -->
                        <td class="py-4 px-4 text-right font-black text-sm sm:text-base whitespace-nowrap">
                            @if($trx['type'] === 'pemasukan')
                                <span class="text-emerald-600">
                                    + Rp {{ number_format($trx['pemasukan'], 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-slate-300 font-normal">-</span>
                            @endif
                        </td>

                        <!-- 4. Pengeluaran -->
                        <td class="py-4 px-4 text-right font-black text-sm sm:text-base whitespace-nowrap">
                            @if($trx['type'] === 'pengeluaran')
                                <span class="text-rose-600">
                                    - Rp {{ number_format($trx['pengeluaran'], 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-slate-300 font-normal">-</span>
                            @endif
                        </td>

                        <!-- 5. Aksi -->
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-1">
                                <!-- Tombol Lihat Detail Modal -->
                                <button type="button" onclick="showTransactionDetailModal('{{ $trx['type'] }}', {{ $trx['id'] }})"
                                    class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 transition-all active:scale-95" title="Lihat Detail Transaksi">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>

                                <!-- Tombol Hapus (Khusus Pengeluaran) -->
                                @if($trx['type'] === 'pengeluaran')
                                    <button type="button" onclick="confirmDeleteExpense({{ $trx['id'] }}, '{{ $trx['code'] }}')"
                                        class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 transition-all active:scale-95" title="Hapus Pengeluaran">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                @endif
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="inbox" class="w-7 h-7"></i>
                            </div>
                            <p class="font-bold text-slate-600 text-sm">Belum ada data transaksi ditemukan</p>
                            <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter rentang tanggal atau kata kunci pencarian Anda.</p>
                            @if($isFilterActive)
                                <div class="mt-4">
                                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 text-white text-xs font-bold rounded-xl shadow-xs">
                                        <span>Reset Filter & Tampilkan Semua</span>
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer Summary -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-slate-500 font-medium">
            <div>
                Menampilkan <strong>{{ count($allTransactions) }}</strong> transaksi
                @if($isFilterActive)
                    &bull; Rentang tanggal: <span class="font-bold text-slate-700">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span>
                @else
                    &bull; Riwayat lengkap realtime
                @endif
            </div>
            <div class="flex items-center gap-4 text-xs font-bold">
                <span class="text-emerald-700">Pemasukan: Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
                <span class="text-slate-300">|</span>
                <span class="text-rose-700">Pengeluaran: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
                <span class="text-slate-300">|</span>
                <span class="{{ $saldoSaatIni >= 0 ? 'text-slate-900' : 'text-rose-700' }}">Saldo: Rp {{ number_format($saldoSaatIni, 0, ',', '.') }}</span>
            </div>
        </div>

    </div>

</div>

<!-- ========================================================================= -->
<!-- Modal Filter Tanggal Gaya Traveloka (Interactive Date Range Picker) -->
<!-- ========================================================================= -->
<div id="travelokaFilterModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex flex-col justify-end sm:justify-center sm:items-center p-0 sm:p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden transition-transform duration-300 translate-y-12 sm:translate-y-0">
        
        <!-- Header Gaya Traveloka -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <button type="button" onclick="closeTravelokaFilterModal()" class="w-9 h-9 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <div class="text-center">
                <h3 class="font-extrabold text-base text-slate-800">Pilih Rentang Tanggal</h3>
                <p class="text-[11px] text-slate-400">Filter transaksi ala Traveloka</p>
            </div>
            <div class="w-9"></div>
        </div>

        <div class="p-5 space-y-5 overflow-y-auto max-h-[75vh]">
            
            <!-- Quick Shortcut Chips (Gaya Traveloka) -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilihan Cepat</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="setPresetDateRange('all')" class="px-3 py-1.5 rounded-full text-xs font-bold border border-slate-200 hover:border-slate-400 bg-white text-slate-700 transition-colors">
                        Semua Riwayat (Tanpa Filter)
                    </button>
                    <button type="button" onclick="setPresetDateRange('today')" class="px-3 py-1.5 rounded-full text-xs font-bold border border-slate-200 hover:border-slate-400 bg-white text-slate-700 transition-colors">
                        Hari Ini
                    </button>
                    <button type="button" onclick="setPresetDateRange('yesterday')" class="px-3 py-1.5 rounded-full text-xs font-bold border border-slate-200 hover:border-slate-400 bg-white text-slate-700 transition-colors">
                        Kemarin
                    </button>
                    <button type="button" onclick="setPresetDateRange('last7')" class="px-3 py-1.5 rounded-full text-xs font-bold border border-slate-200 hover:border-slate-400 bg-white text-slate-700 transition-colors">
                        7 Hari Terakhir
                    </button>
                    <button type="button" onclick="setPresetDateRange('thisMonth')" class="px-3 py-1.5 rounded-full text-xs font-bold border border-slate-200 hover:border-slate-400 bg-white text-slate-700 transition-colors">
                        Bulan Ini
                    </button>
                    <button type="button" onclick="setPresetDateRange('lastMonth')" class="px-3 py-1.5 rounded-full text-xs font-bold border border-slate-200 hover:border-slate-400 bg-white text-slate-700 transition-colors">
                        Bulan Lalu
                    </button>
                    <button type="button" onclick="setPresetDateRange('last30')" class="px-3 py-1.5 rounded-full text-xs font-bold border border-slate-200 hover:border-slate-400 bg-white text-slate-700 transition-colors">
                        30 Hari Terakhir
                    </button>
                </div>
            </div>

            <!-- Date Range Inputs Box (Gaya Traveloka: Pergi - Pulang) -->
            <div class="grid grid-cols-2 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-200">
                
                <!-- Tanggal Mulai -->
                <div class="space-y-1">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider flex items-center gap-1">
                        <i data-lucide="calendar" class="w-3 h-3 text-nochi-orange"></i>
                        <span>Tanggal Mulai</span>
                    </span>
                    <input type="date" id="travelokaStartDate" value="{{ $startDate }}"
                        class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-nochi-orange">
                    <span id="startDayLabel" class="text-[10px] text-slate-400 block font-medium">Pilih tanggal awal</span>
                </div>

                <!-- Tanggal Selesai -->
                <div class="space-y-1">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider flex items-center gap-1">
                        <i data-lucide="calendar" class="w-3 h-3 text-nochi-orange"></i>
                        <span>Tanggal Selesai</span>
                    </span>
                    <input type="date" id="travelokaEndDate" value="{{ $endDate }}"
                        class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-nochi-orange">
                    <span id="endDayLabel" class="text-[10px] text-slate-400 block font-medium">Pilih tanggal akhir</span>
                </div>

            </div>

            <!-- Duration & Info Badge -->
            <div id="rangeDurationBadge" class="p-3 bg-orange-50/70 border border-orange-200 rounded-xl text-xs text-orange-900 flex items-center justify-between">
                <span class="flex items-center gap-1.5 font-medium">
                    <i data-lucide="info" class="w-4 h-4 text-nochi-orange shrink-0"></i>
                    <span id="durationText">Filter baru akan diaktifkan setelah Anda menekan tombol <strong>Terapkan Filter</strong>.</span>
                </span>
            </div>

        </div>

        <!-- Action Buttons (Hapus / Terapkan) -->
        <div class="p-4 border-t border-slate-100 grid grid-cols-2 gap-3 bg-white">
            <button type="button" onclick="clearTravelokaFilter()"
                class="w-full py-3 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-xl font-bold text-xs sm:text-sm transition-colors">
                Hapus Filter
            </button>
            <button type="button" onclick="applyTravelokaFilter()"
                class="w-full py-3 bg-nochi-orange hover:bg-nochi-orangeDark text-white rounded-xl font-bold text-xs sm:text-sm shadow-md transition-colors flex items-center justify-center gap-2">
                <i data-lucide="check" class="w-4 h-4 stroke-[3]"></i>
                <span>Terapkan Filter</span>
            </button>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal Detail Transaksi (Pemasukan & Pengeluaran) -->
<!-- ========================================================================= -->
<div id="trxDetailModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex flex-col justify-end sm:justify-center sm:items-center p-0 sm:p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden transition-transform duration-300 translate-y-12 sm:translate-y-0">
        
        <!-- Header -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <button type="button" onclick="closeTransactionDetailModal()" class="w-9 h-9 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <h3 id="modalDetailTitle" class="font-extrabold text-base text-slate-800">Detail Transaksi</h3>
            <div class="w-9"></div>
        </div>

        <!-- Content Body (Dynamically populated) -->
        <div id="modalDetailContent" class="p-5 overflow-y-auto max-h-[70vh] space-y-4 text-xs sm:text-sm">
            <div class="text-center py-8 text-slate-400">
                <span class="animate-spin inline-block mr-2">&#9696;</span> Memuat data transaksi...
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end">
            <button type="button" onclick="closeTransactionDetailModal()"
                class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold text-xs transition-colors">
                Tutup
            </button>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal Penjelasan Rumus & Hitungan Realtime Finansial (% Margin & OPEX) -->
<!-- ========================================================================= -->
<div id="financialFormulaModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex flex-col justify-end sm:justify-center sm:items-center p-0 sm:p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white w-full sm:max-w-2xl rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden transition-transform duration-300 translate-y-12 sm:translate-y-0">
        
        <!-- Header -->
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center">
                    <i data-lucide="calculator" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-base text-slate-800">Cara Hitung Metrik Finansial (%)</h3>
                    <p class="text-[11px] text-slate-400">Transparansi rumus & data realtime Nochi Farm</p>
                </div>
            </div>
            <button type="button" onclick="closeFinancialFormulaModal()" class="w-9 h-9 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-5 overflow-y-auto max-h-[75vh] space-y-4 text-xs sm:text-sm">
            
            <!-- Ringkasan Angka Realtime Dasar -->
            <div class="grid grid-cols-3 gap-2.5 p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                <div class="p-2.5 bg-white rounded-xl border border-slate-100 shadow-2xs">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Pemasukan (A)</span>
                    <span class="font-black text-emerald-700 text-xs sm:text-sm block mt-0.5">+ Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
                </div>
                <div class="p-2.5 bg-white rounded-xl border border-slate-100 shadow-2xs">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Beban OPEX (B)</span>
                    <span class="font-black text-rose-700 text-xs sm:text-sm block mt-0.5">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
                </div>
                <div class="p-2.5 bg-white rounded-xl border border-slate-100 shadow-2xs">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Saldo Bersih (A - B)</span>
                    <span class="font-black {{ $saldoSaatIni >= 0 ? 'text-slate-900' : 'text-rose-700' }} text-xs sm:text-sm block mt-0.5">Rp {{ number_format($saldoSaatIni, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Kartu 1: Net Profit Margin -->
            <div id="sectionFormulaMargin" class="p-4 rounded-2xl border border-emerald-200 bg-emerald-50/40 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-xs">%</span>
                        <h4 class="font-black text-emerald-950 text-sm sm:text-base">1. Net Profit Margin = {{ $profitMargin }}%</h4>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-md">Laba Bersih</span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    <strong>Definisi:</strong> Mengukur persentase sisa keuntungan bersih yang diperoleh pemilik/investor dari seluruh total omzet penjualan telur & pakan setelah dipotong biaya operasional.
                </p>

                <!-- Rumus Box -->
                <div class="p-3 bg-white rounded-xl border border-emerald-200/80 space-y-1.5 font-mono text-xs">
                    <div class="text-slate-500 font-bold uppercase text-[10px]">Rumus Matematika:</div>
                    <div class="font-black text-slate-800">
                        Net Profit Margin = (Saldo Bersih / Total Pemasukan) &times; 100%
                    </div>
                    <div class="pt-1.5 border-t border-slate-100 text-emerald-700 font-bold">
                        Hitungan Realtime: (Rp {{ number_format($saldoSaatIni, 0, ',', '.') }} &divide; Rp {{ number_format($totalPemasukan, 0, ',', '.') }}) &times; 100% = <strong class="text-emerald-900">{{ $profitMargin }}%</strong>
                    </div>
                </div>

                <div class="p-2.5 bg-emerald-100/60 rounded-xl text-[11px] text-emerald-900 flex items-start gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-700 shrink-0 mt-0.5"></i>
                    <span><strong>Makna Bisnis:</strong> Dari setiap <strong>Rp 100</strong> pendapatan omzet yang masuk ke Nochi Farm, tersisa <strong>Rp {{ $profitMargin }}</strong> sebagai laba bersih peternakan.</span>
                </div>
            </div>

            <!-- Kartu 2: OPEX Ratio -->
            <div id="sectionFormulaOpex" class="p-4 rounded-2xl border border-rose-200 bg-rose-50/40 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-800 flex items-center justify-center font-black text-xs">OP</span>
                        <h4 class="font-black text-rose-950 text-sm sm:text-base">2. OPEX Ratio (Beban Biaya) = {{ $opexRatio }}%</h4>
                    </div>
                    <span class="text-[11px] font-bold text-rose-700 bg-rose-100 px-2.5 py-0.5 rounded-md">Beban Operasional</span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    <strong>Definisi:</strong> Mengukur berapa persen dari pendapatan omzet yang terserap untuk membiayai operasional peternakan (*Operating Expenses* seperti pakan layer, gaji tenaga kerja, listrik PLN, dan BBM pickup).
                </p>

                <!-- Rumus Box -->
                <div class="p-3 bg-white rounded-xl border border-rose-200/80 space-y-1.5 font-mono text-xs">
                    <div class="text-slate-500 font-bold uppercase text-[10px]">Rumus Matematika:</div>
                    <div class="font-black text-slate-800">
                        OPEX Ratio = (Total Pengeluaran / Total Pemasukan) &times; 100%
                    </div>
                    <div class="pt-1.5 border-t border-slate-100 text-rose-700 font-bold">
                        Hitungan Realtime: (Rp {{ number_format($totalPengeluaran, 0, ',', '.') }} &divide; Rp {{ number_format($totalPemasukan, 0, ',', '.') }}) &times; 100% = <strong class="text-rose-900">{{ $opexRatio }}%</strong>
                    </div>
                </div>

                <div class="p-2.5 bg-rose-100/60 rounded-xl text-[11px] text-rose-900 flex items-start gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-700 shrink-0 mt-0.5"></i>
                    <span><strong>Makna Bisnis:</strong> Sebesar <strong>{{ $opexRatio }}%</strong> dari omzet terserap untuk biaya operasional kandang. Semakin efisien belanja operasional, semakin tinggi sisa margin labanya.</span>
                </div>
            </div>

            <!-- Kartu 3: Kas Fisik Kandang (Tunai) -->
            <div id="sectionFormulaKasTunai" class="p-4 rounded-2xl border border-amber-200 bg-amber-50/40 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-black text-xs">
                            <i data-lucide="coins" class="w-4 h-4"></i>
                        </span>
                        <h4 class="font-black text-amber-950 text-sm sm:text-base">3. Kas Fisik Kandang = Rp {{ number_format($saldoKasTunai, 0, ',', '.') }}</h4>
                    </div>
                    <span class="text-[11px] font-bold text-amber-700 bg-amber-100 px-2.5 py-0.5 rounded-md">Kas Tunai</span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    <strong>Definisi:</strong> Menghitung sisa uang kas tunai fisik di brankas/kantor kandang dari total penerimaan penjualan tunai dikurangi belanja operasional tunai.
                </p>

                <!-- Rumus Box -->
                <div class="p-3 bg-white rounded-xl border border-amber-200/80 space-y-1.5 font-mono text-xs">
                    <div class="text-slate-500 font-bold uppercase text-[10px]">Rumus Perhitungan:</div>
                    <div class="font-black text-slate-800">
                        Kas Fisik Kandang = Pemasukan Tunai - Pengeluaran Tunai
                    </div>
                    <div class="pt-1.5 border-t border-slate-100 text-amber-700 font-bold">
                        Hitungan Realtime: Rp {{ number_format($tunaiPemasukan, 0, ',', '.') }} - Rp {{ number_format($tunaiPengeluaran, 0, ',', '.') }} = <strong class="{{ $saldoKasTunai >= 0 ? 'text-amber-900' : 'text-rose-700' }}">Rp {{ number_format($saldoKasTunai, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="p-2.5 bg-amber-100/60 rounded-xl text-[11px] text-amber-900 flex items-start gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-amber-700 shrink-0 mt-0.5"></i>
                    <span><strong>Penjelasan:</strong> Jika bernilai minus, berarti kandang menggunakan dana talangan kas atau sebagian belanja tunai belum diganti dari penarikan rekening bank.</span>
                </div>
            </div>

            <!-- Kartu 4: Saldo Bank / Transfer -->
            <div id="sectionFormulaSaldoBank" class="p-4 rounded-2xl border border-sky-200 bg-sky-50/40 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-sky-100 text-sky-800 flex items-center justify-center font-black text-xs">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                        </span>
                        <h4 class="font-black text-sky-950 text-sm sm:text-base">4. Saldo Bank / Transfer = Rp {{ number_format($saldoBankTransfer, 0, ',', '.') }}</h4>
                    </div>
                    <span class="text-[11px] font-bold text-sky-700 bg-sky-100 px-2.5 py-0.5 rounded-md">Rekening Bank</span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    <strong>Definisi:</strong> Menghitung sisa dana peternakan yang tersimpan di rekening bank dari seluruh transfer penjualan telur & pakan dikurangi pengeluaran non-tunai.
                </p>

                <!-- Rumus Box -->
                <div class="p-3 bg-white rounded-xl border border-sky-200/80 space-y-1.5 font-mono text-xs">
                    <div class="text-slate-500 font-bold uppercase text-[10px]">Rumus Perhitungan:</div>
                    <div class="font-black text-slate-800">
                        Saldo Bank = Pemasukan Transfer - Pengeluaran Transfer
                    </div>
                    <div class="pt-1.5 border-t border-slate-100 text-sky-700 font-bold">
                        Hitungan Realtime: Rp {{ number_format($transferPemasukan, 0, ',', '.') }} - Rp {{ number_format($transferPengeluaran, 0, ',', '.') }} = <strong class="{{ $saldoBankTransfer >= 0 ? 'text-sky-900' : 'text-rose-700' }}">Rp {{ number_format($saldoBankTransfer, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="p-2.5 bg-sky-100/60 rounded-xl text-[11px] text-sky-900 flex items-start gap-2">
                    <i data-lucide="shield-check" class="w-4 h-4 text-sky-700 shrink-0 mt-0.5"></i>
                    <span><strong>Penjelasan:</strong> Dana di rekening bank siap digunakan untuk operasional berikutnya atau ditransfer ke kas fisik kandang saat dibutuhkan.</span>
                </div>
            </div>

            <!-- Kartu 5: Validasi 100% Keseimbangan -->
            <div class="p-3.5 bg-amber-50/80 rounded-2xl border border-amber-200 text-xs text-amber-950 flex flex-wrap items-center justify-between gap-2">
                <span class="font-bold flex items-center gap-1.5">
                    <i data-lucide="scale" class="w-4 h-4 text-amber-600"></i>
                    <span>Validasi Keseimbangan Akuntansi:</span>
                </span>
                <span class="font-mono font-black text-amber-900 bg-white px-2.5 py-1 rounded-lg border border-amber-200 shadow-2xs">
                    {{ $profitMargin }}% (Laba) + {{ $opexRatio }}% (Beban) = 100.0%
                </span>
            </div>

        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end">
            <button type="button" onclick="closeFinancialFormulaModal()"
                class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold text-xs transition-colors">
                Tutup Penjelasan
            </button>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    // -------------------------------------------------------------
    // Financial Formula Modal Controls (% Margin, OPEX, Kas & Bank)
    // -------------------------------------------------------------
    function openFinancialFormulaModal(focusSection = 'all') {
        const modal = document.getElementById('financialFormulaModal');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.remove('translate-y-12');

        const secMargin = document.getElementById('sectionFormulaMargin');
        const secOpex = document.getElementById('sectionFormulaOpex');
        const secKasTunai = document.getElementById('sectionFormulaKasTunai');
        const secSaldoBank = document.getElementById('sectionFormulaSaldoBank');

        if (focusSection === 'margin' && secMargin) {
            secMargin.scrollIntoView({ behavior: 'smooth', block: 'center' });
            secMargin.classList.add('ring-2', 'ring-emerald-400');
            setTimeout(() => secMargin.classList.remove('ring-2', 'ring-emerald-400'), 2000);
        } else if (focusSection === 'opex' && secOpex) {
            secOpex.scrollIntoView({ behavior: 'smooth', block: 'center' });
            secOpex.classList.add('ring-2', 'ring-rose-400');
            setTimeout(() => secOpex.classList.remove('ring-2', 'ring-rose-400'), 2000);
        } else if (focusSection === 'kas_tunai' && secKasTunai) {
            secKasTunai.scrollIntoView({ behavior: 'smooth', block: 'center' });
            secKasTunai.classList.add('ring-2', 'ring-amber-400');
            setTimeout(() => secKasTunai.classList.remove('ring-2', 'ring-amber-400'), 2000);
        } else if (focusSection === 'saldo_bank' && secSaldoBank) {
            secSaldoBank.scrollIntoView({ behavior: 'smooth', block: 'center' });
            secSaldoBank.classList.add('ring-2', 'ring-sky-400');
            setTimeout(() => secSaldoBank.classList.remove('ring-2', 'ring-sky-400'), 2000);
        }

        lucide.createIcons();
    }

    function closeFinancialFormulaModal() {
        const modal = document.getElementById('financialFormulaModal');
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.add('translate-y-12');
    }

    // -------------------------------------------------------------
    // Traveloka Filter Modal Controls
    // -------------------------------------------------------------
    function openTravelokaFilterModal() {
        const modal = document.getElementById('travelokaFilterModal');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.remove('translate-y-12');
        updateDayLabels();
    }

    function closeTravelokaFilterModal() {
        const modal = document.getElementById('travelokaFilterModal');
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.add('translate-y-12');
    }

    const startInput = document.getElementById('travelokaStartDate');
    const endInput = document.getElementById('travelokaEndDate');

    startInput.addEventListener('change', updateDayLabels);
    endInput.addEventListener('change', updateDayLabels);

    function updateDayLabels() {
        const sVal = startInput.value;
        const eVal = endInput.value;

        if (sVal) {
            const d1 = new Date(sVal);
            document.getElementById('startDayLabel').textContent = d1.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' });
        } else {
            document.getElementById('startDayLabel').textContent = 'Pilih tanggal awal';
        }

        if (eVal) {
            const d2 = new Date(eVal);
            document.getElementById('endDayLabel').textContent = d2.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' });
        } else {
            document.getElementById('endDayLabel').textContent = 'Pilih tanggal akhir';
        }

        if (sVal && eVal) {
            const diffMs = new Date(eVal) - new Date(sVal);
            const days = Math.round(diffMs / (1000 * 60 * 60 * 24)) + 1;
            if (days > 0) {
                document.getElementById('durationText').innerHTML = `Rentang dipilih: <strong>${days} Hari</strong>. Tekan <strong>Terapkan Filter</strong> untuk mengaktifkan.`;
            } else {
                document.getElementById('durationText').innerHTML = `<span class="text-rose-600 font-bold">Tanggal selesai tidak boleh sebelum tanggal mulai!</span>`;
            }
        }
    }

    function setPresetDateRange(preset) {
        const now = new Date();
        const formatDate = (d) => {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        if (preset === 'all') {
            startInput.value = '';
            endInput.value = '';
        } else if (preset === 'today') {
            const t = formatDate(now);
            startInput.value = t;
            endInput.value = t;
        } else if (preset === 'yesterday') {
            const y = new Date();
            y.setDate(y.getDate() - 1);
            const str = formatDate(y);
            startInput.value = str;
            endInput.value = str;
        } else if (preset === 'last7') {
            const s = new Date();
            s.setDate(s.getDate() - 6);
            startInput.value = formatDate(s);
            endInput.value = formatDate(now);
        } else if (preset === 'thisMonth') {
            const s = new Date(now.getFullYear(), now.getMonth(), 1);
            startInput.value = formatDate(s);
            endInput.value = formatDate(now);
        } else if (preset === 'lastMonth') {
            const s = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            const e = new Date(now.getFullYear(), now.getMonth(), 0);
            startInput.value = formatDate(s);
            endInput.value = formatDate(e);
        } else if (preset === 'last30') {
            const s = new Date();
            s.setDate(s.getDate() - 29);
            startInput.value = formatDate(s);
            endInput.value = formatDate(now);
        }

        updateDayLabels();
    }

    function applyTravelokaFilter() {
        const sVal = startInput.value;
        const eVal = endInput.value;

        if (!sVal && !eVal) {
            clearTravelokaFilter();
            return;
        }

        if (sVal && !eVal) {
            Swal.fire({ icon: 'warning', title: 'Pilih Tanggal Selesai', text: 'Silakan tentukan tanggal selesai.', confirmButtonColor: '#ea580c' });
            return;
        }

        if (!sVal && eVal) {
            Swal.fire({ icon: 'warning', title: 'Pilih Tanggal Mulai', text: 'Silakan tentukan tanggal mulai.', confirmButtonColor: '#ea580c' });
            return;
        }

        if (new Date(eVal) < new Date(sVal)) {
            Swal.fire({ icon: 'warning', title: 'Rentang Tidak Valid', text: 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.', confirmButtonColor: '#ea580c' });
            return;
        }

        // Redirect dengan filter aktif
        const url = new URL(window.location.href);
        url.searchParams.set('start_date', sVal);
        url.searchParams.set('end_date', eVal);
        window.location.href = url.toString();
    }

    function clearTravelokaFilter() {
        const url = new URL(window.location.href);
        url.searchParams.delete('start_date');
        url.searchParams.delete('end_date');
        window.location.href = url.toString();
    }

    // -------------------------------------------------------------
    // Detail Transaksi Modal Controls
    // -------------------------------------------------------------
    function showTransactionDetailModal(type, id) {
        const modal = document.getElementById('trxDetailModal');
        const content = document.getElementById('modalDetailContent');
        const title = document.getElementById('modalDetailTitle');

        title.textContent = type === 'pemasukan' ? 'Detail Transaksi Pemasukan (Omzet)' : 'Detail Transaksi Pengeluaran Kandang';
        content.innerHTML = `<div class="text-center py-8 text-slate-400"><span class="animate-spin inline-block mr-2">&#9696;</span> Memuat detail...</div>`;

        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.remove('translate-y-12');

        fetch(`/transaksi/${type}/${id}`)
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    content.innerHTML = `<p class="text-center text-rose-600 py-6 font-bold">${res.message}</p>`;
                    return;
                }

                const d = res.data;
                if (type === 'pemasukan') {
                    let itemsHtml = '';
                    if (d.items && d.items.length > 0) {
                        itemsHtml = `
                            <div class="mt-3">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Rincian Barang Terjual</span>
                                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                                            <tr>
                                                <th class="p-2">Item</th>
                                                <th class="p-2 text-center">Qty</th>
                                                <th class="p-2 text-right">Harga Satuan</th>
                                                <th class="p-2 text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            ${d.items.map(it => `
                                                <tr>
                                                    <td class="p-2 font-medium text-slate-800">${it.item_name}</td>
                                                    <td class="p-2 text-center font-bold">${it.quantity} ${it.unit}</td>
                                                    <td class="p-2 text-right text-slate-500">${it.formatted_unit_price}</td>
                                                    <td class="p-2 text-right font-bold text-slate-800">${it.formatted_total_price}</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                    }

                    content.innerHTML = `
                        <div class="bg-emerald-50/70 border border-emerald-200 rounded-2xl p-4 text-center">
                            <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider block">Total Omzet Penjualan</span>
                            <span class="text-2xl sm:text-3xl font-black text-emerald-700 block mt-1">+ ${d.formatted_amount}</span>
                            <span class="text-xs text-emerald-600 font-medium mt-0.5 inline-block">Status: ${d.payment_status} (${d.payment_method})</span>
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 space-y-2.5 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                                <span class="text-slate-500">Nomor Invoice</span>
                                <span class="font-bold text-slate-800 font-mono">#${d.invoice_no}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                                <span class="text-slate-500">Tanggal</span>
                                <span class="font-bold text-slate-800">${d.date}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                                <span class="text-slate-500">Petugas Penginput</span>
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-amber-50 text-amber-900 border border-amber-200/80 font-medium">
                                    <i data-lucide="user-check" class="w-3 h-3 text-amber-600"></i>
                                    <strong class="font-mono">@${d.penginput_username}</strong>
                                    <span class="text-slate-400 text-[10px]">(${d.penginput_name})</span>
                                </span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                                <span class="text-slate-500">Petugas Perjalanan</span>
                                ${d.perjalanan_username ? `
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-sky-50 text-sky-900 border border-sky-200/80 font-medium">
                                        <i data-lucide="truck" class="w-3 h-3 text-sky-600"></i>
                                        <strong class="font-mono">@${d.perjalanan_username}</strong>
                                        <span class="text-sky-700 text-[10px] font-mono">(${d.trip_code || 'Trip'})</span>
                                    </span>
                                ` : `
                                    <span class="text-slate-400 italic text-[11px]">Non-Trip (Penjualan Langsung)</span>
                                `}
                            </div>
                            ${d.trip_route ? `
                            <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                                <span class="text-slate-500">Rute / Kendaraan</span>
                                <span class="font-medium text-slate-700 text-right">${d.trip_route} ${d.trip_vehicle ? '(' + d.trip_vehicle + ')' : ''}</span>
                            </div>
                            ` : ''}
                            <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                                <span class="text-slate-500">Pelanggan</span>
                                <span class="font-bold text-slate-800">${d.customer_name}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                                <span class="text-slate-500">No. HP Pelanggan</span>
                                <span class="font-medium text-slate-700">${d.customer_phone}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-500">Catatan</span>
                                <span class="font-medium text-slate-700">${d.notes}</span>
                            </div>
                        </div>

                        ${itemsHtml}
                    `;
                } else {
                    // Pengeluaran
                    let photoHtml = '';
                    if (d.receipt_photo) {
                        photoHtml = `
                            <div class="mt-3">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Bukti Foto / Nota</span>
                                <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 p-1">
                                    <a href="${d.receipt_photo}" target="_blank" title="Klik untuk memperbesar">
                                        <img src="${d.receipt_photo}" alt="Nota" class="w-full max-h-64 object-contain rounded-xl hover:opacity-95 transition-opacity">
                                    </a>
                                </div>
                            </div>
                        `;
                    }

                    content.innerHTML = `
                        <div class="bg-rose-50/70 border border-rose-200 rounded-2xl p-4 text-center">
                            <span class="text-xs font-bold text-rose-800 uppercase tracking-wider block">Total Pengeluaran</span>
                            <span class="text-2xl sm:text-3xl font-black text-rose-700 block mt-1">- ${d.formatted_amount}</span>
                            <span class="text-xs text-rose-600 font-medium mt-0.5 inline-block">${d.category} &bull; ${d.subcategory}</span>
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 space-y-2.5 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                                <span class="text-slate-500">Kode Transaksi</span>
                                <span class="font-bold text-slate-800 font-mono">#${d.transaction_code}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                                <span class="text-slate-500">Tanggal</span>
                                <span class="font-bold text-slate-800">${d.date}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                                <span class="text-slate-500">Petugas Penginput</span>
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-amber-50 text-amber-900 border border-amber-200/80 font-medium">
                                    <i data-lucide="user-check" class="w-3 h-3 text-amber-600"></i>
                                    <strong class="font-mono">@${d.penginput_username}</strong>
                                    <span class="text-slate-400 text-[10px]">(${d.penginput_name})</span>
                                </span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                                <span class="text-slate-500">Operasional</span>
                                <span class="text-slate-600 font-medium">Kandang (Non-Trip)</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                                <span class="text-slate-500">Keperluan</span>
                                <span class="font-bold text-slate-800 text-right">${d.purpose}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-500">Keterangan</span>
                                <span class="font-medium text-slate-700 text-right">${d.notes}</span>
                            </div>
                        </div>

                        ${photoHtml}
                    `;
                }

                lucide.createIcons();
            })
            .catch(err => {
                console.error(err);
                content.innerHTML = `<p class="text-center text-rose-600 py-6 font-bold">Gagal memuat detail transaksi.</p>`;
            });
    }

    function closeTransactionDetailModal() {
        const modal = document.getElementById('trxDetailModal');
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.add('translate-y-12');
    }

    // -------------------------------------------------------------
    // Delete Expense with Confirmation
    // -------------------------------------------------------------
    function confirmDeleteExpense(id, code) {
        Swal.fire({
            title: 'Hapus Pengeluaran?',
            text: `Apakah Anda yakin ingin menghapus data transaksi #${code}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/pengeluaran/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dihapus',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message || 'Gagal menghapus', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'Gagal memproses penghapusan.', 'error');
                });
            }
        });
    }

    // -------------------------------------------------------------
    // Chart.js Visualizations (Cashflow Trend & Category Doughnut)
    // -------------------------------------------------------------
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Tren Arus Kas Bulanan
        const ctxTrend = document.getElementById('cashflowTrendChart');
        if (ctxTrend && typeof Chart !== 'undefined') {
            const chartLabels = {!! json_encode($chartLabels) !!};
            const chartIncome = {!! json_encode($chartIncome) !!};
            const chartExpense = {!! json_encode($chartExpense) !!};

            new Chart(ctxTrend, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Pemasukan (Omzet)',
                            data: chartIncome,
                            backgroundColor: '#10b981',
                            borderRadius: 8,
                            barPercentage: 0.6,
                            categoryPercentage: 0.6
                        },
                        {
                            label: 'Pengeluaran (Biaya)',
                            data: chartExpense,
                            backgroundColor: '#f43f5e',
                            borderRadius: 8,
                            barPercentage: 0.6,
                            categoryPercentage: 0.6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + ' jt';
                                    if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + ' rb';
                                    return 'Rp ' + value;
                                }
                            }
                        }
                    }
                }
            });
        }

        // 2. Komposisi Kategori Pengeluaran (Doughnut)
        const ctxCategory = document.getElementById('expenseCategoryChart');
        if (ctxCategory && typeof Chart !== 'undefined') {
            const catData = {!! json_encode($categoryBreakdown) !!};
            const labels = catData.map(c => c.category);
            const totals = catData.map(c => c.total);

            const palette = ['#800020', '#f95721', '#0284c7', '#8b5cf6', '#10b981', '#f59e0b', '#ec4899', '#64748b'];

            new Chart(ctxCategory, {
                type: 'doughnut',
                data: {
                    labels: labels.length ? labels : ['Belum Ada Pengeluaran'],
                    datasets: [{
                        data: totals.length ? totals : [1],
                        backgroundColor: totals.length ? palette.slice(0, totals.length) : ['#e2e8f0'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (!totals.length) return 'Belum ada data';
                                    return context.label + ': Rp ' + context.parsed.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
