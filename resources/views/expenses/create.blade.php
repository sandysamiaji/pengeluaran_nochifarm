@extends('layouts.app')

@section('title', 'Catat Pengeluaran Kandang - NOCHI FARM')

@section('content')
<div class="max-w-xl mx-auto">

    <!-- Screen 1: Form Pengeluaran Kandang (Sesuai Mockup) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- Card Header with Wallet Icon -->
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-maroon-800 text-white flex items-center justify-center shrink-0 shadow-md">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">Pengeluaran Kandang</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5 leading-snug">Catat setiap pengeluaran untuk operasional kandang dengan mudah.</p>
            </div>
        </div>

        <!-- Form Body -->
        <form id="expenseForm" action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="p-5 sm:p-6 space-y-5">
            @csrf

            <!-- Quick Template Chips (Dinamis dari Master Template) -->
            <div class="bg-rose-50/50 p-3.5 rounded-2xl border border-rose-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-extrabold uppercase text-maroon-900 tracking-wider flex items-center gap-1.5">
                        <i data-lucide="zap" class="w-3.5 h-3.5 text-nochi-orange"></i>
                        <span>Template Pengeluaran Rutin (1-Klik Isi)</span>
                    </span>
                    <a href="{{ route('master.templates.index') }}" class="text-[10px] font-bold text-nochi-orange hover:text-nochi-orangeDark flex items-center gap-1 hover:underline">
                        <i data-lucide="settings" class="w-3 h-3"></i>
                        <span>Atur di Master</span>
                    </a>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @forelse($templates as $tmpl)
                    <button type="button" onclick="applyQuickTemplate('{{ $tmpl->category }}', '{{ $tmpl->subcategory }}', '{{ addslashes($tmpl->purpose) }}', {{ (float)$tmpl->amount }}, '{{ addslashes($tmpl->notes ?? '') }}')"
                        class="px-2.5 py-1 bg-white hover:bg-rose-100/70 text-slate-700 hover:text-maroon-900 rounded-lg text-[11px] font-bold border border-rose-200 shadow-2xs transition-all flex items-center gap-1 active:scale-95"
                        title="Klik untuk mengisi: {{ $tmpl->purpose }} (Rp {{ number_format($tmpl->amount, 0, ',', '.') }})">
                        <span>{{ $tmpl->name }}</span>
                    </button>
                    @empty
                    <p class="text-[11px] text-slate-400">Belum ada template aktif. <a href="{{ route('master.templates.index') }}" class="text-nochi-orange underline font-bold">Tambah di Master</a></p>
                    @endforelse
                </div>
            </div>

            <!-- 1. Tanggal -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-maroon-800"></i>
                    <span>Tanggal</span>
                </label>
                <div class="relative">
                    <input type="date" id="dateInput" name="date" value="{{ $defaultDate }}" required
                        class="w-full pl-4 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800 focus:border-transparent transition-all">
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>

            <!-- 2. Kategori Trigger -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                    <i data-lucide="layers" class="w-3.5 h-3.5 text-maroon-800"></i>
                    <span>Kategori</span>
                </label>
                <input type="hidden" id="selectedCategory" name="category" value="">
                <button type="button" onclick="openCategoryModal()" id="categoryTriggerBtn"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-left flex items-center justify-between text-sm hover:bg-slate-100/70 focus:outline-none focus:ring-2 focus:ring-maroon-800 transition-all">
                    <div class="flex items-center gap-2.5 truncate">
                        <span id="categoryIconContainer" class="hidden w-6 h-6 rounded-lg bg-maroon-100 text-maroon-800 flex items-center justify-center text-xs"></span>
                        <span id="categoryDisplayText" class="text-slate-400 font-medium">Pilih Kategori</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 shrink-0"></i>
                </button>
            </div>

            <!-- 3. Subkategori Trigger -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                    <i data-lucide="tag" class="w-3.5 h-3.5 text-maroon-800"></i>
                    <span>Subkategori</span>
                </label>
                <input type="hidden" id="selectedSubcategory" name="subcategory" value="">
                <button type="button" onclick="openSubcategoryModal()" id="subcategoryTriggerBtn"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-left flex items-center justify-between text-sm hover:bg-slate-100/70 focus:outline-none focus:ring-2 focus:ring-maroon-800 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="subcategoryDisplayText" class="text-slate-400 font-medium truncate">Pilih Subkategori</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 shrink-0"></i>
                </button>
            </div>

            <!-- 4. Keperluan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                    <i data-lucide="file-text" class="w-3.5 h-3.5 text-maroon-800"></i>
                    <span>Keperluan</span>
                </label>
                <input type="text" id="purposeInput" name="purpose" placeholder="Contoh: Pembelian pakan layer" required
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800 focus:border-transparent transition-all">
            </div>

            <!-- 5. Nominal (Rp) dengan Auto-Format -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                    <div class="w-4 h-4 rounded-full bg-slate-700 text-white flex items-center justify-center text-[9px] font-bold">Rp</div>
                    <span>Nominal (Rp)</span>
                </label>
                <div class="relative">
                    <input type="text" id="amountInput" name="amount" placeholder="Masukkan nominal" required inputmode="numeric"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-lg font-black text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800 focus:border-transparent transition-all">
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1">
                    <i data-lucide="info" class="w-3 h-3 text-slate-400 shrink-0"></i>
                    <span>Nominal akan otomatis diformat (contoh: 350000 &rarr; Rp 350.000)</span>
                </p>
            </div>

            <!-- 6. Sumber Dana Pengeluaran -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                    <i data-lucide="wallet-cards" class="w-3.5 h-3.5 text-maroon-800"></i>
                    <span>Sumber Dana Pengeluaran</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <!-- 1. Omzet Kandang -->
                    <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer hover:bg-slate-100/80 transition-all has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/70 has-[:checked]:text-emerald-950 has-[:checked]:shadow-xs">
                        <input type="radio" name="payment_method" value="Omzet Kandang" checked class="accent-emerald-700 w-4 h-4 shrink-0">
                        <div class="leading-tight">
                            <span class="text-xs font-bold block flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                Omzet Kandang
                            </span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Penghasilan / Kas Kandang</span>
                        </div>
                    </label>

                    <!-- 2. Tunai Pribadi -->
                    <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer hover:bg-slate-100/80 transition-all has-[:checked]:border-sky-600 has-[:checked]:bg-sky-50/70 has-[:checked]:text-sky-950 has-[:checked]:shadow-xs">
                        <input type="radio" name="payment_method" value="Tunai Pribadi" class="accent-sky-700 w-4 h-4 shrink-0">
                        <div class="leading-tight">
                            <span class="text-xs font-bold block flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-sky-500 shrink-0"></span>
                                Tunai Pribadi
                            </span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Modal / Talangan Tunai</span>
                        </div>
                    </label>

                    <!-- 3. Transfer Pribadi -->
                    <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer hover:bg-slate-100/80 transition-all has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50/70 has-[:checked]:text-purple-950 has-[:checked]:shadow-xs">
                        <input type="radio" name="payment_method" value="Transfer Pribadi" class="accent-purple-700 w-4 h-4 shrink-0">
                        <div class="leading-tight">
                            <span class="text-xs font-bold block flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-purple-500 shrink-0"></span>
                                Transfer Pribadi
                            </span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Modal / Rekening Pribadi</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 7. Keterangan (Opsional) with 0/200 Counter -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="message-square" class="w-3.5 h-3.5 text-maroon-800"></i>
                        <span>Keterangan (Opsional)</span>
                    </label>
                    <span id="charCounter" class="text-[11px] text-slate-400 font-medium">0/200</span>
                </div>
                <textarea id="notesInput" name="notes" rows="3" maxlength="200" placeholder="Tambahkan keterangan jika ada..."
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800 focus:border-transparent transition-all resize-none"></textarea>
            </div>

            <!-- 7. Bukti Foto / Nota (Opsional) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                    <i data-lucide="image" class="w-3.5 h-3.5 text-maroon-800"></i>
                    <span>Bukti Foto / Nota (Opsional)</span>
                </label>
                
                <input type="file" id="receiptPhotoInput" name="receipt_photo" accept="image/*" class="hidden" onchange="handleReceiptChange(event)">
                
                <!-- Upload Dropzone Container -->
                <div id="uploadDropzone" onclick="document.getElementById('receiptPhotoInput').click()"
                    class="border-2 border-dashed border-rose-200 hover:border-maroon-700 bg-rose-50/40 hover:bg-rose-50/80 rounded-2xl p-6 text-center cursor-pointer transition-all flex flex-col items-center justify-center group">
                    <div class="w-12 h-12 rounded-full bg-maroon-800 text-white flex items-center justify-center mb-2 shadow-sm group-hover:scale-110 transition-transform">
                        <i data-lucide="camera" class="w-6 h-6"></i>
                    </div>
                    <p class="text-xs sm:text-sm font-bold text-slate-700">Klik untuk menambah foto</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">atau pilih dari galeri</p>
                    <div class="mt-2.5 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200/80 text-[11px] font-semibold text-amber-900 shadow-2xs">
                        <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-600"></i>
                        <span>Otomatis dikonversi ke &plusmn;100 KB</span>
                    </div>
                </div>

                <!-- Compression Progress State Container (Displayed while converting) -->
                <div id="receiptCompressingContainer" class="hidden mt-2 p-5 rounded-2xl border-2 border-amber-300 bg-gradient-to-br from-amber-50 to-orange-50/70 shadow-xs text-center">
                    <div class="flex flex-col items-center justify-center space-y-3">
                        <div class="relative w-12 h-12">
                            <div class="w-12 h-12 rounded-full border-4 border-amber-200 border-t-amber-600 animate-spin"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i data-lucide="image-down" class="w-5 h-5 text-amber-700"></i>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <h4 class="text-sm font-black text-amber-950">Mengonversi Foto Menjadi ~100 KB...</h4>
                            <p class="text-xs text-amber-800 font-medium" id="compressStatusText">Menyesuaikan resolusi dan kualitas gambar...</p>
                            <p class="text-[11px] text-slate-500 font-mono" id="compressSizesText">Mohon tunggu sebentar...</p>
                        </div>

                        <div class="w-full bg-amber-200/70 h-2 rounded-full overflow-hidden">
                            <div id="compressProgressBar" class="h-full bg-nochi-orange rounded-full transition-all duration-300 w-1/3 animate-pulse"></div>
                        </div>
                    </div>
                </div>

                <!-- Preview Container (After successful compression) -->
                <div id="receiptPreviewContainer" class="hidden mt-2 relative rounded-2xl overflow-hidden border border-slate-200 bg-slate-50 shadow-xs">
                    <img id="receiptPreviewImg" src="#" alt="Bukti Nota" class="w-full max-h-64 object-contain bg-slate-100">
                    
                    <!-- Compression Result Info Bar -->
                    <div class="p-3 bg-white border-t border-slate-200 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span class="inline-flex items-center gap-1 text-[11px] font-black text-emerald-800 bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded-md">
                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-600"></i>
                                <span id="compressedBadgeSize">100 KB</span>
                            </span>
                            <span class="text-[11px] text-slate-500" id="compressedSavingsText">Dikonversi optimal</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="document.getElementById('receiptPhotoInput').click()"
                                class="text-xs font-bold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-lg transition-colors">
                                Ganti Foto
                            </button>
                            <button type="button" onclick="removeReceiptPhoto(event)"
                                class="text-xs font-bold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-2.5 py-1 rounded-lg transition-colors">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button (Orange Pill Button with Floppy Icon Sesuai Mockup) -->
            <div class="pt-2">
                <button type="button" onclick="prepareConfirmationModal()"
                    class="w-full py-3.5 bg-nochi-orange hover:bg-nochi-orangeDark active:scale-98 text-white rounded-full font-extrabold text-sm sm:text-base shadow-lg shadow-orange-500/30 flex items-center justify-center gap-2.5 transition-all">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    <span>Simpan Pengeluaran</span>
                </button>
            </div>

        </form>
    </div>

