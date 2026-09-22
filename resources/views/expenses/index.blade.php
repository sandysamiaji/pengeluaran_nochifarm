@extends('layouts.app')

@section('title', 'Daftar Pengeluaran Kandang - NOCHI FARM')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">
                <i data-lucide="wallet" class="w-4 h-4 text-nochi-orange"></i>
                <span>Operasional Kandang</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Daftar Pengeluaran Kandang</h1>
            <p class="text-xs text-slate-400 mt-0.5">Seluruh catatan biaya operasional yang diinput di Nochi Farm Pengeluaran.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('expenses.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-nochi-orange hover:bg-nochi-orangeDark text-white rounded-xl text-xs sm:text-sm font-bold shadow-md transition-all active:scale-95">
                <i data-lucide="plus" class="w-4 h-4 stroke-[3]"></i>
                <span>+ Catat Pengeluaran Baru</span>
            </a>
        </div>
    </div>

    <!-- Summary Box & Filter Bar -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <div class="bg-gradient-to-br from-rose-900 via-rose-800 to-rose-950 text-white rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <span class="text-xs font-bold text-rose-200 uppercase tracking-wider">Total Biaya Pengeluaran</span>
            <div class="my-2">
                <span class="text-2xl font-black block">Rp {{ number_format($totalNominal, 0, ',', '.') }}</span>
            </div>
            <span class="text-[11px] text-rose-200/80">Total dari {{ $expenses->total() }} transaksi tercatat</span>
        </div>

        <!-- Filter Card -->
        <div class="md:col-span-2 bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between">
            <form method="GET" action="{{ route('expenses.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Kategori</label>
                        <select name="category" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-maroon-800">
                            <option value="all">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat['name'] }}" {{ $category === $cat['name'] ? 'selected' : '' }}>
                                    {{ $cat['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-maroon-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-maroon-800">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="q" value="{{ $search }}" placeholder="Cari keperluan, kode nota, atau keterangan..."
                            class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-maroon-800">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all">
                        Filter
                    </button>
                    <a href="{{ route('expenses.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                        Reset
                    </a>
                </div>
            </form>
        </div>

    </div>

    <!-- Expense List Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Tanggal & Kode</th>
                        <th class="py-3 px-4">Kategori & Subkategori</th>
                        <th class="py-3 px-4">Keperluan & Keterangan</th>
                        <th class="py-3 px-4 text-right">Nominal</th>
                        <th class="py-3 px-4 text-center">Nota</th>
                        <th class="py-3 px-4 text-center w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($expenses as $idx => $exp)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-3.5 px-4 text-center text-slate-400 font-bold">
                            {{ $expenses->firstItem() + $idx }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="font-bold text-slate-800 block">{{ \Carbon\Carbon::parse($exp->date)->translatedFormat('d M Y') }}</span>
                            <span class="text-[11px] text-slate-400 font-mono">#{{ $exp->transaction_code }}</span>
                            <div class="mt-1">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-800 bg-amber-50 border border-amber-200/60 px-1.5 py-0.5 rounded" title="Penginput: {{ $exp->user->name ?? 'Admin' }}">
                                    <i data-lucide="user" class="w-3 h-3 text-amber-600"></i>
                                    @<span>{{ $exp->user ? ($exp->user->username ?: $exp->user->name) : 'admin' }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-block px-2.5 py-0.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                {{ $exp->category }}
                            </span>
                            <span class="text-[11px] text-slate-500 block mt-0.5 font-medium">{{ $exp->subcategory }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-extrabold text-slate-800 block">{{ $exp->purpose }}</span>
                            @if($exp->notes)
                                <span class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">{{ $exp->notes }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right whitespace-nowrap font-black text-rose-600 text-sm sm:text-base">
                            - {{ $exp->formatted_amount }}
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            @if($exp->receipt_photo)
                                <a href="{{ asset($exp->receipt_photo) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-2 py-1 rounded-lg">
                                    <i data-lucide="image" class="w-3.5 h-3.5"></i>
                                    <span>Lihat</span>
                                </a>
                            @else
                                <span class="text-slate-300 text-xs">-</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Hapus pengeluaran #{{ $exp->transaction_code }}?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors" title="Hapus Pengeluaran">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-slate-400">
                            <p class="font-bold text-slate-600 text-sm">Tidak ada data pengeluaran ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
