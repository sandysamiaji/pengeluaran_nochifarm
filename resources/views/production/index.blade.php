@extends('layouts.app')

@section('title', 'Data & Monitoring Produksi Telur - NOCHI FARM')

@section('content')
<div class="space-y-6">

    <!-- ========================================================================= -->
    <!-- 1. Header Section: Modern Executive Command Center -->
    <!-- ========================================================================= -->
    <div class="relative overflow-hidden bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <!-- Ambient decorative background glow -->
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-amber-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-900 border border-amber-200/80 text-[10px] font-extrabold uppercase tracking-wider shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>Data Input Kandang Realtime</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-[10px] font-extrabold shadow-2xs">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Live Sync</span>
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span>Monitoring Produksi Telur</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 max-w-2xl leading-relaxed">
                    Rekapitulasi hasil panen telur dari petugas kandang secara realtime, otomatis terkonversi ke satuan <b>Peti & Kg</b>, lengkap dengan tracking kualitas & username penginput.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 pt-2 lg:pt-0">
                @if(!empty($startDate) || !empty($endDate) || (!empty($coopId) && $coopId !== 'all') || !empty($search))
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-900 text-xs font-bold border border-amber-200/80 shadow-2xs">
                        <i data-lucide="filter" class="w-3.5 h-3.5 text-amber-600"></i>
                        <span>Filter Aktif</span>
                        <a href="{{ route('production.index') }}" class="ml-1 text-amber-700 hover:text-rose-600 font-black text-sm" title="Reset Semua Filter">&times;</a>
                    </div>
                @endif
                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-bold text-slate-700 shadow-2xs">
                    <i data-lucide="layers" class="w-4 h-4 text-amber-600"></i>
                    <span>Total Laporan: <strong class="text-slate-900">{{ number_format($productions->total(), 0, ',', '.') }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. 4 Grid Metric Cards (Total Telur, Peti & Kg, Hari Ini, dan Kualitas) -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- CARD 1: Total Telur Terkumpul (Butir) -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-all relative overflow-hidden flex flex-col justify-between group">
            <!-- Top Gradient Accent Stripe -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-400 via-orange-500 to-amber-600"></div>

            <div>
                <!-- Top Header: Label & Icon -->
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Total Telur Terkumpul</span>
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-100 to-orange-50 text-amber-700 border border-amber-200/80 shadow-inner flex items-center justify-center group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5 fill-amber-500 drop-shadow-2xs" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C8.13 2 5 6.48 5 12c0 4.42 3.13 8 7 8s7-3.58 7-8c0-5.52-3.13-10-7-10z"/>
                        </svg>
                    </div>
                </div>

                <!-- Main Number -->
                <div class="mt-3">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight font-mono">
                            {{ number_format($totalEggs, 0, ',', '.') }}
                        </span>
                        <span class="text-xs sm:text-sm font-extrabold text-slate-400">Butir</span>
                    </div>
                    <span class="text-[11px] text-slate-400 font-medium block mt-0.5">Akumulasi seluruh butir hasil panen</span>
                </div>

                <!-- Visual Ratio Bar (Baik vs Pecah) -->
                @php
                    $goodRatio = $totalEggs > 0 ? min(100, max(0, ($totalGoodEggs / $totalEggs) * 100)) : 100;
                    $brokenRatio = $totalEggs > 0 ? min(100, max(0, ($totalBrokenEggs / $totalEggs) * 100)) : 0;
                @endphp
                <div class="mt-3.5 w-full bg-slate-100 h-2 rounded-full overflow-hidden flex shadow-inner">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-full rounded-l-full" style="width: {{ $goodRatio }}%" title="Telur Baik: {{ number_format($goodRatio, 1) }}%"></div>
                    @if($brokenRatio > 0)
                        <div class="bg-rose-500 h-full rounded-r-full" style="width: {{ $brokenRatio }}%" title="Telur Pecah: {{ number_format($brokenRatio, 1) }}%"></div>
                    @endif
                </div>
            </div>

            <!-- Bottom Badges: Baik & Pecah -->
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs gap-2">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-bold text-[11px] shadow-2xs">
                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span>Baik: <strong class="text-emerald-950">{{ number_format($totalGoodEggs, 0, ',', '.') }}</strong></span>
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-rose-50 text-rose-800 border border-rose-200/80 font-bold text-[11px] shadow-2xs">
                    <i data-lucide="alert-circle" class="w-3.5 h-3.5 text-rose-600"></i>
                    <span>Pecah: <strong class="text-rose-950">{{ number_format($totalBrokenEggs, 0, ',', '.') }}</strong></span>
                </span>
            </div>
        </div>

        <!-- CARD 2: Total Peti & Kg (Konversi Lengkap) -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-all relative overflow-hidden flex flex-col justify-between group">
            <!-- Top Gradient Accent Stripe -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-orange-400 via-amber-500 to-yellow-500"></div>

            <div>
                <!-- Top Header: Label & Icon -->
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Konversi Peti & Kg</span>
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-orange-100 to-amber-50 text-orange-700 border border-orange-200/80 shadow-inner flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i data-lucide="package" class="w-5 h-5 text-orange-600"></i>
                    </div>
                </div>

                <!-- Main Number -->
                <div class="mt-3">
                    <div class="flex flex-wrap items-baseline gap-1.5">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-mono">
                            {{ number_format($intPeti, 0, ',', '.') }}
                        </span>
                        <span class="text-xs sm:text-sm font-extrabold text-slate-500">Peti</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-black bg-amber-100/80 text-amber-900 border border-amber-300 shadow-2xs ml-0.5">
                            + {{ number_format($sisaKg, 1, ',', '.') }} Kg
                        </span>
                    </div>
                    <div class="mt-1 text-xs text-slate-500 font-semibold flex items-center gap-1.5">
                        <span>Total Bobot:</span>
                        <span class="text-slate-900 font-extrabold">{{ number_format($totalWeightKg, 1, ',', '.') }} Kg</span>
                        <span class="text-slate-400 font-normal">({{ number_format($totalCrates, 2, ',', '.') }} Peti eq)</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Badges: Standar Peti & Record Count -->
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs gap-2">
                <span class="text-[11px] font-bold text-slate-400 flex items-center gap-1">
                    <i data-lucide="scale" class="w-3.5 h-3.5 text-amber-600"></i>
                    <span>1 Peti = 10 Kg</span>
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-50 text-amber-900 border border-amber-200 font-extrabold text-[11px] shadow-2xs">
                    <i data-lucide="database" class="w-3 h-3 text-amber-700"></i>
                    <span>{{ $productions->total() }} Data Input</span>
                </span>
            </div>
        </div>

        <!-- CARD 3: Produksi Hari Ini -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-all relative overflow-hidden flex flex-col justify-between group">
            <!-- Top Gradient Accent Stripe -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-400 via-teal-500 to-green-600"></div>

            <div>
                <!-- Top Header: Label & Icon -->
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Produksi Hari Ini</span>
                        @if($todayEggs > 0)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 animate-pulse">
                                Live
                            </span>
                        @else
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-500">
                                Standby
                            </span>
                        @endif
                    </div>
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-100 to-teal-50 text-emerald-700 border border-emerald-200/80 shadow-inner flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i data-lucide="calendar-check" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                </div>

                <!-- Main Number -->
                <div class="mt-3">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl sm:text-4xl font-black tracking-tight font-mono {{ $todayEggs > 0 ? 'text-emerald-700' : 'text-slate-800' }}">
                            {{ number_format($todayEggs, 0, ',', '.') }}
                        </span>
                        <span class="text-xs sm:text-sm font-extrabold text-slate-400">Butir</span>
                    </div>
                    <div class="mt-1 text-xs font-bold {{ $todayEggs > 0 ? 'text-emerald-700' : 'text-slate-400' }}">
                        {{ $todayIntPeti }} Peti + {{ number_format($todaySisaKg, 1, ',', '.') }} Kg
                        @if($todayWeightKg > 0)
                            <span class="font-normal text-slate-400">({{ number_format($todayWeightKg, 1, ',', '.') }} Kg)</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bottom Badges: Detail Hari Ini -->
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs gap-2">
                @if($todayEggs > 0)
                    <span class="text-[11px] font-bold text-emerald-700 flex items-center gap-1">
                        <i data-lucide="check" class="w-3 h-3"></i> Baik: <strong>{{ number_format($todayGoodEggs, 0, ',', '.') }}</strong>
                    </span>
                    <span class="text-[11px] font-bold text-rose-600 flex items-center gap-1">
                        <i data-lucide="x" class="w-3 h-3"></i> Pecah: <strong>{{ number_format($todayBrokenEggs, 0, ',', '.') }}</strong>
                    </span>
                @else
                    <span class="text-[11px] font-medium text-slate-400 flex items-center gap-1">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-300"></i>
                        <span>Menunggu input kandang hari ini</span>
                    </span>
                    <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-200">
                        {{ \Carbon\Carbon::today()->translatedFormat('d M') }}
                    </span>
                @endif
            </div>
        </div>

        <!-- CARD 4: Kualitas & Grade Telur -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-all relative overflow-hidden flex flex-col justify-between group">
            <!-- Top Gradient Accent Stripe -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-400 via-indigo-500 to-violet-600"></div>

            <div>
                <!-- Top Header: Label & Icon -->
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Kualitas Telur Utuh</span>
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-100 to-violet-50 text-indigo-700 border border-indigo-200/80 shadow-inner flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i data-lucide="award" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                </div>

                <!-- Main Number & Grade Badge -->
                @php
                    $goodPct = $totalEggs > 0 ? round(($totalGoodEggs / $totalEggs) * 100, 1) : 100;
                    $brokenPct = $totalEggs > 0 ? round(($totalBrokenEggs / $totalEggs) * 100, 1) : 0;
                @endphp
                <div class="mt-3">
                    <div class="flex items-baseline justify-between gap-2">
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl sm:text-4xl font-black text-indigo-950 tracking-tight font-mono">
                                {{ $goodPct }}%
                            </span>
                        </div>
                        @if($goodPct >= 97)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 font-extrabold text-[10px] shadow-2xs">
                                <span>👑</span>
                                <span>Grade Prima</span>
                            </span>
                        @elseif($goodPct >= 90)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-sky-50 text-sky-800 border border-sky-200 font-extrabold text-[10px] shadow-2xs">
                                <span>👍</span>
                                <span>Grade Standar</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 font-extrabold text-[10px] shadow-2xs">
                                <span>⚠️</span>
                                <span>Perlu Cek</span>
                            </span>
                        @endif
                    </div>
                    <span class="text-[11px] text-slate-400 font-medium block mt-0.5">Persentase butir utuh layak jual</span>
                </div>

                <!-- Visual Bar Kualitas -->
                <div class="mt-3.5 w-full bg-slate-100 h-2 rounded-full overflow-hidden shadow-inner">
                    <div class="bg-gradient-to-r from-indigo-500 to-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $goodPct }}%"></div>
                </div>
            </div>

            <!-- Bottom Badges: Rincian Utuh vs Rusak -->
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs gap-2">
                <span class="text-[11px] font-bold text-slate-600 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Utuh: <strong class="text-slate-800">{{ $goodPct }}%</strong></span>
                </span>
                <span class="text-[11px] font-bold text-slate-600 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Rusak: <strong class="text-rose-600">{{ $brokenPct }}%</strong></span>
                </span>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 3. Filter Bar: Tanggal, Kandang, & Pencarian -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('production.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Dari Tanggal -->
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-amber-600"></i>
                        <span>Dari Tanggal</span>
                    </label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition-all shadow-2xs">
                </div>

                <!-- Sampai Tanggal -->
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="calendar-check-2" class="w-3.5 h-3.5 text-amber-600"></i>
                        <span>Sampai Tanggal</span>
                    </label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition-all shadow-2xs">
                </div>

                <!-- Filter Kandang -->
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="home" class="w-3.5 h-3.5 text-amber-600"></i>
                        <span>Filter Kandang</span>
                    </label>
                    <select name="coop_id"
                        class="w-full px-3.5 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition-all shadow-2xs">
                        <option value="all">🔍 Semua Kandang (Seluruh Blok)</option>
                        @foreach($coops as $c)
                            <option value="{{ $c->id }}" {{ $coopId == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pencarian Cepat -->
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-amber-600"></i>
                        <span>Pencarian Cepat</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="q" value="{{ $search }}" placeholder="Cari kandang, @petugas..."
                            class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition-all shadow-2xs">
                    </div>
                </div>
            </div>

            <!-- Action Buttons: Reset & Filter -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <div class="text-xs text-slate-400 font-medium hidden sm:block">
                    Menampilkan data hasil input panen telur terverifikasi sistem
                </div>
                <div class="flex items-center gap-2 ml-auto">
                    @if(!empty($startDate) || !empty($endDate) || (!empty($coopId) && $coopId !== 'all') || !empty($search))
                        <a href="{{ route('production.index') }}" 
                           class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                    <button type="submit" 
                            class="px-5 py-2.5 bg-slate-900 hover:bg-black active:scale-95 text-white rounded-xl text-xs font-extrabold transition-all shadow-sm flex items-center gap-2">
                        <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span>Terapkan Filter</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. Tampilan Desktop: Tabel Lengkap Hasil Input Telur (md: ke atas) -->
    <!-- ========================================================================= -->
    <div class="hidden md:block bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Table Top Header -->
        <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 border border-amber-200/80 flex items-center justify-center">
                    <i data-lucide="table-properties" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-800 tracking-tight">Daftar Hasil Input Panen Telur</h2>
                    <p class="text-[11px] text-slate-400 font-medium">Catatan real-time pengumpulan telur per kandang & kloter</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-50 text-slate-600 border border-slate-200/70 text-[11px] font-bold">
                    <i data-lucide="scale" class="w-3.5 h-3.5 text-amber-600"></i>
                    <span>Standar 1 Peti = 10 Kg</span>
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/90 border-b border-slate-100 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Tanggal & Waktu</th>
                        <th class="py-3.5 px-4">Kandang & Kloter</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Butir</th>
                        <th class="py-3.5 px-4 text-center">Peti & Kg</th>
                        <th class="py-3.5 px-4">Petugas Penginput</th>
                        <th class="py-3.5 px-4 text-center w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($productions as $idx => $prod)
                    @php
                        $crates = (float) $prod->crates_count;
                        $wholePeti = (int) floor($crates);
                        $remKg = round(($crates - $wholePeti) * 10, 1);
                        $username = $prod->user ? ($prod->user->username ?: strtolower(str_replace(' ', '', $prod->user->name))) : 'petugas';
                        $fullname = $prod->user ? $prod->user->name : 'Petugas Kandang';
                    @endphp
                    <tr class="hover:bg-amber-50/40 transition-colors group">
                        <td class="py-3.5 px-4 text-center text-slate-400 font-bold font-mono text-xs">
                            {{ $productions->firstItem() + $idx }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="space-y-0.5">
                                <span class="font-bold text-slate-900 block text-xs sm:text-sm">{{ \Carbon\Carbon::parse($prod->date)->translatedFormat('d M Y') }}</span>
                                <span class="inline-flex items-center gap-1 text-[10.5px] font-mono text-slate-500 bg-slate-100/80 px-2 py-0.5 rounded-md border border-slate-200/50">
                                    <i data-lucide="clock" class="w-3 h-3 text-slate-400"></i>
                                    <span>{{ $prod->time ? \Carbon\Carbon::parse($prod->time)->format('H:i') . ' WIB' : ($prod->created_at ? $prod->created_at->format('H:i') . ' WIB' : '-') }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="space-y-1">
                                <span class="font-extrabold text-slate-900 block text-xs sm:text-sm flex items-center gap-1.5">
                                    <i data-lucide="home" class="w-3.5 h-3.5 text-amber-600 shrink-0"></i>
                                    <span>{{ $prod->coop ? $prod->coop->name : 'Kandang Umum' }}</span>
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-50 text-amber-900 border border-amber-200/80">
                                    <span>{{ $prod->flock ? $prod->flock->name : ($prod->coop && $prod->coop->flock ? $prod->coop->flock->name : 'Kloter') }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="text-center space-y-1">
                                <span class="text-base font-black text-slate-900 font-mono tracking-tight block">
                                    {{ number_format($prod->total_eggs, 0, ',', '.') }} <span class="text-xs font-bold text-slate-400">Btr</span>
                                </span>
                                <div class="inline-flex items-center gap-1.5 text-[10.5px]">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-bold" title="Telur Utuh / Baik">
                                        <i data-lucide="check" class="w-3 h-3 text-emerald-600"></i>
                                        <span>{{ number_format($prod->good_eggs, 0, ',', '.') }}</span>
                                    </span>
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md bg-rose-50 text-rose-800 border border-rose-200/80 font-bold" title="Telur Retak / Pecah">
                                        <i data-lucide="x" class="w-3 h-3 text-rose-600"></i>
                                        <span>{{ number_format($prod->broken_eggs, 0, ',', '.') }}</span>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="text-center space-y-1">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-black bg-gradient-to-r from-amber-50 to-orange-50 text-amber-950 border border-amber-200/90 shadow-2xs font-mono">
                                    <i data-lucide="package" class="w-3 h-3 text-amber-600"></i>
                                    <span>{{ $wholePeti }} Peti + {{ number_format($remKg, 1, ',', '.') }} Kg</span>
                                </span>
                                <span class="text-[11px] text-slate-400 font-semibold block">
                                    Bobot: <strong class="text-slate-600">{{ number_format($prod->weight_kg, 1, ',', '.') }} Kg</strong>
                                </span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-amber-100 to-orange-50 text-amber-900 border border-amber-200 font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                    {{ strtoupper(substr($fullname, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-800 font-mono block">{{ '@' . $username }}</span>
                                    <span class="text-[10.5px] text-slate-400 font-medium block">{{ $fullname }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <button type="button" onclick="openProductionDetailModal({{ $prod->id }})"
                                class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-800 border border-slate-200/70 hover:border-amber-300 transition-all flex items-center justify-center shadow-2xs group-hover:scale-105" title="Lihat Detail Input">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2.5">
                                <i data-lucide="inbox" class="w-6 h-6"></i>
                            </div>
                            <p class="font-bold text-slate-600 text-sm">Belum ada data penginputan telur ditemukan</p>
                            <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter rentang tanggal atau kandang Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer Summary -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-slate-500 font-medium">
            <div>
                Menampilkan <strong>{{ count($productions) }}</strong> dari <strong>{{ $productions->total() }}</strong> catatan input produksi telur
            </div>
            <div class="flex items-center gap-4 text-xs font-bold">
                <span class="text-amber-800">Total Telur: {{ number_format($totalEggs, 0, ',', '.') }} Butir</span>
                <span class="text-slate-300">|</span>
                <span class="text-emerald-700">Total: {{ $intPeti }} Peti + {{ number_format($sisaKg, 1, ',', '.') }} Kg</span>
            </div>
        </div>

        @if($productions->hasPages())
            <div class="p-4 bg-slate-50 border-t border-slate-100">
                {{ $productions->links() }}
            </div>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- 5. Tampilan Mobile: Modern Card Layout Sesuai Permintaan (< md) -->
    <!-- Klik card: Detail View | Menampilkan username penginput & konversi peti+kg -->
    <!-- ========================================================================= -->
    <div class="block md:hidden space-y-3">
        @forelse($productions as $idx => $prod)
        @php
            $crates = (float) $prod->crates_count;
            $wholePeti = (int) floor($crates);
            $remKg = round(($crates - $wholePeti) * 10, 1);
            $username = $prod->user ? ($prod->user->username ?: strtolower(str_replace(' ', '', $prod->user->name))) : 'petugas';
            $fullname = $prod->user ? $prod->user->name : 'Petugas Kandang';
        @endphp
        <div onclick="openProductionDetailModal({{ $prod->id }})"
             class="bg-white rounded-3xl p-4 border border-slate-200/80 shadow-xs hover:border-amber-300 hover:shadow-sm active:bg-slate-50 transition-all cursor-pointer relative group overflow-hidden">
            <!-- Left accent indicator -->
            <div class="absolute top-0 left-0 bottom-0 w-1.5 bg-gradient-to-b from-amber-400 to-orange-500 rounded-l-3xl"></div>

            <div class="pl-2 space-y-3">
                <!-- Card Top: Tanggal, Waktu & Kandang -->
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <span class="font-extrabold text-slate-900 text-xs sm:text-sm block">
                            {{ \Carbon\Carbon::parse($prod->date)->translatedFormat('d M Y') }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-[10px] font-mono text-slate-400 mt-0.5">
                            <i data-lucide="clock" class="w-3 h-3 text-slate-300"></i>
                            <span>{{ $prod->time ? \Carbon\Carbon::parse($prod->time)->format('H:i') . ' WIB' : ($prod->created_at ? $prod->created_at->format('H:i') . ' WIB' : '-') }}</span>
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs">
                            {{ $prod->coop ? $prod->coop->name : 'Kandang' }}
                        </span>
                        <div class="w-7 h-7 rounded-xl flex items-center justify-center text-slate-400 bg-slate-50 border border-slate-200/60 group-hover:text-amber-700 group-hover:bg-amber-50 transition-colors">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>

                <!-- Card Middle: Hasil Panen & Konversi -->
                <div class="grid grid-cols-2 gap-2 bg-gradient-to-br from-slate-50 to-amber-50/30 p-3 rounded-2xl border border-slate-100">
                    <div>
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Hasil Telur</span>
                        <span class="text-base font-black text-slate-900 block mt-0.5 font-mono">
                            {{ number_format($prod->total_eggs, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">Btr</span>
                        </span>
                        <div class="flex items-center gap-1.5 text-[10px] mt-1">
                            <span class="text-emerald-700 font-extrabold flex items-center gap-0.5">
                                <i data-lucide="check" class="w-2.5 h-2.5"></i> {{ $prod->good_eggs }}
                            </span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-rose-600 font-extrabold flex items-center gap-0.5">
                                <i data-lucide="x" class="w-2.5 h-2.5"></i> {{ $prod->broken_eggs }}
                            </span>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Peti & Kg</span>
                        <span class="text-xs sm:text-sm font-black text-amber-900 block mt-0.5 font-mono">
                            {{ $wholePeti }} Peti + {{ number_format($remKg, 1, ',', '.') }} Kg
                        </span>
                        <span class="text-[10px] text-slate-400 font-semibold block mt-1">
                            Bobot: {{ number_format($prod->weight_kg, 1, ',', '.') }} Kg
                        </span>
                    </div>
                </div>

                <!-- Card Footer: Petugas & Waktu Simpan -->
                <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-xs">
                    <div class="flex items-center gap-1.5">
                        <div class="w-5 h-5 rounded-full bg-amber-100 text-amber-900 font-black text-[10px] flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($fullname, 0, 1)) }}
                        </div>
                        <span class="text-[11px] font-bold text-slate-700 font-mono">{{ '@' . $username }}</span>
                    </div>

                    <span class="text-[10.5px] text-slate-400 font-medium">
                        {{ $prod->created_at ? $prod->created_at->diffForHumans() : '-' }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-xs text-center text-slate-400">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2.5">
                <i data-lucide="inbox" class="w-6 h-6"></i>
            </div>
            <p class="font-bold text-slate-600 text-sm">Belum ada data penginputan telur ditemukan</p>
            <p class="text-xs text-slate-400 mt-1">Silakan lakukan pencatatan melalui modul input kandang.</p>
        </div>
        @endforelse

        <!-- Mobile Pagination (10 per halaman) -->
        @if($productions->hasPages())
            <div class="p-4 bg-white rounded-3xl border border-slate-200/80 shadow-xs">
                {{ $productions->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ========================================================================= -->
<!-- Modal Detail Hasil Penginputan Telur (Slide-Up Modal di Mobile) -->
<!-- ========================================================================= -->
<div id="prodDetailModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex flex-col justify-end sm:justify-center sm:items-center p-0 sm:p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden transition-transform duration-300 translate-y-12 sm:translate-y-0">
        
        <!-- Header -->
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200/80 flex items-center justify-center shadow-2xs">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-black text-sm sm:text-base text-slate-900 tracking-tight">Detail Hasil Panen Telur</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Informasi rincian input kandang & verifikasi</p>
                </div>
            </div>
            <button type="button" onclick="closeProductionDetailModal()" class="w-8 h-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Content Body (Dynamically populated) -->
        <div id="prodDetailContent" class="p-5 overflow-y-auto max-h-[70vh] space-y-4 text-xs sm:text-sm">
            <div class="text-center py-8 text-slate-400">
                <span class="animate-spin inline-block mr-2">&#9696;</span> Memuat detail produksi...
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end">
            <button type="button" onclick="closeProductionDetailModal()"
                class="px-5 py-2.5 bg-slate-900 hover:bg-black text-white rounded-xl font-bold text-xs transition-colors shadow-2xs">
                Tutup
            </button>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    function openProductionDetailModal(id) {
        const modal = document.getElementById('prodDetailModal');
        const content = document.getElementById('prodDetailContent');

        content.innerHTML = `<div class="text-center py-8 text-slate-400"><span class="animate-spin inline-block mr-2">&#9696;</span> Memuat detail input telur...</div>`;

        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.remove('translate-y-12');

        fetch(`/produksi/${id}`)
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    content.innerHTML = `<p class="text-center text-rose-600 py-6 font-bold">${res.message || 'Gagal memuat detail'}</p>`;
                    return;
                }

                const d = res.data;
                content.innerHTML = `
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50/50 border border-amber-200/80 rounded-3xl p-5 text-center relative overflow-hidden">
                        <span class="text-[10.5px] font-extrabold text-amber-800 uppercase tracking-wider block">Total Panen Telur</span>
                        <span class="text-3xl sm:text-4xl font-black text-amber-950 font-mono block mt-1">${d.total_eggs.toLocaleString('id-ID')} <span class="text-sm font-bold text-slate-400">Butir</span></span>
                        <div class="mt-2 inline-flex items-center gap-2 bg-white px-3.5 py-1.5 rounded-2xl border border-amber-200 shadow-2xs">
                            <i data-lucide="package" class="w-3.5 h-3.5 text-amber-600"></i>
                            <span class="font-black text-amber-950 text-xs font-mono">${d.converted_str}</span>
                            <span class="text-slate-300">|</span>
                            <span class="text-slate-500 font-bold text-xs">${d.weight_kg} Kg</span>
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 space-y-2.5 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-slate-200/60 items-center">
                            <span class="text-slate-500 font-medium">Tanggal & Waktu</span>
                            <span class="font-bold text-slate-900">${d.display_date} &bull; ${d.time ? d.time + ' WIB' : '-'}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-200/60 items-center">
                            <span class="text-slate-500 font-medium">Kandang & Kloter</span>
                            <span class="font-extrabold text-slate-900 text-right">${d.coop_name} &bull; ${d.flock_name}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-200/60 items-center">
                            <span class="text-slate-500 font-medium">Petugas Penginput</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-50 text-amber-900 border border-amber-200/80 font-medium shadow-2xs">
                                <i data-lucide="user-check" class="w-3.5 h-3.5 text-amber-600"></i>
                                <strong class="font-mono">@${d.penginput_username}</strong>
                                <span class="text-slate-400 text-[10.5px]">(${d.penginput_name})</span>
                            </span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-200/60 items-center">
                            <span class="text-slate-500 font-medium">Waktu Simpan</span>
                            <span class="font-semibold text-slate-700">${d.created_at_time || '-'} (${d.created_at_diff || '-'})</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-200/60 items-center">
                            <span class="text-slate-500 font-medium">Telur Baik / Utuh</span>
                            <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200/70">${d.good_eggs.toLocaleString('id-ID')} Butir</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-200/60 items-center">
                            <span class="text-slate-500 font-medium">Telur Rusak / Pecah</span>
                            <span class="font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-lg border border-rose-200/70">${d.broken_eggs.toLocaleString('id-ID')} Butir</span>
                        </div>
                        <div class="flex justify-between py-1.5 items-center">
                            <span class="text-slate-500 font-medium">Catatan Petugas</span>
                            <span class="font-medium text-slate-700 text-right">${d.notes || '-'}</span>
                        </div>
                    </div>
                `;

                lucide.createIcons();
            })
            .catch(err => {
                console.error(err);
                content.innerHTML = `<p class="text-center text-rose-600 py-6 font-bold">Gagal memuat detail data produksi.</p>`;
            });
    }

    function closeProductionDetailModal() {
        const modal = document.getElementById('prodDetailModal');
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.add('translate-y-12');
    }
</script>
@endpush