</div>

<!-- ========================================================================= -->
<!-- Screen 2: Modal Pilih Kategori (12 Kategori Lengkap Sesuai Mockup) -->
<!-- ========================================================================= -->
<div id="categoryModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex flex-col justify-end sm:justify-center sm:items-center p-0 sm:p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white w-full sm:max-w-md max-h-[90vh] rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden transition-transform duration-300 translate-y-12 sm:translate-y-0">
        
        <!-- Header Modal -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-white sticky top-0 z-10">
            <button type="button" onclick="closeCategoryModal()" class="w-9 h-9 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-600 transition-colors">
                <i data-lucide="chevron-left" class="w-6 h-6"></i>
            </button>
            <h3 class="font-extrabold text-base text-slate-800">Pilih Kategori</h3>
            <div class="w-9"></div> <!-- Spacer balance -->
        </div>

        <!-- Search Bar -->
        <div class="p-3 border-b border-slate-100 bg-slate-50/60">
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" id="categorySearchInput" oninput="filterCategories(this.value)" placeholder="Cari kategori..."
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-maroon-800">
            </div>
        </div>

        <!-- Category Items List -->
        <div id="categoryListContainer" class="p-2 overflow-y-auto divide-y divide-slate-100 flex-1">
            @foreach($categories as $cat)
            <div class="category-item flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors group"
                onclick="selectCategory('{{ $cat['name'] }}', '{{ $cat['icon'] }}', {{ json_encode($cat['subcategories']) }})"
                data-name="{{ strtolower($cat['name']) }}"
                data-desc="{{ strtolower($cat['description']) }}">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-maroon-50 border border-maroon-100 text-maroon-800 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <i data-lucide="{{ $cat['icon'] }}" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">{{ $cat['name'] }}</h4>
                        <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $cat['description'] }}</p>
                    </div>
                </div>
                <i data-lucide="chevron-right" class="w-5 h-5 text-slate-300 group-hover:text-maroon-800 group-hover:translate-x-0.5 transition-all shrink-0"></i>
            </div>
            @endforeach
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- Screen 3: Modal Pilih Subkategori (Sesuai Mockup) -->
<!-- ========================================================================= -->
<div id="subcategoryModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex flex-col justify-end sm:justify-center sm:items-center p-0 sm:p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white w-full sm:max-w-md max-h-[90vh] rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden transition-transform duration-300 translate-y-12 sm:translate-y-0">
        
        <!-- Header Modal -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-white sticky top-0 z-10">
            <button type="button" onclick="closeSubcategoryModal()" class="w-9 h-9 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-600 transition-colors">
                <i data-lucide="chevron-left" class="w-6 h-6"></i>
            </button>
            <h3 class="font-extrabold text-base text-slate-800">Pilih Subkategori</h3>
            <div class="w-9"></div>
        </div>

        <!-- Category Banner -->
        <div class="p-4 bg-maroon-50/60 border-b border-maroon-100 flex items-center gap-3">
            <div id="subcatParentIcon" class="w-10 h-10 rounded-xl bg-maroon-800 text-white flex items-center justify-center shrink-0">
                <i data-lucide="package-open" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 id="subcatParentName" class="text-sm font-extrabold text-maroon-900">Pakan</h4>
                <p id="subcatParentSub" class="text-xs text-maroon-700/80">Pilih subkategori pengeluaran pakan</p>
            </div>
        </div>

        <!-- Subcategories List -->
        <div id="subcategoryItemsContainer" class="p-2 overflow-y-auto divide-y divide-slate-100 flex-1">
            <!-- Dynamically populated -->
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- Screen 4: Modal Konfirmasi Pengeluaran (Sesuai Mockup) -->
<!-- ========================================================================= -->
<div id="confirmationModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex flex-col justify-end sm:justify-center sm:items-center p-0 sm:p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden transition-transform duration-300 translate-y-12 sm:translate-y-0">
        
        <!-- Header -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <button type="button" onclick="closeConfirmationModal()" class="w-9 h-9 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-600 transition-colors">
                <i data-lucide="chevron-left" class="w-6 h-6"></i>
            </button>
            <h3 class="font-extrabold text-base text-slate-800">Konfirmasi Pengeluaran</h3>
            <div class="w-9"></div>
        </div>

        <!-- Confirmation Card Table -->
        <div class="p-5 overflow-y-auto max-h-[60vh] space-y-3.5">
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 divide-y divide-slate-200/60 text-xs sm:text-sm">
                
                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-slate-500 font-medium flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-maroon-800"></i>
                        <span>Tanggal</span>
                    </span>
                    <span id="confirmDate" class="font-bold text-slate-800 text-right">-</span>
                </div>

                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-slate-500 font-medium flex items-center gap-1.5">
                        <i data-lucide="layers" class="w-3.5 h-3.5 text-maroon-800"></i>
                        <span>Kategori</span>
                    </span>
                    <span id="confirmCategory" class="font-bold text-slate-800 text-right">-</span>
                </div>

                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-slate-500 font-medium flex items-center gap-1.5">
                        <i data-lucide="tag" class="w-3.5 h-3.5 text-maroon-800"></i>
                        <span>Subkategori</span>
                    </span>
                    <span id="confirmSubcategory" class="font-bold text-slate-800 text-right">-</span>
                </div>

                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-slate-500 font-medium flex items-center gap-1.5">
                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-maroon-800"></i>
                        <span>Keperluan</span>
                    </span>
                    <span id="confirmPurpose" class="font-bold text-slate-800 text-right">-</span>
                </div>

                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-slate-500 font-medium flex items-center gap-1.5">
                        <i data-lucide="circle-dollar-sign" class="w-3.5 h-3.5 text-maroon-800"></i>
                        <span>Nominal</span>
                    </span>
                    <span id="confirmAmount" class="font-black text-rose-700 text-base text-right">-</span>
                </div>

                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-slate-500 font-medium flex items-center gap-1.5">
                        <i data-lucide="credit-card" class="w-3.5 h-3.5 text-maroon-800"></i>
                        <span>Metode Bayar</span>
                    </span>
                    <span id="confirmPaymentMethod" class="font-bold text-slate-800 text-right">-</span>
                </div>

                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-slate-500 font-medium flex items-center gap-1.5">
                        <i data-lucide="message-square" class="w-3.5 h-3.5 text-maroon-800"></i>
                        <span>Keterangan</span>
                    </span>
                    <span id="confirmNotes" class="font-medium text-slate-700 text-right max-w-[60%]">-</span>
                </div>

                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-slate-500 font-medium flex items-center gap-1.5">
                        <i data-lucide="image" class="w-3.5 h-3.5 text-maroon-800"></i>
                        <span>Bukti Nota</span>
                    </span>
                    <div id="confirmReceiptContainer" class="text-right">
                        <span id="confirmReceiptText" class="text-slate-400 font-medium">Tidak ada foto</span>
                        <div id="confirmReceiptWrapper" class="hidden flex items-center gap-2 justify-end">
                            <span id="confirmReceiptBadge" class="text-[10px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded">~100 KB</span>
                            <img id="confirmReceiptImg" src="#" alt="Thumbnail" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-xs">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Action Buttons (Batal vs Simpan Sesuai Mockup) -->
        <div class="p-4 border-t border-slate-100 grid grid-cols-2 gap-3 bg-white">
            <button type="button" onclick="closeConfirmationModal()"
                class="w-full py-3 border border-red-300 text-red-600 hover:bg-red-50 rounded-xl font-bold text-sm transition-colors">
                Batal
            </button>
            <button type="button" onclick="submitExpenseForm()" id="btnFinalSubmit"
                class="w-full py-3 bg-nochi-orange hover:bg-nochi-orangeDark text-white rounded-xl font-bold text-sm shadow-md transition-colors flex items-center justify-center gap-2">
                <span>Simpan</span>
            </button>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- Screen 5: Modal Pengeluaran Berhasil Disimpan (Sesuai Mockup) -->
