@extends('layouts.app')

@section('title', 'Data & Monitoring Produksi Telur - NOCHI FARM')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">
                <i data-lucide="egg" class="w-4 h-4 text-amber-600"></i>
                <span>Data Input Kandang Realtime</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Monitoring Produksi Telur</h1>
            <p class="text-xs text-slate-400 mt-0.5">Hasil penginputan telur dari petugas kandang, konversi peti & kg, serta riwayat lengkap penginput.</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 border border-amber-200/80 text-xs font-bold shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Terhubung Realtime</span>
            </span>
        </div>
    </div>

    <!-- 4 Grid Metric Cards (Total Telur, Peti & Kg, Hari Ini, dan Kualitas) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        
        <!-- Card 1: Total Telur Terkumpul (Butir) -->
        <div class="bg-gradient-to-br from-amber-600 via-amber-700 to-amber-800 text-white rounded-2xl p-4.5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-amber-100 uppercase tracking-wider">Total Telur (Butir)</span>
                <div class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center text-white">
                    <i data-lucide="egg" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="my-2.5">
                <span class="text-2xl sm:text-3xl font-black tracking-tight block">
                    {{ number_format($totalEggs, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-amber-200 font-medium">Butir Telur Terkumpul</span>
            </div>
            <div class="pt-2 border-t border-white/20 flex items-center justify-between text-[11px] text-amber-100">
                <span>Baik: <strong>{{ number_format($totalGoodEggs, 0, ',', '.') }}</strong></span>
                <span>Pecah: <strong>{{ number_format($totalBrokenEggs, 0, ',', '.') }}</strong></span>
            </div>
        </div>

        <!-- Card 2: Total Peti & Kg (Konversi Lengkap) -->
        <div class="bg-white rounded-2xl p-4.5 border border-slate-100 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Peti & Kg</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center">
                    <i data-lucide="package" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="my-2.5">
                <span class="text-xl sm:text-2xl font-black text-amber-900 tracking-tight block">
                    {{ $intPeti }} Peti + {{ number_format($sisaKg, 1, ',', '.') }} Kg
                </span>
                <span class="text-[11px] text-slate-400 font-medium">
                    Total: {{ number_format($totalWeightKg, 1, ',', '.') }} Kg ({{ number_format($totalCrates, 2, ',', '.') }} Peti)
                </span>
            </div>
            <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                <span>Standar 1 Peti = 10 Kg</span>
                <span class="font-bold text-amber-800">{{ $productions->total() }} Data</span>
            </div>
        </div>

        <!-- Card 3: Produksi Hari Ini -->
        <div class="bg-white rounded-2xl p-4.5 border border-slate-100 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Produksi Hari Ini</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center">
                    <i data-lucide="calendar-check" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="my-2.5">
                <span class="text-xl sm:text-2xl font-black text-emerald-800 tracking-tight block">
                    {{ number_format($todayEggs, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-500">Butir</span>
                </span>
                <span class="text-[11px] text-emerald-700 font-semibold block mt-0.5">
                    {{ $todayIntPeti }} Peti + {{ number_format($todaySisaKg, 1, ',', '.') }} Kg
                </span>
            </div>
            <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                <span>Baik: <strong class="text-emerald-700">{{ $todayGoodEggs }}</strong></span>
                <span>Pecah: <strong class="text-rose-600">{{ $todayBrokenEggs }}</strong></span>
            </div>
        </div>

        <!-- Card 4: Kualitas & Persentase Telur Baik -->
        <div class="bg-white rounded-2xl p-4.5 border border-slate-100 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kualitas Telur</span>
                <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-700 border border-sky-200 flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="my-2.5">
                @php
                    $goodPct = $totalEggs > 0 ? round(($totalGoodEggs / $totalEggs) * 100, 1) : 100;
                    $brokenPct = $totalEggs > 0 ? round(($totalBrokenEggs / $totalEggs) * 100, 1) : 0;
                @endphp
                <span class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight block">
                    {{ $goodPct }}%
                </span>
                <span class="text-[11px] text-slate-400 font-medium">Persentase Telur Utuh / Baik</span>
            </div>
            <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                <span>Retak/Rusak: <strong class="text-rose-600">{{ $brokenPct }}%</strong></span>
                <span class="text-emerald-600 font-bold">Grade Prima</span>
            </div>
        </div>

    </div>

    <!-- Filter Bar: Tanggal, Kandang, & Pencarian -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-xs">
        <form method="GET" action="{{ route('production.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-maroon-800">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-maroon-800">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Filter Kandang</label>
                    <select name="coop_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-maroon-800">
                        <option value="all">Semua Kandang</option>
                        @foreach($coops as $c)
                            <option value="{{ $c->id }}" {{ $coopId == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Pencarian</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="q" value="{{ $search }}" placeholder="Cari kandang, @petugas..."
                            class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-maroon-800">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('production.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Reset
                </a>
                <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. Tampilan Desktop: Tabel Lengkap Hasil Input Telur (md: ke atas) -->
    <!-- ========================================================================= -->
    <div class="hidden md:block bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
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
                        $username = $prod->user ? ($prod->user->username ?: $prod->user->name) : 'petugas';
                        $fullname = $prod->user ? $prod->user->name : 'Petugas Kandang';
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-3.5 px-4 text-center text-slate-400 font-bold">
                            {{ $productions->firstItem() + $idx }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="font-bold text-slate-800 block">{{ \Carbon\Carbon::parse($prod->date)->translatedFormat('d M Y') }}</span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                {{ $prod->time ? \Carbon\Carbon::parse($prod->time)->format('H:i') . ' WIB' : ($prod->created_at ? $prod->created_at->format('H:i') . ' WIB' : '-') }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-extrabold text-slate-800 block">
                                {{ $prod->coop ? $prod->coop->name : 'Kandang Umum' }}
                            </span>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/70 mt-0.5">
                                {{ $prod->flock ? $prod->flock->name : ($prod->coop && $prod->coop->flock ? $prod->coop->flock->name : 'Kloter') }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <span class="font-black text-slate-800 text-sm block">
                                {{ number_format($prod->total_eggs, 0, ',', '.') }} Butir
                            </span>
                            <div class="flex items-center justify-center gap-1.5 text-[10px] text-slate-400 mt-0.5">
                                <span class="text-emerald-700 font-bold" title="Telur Baik">&#10003; {{ $prod->good_eggs }}</span>
                                <span>&bull;</span>
                                <span class="text-rose-600 font-bold" title="Telur Retak/Pecah">&#10007; {{ $prod->broken_eggs }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <span class="inline-block px-2.5 py-1 rounded-xl text-xs font-black bg-amber-50 text-amber-900 border border-amber-200">
                                {{ $wholePeti }} Peti + {{ number_format($remKg, 1, ',', '.') }} Kg
                            </span>
                            <span class="text-[11px] text-slate-400 block mt-0.5">
                                Bobot: {{ number_format($prod->weight_kg, 1, ',', '.') }} Kg
                            </span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-xs font-medium border border-slate-200">
                                <i data-lucide="user-check" class="w-3.5 h-3.5 text-amber-600"></i>
                                <span class="text-slate-500 font-bold">@<span>{{ $username }}</span></span>
                            </span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $fullname }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <button type="button" onclick="openProductionDetailModal({{ $prod->id }})"
                                class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 transition-colors" title="Lihat Detail Input">
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
    <!-- 2. Tampilan Mobile: Modern Card Layout Sesuai Permintaan (< md) -->
    <!-- Klik card: Detail View | Menampilkan username penginput & konversi peti+kg -->
    <!-- ========================================================================= -->
    <div class="block md:hidden space-y-3">
        @forelse($productions as $idx => $prod)
        @php
            $crates = (float) $prod->crates_count;
            $wholePeti = (int) floor($crates);
            $remKg = round(($crates - $wholePeti) * 10, 1);
            $username = $prod->user ? ($prod->user->username ?: $prod->user->name) : 'petugas';
            $fullname = $prod->user ? $prod->user->name : 'Petugas Kandang';
        @endphp
        <div onclick="openProductionDetailModal({{ $prod->id }})"
             class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs hover:border-slate-300 hover:shadow-sm active:bg-slate-50 transition-all cursor-pointer relative group">
            
            <!-- Header Card: Tanggal & Waktu + Badge Kandang -->
            <div class="flex items-center justify-between gap-2 mb-2">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="font-extrabold text-slate-800 text-xs">
                        {{ \Carbon\Carbon::parse($prod->date)->translatedFormat('d M Y') }}
                    </span>
                    <span class="text-[11px] font-mono text-slate-400">
                        {{ $prod->time ? \Carbon\Carbon::parse($prod->time)->format('H:i') : ($prod->created_at ? $prod->created_at->format('H:i') : '-') }}
                    </span>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        {{ $prod->coop ? $prod->coop->name : 'Kandang' }}
                    </span>
                    <button type="button" onclick="event.stopPropagation(); openProductionDetailModal({{ $prod->id }})"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 active:scale-95 transition-all"
                        title="Lihat Detail">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Body Card: Jumlah Butir & Konversi Peti + Kg -->
            <div class="flex items-center justify-between gap-2 my-2.5 bg-slate-50/70 p-3 rounded-xl border border-slate-100">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Hasil Panen</span>
                    <span class="text-base font-black text-slate-900 block mt-0.5">
                        {{ number_format($prod->total_eggs, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">Butir</span>
                    </span>
                    <div class="flex items-center gap-2 text-[10px] text-slate-500 mt-0.5">
                        <span class="text-emerald-700 font-bold">&#10003; {{ $prod->good_eggs }} Baik</span>
                        <span>&bull;</span>
                        <span class="text-rose-600 font-bold">&#10007; {{ $prod->broken_eggs }} Retak</span>
                    </div>
                </div>

                <div class="text-right">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Konversi Berat</span>
                    <span class="text-xs sm:text-sm font-black text-amber-800 block mt-0.5">
                        {{ $wholePeti }} Peti + {{ number_format($remKg, 1, ',', '.') }} Kg
                    </span>
                    <span class="text-[10px] text-slate-400 block mt-0.5">
                        Bobot: {{ number_format($prod->weight_kg, 1, ',', '.') }} Kg
                    </span>
                </div>
            </div>

            <!-- Footer Card: Username Penginput & Waktu Input -->
            <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-amber-50 text-amber-900 border border-amber-200/70 text-[11px] font-medium" title="Petugas Input: {{ $fullname }}">
                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-amber-600"></i>
                    <span class="text-amber-700 font-bold">Input:</span>
                    <span class="font-black text-amber-950 font-mono">@<span>{{ $username }}</span></span>
                </span>

                <span class="text-[11px] text-slate-400 font-medium">
                    {{ $prod->created_at ? $prod->created_at->diffForHumans() : '-' }}
                </span>
            </div>

        </div>
        @empty
        <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-xs text-center text-slate-400">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2.5">
                <i data-lucide="inbox" class="w-6 h-6"></i>
            </div>
            <p class="font-bold text-slate-600 text-sm">Belum ada data penginputan telur ditemukan</p>
            <p class="text-xs text-slate-400 mt-1">Silakan lakukan pencatatan melalui modul input kandang.</p>
        </div>
        @endforelse

        <!-- Mobile Pagination (10 per halaman) -->
        @if($productions->hasPages())
            <div class="p-4 bg-white rounded-2xl border border-slate-100 shadow-xs">
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
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <button type="button" onclick="closeProductionDetailModal()" class="w-9 h-9 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <h3 class="font-extrabold text-base text-slate-800">Detail Hasil Panen Telur</h3>
            <div class="w-9"></div>
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
                class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold text-xs transition-colors">
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
                    <div class="bg-amber-50/70 border border-amber-200 rounded-2xl p-4 text-center">
                        <span class="text-xs font-bold text-amber-800 uppercase tracking-wider block">Total Panen Telur</span>
                        <span class="text-2xl sm:text-3xl font-black text-amber-900 block mt-1">${d.total_eggs.toLocaleString('id-ID')} Butir</span>
                        <span class="text-xs font-extrabold text-amber-700 mt-1 inline-block bg-white px-3 py-1 rounded-xl border border-amber-200 shadow-2xs">
                            ${d.converted_str} (${d.weight_kg} Kg)
                        </span>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 space-y-2.5 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                            <span class="text-slate-500">Tanggal & Waktu</span>
                            <span class="font-bold text-slate-800">${d.display_date} &bull; ${d.time ? d.time + ' WIB' : '-'}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                            <span class="text-slate-500">Kandang & Kloter</span>
                            <span class="font-bold text-slate-800 text-right">${d.coop_name} &bull; ${d.flock_name}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                            <span class="text-slate-500">Petugas Penginput</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 text-amber-900 border border-amber-200/80 font-medium">
                                <i data-lucide="user-check" class="w-3.5 h-3.5 text-amber-600"></i>
                                <strong class="font-mono">@${d.penginput_username}</strong>
                                <span class="text-slate-400 text-[10px]">(${d.penginput_name})</span>
                            </span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                            <span class="text-slate-500">Waktu Simpan</span>
                            <span class="font-medium text-slate-700">${d.created_at_time || '-'} (${d.created_at_diff || '-'})</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                            <span class="text-slate-500">Telur Baik</span>
                            <span class="font-bold text-emerald-700">${d.good_eggs.toLocaleString('id-ID')} Butir</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                            <span class="text-slate-500">Telur Rusak / Pecah</span>
                            <span class="font-bold text-rose-600">${d.broken_eggs.toLocaleString('id-ID')} Butir</span>
                        </div>
                        <div class="flex justify-between py-1 items-center">
                            <span class="text-slate-500">Catatan Petugas</span>
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
