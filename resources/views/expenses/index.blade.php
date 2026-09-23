@extends('layouts.app')

@section('title', 'Daftar Pengeluaran Kandang - NOCHI FARM')

@section('content')
<div id="expensesTableSection" class="space-y-6">

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

    <!-- ========================================================================= -->
    <!-- 1. Tampilan Desktop: Tabel Lengkap (md: ke atas) -->
    <!-- ========================================================================= -->
    <div class="hidden md:block bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
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
                        <th class="py-3 px-4 text-center w-28">Aksi</th>
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
                            <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                @php
                                    $method = $exp->payment_method ?? 'Omzet Kandang';
                                    $methodBadge = match($method) {
                                        'Tunai Pribadi' => 'bg-sky-50 text-sky-700 border-sky-200/80',
                                        'Transfer Pribadi' => 'bg-purple-50 text-purple-700 border-purple-200/80',
                                        default => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded border {{ $methodBadge }}">
                                    {{ $method }}
                                </span>
                                @if($exp->notes)
                                    <span class="text-[11px] text-slate-400 line-clamp-1">&bull; {{ $exp->notes }}</span>
                                @endif
                            </div>
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
                            <div class="inline-flex items-center gap-1">
                                <!-- Tombol Lihat Detail -->
                                <button type="button" onclick="openExpenseDetailModal({{ $exp->id }})"
                                    class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors" title="Lihat Detail">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <!-- Tombol Edit -->
                                <button type="button" onclick="openEditExpenseModal({{ $exp->id }})"
                                    class="p-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 transition-colors" title="Edit Pengeluaran">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <!-- Tombol Hapus -->
                                <button type="button" onclick="confirmDeleteExpenseLocal({{ $exp->id }}, '{{ $exp->transaction_code }}')"
                                    class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors" title="Hapus Pengeluaran">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
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
    </div>

    <!-- ========================================================================= -->
    <!-- 2. Tampilan Mobile: Modern Card Layout Sesuai Permintaan (< md) -->
    <!-- Klik card: Detail View | Titik 3: Pengaturan Edit & Hapus -->
    <!-- ========================================================================= -->
    <div class="block md:hidden space-y-3">
        @forelse($expenses as $idx => $exp)
        <div onclick="openExpenseDetailModal({{ $exp->id }})"
             class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs hover:border-slate-300 hover:shadow-sm active:bg-slate-50 transition-all cursor-pointer relative group">
            
            <!-- Header Card: Tanggal & Kode + Kategori + Titik 3 -->
            <div class="flex items-center justify-between gap-2 mb-2">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="font-extrabold text-slate-800 text-xs">
                        {{ \Carbon\Carbon::parse($exp->date)->translatedFormat('d M Y') }}
                    </span>
                    <span class="text-[11px] font-mono text-slate-400">#{{ $exp->transaction_code }}</span>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $exp->category }}
                    </span>

                    <!-- Menu Titik 3 (Dropdown Pengaturan: Edit, Hapus, Detail) -->
                    <div class="relative inline-block text-left" onclick="event.stopPropagation()">
                        <button type="button"
                            onclick="event.stopPropagation(); toggleExpenseDropdown('exp-drop-{{ $exp->id }}')"
                            class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 active:scale-95 transition-all"
                            title="Pengaturan">
                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                        </button>

                        <div id="exp-drop-{{ $exp->id }}"
                             class="hidden dropdown-menu-exp absolute right-0 top-9 z-30 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 text-xs text-slate-700 animate-in fade-in zoom-in-95 duration-100">
                            <button type="button"
                                onclick="event.stopPropagation(); closeAllExpenseDropdowns(); openExpenseDetailModal({{ $exp->id }})"
                                class="w-full px-3.5 py-2.5 text-left hover:bg-slate-50 flex items-center gap-2.5 font-medium transition-colors">
                                <i data-lucide="eye" class="w-4 h-4 text-slate-500"></i>
                                <span>Lihat Detail</span>
                            </button>
                            <button type="button"
                                onclick="event.stopPropagation(); closeAllExpenseDropdowns(); openEditExpenseModal({{ $exp->id }})"
                                class="w-full px-3.5 py-2.5 text-left hover:bg-amber-50 flex items-center gap-2.5 font-medium text-amber-700 transition-colors">
                                <i data-lucide="pencil" class="w-4 h-4 text-amber-600"></i>
                                <span>Edit Pengeluaran</span>
                            </button>
                            <div class="border-t border-slate-100 my-1"></div>
                            <button type="button"
                                onclick="event.stopPropagation(); closeAllExpenseDropdowns(); confirmDeleteExpenseLocal({{ $exp->id }}, '{{ $exp->transaction_code }}')"
                                class="w-full px-3.5 py-2.5 text-left hover:bg-rose-50 flex items-center gap-2.5 font-bold text-rose-600 transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4 text-rose-600"></i>
                                <span>Hapus Pengeluaran</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Body Card: Keperluan & Subkategori / Keterangan -->
            <div class="space-y-1 mb-2.5">
                <h4 class="font-extrabold text-slate-900 text-sm leading-snug">
                    {{ $exp->purpose }}
                </h4>
                <div class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
                    <span class="text-slate-600 font-medium bg-slate-50 px-2 py-0.5 rounded border border-slate-100 text-[11px]">
                        {{ $exp->subcategory }}
                    </span>
                    @if($exp->notes)
                        <span class="text-slate-400 truncate max-w-[220px]" title="{{ $exp->notes }}">&bull; {{ $exp->notes }}</span>
                    @endif
                </div>
            </div>

            <!-- Footer Card: Penginput & Nota (Kiri) + Nominal (Kanan) -->
            <div class="flex items-center justify-between pt-2.5 border-t border-slate-100 text-xs">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-800 bg-amber-50 border border-amber-200/60 px-1.5 py-0.5 rounded">
                        <i data-lucide="user" class="w-3 h-3 text-amber-600"></i>
                        @<span>{{ $exp->user ? ($exp->user->username ?: $exp->user->name) : 'admin' }}</span>
                    </span>

                    @if($exp->receipt_photo)
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200/60">
                            <i data-lucide="paperclip" class="w-3 h-3 text-blue-600"></i> Nota
                        </span>
                    @endif

                    <span class="text-[10px] text-slate-400 font-medium">
                        {{ $exp->payment_method ?? 'Kas Tunai' }}
                    </span>
                </div>

                <div class="text-right">
                    <span class="font-black text-rose-600 text-sm sm:text-base whitespace-nowrap">
                        - {{ $exp->formatted_amount }}
                    </span>
                </div>
            </div>

        </div>
        @empty
        <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-xs text-center text-slate-400">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2.5">
                <i data-lucide="inbox" class="w-6 h-6"></i>
            </div>
            <p class="font-bold text-slate-600 text-sm">Tidak ada data pengeluaran ditemukan</p>
            <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter kategori atau kata kunci Anda.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($expenses->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-100 shadow-xs">
            {{ $expenses->links() }}
        </div>
    @endif

</div>

<!-- ========================================================================= -->
<!-- Modal Detail Pengeluaran (Tampilan Mewah Slide-Up di Mobile) -->
<!-- ========================================================================= -->
<div id="expDetailModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex flex-col justify-end sm:justify-center sm:items-center p-0 sm:p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden transition-transform duration-300 translate-y-12 sm:translate-y-0">
        
        <!-- Header -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <button type="button" onclick="closeExpenseDetailModal()" class="w-9 h-9 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <h3 class="font-extrabold text-base text-slate-800">Detail Pengeluaran Kandang</h3>
            <div class="w-9"></div>
        </div>

        <!-- Content Body (Dynamically populated) -->
        <div id="expDetailContent" class="p-5 overflow-y-auto max-h-[70vh] space-y-4 text-xs sm:text-sm">
            <div class="text-center py-8 text-slate-400">
                <span class="animate-spin inline-block mr-2">&#9696;</span> Memuat data pengeluaran...
            </div>
        </div>

        <!-- Footer -->
        <div id="expDetailFooter" class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between gap-2">
            <div class="flex items-center gap-1.5" id="expDetailActions">
                <!-- Tombol Edit & Hapus akan disisipkan di sini -->
            </div>
            <button type="button" onclick="closeExpenseDetailModal()"
                class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold text-xs transition-colors">
                Tutup
            </button>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal Edit Pengeluaran Kandang -->
<!-- ========================================================================= -->
<div id="editExpenseModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex flex-col justify-end sm:justify-center sm:items-center p-0 sm:p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden transition-transform duration-300 translate-y-12 sm:translate-y-0">
        
        <!-- Header -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <button type="button" onclick="closeEditExpenseModal()" class="w-9 h-9 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <div class="text-center">
                <h3 class="font-extrabold text-base text-slate-800">Edit Pengeluaran</h3>
<!-- Modal Detail Pengeluaran (Pop-up View) -->
<div id="expenseDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 opacity-0 pointer-events-none transition-all duration-300 backdrop-blur-xs">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 transform translate-y-12 transition-all duration-300 max-h-[90vh] overflow-y-auto">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 flex items-center justify-center">
                    <i data-lucide="receipt" class="w-5 h-5"></i>
                </div>
                <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Detail Pengeluaran Kandang</h3>
            </div>
            <button type="button" onclick="closeExpenseDetailModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div id="expDetailContent" class="py-4 space-y-4">
            <!-- Populated dynamically via JS -->
        </div>

        <div id="expDetailActions" class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <!-- Action buttons populated dynamically -->
        </div>

    </div>
</div>

<!-- Modal Edit Pengeluaran (AJAX Dynamic) -->
<div id="editExpenseModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 opacity-0 pointer-events-none transition-all duration-300 backdrop-blur-xs">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 transform translate-y-12 transition-all duration-300 max-h-[90vh] overflow-y-auto">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center">
                    <i data-lucide="edit-3" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Edit Pengeluaran</h3>
                    <span id="editExpCodeBadge" class="text-xs text-slate-400 font-mono font-semibold">#EXP-...</span>
                </div>
            </div>
            <button type="button" onclick="closeEditExpenseModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="editExpenseForm" onsubmit="submitEditExpense(event)" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" id="edit_expense_id" name="id">

            <!-- 1. Tanggal -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Pengeluaran</label>
                <input type="date" id="edit_date" name="date" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800">
            </div>

            <!-- 2. Kategori -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori</label>
                <select id="edit_category" name="category" required onchange="onEditCategoryChange()"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat['name'] }}">{{ $cat['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Subkategori -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Subkategori</label>
                <select id="edit_subcategory" name="subcategory" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800">
                    <option value="">Pilih Subkategori</option>
                </select>
            </div>

            <!-- 4. Keperluan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keperluan / Kebutuhan</label>
                <input type="text" id="edit_purpose" name="purpose" required maxlength="255"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800">
            </div>

            <!-- 5. Nominal (Rp) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nominal (Rp)</label>
                <input type="text" id="edit_amount" name="amount" required inputmode="numeric" oninput="formatRupiahInput(this)"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm sm:text-base font-black text-rose-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800">
            </div>

            <!-- 6. Sumber Dana Pengeluaran -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Sumber Dana Pengeluaran</label>
                <select id="edit_payment_method" name="payment_method"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800">
                    <option value="Omzet Kandang">Omzet Kandang (Penghasilan / Kas Kandang)</option>
                    <option value="Tunai Pribadi">Tunai Pribadi (Modal / Talangan Tunai)</option>
                    <option value="Transfer Pribadi">Transfer Pribadi (Modal / Rekening Pribadi)</option>
                </select>
            </div>

            <!-- 7. Catatan / Keterangan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan / Keterangan (Opsional)</label>
                <textarea id="edit_notes" name="notes" rows="2"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-normal text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800"></textarea>
            </div>

            <!-- 8. Foto Nota -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bukti Foto / Nota (Opsional)</label>
                <div id="edit_existing_photo_container" class="hidden mb-2">
                    <span class="text-[11px] text-slate-400 block mb-1">Foto saat ini:</span>
                    <a id="edit_existing_photo_link" href="#" target="_blank">
                        <img id="edit_existing_photo_img" src="" alt="Nota" class="h-20 w-auto rounded-lg border border-slate-200 object-cover">
                    </a>
                </div>
                <input type="file" id="edit_receipt_photo" name="receipt_photo" accept="image/*"
                    class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                <span class="text-[10px] text-slate-400 block mt-1">Kosongkan jika tidak ingin mengubah foto nota.</span>
            </div>

            <!-- Footer Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeEditExpenseModal()"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" id="btnSubmitEditExpense"
                    class="px-5 py-2.5 bg-maroon-800 hover:bg-maroon-900 text-white rounded-xl font-bold text-xs shadow-md transition-all active:scale-95 flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const masterCategoriesData = @json($categories);

    // -------------------------------------------------------------
    // Dropdown Titik 3 Mobile Card
    // -------------------------------------------------------------
    function toggleExpenseDropdown(id) {
        const target = document.getElementById(id);
        const isHidden = target.classList.contains('hidden');
        closeAllExpenseDropdowns();
        if (isHidden) {
            target.classList.remove('hidden');
        }
    }

    function closeAllExpenseDropdowns() {
        document.querySelectorAll('.dropdown-menu-exp').forEach(el => el.classList.add('hidden'));
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-menu-exp')) {
            closeAllExpenseDropdowns();
        }
    });

    // -------------------------------------------------------------
    // Format Rupiah Input Helper
    // -------------------------------------------------------------
    function formatRupiahInput(input) {
        let val = input.value.replace(/[^0-9]/g, '');
        if (val) {
            input.value = 'Rp ' + parseInt(val, 10).toLocaleString('id-ID');
        } else {
            input.value = '';
        }
    }

    function onEditCategoryChange() {
        const catName = document.getElementById('edit_category').value;
        const subcatSelect = document.getElementById('edit_subcategory');
        subcatSelect.innerHTML = '<option value="">Pilih Subkategori</option>';

        const foundCat = masterCategoriesData.find(c => c.name === catName);
        if (foundCat && foundCat.subcategories) {
            foundCat.subcategories.forEach(sub => {
                const opt = document.createElement('option');
                opt.value = sub;
                opt.textContent = sub;
                subcatSelect.appendChild(opt);
            });
        }
    }

    // -------------------------------------------------------------
    // Detail Modal Controls
    // -------------------------------------------------------------
    function openExpenseDetailModal(id) {
        const modal = document.getElementById('expenseDetailModal');
        const content = document.getElementById('expDetailContent');
        const actions = document.getElementById('expDetailActions');

        content.innerHTML = `<div class="text-center py-8 text-slate-400"><span class="animate-spin inline-block mr-2">&#9696;</span> Memuat data pengeluaran...</div>`;
        actions.innerHTML = '';

        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.remove('translate-y-12');

        fetch(`/pengeluaran/${id}`)
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    content.innerHTML = `<p class="text-center text-rose-600 py-6 font-bold">${res.message || 'Gagal memuat detail'}</p>`;
                    return;
                }

                const d = res.data;
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
                            <span class="font-bold text-slate-800">${d.display_date}</span>
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
                            <span class="text-slate-500">Sumber Dana</span>
                            <span class="font-semibold text-slate-700">${d.payment_method || 'Omzet Kandang'}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60 items-center">
                            <span class="text-slate-500">Keperluan</span>
                            <span class="font-bold text-slate-800 text-right">${d.purpose}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-slate-500">Keterangan / Catatan</span>
                            <span class="font-medium text-slate-700 text-right">${d.notes || '-'}</span>
                        </div>
                    </div>

                    ${photoHtml}
                `;

                // Set Action Buttons di Footer Modal Detail
                actions.innerHTML = `
                    <button type="button" onclick="closeExpenseDetailModal(); openEditExpenseModal(${d.id});"
                        class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-xl font-bold text-xs transition-colors flex items-center gap-1.5">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                        <span>Edit</span>
                    </button>
                    <button type="button" onclick="closeExpenseDetailModal(); confirmDeleteExpenseLocal(${d.id}, '${d.transaction_code}');"
                        class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl font-bold text-xs transition-colors flex items-center gap-1.5">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Hapus</span>
                    </button>
                `;

                lucide.createIcons();
            })
            .catch(err => {
                console.error(err);
                content.innerHTML = `<p class="text-center text-rose-600 py-6 font-bold">Terjadi kesalahan saat memuat data pengeluaran.</p>`;
            });
    }

    function closeExpenseDetailModal() {
        const modal = document.getElementById('expenseDetailModal');
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.add('translate-y-12');
    }

    // -------------------------------------------------------------
    // Edit Modal Controls
    // -------------------------------------------------------------
    function openEditExpenseModal(id) {
        const modal = document.getElementById('editExpenseModal');
        const codeBadge = document.getElementById('editExpCodeBadge');

        // Loading state
        codeBadge.textContent = 'Memuat data...';
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.remove('translate-y-12');

        fetch(`/pengeluaran/${id}`)
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    Swal.fire('Error', res.message || 'Gagal memuat data pengeluaran', 'error');
                    closeEditExpenseModal();
                    return;
                }

                const d = res.data;
                codeBadge.textContent = `#${d.transaction_code}`;
                document.getElementById('edit_expense_id').value = d.id;
                document.getElementById('edit_date').value = d.date;
                document.getElementById('edit_purpose').value = d.purpose;
                document.getElementById('edit_amount').value = 'Rp ' + Math.round(d.amount).toLocaleString('id-ID');
                document.getElementById('edit_payment_method').value = d.payment_method || 'Omzet Kandang';
                document.getElementById('edit_notes').value = d.notes || '';

                // Set Kategori & populate subkategori
                document.getElementById('edit_category').value = d.category;
                onEditCategoryChange();
                document.getElementById('edit_subcategory').value = d.subcategory;

                // Photo preview
                const photoCont = document.getElementById('edit_existing_photo_container');
                const photoImg = document.getElementById('edit_existing_photo_img');
                const photoLink = document.getElementById('edit_existing_photo_link');
                if (d.receipt_photo) {
                    photoImg.src = d.receipt_photo;
                    photoLink.href = d.receipt_photo;
                    photoCont.classList.remove('hidden');
                } else {
                    photoCont.classList.add('hidden');
                }

                // Reset file input
                document.getElementById('edit_receipt_photo').value = '';

                lucide.createIcons();
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Gagal memuat data pengeluaran untuk diedit.', 'error');
                closeEditExpenseModal();
            });
    }

    function closeEditExpenseModal() {
        const modal = document.getElementById('editExpenseModal');
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.add('translate-y-12');
    }

    function submitEditExpense(e) {
        e.preventDefault();
        const id = document.getElementById('edit_expense_id').value;
        const form = document.getElementById('editExpenseForm');
        const formData = new FormData(form);
        formData.append('_method', 'PUT');

        const btn = document.getElementById('btnSubmitEditExpense');
        const origBtnText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<span class="animate-spin mr-1.5">&#9696;</span> Menyimpan...`;

        fetch(`/pengeluaran/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = origBtnText;

            if (res.success) {
                closeEditExpenseModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Diperbarui',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Gagal', res.message || 'Terjadi kesalahan saat menyimpan perubahan.', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = origBtnText;
            console.error(err);
            Swal.fire('Error', 'Gagal memproses perubahan.', 'error');
        });
    }

    // -------------------------------------------------------------
    // Delete Expense
    // -------------------------------------------------------------
    function confirmDeleteExpenseLocal(id, code) {
        Swal.fire({
            title: 'Hapus Pengeluaran?',
            text: `Apakah Anda yakin ingin menghapus data #${code}?`,
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
                        Swal.fire('Error', res.message || 'Gagal menghapus pengeluaran', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'Gagal memproses penghapusan.', 'error');
                });
            }
        });
    }
</script>
@endpush