<!-- ========================================================================= -->
<div id="successModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white w-full max-w-sm rounded-3xl shadow-2xl p-6 text-center space-y-4 transform transition-transform duration-300 scale-95">
        
        <!-- Big Green Checkmark -->
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-inner">
            <i data-lucide="check" class="w-8 h-8 stroke-[3]"></i>
        </div>

        <div>
            <h3 class="text-lg font-black text-slate-800 tracking-tight">Pengeluaran Berhasil Disimpan</h3>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">Pengeluaran kandang telah dicatat dengan sukses.</p>
        </div>

        <div id="successSummaryBox" class="bg-slate-50 rounded-xl p-3 text-xs text-slate-600 text-left border border-slate-100 space-y-1">
            <div class="flex justify-between font-bold text-slate-800">
                <span id="successCode">EXP-2026...</span>
                <span id="successAmount" class="text-rose-600">Rp 0</span>
            </div>
            <p id="successPurpose" class="text-slate-500 truncate">-</p>
        </div>

        <div class="pt-2 space-y-2.5">
            <button type="button" onclick="resetFormForNewExpense()"
                class="w-full py-3 bg-nochi-orange hover:bg-nochi-orangeDark text-white rounded-full font-bold text-sm shadow-md transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus" class="w-4 h-4 stroke-[3]"></i>
                <span>Tambah Pengeluaran Lagi</span>
            </button>
            <a href="{{ route('dashboard') }}"
                class="w-full py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-full font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-2">
                <i data-lucide="list" class="w-4 h-4"></i>
                <span>Lihat Daftar Transaksi</span>
            </a>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentSelectedCategorySubcategories = [];
    let receiptBlobUrl = null;

    // Quick Template Applicator
    function applyQuickTemplate(category, subcategory, purpose, amount, notes) {
        document.getElementById('selectedCategory').value = category;
        document.getElementById('categoryDisplayText').textContent = category;
        document.getElementById('categoryDisplayText').classList.remove('text-slate-400');
        document.getElementById('categoryDisplayText').classList.add('text-slate-800', 'font-bold');

        document.getElementById('selectedSubcategory').value = subcategory;
        document.getElementById('subcategoryDisplayText').textContent = subcategory;
        document.getElementById('subcategoryDisplayText').classList.remove('text-slate-400');
        document.getElementById('subcategoryDisplayText').classList.add('text-slate-800', 'font-bold');

        document.getElementById('purposeInput').value = purpose;
        document.getElementById('amountInput').value = 'Rp ' + parseInt(amount, 10).toLocaleString('id-ID');
        document.getElementById('notesInput').value = notes || '';
        document.getElementById('charCounter').textContent = (notes ? notes.length : 0) + '/200';

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Template Terisi',
            text: purpose,
            timer: 1800,
            showConfirmButton: false
        });
    }

    // Currency Formatter Helper
    const amountInput = document.getElementById('amountInput');
    amountInput.addEventListener('input', function(e) {
        let val = this.value.replace(/[^0-9]/g, '');
        if (val) {
            this.value = 'Rp ' + parseInt(val, 10).toLocaleString('id-ID');
        } else {
            this.value = '';
        }
    });

    // Character counter for Notes
    const notesInput = document.getElementById('notesInput');
    const charCounter = document.getElementById('charCounter');
    notesInput.addEventListener('input', function() {
        charCounter.textContent = this.value.length + '/200';
    });

    // Receipt Photo Auto-Compression to ~100 KB
    let compressedReceiptFile = null;
    let compressedReceiptInfo = null;

    function formatBytes(bytes, decimals = 1) {
        if (!bytes || bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    /**
     * Kompresi gambar sisi klien menggunakan HTML5 Canvas menjadi sekitar 100 KB
     */
    function compressImageToTargetSize(file, targetSizeKB = 100, onProgress = null) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function(e) {
                const img = new Image();
                img.src = e.target.result;
                img.onload = function() {
                    let width = img.naturalWidth;
                    let height = img.naturalHeight;

                    // Batas resolusi awal maksimum agar teks nota tetap tajam (maks 1400px)
                    const maxEdge = 1400;
                    if (width > maxEdge || height > maxEdge) {
                        if (width > height) {
                            height = Math.round((height * maxEdge) / width);
                            width = maxEdge;
                        } else {
                            width = Math.round((width * maxEdge) / height);
                            height = maxEdge;
                        }
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');

                    // Latar belakang putih jika PNG transparan
                    ctx.fillStyle = '#FFFFFF';
                    ctx.fillRect(0, 0, width, height);
                    ctx.drawImage(img, 0, 0, width, height);

                    const targetBytes = targetSizeKB * 1024;
                    let minQuality = 0.15;
                    let maxQuality = 0.90;
                    let quality = 0.75;
                    let attempts = 0;

                    function step() {
                        attempts++;
                        if (onProgress) {
                            onProgress(`Menyesuaikan resolusi & kualitas foto (Langkah ${attempts}/6)...`);
                        }

                        canvas.toBlob(function(blob) {
                            if (!blob) {
                                reject(new Error('Gagal mengekspor canvas ke blob'));
                                return;
                            }

                            // Jika sudah di bawah target (<= 100 KB)
                            if (blob.size <= targetBytes) {
                                // Jika sudah cukup optimal atau sudah mencoba beberapa kali
                                if (blob.size >= targetBytes * 0.75 || attempts >= 6 || (maxQuality - minQuality) < 0.06) {
                                    return finish(blob);
                                }
                                minQuality = quality;
                                quality = (minQuality + maxQuality) / 2;
                                setTimeout(step, 30);
                            } else {
                                // Ukuran masih di atas 100 KB
                                maxQuality = quality;
                                if (quality > 0.35) {
                                    quality = (minQuality + maxQuality) / 2;
                                    setTimeout(step, 30);
                                } else {
                                    // Perkecil dimensi gambar sedikit dan reset quality
                                    width = Math.round(width * 0.82);
                                    height = Math.round(height * 0.82);
                                    canvas.width = width;
                                    canvas.height = height;
                                    ctx.fillStyle = '#FFFFFF';
                                    ctx.fillRect(0, 0, width, height);
                                    ctx.drawImage(img, 0, 0, width, height);
                                    quality = 0.65;
                                    minQuality = 0.15;
                                    maxQuality = 0.85;
                                    setTimeout(step, 30);
                                }
                            }
                        }, 'image/jpeg', quality);
                    }

                    function finish(blob) {
                        const baseName = file.name.replace(/\.[^/.]+$/, "");
                        const finalFile = new File([blob], `${baseName}_100kb.jpg`, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });
                        resolve({
                            file: finalFile,
                            blob: blob,
                            originalSize: file.size,
                            compressedSize: blob.size,
                            width: width,
                            height: height
                        });
                    }

                    step();
                };
                img.onerror = (err) => reject(err);
            };
            reader.onerror = (err) => reject(err);
        });
    }

    async function handleReceiptChange(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validasi ekstensi/tipe gambar
        if (!file.type.startsWith('image/')) {
            Swal.fire({
                icon: 'warning',
                title: 'Bukan Gambar',
                text: 'Silakan pilih file foto / gambar nota (JPG, PNG, WebP).',
                confirmButtonColor: '#ea580c'
            });
            event.target.value = '';
            return;
        }

        const dropzone = document.getElementById('uploadDropzone');
        const compressBox = document.getElementById('receiptCompressingContainer');
        const previewContainer = document.getElementById('receiptPreviewContainer');
        const statusText = document.getElementById('compressStatusText');
        const sizesText = document.getElementById('compressSizesText');
        const progressBar = document.getElementById('compressProgressBar');

        // Tampilkan state loading kompresi
        dropzone.classList.add('hidden');
        previewContainer.classList.add('hidden');
        compressBox.classList.remove('hidden');

        const originalSizeStr = formatBytes(file.size);
        sizesText.textContent = `Ukuran asli: ${originalSizeStr} ➔ Mengompresi ke ~100 KB...`;
        progressBar.style.width = '30%';

        try {
            statusText.textContent = 'Membaca data dan menganalisis foto nota...';
            await new Promise(r => setTimeout(r, 60));

            progressBar.style.width = '65%';
            const result = await compressImageToTargetSize(file, 100, function(stepMsg) {
                statusText.textContent = stepMsg;
            });

            progressBar.style.width = '100%';
            await new Promise(r => setTimeout(r, 120));

            compressedReceiptFile = result.file;
            compressedReceiptInfo = result;

            // Masukkan file terkompresi ke dalam input form via DataTransfer
            const dt = new DataTransfer();
            dt.items.add(result.file);
            document.getElementById('receiptPhotoInput').files = dt.files;

            // Pasang preview
            if (receiptBlobUrl) {
                URL.revokeObjectURL(receiptBlobUrl);
            }
            receiptBlobUrl = URL.createObjectURL(result.blob);
            document.getElementById('receiptPreviewImg').src = receiptBlobUrl;

            // Tampilkan rincian hasil konversi
            const compSizeStr = formatBytes(result.compressedSize);
            document.getElementById('compressedBadgeSize').textContent = `Terkompresi: ${compSizeStr}`;
            
            const savingsPct = Math.max(0, Math.round(((result.originalSize - result.compressedSize) / result.originalSize) * 100));
            document.getElementById('compressedSavingsText').textContent = savingsPct > 0 
                ? `(Hemat ${savingsPct}% dari ${originalSizeStr})` 
                : `(Ukuran sudah ideal)`;

            compressBox.classList.add('hidden');
            previewContainer.classList.remove('hidden');
            lucide.createIcons();

            // Beri feedback notifikasi toast
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Foto Berhasil Dikonversi!',
                text: `Ukuran foto sekarang: ${compSizeStr}`,
                timer: 2200,
                showConfirmButton: false
            });

        } catch (err) {
            console.error('Error saat kompresi gambar:', err);
            compressBox.classList.add('hidden');
            dropzone.classList.remove('hidden');
            event.target.value = '';
            Swal.fire({
                icon: 'error',
                title: 'Gagal Mengompresi Foto',
                text: 'Terjadi kendala saat memproses gambar. Silakan coba pilih gambar lain.',
                confirmButtonColor: '#ea580c'
            });
        }
    }

    function removeReceiptPhoto(event) {
        if (event && event.stopPropagation) {
            event.stopPropagation();
        }
        document.getElementById('receiptPhotoInput').value = '';
        document.getElementById('receiptPreviewImg').src = '#';
        document.getElementById('receiptPreviewContainer').classList.add('hidden');
        document.getElementById('receiptCompressingContainer').classList.add('hidden');
        document.getElementById('uploadDropzone').classList.remove('hidden');
        if (receiptBlobUrl) {
            URL.revokeObjectURL(receiptBlobUrl);
            receiptBlobUrl = null;
        }
        compressedReceiptFile = null;
        compressedReceiptInfo = null;
    }

    // Modal Kategori Controls
    function openCategoryModal() {
        const modal = document.getElementById('categoryModal');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.remove('translate-y-12');
    }

    function closeCategoryModal() {
        const modal = document.getElementById('categoryModal');
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.add('translate-y-12');
    }

    function filterCategories(query) {
        const q = query.toLowerCase().trim();
        const items = document.querySelectorAll('.category-item');
        items.forEach(item => {
            const name = item.getAttribute('data-name');
            const desc = item.getAttribute('data-desc');
            if (name.includes(q) || desc.includes(q)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function selectCategory(name, icon, subcategories) {
        document.getElementById('selectedCategory').value = name;
        document.getElementById('categoryDisplayText').textContent = name;
        document.getElementById('categoryDisplayText').classList.remove('text-slate-400');
        document.getElementById('categoryDisplayText').classList.add('text-slate-800', 'font-bold');

        // Reset subcategory when category changes
        document.getElementById('selectedSubcategory').value = '';
        document.getElementById('subcategoryDisplayText').textContent = 'Pilih Subkategori';
        document.getElementById('subcategoryDisplayText').classList.add('text-slate-400');
        document.getElementById('subcategoryDisplayText').classList.remove('text-slate-800', 'font-bold');

        currentSelectedCategorySubcategories = subcategories || [];

        closeCategoryModal();

        // Immediately open subcategory modal for smooth UX!
        setTimeout(() => {
            openSubcategoryModal();
        }, 150);
    }

    // Modal Subkategori Controls
    function openSubcategoryModal() {
        const categoryName = document.getElementById('selectedCategory').value;
        if (!categoryName) {
            openCategoryModal();
            return;
        }

        document.getElementById('subcatParentName').textContent = categoryName;
        document.getElementById('subcatParentSub').textContent = 'Pilih subkategori pengeluaran ' + categoryName.toLowerCase();

        const container = document.getElementById('subcategoryItemsContainer');
        container.innerHTML = '';

        if (currentSelectedCategorySubcategories.length === 0) {
            currentSelectedCategorySubcategories = ['Umum', 'Lainnya'];
        }

        currentSelectedCategorySubcategories.forEach(sub => {
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between p-3.5 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors group';
            row.onclick = function() { selectSubcategory(sub); };
            row.innerHTML = `
                <span class="text-sm font-semibold text-slate-800">${sub}</span>
                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 group-hover:text-maroon-800 transition-colors"></i>
            `;
            container.appendChild(row);
        });

        lucide.createIcons();

        const modal = document.getElementById('subcategoryModal');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.remove('translate-y-12');
    }

    function closeSubcategoryModal() {
        const modal = document.getElementById('subcategoryModal');
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.add('translate-y-12');
    }

    function selectSubcategory(sub) {
        document.getElementById('selectedSubcategory').value = sub;
        document.getElementById('subcategoryDisplayText').textContent = sub;
        document.getElementById('subcategoryDisplayText').classList.remove('text-slate-400');
        document.getElementById('subcategoryDisplayText').classList.add('text-slate-800', 'font-bold');

        closeSubcategoryModal();
    }

    // Modal Konfirmasi Pengeluaran Controls
    function prepareConfirmationModal() {
        const dateVal = document.getElementById('dateInput').value;
        const categoryVal = document.getElementById('selectedCategory').value;
        const subcategoryVal = document.getElementById('selectedSubcategory').value;
        const purposeVal = document.getElementById('purposeInput').value.trim();
        const amountVal = document.getElementById('amountInput').value.trim();
        const notesVal = document.getElementById('notesInput').value.trim();

        if (!categoryVal) {
            Swal.fire({ icon: 'warning', title: 'Pilih Kategori', text: 'Silakan pilih kategori pengeluaran terlebih dahulu.', confirmButtonColor: '#ea580c' });
            return;
        }
        if (!subcategoryVal) {
            Swal.fire({ icon: 'warning', title: 'Pilih Subkategori', text: 'Silakan pilih subkategori pengeluaran terlebih dahulu.', confirmButtonColor: '#ea580c' });
            return;
        }
        if (!purposeVal) {
            Swal.fire({ icon: 'warning', title: 'Isi Keperluan', text: 'Silakan tuliskan keperluan pengeluaran.', confirmButtonColor: '#ea580c' });
            return;
        }
        if (!amountVal || amountVal === 'Rp 0') {
            Swal.fire({ icon: 'warning', title: 'Isi Nominal', text: 'Silakan masukkan nominal pengeluaran yang valid.', confirmButtonColor: '#ea580c' });
            return;
        }

        // Format tanggal bahasa Indonesia
        const d = new Date(dateVal);
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        document.getElementById('confirmDate').textContent = d.toLocaleDateString('id-ID', options);
        document.getElementById('confirmCategory').textContent = categoryVal;
        document.getElementById('confirmSubcategory').textContent = subcategoryVal;
        document.getElementById('confirmPurpose').textContent = purposeVal;
        document.getElementById('confirmAmount').textContent = amountVal;
        
        const paymentMethodVal = document.querySelector('input[name="payment_method"]:checked')?.value || 'Omzet Kandang';
        document.getElementById('confirmPaymentMethod').textContent = paymentMethodVal;

        document.getElementById('confirmNotes').textContent = notesVal || '-';

        const receiptInput = document.getElementById('receiptPhotoInput');
        if (receiptInput.files && receiptInput.files[0]) {
            document.getElementById('confirmReceiptText').classList.add('hidden');
            const wrapper = document.getElementById('confirmReceiptWrapper');
            const badge = document.getElementById('confirmReceiptBadge');
            const previewImg = document.getElementById('confirmReceiptImg');
            
            previewImg.src = receiptBlobUrl;
            badge.textContent = compressedReceiptInfo ? ('~' + formatBytes(compressedReceiptInfo.compressedSize)) : '~100 KB';
            wrapper.classList.remove('hidden');
        } else {
            document.getElementById('confirmReceiptText').classList.remove('hidden');
            document.getElementById('confirmReceiptWrapper').classList.add('hidden');
        }

        const modal = document.getElementById('confirmationModal');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.remove('translate-y-12');
    }

    function closeConfirmationModal() {
        const modal = document.getElementById('confirmationModal');
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('.bg-white').classList.add('translate-y-12');
    }

    // Submit Expense Form via AJAX
    function submitExpenseForm() {
        const form = document.getElementById('expenseForm');
        const formData = new FormData(form);
        const btn = document.getElementById('btnFinalSubmit');

        btn.disabled = true;
        btn.innerHTML = `<span class="animate-spin mr-2">&#9696;</span> Menyimpan...`;

        fetch("{{ route('expenses.store') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = `<span>Simpan</span>`;

            if (res.success) {
                closeConfirmationModal();

                // Open Screen 5: Success Modal
                document.getElementById('successCode').textContent = res.data.transaction_code;
                document.getElementById('successAmount').textContent = res.data.formatted_amount;
                document.getElementById('successPurpose').textContent = res.data.purpose;

                const successModal = document.getElementById('successModal');
                successModal.classList.remove('opacity-0', 'pointer-events-none');
                successModal.querySelector('div').classList.remove('scale-95');
                successModal.querySelector('div').classList.add('scale-100');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: res.message || 'Terjadi kesalahan pada sistem.',
                    confirmButtonColor: '#ea580c'
                });
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = `<span>Simpan</span>`;
            console.error('Error:', err);
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Bermasalah',
                text: 'Gagal menghubungi server.',
                confirmButtonColor: '#ea580c'
            });
        });
    }

    function resetFormForNewExpense() {
        document.getElementById('expenseForm').reset();
        document.getElementById('selectedCategory').value = '';
        document.getElementById('categoryDisplayText').textContent = 'Pilih Kategori';
        document.getElementById('categoryDisplayText').classList.add('text-slate-400');
        document.getElementById('categoryDisplayText').classList.remove('text-slate-800', 'font-bold');

        document.getElementById('selectedSubcategory').value = '';
        document.getElementById('subcategoryDisplayText').textContent = 'Pilih Subkategori';
        document.getElementById('subcategoryDisplayText').classList.add('text-slate-400');
        document.getElementById('subcategoryDisplayText').classList.remove('text-slate-800', 'font-bold');

        document.getElementById('charCounter').textContent = '0/200';
        removeReceiptPhoto(new Event('dummy'));

        // Close success modal
        const successModal = document.getElementById('successModal');
        successModal.classList.add('opacity-0', 'pointer-events-none');
        successModal.querySelector('div').classList.remove('scale-100');
        successModal.querySelector('div').classList.add('scale-95');
    }
</script>
@endpush
