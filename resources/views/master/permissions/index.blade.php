@extends('layouts.app')

@section('title', 'Manajemen Hak Akses & Pengguna - NOCHI FARM')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">
                <i data-lucide="shield-check" class="w-4 h-4 text-nochi-orange"></i>
                <span>Super Administrator</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Manajemen Hak Akses & Pengguna</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola izin login, visibilitas menu navigasi, dan hak akses aksi (lihat, catat, ubah, hapus) seluruh pengguna sistem pengeluaran.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="openAddUserModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-nochi-orange hover:bg-nochi-orangeDark text-white rounded-xl text-xs sm:text-sm font-bold shadow-md transition-all active:scale-95">
                <i data-lucide="user-plus" class="w-4 h-4 stroke-[2.5]"></i>
                <span>+ Tambah Pengguna Baru</span>
            </button>
        </div>
    </div>

    <!-- Main Two-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- ========================================================================= -->
        <!-- KOLOM KIRI: Daftar Pengguna (lg:col-span-4) -->
        <!-- ========================================================================= -->
        <div class="lg:col-span-4 space-y-4">
            
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xs p-4 space-y-3">
                
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Daftar Pengguna Farm</span>
                    <span class="px-2 py-0.5 bg-slate-100 rounded-full text-[11px] font-bold text-slate-600">{{ $users->count() }} User</span>
                </div>

                <!-- Search Input User -->
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" id="searchUserBox" onkeyup="filterUsersList()" placeholder="Cari nama atau username..."
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-maroon-800">
                </div>

                <!-- Users List -->
                <div class="space-y-1.5 max-h-[600px] overflow-y-auto pr-1" id="usersListContainer">
                    @foreach($users as $u)
                        @php
                            $isSelected = $selectedUser && $selectedUser->id === $u->id;
                            $canLogin = \App\Services\ExpensePermissionService::canLogin($u);
                        @endphp
                        <a href="{{ route('master.permissions', ['user_id' => $u->id]) }}"
                           class="user-item-card flex items-center justify-between p-3 rounded-xl transition-all border {{ $isSelected ? 'bg-rose-50/80 border-rose-300 shadow-xs' : 'bg-white hover:bg-slate-50 border-slate-100' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <!-- User Avatar / Initials -->
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-sm shrink-0 {{ $u->role === 'admin' ? 'bg-maroon-800 text-white' : 'bg-slate-200 text-slate-700' }}">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <h4 class="font-extrabold text-xs text-slate-800 truncate user-item-name">{{ $u->name }}</h4>
                                        @if($u->role === 'admin')
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase bg-rose-100 text-maroon-900 border border-rose-200">Admin</span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-slate-100 text-slate-600">Staf</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400 truncate user-item-username">&#64;{{ $u->username ?: 'user' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <!-- Status Login Indicator -->
                                @if($u->role === 'admin')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200" title="Admin memiliki akses penuh">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                        <span>Full</span>
                                    </span>
                                @elseif($canLogin)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200" title="Boleh login ke pengeluaran">
                                        <i data-lucide="shield-check" class="w-3 h-3"></i>
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200" title="Terkunci / Belum boleh login">
                                        <i data-lucide="lock" class="w-3 h-3"></i>
                                        <span>Terkunci</span>
                                    </span>
                                @endif
                                
                                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300"></i>
                            </div>
                        </a>
                    @endforeach
                </div>

            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- KOLOM KANAN: Panel Konfigurasi Izin User Terpilih (lg:col-span-8) -->
        <!-- ========================================================================= -->
        <div class="lg:col-span-8 space-y-5">
            
            @if($selectedUser)
                <!-- Selected User Banner Card -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-xs p-5 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-xl font-black shrink-0 {{ $selectedUser->role === 'admin' ? 'bg-maroon-800 text-white shadow-md shadow-maroon-900/20' : 'bg-slate-200 text-slate-700' }}">
                                {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h2 class="text-lg font-black text-slate-800">{{ $selectedUser->name }}</h2>
                                    @if($selectedUser->role === 'admin')
                                        <span class="px-2 py-0.5 rounded-md text-xs font-black uppercase bg-maroon-100 text-maroon-900 border border-maroon-200">
                                            Administrator (Full Access)
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md text-xs font-bold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                            Pengguna / Staf
                                        </span>
                                    @endif
                                    
                                    @if($selectedUser->is_active)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Akun Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Akun Dinonaktifkan
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400 mt-1">
                                    &#64;{{ $selectedUser->username ?: '-' }} &bull; {{ $selectedUser->email }} &bull; Telp: {{ $selectedUser->phone ?: '-' }}
                                </p>
                            </div>
                        </div>

                        <!-- User Management Quick Actions -->
                        <div class="flex items-center gap-2 self-start sm:self-auto">
                            <button type="button" onclick="openEditUserModal({{ json_encode($selectedUser) }})"
                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                <span>Edit Akun</span>
                            </button>

                            @if($selectedUser->id !== auth()->id())
                                <form action="{{ route('master.users.toggle-active', $selectedUser->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="px-3 py-1.5 {{ $selectedUser->is_active ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200' }} rounded-xl text-xs font-bold transition-all">
                                        {{ $selectedUser->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Role Admin Notice OR Bulk Actions for Non-Admin -->
                    @if($selectedUser->role === 'admin')
                        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-3">
                            <i data-lucide="info" class="w-5 h-5 text-amber-600 shrink-0"></i>
                            <div>
                                <span class="font-bold block">Akun Administrator Memiliki Akses Penuh Permanen</span>
                                <span class="text-amber-700">Administrator dapat mengakses semua halaman, tab navigasi, tombol tambah/edit/hapus, serta seluruh menu tanpa batasan toggle.</span>
                            </div>
                        </div>
                    @else
                        <!-- Bulk Actions Toolbar -->
                        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2.5">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aksi Cepat:</span>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <!-- Grant All -->
                                <form action="{{ route('master.permissions.bulk', $selectedUser->id) }}" method="POST" onsubmit="return confirm('Berikan seluruh hak akses dan izin login untuk {{ $selectedUser->name }}?');">
                                    @csrf
                                    <input type="hidden" name="action" value="grant_all">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold flex items-center gap-1.5 transition-all">
                                        <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
                                        <span>Aktifkan Semua Izin</span>
                                    </button>
                                </form>

                                <!-- Revoke All -->
                                <form action="{{ route('master.permissions.bulk', $selectedUser->id) }}" method="POST" onsubmit="return confirm('Kunci akun dan matikan seluruh hak akses untuk {{ $selectedUser->name }}? User ini tidak akan bisa login.');">
                                    @csrf
                                    <input type="hidden" name="action" value="revoke_all">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold flex items-center gap-1.5 transition-all">
                                        <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                                        <span>Kunci / Cabut Semua</span>
                                    </button>
                                </form>

                                <!-- Reset Default -->
                                <form action="{{ route('master.permissions.bulk', $selectedUser->id) }}" method="POST" onsubmit="return confirm('Reset seluruh hak akses {{ $selectedUser->name }} ke nilai default?');">
                                    @csrf
                                    <input type="hidden" name="action" value="reset_default">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold flex items-center gap-1.5 transition-all">
                                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                        <span>Reset Default</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- ================================================================= -->
                <!-- PERMISSION CATEGORIES ACCORDION / CARDS -->
                <!-- ================================================================= -->
                <div class="space-y-4">
                    @foreach($allPermissions as $catKey => $category)
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
                            
                            <!-- Category Header -->
                            <div class="p-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-white text-maroon-800 flex items-center justify-center border border-slate-200 shadow-2xs">
                                        <i data-lucide="{{ $category['icon'] ?? 'settings' }}" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-extrabold text-xs sm:text-sm text-slate-800">{{ $category['label'] }}</h3>
                                        <span class="text-[10px] text-slate-400">{{ count($category['items']) }} Hak Akses Fitur</span>
                                    </div>
                                </div>

                                @if($selectedUser->role !== 'admin')
                                    <div class="flex items-center gap-1">
                                        <!-- Category Grant Bulk -->
                                        <form action="{{ route('master.permissions.bulk', $selectedUser->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="action" value="grant_category">
                                            <input type="hidden" name="category_key" value="{{ $catKey }}">
                                            <button type="submit" class="px-2 py-1 rounded-lg text-[10px] font-bold text-emerald-700 hover:bg-emerald-50 transition-colors" title="Buka semua izin pada kategori ini">
                                                Buka Semua
                                            </button>
                                        </form>
                                        <span class="text-slate-300 text-xs">&bull;</span>
                                        <!-- Category Revoke Bulk -->
                                        <form action="{{ route('master.permissions.bulk', $selectedUser->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="action" value="revoke_category">
                                            <input type="hidden" name="category_key" value="{{ $catKey }}">
                                            <button type="submit" class="px-2 py-1 rounded-lg text-[10px] font-bold text-rose-700 hover:bg-rose-50 transition-colors" title="Tutup semua izin pada kategori ini">
                                                Kunci Semua
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <!-- Category Items List -->
                            <div class="divide-y divide-slate-100">
                                @foreach($category['items'] as $itemKey => $item)
                                    @php
                                        $isEnabled = $selectedUser->role === 'admin' ? true : ($userPermissionsMap[$itemKey] ?? $item['default']);
                                    @endphp
                                    <div class="p-4 flex items-center justify-between gap-4 hover:bg-slate-50/50 transition-colors">
                                        <div class="space-y-0.5 pr-2">
                                            <div class="flex items-center gap-2">
                                                <label for="toggle-{{ $itemKey }}" class="font-bold text-xs sm:text-sm text-slate-800 cursor-pointer">
                                                    {{ $item['label'] }}
                                                </label>
                                                @if($itemKey === 'app_login_access')
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-black uppercase bg-orange-100 text-nochi-orange border border-orange-200">
                                                        Kunci Utama Login
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-[11px] text-slate-500 leading-relaxed">{{ $item['desc'] }}</p>
                                        </div>

                                        <div class="shrink-0 flex items-center">
                                            @if($selectedUser->role === 'admin')
                                                <!-- Admin Always Enabled Badge -->
                                                <div class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-extrabold flex items-center gap-1.5 select-none" title="Admin memiliki akses penuh secara permanen">
                                                    <i data-lucide="check" class="w-3.5 h-3.5 stroke-[3]"></i>
                                                    <span>Selalu Aktif</span>
                                                </div>
                                            @else
                                                <!-- Modern iOS-Style Toggle Switch -->
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox"
                                                           id="toggle-{{ $itemKey }}"
                                                           class="sr-only peer"
                                                           {{ $isEnabled ? 'checked' : '' }}
                                                           onchange="handlePermissionToggle({{ $selectedUser->id }}, '{{ $itemKey }}', this)">
                                                    <div class="w-12 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[3px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600 peer-checked:shadow-sm"></div>
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @endforeach
                </div>

            @else
                <div class="bg-white rounded-2xl border border-slate-100 shadow-xs p-12 text-center text-slate-400 space-y-2">
                    <i data-lucide="user-x" class="w-10 h-10 mx-auto text-slate-300"></i>
                    <p class="font-bold text-slate-600">Tidak ada pengguna terpilih</p>
                    <p class="text-xs text-slate-400">Pilih salah satu pengguna dari kolom kiri untuk mengatur hak aksesnya.</p>
                </div>
            @endif

        </div>

    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL TAMBAH PENGGUNA BARU -->
<!-- ========================================================================= -->
<div id="addUserModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden p-6 space-y-4 transform transition-transform duration-300 scale-95">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-orange-50 text-nochi-orange flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </div>
                <h3 class="font-extrabold text-base text-slate-800">Tambah Pengguna Baru</h3>
            </div>
            <button type="button" onclick="closeAddUserModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('master.users.store') }}" method="POST" class="space-y-3.5">
            @csrf

            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Username</label>
                    <input type="text" name="username" required placeholder="budi_farm"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Peran (Role)</label>
                    <select name="role" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
                        <option value="user" selected>Pengguna / Staf (Wajib Izin)</option>
                        <option value="admin">Administrator (Full Access)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Email</label>
                <input type="email" name="email" required placeholder="budi@nochifarm.com"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Password</label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">No. WhatsApp / HP</label>
                    <input type="text" name="phone" placeholder="08123456789"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
                </div>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeAddUserModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-nochi-orange hover:bg-nochi-orangeDark text-white rounded-xl text-xs font-bold shadow-md transition-all">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT PENGGUNA -->
<!-- ========================================================================= -->
<div id="editUserModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden p-6 space-y-4 transform transition-transform duration-300 scale-95">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="user-check" class="w-4 h-4"></i>
                </div>
                <h3 class="font-extrabold text-base text-slate-800">Edit Data Pengguna</h3>
            </div>
            <button type="button" onclick="closeEditUserModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="editUserForm" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Nama Lengkap</label>
                <input type="text" id="edit_name" name="name" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Username</label>
                    <input type="text" id="edit_username" name="username" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Peran (Role)</label>
                    <select id="edit_role" name="role" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
                        <option value="user">Pengguna / Staf</option>
                        <option value="admin">Administrator (Full Access)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Email</label>
                <input type="email" id="edit_email" name="email" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Password Baru (Opsional)</label>
                    <input type="password" name="password" minlength="6" placeholder="Kosongkan jika tetap"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">No. WhatsApp / HP</label>
                    <input type="text" id="edit_phone" name="phone" placeholder="08123456789"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-maroon-800">
                </div>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeEditUserModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-maroon-800 hover:bg-maroon-900 text-white rounded-xl text-xs font-bold shadow-md transition-all">
                    Perbarui Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // AJAX Toggle Switch Hak Akses Real-Time
    function handlePermissionToggle(userId, permissionKey, checkboxElement) {
        const isEnabled = checkboxElement.checked;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Optimistic UI state
        checkboxElement.disabled = true;

        fetch(`/master/hak-akses/${userId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                permission_key: permissionKey,
                is_enabled: isEnabled,
            })
        })
        .then(response => response.json())
        .then(data => {
            checkboxElement.disabled = false;
            if (data.success) {
                // Tampilkan Toast feedback ringan
                if (typeof Swal !== 'undefined') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                    Toast.fire({
                        icon: isEnabled ? 'success' : 'info',
                        title: data.message
                    });
                }
            } else {
                checkboxElement.checked = !isEnabled; // Revert
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengubah Izin',
                        text: data.message || 'Terjadi kesalahan sistem.',
                    });
                } else {
                    alert(data.message || 'Gagal mengubah izin.');
                }
            }
        })
        .catch(err => {
            checkboxElement.disabled = false;
            checkboxElement.checked = !isEnabled; // Revert
            console.error(err);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Gagal terhubung ke server database.',
                });
            }
        });
    }

    // Filter daftar user di kolom kiri
    function filterUsersList() {
        const query = document.getElementById('searchUserBox').value.toLowerCase();
        const cards = document.querySelectorAll('.user-item-card');

        cards.forEach(card => {
            const name = card.querySelector('.user-item-name')?.textContent.toLowerCase() || '';
            const username = card.querySelector('.user-item-username')?.textContent.toLowerCase() || '';

            if (name.includes(query) || username.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Modal Handlers
    function openAddUserModal() {
        const modal = document.getElementById('addUserModal');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('div').classList.remove('scale-95');
        modal.querySelector('div').classList.add('scale-100');
    }

    function closeAddUserModal() {
        const modal = document.getElementById('addUserModal');
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('div').classList.remove('scale-100');
        modal.querySelector('div').classList.add('scale-95');
    }

    function openEditUserModal(user) {
        const modal = document.getElementById('editUserModal');
        const form = document.getElementById('editUserForm');
        form.action = `/master/users/${user.id}/update`;

        document.getElementById('edit_name').value = user.name || '';
        document.getElementById('edit_username').value = user.username || '';
        document.getElementById('edit_email').value = user.email || '';
        document.getElementById('edit_phone').value = user.phone || '';
        document.getElementById('edit_role').value = user.role || 'user';

        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('div').classList.remove('scale-95');
        modal.querySelector('div').classList.add('scale-100');
    }

    function closeEditUserModal() {
        const modal = document.getElementById('editUserModal');
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('div').classList.remove('scale-100');
        modal.querySelector('div').classList.add('scale-95');
    }
</script>
@endpush
@endsection
