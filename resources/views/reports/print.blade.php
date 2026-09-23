<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Resmi - NOCHI FARM</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-nochi.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .page-break {
                page-break-before: always;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8">

    <!-- Top Action Bar (Screen Only) -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900">
            &larr; Kembali ke Dashboard
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2.5 bg-rose-900 hover:bg-rose-950 text-white rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Dokumen / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Printable Paper Sheet (A4 Canvas) -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-md border border-slate-200">
        
        <!-- Official Letterhead (Kop Surat Nochi Farm) -->
        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-5 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo-nochi.png') }}" alt="Logo Nochi Farm" class="w-16 h-16 object-contain">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900 uppercase">NOCHI FARM</h1>
                    <p class="text-xs font-bold text-rose-800 tracking-wider uppercase">PETERNAK AYAM PETELUR & DISTRIBUSI TELUR SEGAR</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Jl. Raya Peternakan Nochi Farm &bull; Kebumen, Jawa Tengah &bull; Telp: (0287) 662-890</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-slate-100 text-slate-800 rounded-lg text-xs font-black uppercase tracking-wider">
                    Laporan Keuangan
                </span>
                <p class="text-[10px] text-slate-400 mt-1">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        <!-- Report Meta Info -->
        <div class="mb-6 bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
            <div>
                <span class="text-slate-500 font-medium">Periode Laporan:</span>
                <strong class="text-slate-900 ml-1">
                    @if($isFilterActive)
                        {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                    @else
                        Semua Riwayat s/d {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}
                    @endif
                </strong>
            </div>
            <div>
                <span class="text-slate-500 font-medium">Klasifikasi:</span>
                <strong class="text-emerald-700 ml-1">Laporan Arus Kas & Laba Rugi Operasional</strong>
            </div>
        </div>

        <!-- Executive Financial Highlights -->
        <div class="grid grid-cols-3 gap-4 mb-6 text-center">
            <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Total Pemasukan (Omzet)</span>
                <span class="text-lg sm:text-xl font-black text-emerald-700 block mt-1">+ Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
                <span class="text-[10px] text-emerald-600 mt-0.5 block">{{ $sales->count() }} Transaksi Nota</span>
            </div>
            <div class="p-4 bg-rose-50 rounded-xl border border-rose-200">
                <span class="text-[10px] font-bold text-rose-800 uppercase tracking-wider block">Total Pengeluaran (Biaya)</span>
                <span class="text-lg sm:text-xl font-black text-rose-700 block mt-1">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
                <span class="text-[10px] text-rose-600 mt-0.5 block">{{ $expenses->count() }} Biaya Tercatat</span>
            </div>
            <div class="p-4 bg-slate-900 text-white rounded-xl shadow-xs">
                <span class="text-[10px] font-bold text-rose-200 uppercase tracking-wider block">Laba Bersih (Saldo Kas)</span>
                <span class="text-lg sm:text-xl font-black block mt-1 {{ $saldoKas >= 0 ? 'text-white' : 'text-rose-400' }}">
                    Rp {{ number_format($saldoKas, 0, ',', '.') }}
                </span>
                <span class="text-[10px] {{ $saldoKas >= 0 ? 'text-emerald-400' : 'text-rose-400' }} mt-0.5 block">
                    {{ $saldoKas >= 0 ? 'Surplus / Profit' : 'Defisit' }} ({{ $totalPemasukan > 0 ? round(($saldoKas / $totalPemasukan) * 100, 1) : 0 }}% Margin)
                </span>
            </div>
        </div>

        <!-- Breakdown Pos Biaya Pengeluaran Kandang -->
        <div class="mb-8">
            <h3 class="text-xs font-black uppercase text-slate-800 tracking-wider mb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-800"></span>
                <span>Rekapitulasi Biaya Pengeluaran per Kategori</span>
            </h3>
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-left text-xs border-collapse">
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
                        <tr>
                            <td class="py-2 px-3 text-center text-slate-400 font-bold">{{ $loop->iteration }}</td>
                            <td class="py-2 px-3 font-semibold text-slate-800">{{ $cb['category'] }}</td>
                            <td class="py-2 px-3 text-center text-slate-500">{{ $cb['count'] }} kali</td>
                            <td class="py-2 px-3 text-right font-bold text-rose-700">Rp {{ number_format($cb['total'], 0, ',', '.') }}</td>
                            <td class="py-2 px-3 text-right font-semibold text-slate-600">{{ $cb['percentage'] }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-3 text-center text-slate-400">Belum ada data pengeluaran pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold border-t border-slate-200 text-slate-800">
                        <tr>
                            <td colspan="3" class="py-2.5 px-3 text-right">TOTAL PENGELUARAN</td>
                            <td class="py-2.5 px-3 text-right text-rose-700 font-black">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-right">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Rincian Seluruh Mutasi Transaksi -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-black uppercase text-slate-800 tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-800"></span>
                    <span>Rincian Buku Kas & Seluruh Mutasi Transaksi</span>
                </h3>
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
                <span class="text-[11px] font-bold text-slate-500">Total: {{ $allCombined->count() }} Transaksi</span>
            </div>
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-left text-[11px] border-collapse">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-2 px-2 text-center w-8">No</th>
                            <th class="py-2 px-2">Tanggal</th>
                            <th class="py-2 px-2">Kode / Ref</th>
                            <th class="py-2 px-2">Keterangan / Keperluan</th>
                            <th class="py-2 px-2 text-right">Pemasukan (Rp)</th>
                            <th class="py-2 px-2 text-right">Pengeluaran (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($allCombined as $idx => $row)
                        <tr>
                            <td class="py-1.5 px-2 text-center text-slate-400">{{ $idx + 1 }}</td>
                            <td class="py-1.5 px-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                            <td class="py-1.5 px-2 font-mono text-[10px] text-slate-500">{{ $row['code'] }}</td>
                            <td class="py-1.5 px-2 font-medium text-slate-800 truncate max-w-xs">{{ $row['desc'] }}</td>
                            <td class="py-1.5 px-2 text-right font-semibold text-emerald-700">
                                {{ $row['in'] > 0 ? number_format($row['in'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-1.5 px-2 text-right font-semibold text-rose-700">
                                {{ $row['out'] > 0 ? number_format($row['out'], 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-slate-400">Tidak ada data transaksi pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold border-t border-slate-200 text-slate-800">
                        <tr>
                            <td colspan="4" class="py-2.5 px-2 text-right font-extrabold uppercase">TOTAL MUTASI ({{ $allCombined->count() }} TRANSAKSI)</td>
                            <td class="py-2.5 px-2 text-right font-black text-emerald-700">+ Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-2 text-right font-black text-rose-700">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Lembar Pengesahan / Tanda Tangan (Investor & Pengelola) -->
        <div class="mt-12 pt-6 border-t border-slate-200">
            <p class="text-xs text-slate-500 text-center mb-8">Laporan ini dibuat dengan sebenar-benarnya berdasarkan catatan pembukuan kandang Nochi Farm yang sah.</p>
            
            <div class="grid grid-cols-3 gap-8 text-center text-xs">
                <div>
                    <p class="text-slate-500 mb-16">Dibuat Oleh,<br><strong class="text-slate-800">Petugas Administrasi</strong></p>
                    <div class="border-b border-slate-400 w-36 mx-auto mb-1"></div>
                    <p class="text-[11px] text-slate-600 font-bold">( ....................................... )</p>
                </div>
                <div>
                    <p class="text-slate-500 mb-16">Diperiksa Oleh,<br><strong class="text-slate-800">Manager Operasional Farm</strong></p>
                    <div class="border-b border-slate-400 w-36 mx-auto mb-1"></div>
                    <p class="text-[11px] text-slate-600 font-bold">( ....................................... )</p>
                </div>
                <div>
                    <p class="text-slate-500 mb-16">Disetujui Oleh,<br><strong class="text-slate-800">Owner / Perwakilan Investor</strong></p>
                    <div class="border-b border-slate-400 w-36 mx-auto mb-1"></div>
                    <p class="text-[11px] text-slate-600 font-bold">( ....................................... )</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
