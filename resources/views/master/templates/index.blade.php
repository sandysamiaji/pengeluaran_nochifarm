@extends('layouts.app')

@section('title', 'Master Template Pengeluaran Rutin - NOCHI FARM')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">
                <i data-lucide="zap" class="w-4 h-4 text-nochi-orange"></i>
                <span>Data Master Sistem</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Master Template Pengeluaran</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola daftar pengeluaran rutin 1-klik yang muncul pada formulir pencatatan kandang.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('expenses.create') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all">
                &larr; Ke Form Pengeluaran
            </a>
            @canExpense('template_create')
            <button type="button" onclick="openAddModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-nochi-orange hover:bg-nochi-orangeDark text-white rounded-xl text-xs sm:text-sm font-bold shadow-md transition-all active:scale-95">
                <i data-lucide="plus" class="w-4 h-4 stroke-[3]"></i>
                <span>+ Tambah Template Baru</span>
            </button>
            @endcanExpense
        </div>
    </div>

    <!-- Templates List Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Template</th>
                        <th class="py-3 px-4">Kategori & Subkategori</th>
                        <th class="py-3 px-4">Keperluan Default</th>
                        <th class="py-3 px-4 text-right">Nominal Default</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($templates as $idx => $tmpl)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-3.5 px-4 text-center text-slate-400 font-bold">
                            {{ $idx + 1 }}
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-800">
                            <span class="text-sm block">{{ $tmpl->name }}</span>
                            @if($tmpl->notes)
                                <span class="text-[11px] text-slate-400 font-normal line-clamp-1 mt-0.5">{{ $tmpl->notes }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-block px-2.5 py-0.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                {{ $tmpl->category }}
                            </span>
                            <span class="text-[11px] text-slate-500 block mt-0.5 font-medium">{{ $tmpl->subcategory }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-700">
                            {{ $tmpl->purpose }}
                        </td>
                        <td class="py-3.5 px-4 text-right whitespace-nowrap font-black text-rose-600 text-sm">
                            {{ $tmpl->formatted_amount }}
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            @canExpense('template_toggle')
                            <form action="{{ route('master.templates.toggle', $tmpl->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $tmpl->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}" title="Klik untuk mengubah status">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $tmpl->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    <span>{{ $tmpl->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </button>
                            </form>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $tmpl->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $tmpl->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                <span>{{ $tmpl->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </span>
                            @endcanExpense
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-1">
                                @canExpense('template_edit')
                                <button type="button" onclick="openEditModal({{ json_encode($tmpl) }})"
                                    class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors" title="Edit Template">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </button>
                                @endcanExpense

                                @canExpense('template_delete')
                                <form action="{{ route('master.templates.destroy', $tmpl->id) }}" method="POST" onsubmit="return confirm('Hapus template ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors" title="Hapus Template">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @endcanExpense
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <p class="font-bold text-slate-600">Belum ada template pengeluaran</p>
                            <p class="text-xs text-slate-400 mt-1">Tambahkan template untuk mempercepat pencatatan pengeluaran rutin.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Tambah Template -->
<div id="addTemplateModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden p-6 space-y-4 transform transition-transform duration-300 scale-95">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-base text-slate-800">Tambah Template Pengeluaran</h3>
            <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('master.templates.store') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Tombol / Label</label>
                <input type="text" name="name" placeholder="Contoh: 🌾 Pakan Layer (50 Sak)" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
                    <select id="addCatSelect" name="category" onchange="updateAddSubcategories()" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $c)
                            <option value="{{ $c['name'] }}">{{ $c['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Subkategori</label>
                    <select id="addSubcatSelect" name="subcategory" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
                        <option value="">Pilih Subkategori</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keperluan</label>
                <input type="text" name="purpose" placeholder="Contoh: Pembelian pakan layer 50 sak" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Default (Rp)</label>
                <input type="number" name="amount" placeholder="Contoh: 4000000" required min="0"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-black focus:outline-none focus:ring-2 focus:ring-maroon-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Catatan opsional..."
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium resize-none"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-slate-500 font-bold text-xs hover:bg-slate-100 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-nochi-orange hover:bg-nochi-orangeDark text-white font-bold text-xs rounded-xl shadow-md">Simpan Template</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Template -->
<div id="editTemplateModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden p-6 space-y-4 transform transition-transform duration-300 scale-95">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-base text-slate-800">Edit Template Pengeluaran</h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="editForm" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Tombol / Label</label>
                <input type="text" id="editName" name="name" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
                    <select id="editCatSelect" name="category" onchange="updateEditSubcategories()" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
                        @foreach($categories as $c)
                            <option value="{{ $c['name'] }}">{{ $c['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Subkategori</label>
                    <select id="editSubcatSelect" name="subcategory" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keperluan</label>
                <input type="text" id="editPurpose" name="purpose" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Default (Rp)</label>
                <input type="number" id="editAmount" name="amount" required min="0"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-black focus:outline-none focus:ring-2 focus:ring-maroon-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan (Opsional)</label>
                <textarea id="editNotes" name="notes" rows="2"
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium resize-none"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-slate-500 font-bold text-xs hover:bg-slate-100 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-nochi-orange hover:bg-nochi-orangeDark text-white font-bold text-xs rounded-xl shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const categoriesData = {!! json_encode($categories) !!};

    function openAddModal() {
        const modal = document.getElementById('addTemplateModal');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('div').classList.remove('scale-95');
    }

    function closeAddModal() {
        const modal = document.getElementById('addTemplateModal');
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('div').classList.add('scale-95');
    }

    function updateAddSubcategories() {
        const catName = document.getElementById('addCatSelect').value;
        const subSelect = document.getElementById('addSubcatSelect');
        subSelect.innerHTML = '<option value="">Pilih Subkategori</option>';

        const found = categoriesData.find(c => c.name === catName);
        if (found && found.subcategories) {
            found.subcategories.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s;
                opt.textContent = s;
                subSelect.appendChild(opt);
            });
        }
    }

    function openEditModal(tmpl) {
        document.getElementById('editForm').action = `/master/templates/${tmpl.id}`;
        document.getElementById('editName').value = tmpl.name;
        document.getElementById('editCatSelect').value = tmpl.category;
        updateEditSubcategories(tmpl.subcategory);
        document.getElementById('editPurpose').value = tmpl.purpose;
        document.getElementById('editAmount').value = parseInt(tmpl.amount, 10);
        document.getElementById('editNotes').value = tmpl.notes || '';

        const modal = document.getElementById('editTemplateModal');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('div').classList.remove('scale-95');
    }

    function closeEditModal() {
        const modal = document.getElementById('editTemplateModal');
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('div').classList.add('scale-95');
    }

    function updateEditSubcategories(selectedSub = null) {
        const catName = document.getElementById('editCatSelect').value;
        const subSelect = document.getElementById('editSubcatSelect');
        subSelect.innerHTML = '<option value="">Pilih Subkategori</option>';

        const found = categoriesData.find(c => c.name === catName);
        if (found && found.subcategories) {
            found.subcategories.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s;
                opt.textContent = s;
                if (selectedSub && selectedSub === s) {
                    opt.selected = true;
                }
                subSelect.appendChild(opt);
            });
        }
    }
</script>
@endpush
