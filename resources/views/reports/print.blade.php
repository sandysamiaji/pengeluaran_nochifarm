<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Laporan Keuangan Resmi - NOCHI FARM</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-nochi.png') }}">
    
    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #1e293b;
            -webkit-tap-highlight-color: transparent;
        }

        /* Custom Scrollbar for mobile table overflow */
        .table-scroll::-webkit-scrollbar {
            height: 5px;
        }
        .table-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                color: #0f172a !important;
            }
            .printable-canvas {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                border-radius: 0 !important;
            }
            .page-break {
                page-break-before: always;
            }
            table {
                page-break-inside: auto;
                width: 100% !important;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-row-group !important;
            }
            .print-grid-3 {
                display: grid !important;
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                gap: 1rem !important;
            }
            .print-signature-grid {
                display: grid !important;
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                gap: 1.5rem !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 p-2 sm:p-6 lg:p-8 min-h-screen text-slate-800">

    <!-- ========================================================================= -->
    <!-- 1. Top Action Bar & Filter Bar (Screen Only - No Print) -->
    <!-- ========================================================================= -->
    <div class="max-w-4xl mx-auto space-y-3 mb-4 sm:mb-6 no-print">
        
        <!-- Action Header -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white p-3.5 sm:p-4 rounded-2xl shadow-xs border border-slate-200">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center sm:justify-start gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 px-3 py-2 rounded-xl hover:bg-slate-50 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Dashboard</span>
            </a>

            <div class="flex items-center gap-2">
                <button type="button" onclick="toggleFilterDrawer()"
                    class="flex-1 sm:flex-none px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-slate-500"></i>
                    <span>{{ $isFilterActive ? 'Ubah Periode Filter' : 'Filter Periode' }}</span>
                    @if($isFilterActive)
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                    @endif
                </button>

                <button onclick="window.print()"
                    class="flex-1 sm:flex-none px-5 py-2.5 bg-gradient-to-r from-rose-900 to-rose-800 hover:from-rose-950 hover:to-rose-900 text-white rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 active:scale-95">
                    <i data-lucide="printer" class="w-4 h-4 stroke-[2.5]"></i>
                    <span>Cetak / PDF</span>
                </button>
            </div>
        </div>

        <!-- Filter Panel Box (Bisa Ditoggle atau Langsung Tampak) -->
        <div id="filterPanelBox" class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-slate-200 space-y-4 transition-all">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-orange-50 text-nochi-orange flex items-center justify-center">
                        <i data-lucide="filter" class="w-4 h-4 text-orange-600"></i>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-extrabold text-slate-800">Filter Periode Laporan Keuangan</h4>
                        <p class="text-[11px] text-slate-400">Pilih rentang tanggal transaksi yang ingin dicetak ke lembar laporan resmi</p>
                    </div>
                </div>

                @if($isFilterActive)
                    <a href="{{ route('report.print') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 hover:text-rose-800 bg-rose-50 px-2.5 py-1 rounded-lg self-start sm:self-auto transition-colors">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        <span>Reset ke Semua Riwayat</span>
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('report.print') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Dari Tanggal (Mulai)</label>
                        <div class="relative">
                            <input type="date" name="start_date" id="inputStartDate" value="{{ $startDate }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-800">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal (Selesai)</label>
                        <div class="relative">
                            <input type="date" name="end_date" id="inputEndDate" value="{{ $endDate }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-800">
                        </div>
                    </div>
                </div>

                <!-- Preset Quick Buttons (1-Klik) -->
                <div class="flex items-center gap-1.5 flex-wrap pt-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mr-1">Preset Cepat:</span>
                    <button type="button" onclick="setPresetDate('today')"
                        class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-semibold transition-colors">
                        Hari Ini
                    </button>
                    <button type="button" onclick="setPresetDate('last7')"
                        class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-semibold transition-colors">
                        7 Hari Terakhir
                    </button>
                    <button type="button" onclick="setPresetDate('thisMonth')"
                        class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-semibold transition-colors">
                        Bulan Ini
                    </button>
                    <button type="button" onclick="setPresetDate('lastMonth')"
                        class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-semibold transition-colors">
                        Bulan Lalu
                    </button>
                    <button type="button" onclick="setPresetDate('all')"
                        class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-semibold transition-colors">
                        Semua Riwayat
                    </button>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="submit"
                        class="w-full sm:w-auto px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Terapkan Filter Laporan</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 2. Printable Paper Sheet (Kanvas A4 - Responsive on Mobile) -->
    <!-- ========================================================================= -->
    <div class="printable-canvas max-w-4xl mx-auto bg-white p-4 sm:p-8 lg:p-12 rounded-2xl sm:rounded-3xl shadow-xs sm:shadow-md border border-slate-200/80">
        
        <!-- Official Letterhead (Kop Surat Nochi Farm) -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b-2 border-slate-900 pb-4 sm:pb-5 mb-5 sm:mb-6 gap-3 sm:gap-4">
            <div class="flex items-center gap-3 sm:gap-4">
                <img src="{{ asset('images/logo-nochi.png') }}" alt="Logo Nochi Farm" class="w-12 h-12 sm:w-16 sm:h-16 object-contain shrink-0">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 uppercase">NOCHI FARM</h1>
                    <p class="text-[10px] sm:text-xs font-bold text-rose-800 tracking-wider uppercase">PETERNAK AYAM PETELUR & DISTRIBUSI TELUR SEGAR</p>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 leading-snug">Jl. Raya Peternakan Nochi Farm &bull; Kebumen, Jawa Tengah &bull; Telp: (0287) 662-890</p>
                </div>
            </div>
            <div class="text-left sm:text-right w-full sm:w-auto flex sm:flex-col items-center sm:items-end justify-between sm:justify-center border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-100">
                <span class="inline-block px-2.5 py-1 bg-slate-100 text-slate-800 rounded-lg text-[10px] sm:text-xs font-black uppercase tracking-wider">
                    Laporan Keuangan
                </span>
                <p class="text-[9px] sm:text-[10px] text-slate-400 sm:mt-1">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        <!-- Report Meta Info (Periode & Klasifikasi) -->
        <div class="mb-5 sm:mb-6 bg-slate-50 p-3.5 sm:p-4 rounded-xl border border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 text-xs">
            <div class="space-y-0.5">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-slate-500 font-medium">Periode Laporan:</span>
                    <strong class="text-slate-900 font-extrabold">
                        @if($isFilterActive)
                            @if(!empty($startDate) && !empty($endDate))
                                {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                            @elseif(!empty($startDate))
                                Mulai {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d Sekarang
                            @elseif(!empty($endDate))
                                S/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                            @endif
                        @else
                            Semua Riwayat s/d {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}
                        @endif
                    </strong>
                    @if($isFilterActive)
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-orange-100 text-orange-800 no-print">Terfilter</span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-400">Total data tercakup: {{ $sales->count() + $expenses->count() }} transaksi ({{ $sales->count() }} penjualan, {{ $expenses->count() }} pengeluaran)</p>
            </div>
            <div class="pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200/60">
                <span class="text-slate-500 font-medium">Klasifikasi:</span>
                <strong class="text-emerald-700 ml-1 font-extrabold block sm:inline">Laporan Arus Kas & Laba Rugi Operasional</strong>
            </div>
        </div>

        <!-- Executive Financial Highlights Cards -->
        <div class="print-grid-3 grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6 text-center">
            
            <!-- 1. Total Pemasukan -->
            <div class="p-3.5 sm:p-4 bg-emerald-50/90 rounded-xl border border-emerald-200 flex flex-col justify-between">
                <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Total Pemasukan (Omzet)</span>
                <span class="text-base sm:text-lg lg:text-xl font-black text-emerald-700 block my-1">+ Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
                <span class="text-[10px] text-emerald-600 font-semibold block">{{ $sales->count() }} Transaksi Penjualan</span>
            </div>

            <!-- 2. Total Pengeluaran -->
            <div class="p-3.5 sm:p-4 bg-rose-50/90 rounded-xl border border-rose-200 flex flex-col justify-between">
                <span class="text-[10px] font-bold text-rose-800 uppercase tracking-wider block">Total Pengeluaran (Biaya)</span>
                <span class="text-base sm:text-lg lg:text-xl font-black text-rose-700 block my-1">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
                <span class="text-[10px] text-rose-600 font-semibold block">{{ $expenses->count() }} Biaya Kandang</span>
            </div>

            <!-- 3. Laba Bersih -->
            <div class="p-3.5 sm:p-4 bg-slate-900 text-white rounded-xl shadow-xs flex flex-col justify-between">
                <span class="text-[10px] font-bold text-rose-200 uppercase tracking-wider block">Laba Bersih (Saldo Kas)</span>
                <span class="text-base sm:text-lg lg:text-xl font-black block my-1 {{ $saldoKas >= 0 ? 'text-white' : 'text-rose-400' }}">
                    Rp {{ number_format($saldoKas, 0, ',', '.') }}
                </span>
                <span class="text-[10px] {{ $saldoKas >= 0 ? 'text-emerald-400' : 'text-rose-400' }} font-bold block">
                    {{ $saldoKas >= 0 ? 'Surplus / Profit' : 'Defisit' }} ({{ $totalPemasukan > 0 ? round(($saldoKas / $totalPemasukan) * 100, 1) : 0 }}% Margin)
                </span>
            </div>
        </div>

        <!-- ===================================================================== -->
        <!-- TABLE 1: Breakdown Pos Biaya Pengeluaran Kandang -->
        <!-- ===================================================================== -->
        <div class="mb-6 sm:mb-8">
            <h3 class="text-xs font-black uppercase text-slate-800 tracking-wider mb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-800"></span>
                <span>Rekapitulasi Biaya Pengeluaran per Kategori</span>
            </h3>
            
            <div class="table-scroll border border-slate-200 rounded-xl overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse min-w-[500px]">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3 w-10 text-center">No</th>
                            <th class="py-2.5 px-3">Kategori Beban</th>
                            <th class="py-2.5 px-3 text-center">Frekuensi</th>
                            <th class="py-2.5 px-3 text-right">Total Nominal (Rp)</th>
                            <th class="py-2.5 px-3 text-right w-24">Porsi (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($categoryBreakdown as $cb)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2 px-3 text-center text-slate-400 font-bold">{{ $loop->iteration }}</td>
                            <td class="py-2 px-3 font-semibold text-slate-800">{{ $cb['category'] }}</td>
                            <td class="py-2 px-3 text-center text-slate-500">{{ $cb['count'] }} kali</td>
                            <td class="py-2 px-3 text-right font-bold text-rose-700">Rp {{ number_format($cb['total'], 0, ',', '.') }}</td>
                            <td class="py-2 px-3 text-right font-semibold text-slate-600">{{ $cb['percentage'] }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-slate-400">Belum ada data pengeluaran pada periode ini.</td>
                        </tr>
                        @endforelse

                        @if($categoryBreakdown->count() > 0)
                        <tr class="bg-slate-50 font-bold border-t-2 border-slate-200 text-slate-800">
                            <td colspan="3" class="py-2.5 px-3 text-right uppercase text-[11px] font-extrabold">TOTAL BIAYA PENGELUARAN</td>
                            <td class="py-2.5 px-3 text-right text-rose-700 font-black">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-right">100%</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===================================================================== -->
        <!-- TABLE 2: Rincian Seluruh Mutasi Transaksi (Tanpa Batasan 30) -->
        <!-- ===================================================================== -->
        <div class="mb-8">
            @php
                $allCombined = collect();
                foreach($sales as $s) {
                    $allCombined->push(['date' => $s->date, 'code' => $s->invoice_no ?: ('INV-'.$s->id), 'desc' => 'Penjualan' . ($s->customer_name ? ' - '.$s->customer_name : ''), 'in' => $s->total_amount, 'out' => 0]);
                }
                foreach($expenses as $e) {
                    $allCombined->push(['date' => $e->date, 'code' => $e->transaction_code, 'desc' => $e->purpose . ' (' . $e->category . ')', 'in' => 0, 'out' => $e->amount]);
                }
                $allCombined = $allCombined->sortBy('date');
            @endphp

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2">
                <h3 class="text-xs font-black uppercase text-slate-800 tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-800"></span>
                    <span>Rincian Buku Kas & Seluruh Mutasi Transaksi</span>
                </h3>
                <span class="text-[11px] font-bold text-slate-500">
                    Total: {{ $allCombined->count() }} Transaksi Lengkap
                </span>
            </div>

            <div class="table-scroll border border-slate-200 rounded-xl overflow-x-auto">
                <table class="w-full text-left text-[11px] border-collapse min-w-[580px]">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-2 px-2 text-center w-8">No</th>
                            <th class="py-2 px-2 whitespace-nowrap">Tanggal</th>
                            <th class="py-2 px-2 whitespace-nowrap">Kode / Ref</th>
                            <th class="py-2 px-2">Keterangan / Keperluan</th>
                            <th class="py-2 px-2 text-right whitespace-nowrap">Pemasukan (Rp)</th>
                            <th class="py-2 px-2 text-right whitespace-nowrap">Pengeluaran (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($allCombined as $idx => $row)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-1.5 px-2 text-center text-slate-400">{{ $idx + 1 }}</td>
                            <td class="py-1.5 px-2 whitespace-nowrap text-slate-700">{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                            <td class="py-1.5 px-2 font-mono text-[10px] text-slate-500 whitespace-nowrap">{{ $row['code'] }}</td>
                            <td class="py-1.5 px-2 font-medium text-slate-800">{{ $row['desc'] }}</td>
                            <td class="py-1.5 px-2 text-right font-semibold text-emerald-700 whitespace-nowrap">
                                {{ $row['in'] > 0 ? number_format($row['in'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-1.5 px-2 text-right font-semibold text-rose-700 whitespace-nowrap">
                                {{ $row['out'] > 0 ? number_format($row['out'], 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">Tidak ada data transaksi yang tercatat pada rentang periode ini.</td>
                        </tr>
                        @endforelse

                        @if($allCombined->count() > 0)
                        <!-- Baris Total Mutasi di Bagian Body Paling Bawah (Hanya Muncul Sekali di Akhir Data) -->
                        <tr class="bg-slate-100/90 font-bold border-t-2 border-slate-300 text-slate-800">
                            <td colspan="4" class="py-2.5 px-2 text-right font-extrabold uppercase text-[10px] sm:text-[11px]">
                                TOTAL MUTASI ({{ $allCombined->count() }} TRANSAKSI)
                            </td>
                            <td class="py-2.5 px-2 text-right font-black text-emerald-700 whitespace-nowrap">
                                + Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-2 text-right font-black text-rose-700 whitespace-nowrap">
                                - Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===================================================================== -->
        <!-- 3. Lembar Pengesahan / Tanda Tangan Resmi (Investor & Pengelola) -->
        <!-- ===================================================================== -->
        <div class="mt-8 sm:mt-12 pt-6 border-t border-slate-200">
            <p class="text-[11px] sm:text-xs text-slate-500 text-center mb-6 sm:mb-8">
                Laporan ini dibuat dengan sebenar-benarnya berdasarkan catatan pembukuan kas & keuangan kandang Nochi Farm yang sah.
            </p>
            
            <div class="print-signature-grid grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8 text-center text-xs">
                <div class="space-y-1">
                    <p class="text-slate-500 mb-12 sm:mb-16 leading-relaxed">
                        Dibuat Oleh,<br>
                        <strong class="text-slate-800">Petugas Administrasi</strong>
                    </p>
                    <div class="border-b border-slate-400 w-36 mx-auto mb-1"></div>
                    <p class="text-[11px] text-slate-600 font-bold">( ....................................... )</p>
                </div>
                <div class="space-y-1">
                    <p class="text-slate-500 mb-12 sm:mb-16 leading-relaxed">
                        Diperiksa Oleh,<br>
                        <strong class="text-slate-800">Manager Operasional Farm</strong>
                    </p>
                    <div class="border-b border-slate-400 w-36 mx-auto mb-1"></div>
                    <p class="text-[11px] text-slate-600 font-bold">( ....................................... )</p>
                </div>
                <div class="space-y-1">
                    <p class="text-slate-500 mb-12 sm:mb-16 leading-relaxed">
                        Disetujui Oleh,<br>
                        <strong class="text-slate-800">Owner / Investor Nochi Farm</strong>
                    </p>
                    <div class="border-b border-slate-400 w-36 mx-auto mb-1"></div>
                    <p class="text-[11px] text-slate-600 font-bold">( ....................................... )</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Script Init & Quick Presets -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });

        function toggleFilterDrawer() {
            const panel = document.getElementById('filterPanelBox');
            if (panel) {
                panel.classList.toggle('hidden');
                if (!panel.classList.contains('hidden')) {
                    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }
        }

        function setPresetDate(type) {
            const startInput = document.getElementById('inputStartDate');
            const endInput = document.getElementById('inputEndDate');
            if (!startInput || !endInput) return;

            const now = new Date();
            const formatIso = (d) => {
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            if (type === 'today') {
                const todayStr = formatIso(now);
                startInput.value = todayStr;
                endInput.value = todayStr;
            } else if (type === 'last7') {
                const past7 = new Date();
                past7.setDate(now.getDate() - 6);
                startInput.value = formatIso(past7);
                endInput.value = formatIso(now);
            } else if (type === 'thisMonth') {
                const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
                startInput.value = formatIso(firstDay);
                endInput.value = formatIso(now);
            } else if (type === 'lastMonth') {
                const firstDayLastMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                const lastDayLastMonth = new Date(now.getFullYear(), now.getMonth(), 0);
                startInput.value = formatIso(firstDayLastMonth);
                endInput.value = formatIso(lastDayLastMonth);
            } else if (type === 'all') {
                startInput.value = '';
                endInput.value = '';
            }
        }
    </script>
</body>
</html>
