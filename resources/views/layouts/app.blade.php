<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NOCHI FARM - Pengeluaran & Transaksi Kandang')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-nochi.png') }}">

    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: {
                            50: '#fdf2f4',
                            100: '#fbe6e9',
                            200: '#f7d0d6',
                            300: '#f0aab5',
                            400: '#e5788a',
                            500: '#d34d64',
                            600: '#b8324b',
                            700: '#9b243b',
                            800: '#800020', // Primary Deep Maroon
                            900: '#6d1323',
                            950: '#400610',
                        },
                        nochi: {
                            orange: '#f95721',
                            orangeDark: '#e04512',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js for Financial Trends & Breakdown (Local Asset with Fallback) -->
    <script src="{{ asset('js/chart.umd.min.js') }}"></script>
    <script>window.Chart || document.write('<script src="https://unpkg.com/chart.js/dist/chart.umd.js"><\/script>')</script>

    <style>
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            -webkit-tap-highlight-color: transparent;
        }

        /* Deep Maroon Gradient */
        .bg-maroon-gradient {
            background: linear-gradient(135deg, #520b16 0%, #800020 50%, #991b1b 100%);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 6px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .farm-card {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
            border-radius: 16px;
        }

        /* Traveloka Range Highlight Styles */
        .calendar-day-in-range {
            background-color: #fed7aa !important;
            color: #9a3412 !important;
            border-radius: 0 !important;
        }
        .calendar-day-range-start {
            background-color: #f95721 !important;
            color: #ffffff !important;
            border-top-left-radius: 9999px !important;
            border-bottom-left-radius: 9999px !important;
        }
        .calendar-day-range-end {
            background-color: #f95721 !important;
            color: #ffffff !important;
            border-top-right-radius: 9999px !important;
            border-bottom-right-radius: 9999px !important;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col antialiased bg-slate-50 text-slate-800">

    <!-- Top Loading Progress Bar SPA -->
    <div id="spaProgressBar" class="fixed top-0 left-0 h-1 bg-gradient-to-r from-amber-400 via-nochi-orange to-rose-600 z-[9999] transition-all duration-300 pointer-events-none opacity-0 shadow-sm shadow-orange-500/50" style="width: 0%;"></div>

    <!-- Top App Bar / Header (Presisi Sesuai Mockup) -->
    <header class="bg-maroon-gradient text-white sticky top-0 z-30 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                
                <!-- Logo & Brand Header -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/10 p-1 flex items-center justify-center border border-white/20 shadow-inner group-hover:scale-105 transition-transform">
                            <img src="{{ asset('images/logo-nochi.png') }}" alt="Logo Nochi Farm" class="w-full h-full object-contain">
                        </div>
                        <div class="leading-tight">
                            <span class="text-base sm:text-xl font-black tracking-tight text-white block uppercase">
                                NOCHI FARM
                            </span>
                            <span class="text-[10px] sm:text-xs text-orange-200 font-semibold tracking-wider block uppercase">
                                PETERNAK AYAM PETELUR
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Menu -->
                <nav id="desktopNavMenu" class="hidden md:flex items-center gap-1.5 bg-black/15 p-1 rounded-xl backdrop-blur-sm border border-white/10">
                    @canExpense('menu_dashboard')
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>
                    @endcanExpense

                    @canExpense('menu_expenses_create')
                    <a href="{{ route('expenses.create') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('expenses.create') ? 'bg-nochi-orange text-white shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Catat Pengeluaran</span>
                    </a>
                    @endcanExpense

                    @canExpense('menu_expenses_index')
                    <a href="{{ route('expenses.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('expenses.index') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="receipt" class="w-4 h-4"></i>
                        <span>Riwayat Pengeluaran</span>
                    </a>
                    @endcanExpense

                    @canExpense('menu_production')
                    <a href="{{ route('production.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('production.*') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="egg" class="w-4 h-4"></i>
                        <span>Produksi Telur</span>
                    </a>
                    @endcanExpense

                    @canExpense('menu_master_templates')
                    <a href="{{ route('master.templates.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('master.templates.*') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                        <span>Master Template</span>
                    </a>
                    @endcanExpense

                    @if(auth()->check() && auth()->user()->role === 'admin')
                    <a href="{{ route('master.permissions') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('master.permissions*') ? 'bg-amber-400 text-maroon-950 shadow-sm' : 'text-amber-200 hover:text-white hover:bg-white/10' }} transition-all" title="Kelola Hak Akses Pengguna">
                        <i data-lucide="shield-check" class="w-4 h-4 text-amber-300"></i>
                        <span>Hak Akses</span>
                    </a>
                    @endif
                </nav>

                <!-- Right Header Actions (User Dropdown & Burger Button) -->
                <div class="flex items-center gap-2.5">
                    @canExpense('menu_expenses_create')
                    <a href="{{ route('expenses.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-nochi-orange hover:bg-nochi-orangeDark text-white text-xs font-bold shadow-md hover:shadow-lg transition-all active:scale-95">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Pengeluaran Baru</span>
                    </a>
                    @endcanExpense

                    @if(auth()->check())
                    <!-- User Profile Dropdown Desktop -->
                    <div class="relative hidden sm:inline-block text-left" id="userMenuContainer">
                        <button type="button" onclick="toggleUserDropdown()"
                            class="flex items-center gap-2 p-1.5 pr-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 transition-all text-white focus:outline-none">
                            <div class="w-7 h-7 rounded-lg bg-orange-500 text-white font-extrabold flex items-center justify-center text-xs shadow-inner">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="text-left leading-none max-w-[120px] truncate">
                                <span class="text-xs font-bold block truncate">{{ auth()->user()->name }}</span>
                                <span class="text-[9px] text-orange-200 font-semibold uppercase">{{ auth()->user()->role === 'admin' ? 'Admin' : 'Staf' }}</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-white/70"></i>
                        </button>

                        <div id="userDropdownMenu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 text-slate-800 z-50 animate-in fade-in zoom-in-95 duration-100">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-slate-400 font-mono truncate">&#64;{{ auth()->user()->username ?: 'user' }}</p>
                                <div class="mt-1">
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-black uppercase {{ auth()->user()->role === 'admin' ? 'bg-maroon-100 text-maroon-900' : 'bg-slate-100 text-slate-700' }}">
                                        {{ auth()->user()->role === 'admin' ? 'Administrator' : 'Pengguna Farm' }}
                                    </span>
                                </div>
                            </div>

                            @if(auth()->user()->role === 'admin')
                            <a href="{{ route('master.permissions') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-rose-50 hover:text-maroon-900 transition-colors">
                                <i data-lucide="shield-check" class="w-4 h-4 text-nochi-orange"></i>
                                <span>Manajemen Hak Akses</span>
                            </a>
                            @endif

                            <div class="border-t border-slate-100 my-1"></div>

                            <form action="{{ route('logout') }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors text-left">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Keluar / Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    <!-- Hamburger Button Mobile -->
                    <button onclick="toggleMobileDrawer()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 transition-all flex items-center justify-center text-white border border-white/15" title="Menu Navigasi">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Drawer Menu Mobile (Slide from right) -->
    <div id="mobileDrawerBackdrop" class="fixed inset-0 bg-slate-900/60 z-50 opacity-0 pointer-events-none transition-opacity duration-300 backdrop-blur-xs" onclick="toggleMobileDrawer()"></div>
    <div id="mobileDrawer" class="fixed top-0 right-0 h-full w-80 max-w-[85vw] bg-white z-50 shadow-2xl translate-x-full transition-transform duration-300 flex flex-col">
        <div class="p-5 bg-maroon-gradient text-white flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo-nochi.png') }}" alt="Logo" class="w-9 h-9 object-contain">
                <div>
                    <h3 class="font-extrabold text-sm tracking-wide">NOCHI FARM</h3>
                    <p class="text-[10px] text-orange-200">Sistem Keuangan & Pengeluaran</p>
                </div>
            </div>
            <button onclick="toggleMobileDrawer()" class="text-white/80 hover:text-white p-1 rounded-lg">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        @if(auth()->check())
        <!-- Current User Profile in Drawer -->
        <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-maroon-800 text-white font-extrabold flex items-center justify-center text-sm shadow-xs">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-black text-slate-800 truncate">{{ auth()->user()->name }}</p>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase {{ auth()->user()->role === 'admin' ? 'bg-rose-100 text-maroon-900' : 'bg-slate-200 text-slate-700' }}">
                        {{ auth()->user()->role === 'admin' ? 'Admin' : 'Staf' }}
                    </span>
                    <span class="text-[10px] text-slate-400 font-mono truncate">&#64;{{ auth()->user()->username ?: 'user' }}</span>
                </div>
            </div>
        </div>
        @endif

        <div id="mobileDrawerMenu" class="p-4 flex-1 overflow-y-auto space-y-1.5">
            @canExpense('menu_dashboard')
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-rose-50 text-maroon-800' : 'text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="home" class="w-4 h-4 text-maroon-700"></i>
                <span>Beranda & Transaksi</span>
            </a>
            @endcanExpense

            @canExpense('menu_production')
            <a href="{{ route('production.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('production.*') ? 'bg-rose-50 text-maroon-800' : 'text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="egg" class="w-4 h-4 text-amber-600"></i>
                <span>Produksi Telur</span>
            </a>
            @endcanExpense

            @canExpense('menu_expenses_create')
            <a href="{{ route('expenses.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('expenses.create') ? 'bg-orange-50 text-nochi-orange' : 'text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="plus-circle" class="w-4 h-4 text-nochi-orange"></i>
                <span>Catat Pengeluaran Kandang</span>
            </a>
            @endcanExpense

            @canExpense('menu_expenses_index')
            <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('expenses.index') ? 'bg-rose-50 text-maroon-800' : 'text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="receipt" class="w-4 h-4 text-maroon-700"></i>
                <span>Daftar Pengeluaran</span>
            </a>
            @endcanExpense

            @canExpense('menu_master_templates')
            <a href="{{ route('master.templates.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('master.templates.*') ? 'bg-rose-50 text-maroon-800' : 'text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="settings" class="w-4 h-4 text-maroon-700"></i>
                <span>Master Template Pengeluaran</span>
            </a>
            @endcanExpense

            @if(auth()->check() && auth()->user()->role === 'admin')
            <div class="pt-2 my-2 border-t border-slate-100">
                <span class="px-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Pengaturan Admin</span>
                <a href="{{ route('master.permissions') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('master.permissions*') ? 'bg-amber-50 text-amber-900 border border-amber-200' : 'text-amber-800 hover:bg-amber-50' }}">
                    <i data-lucide="shield-check" class="w-4 h-4 text-amber-600"></i>
                    <span>Manajemen Hak Akses</span>
                </a>
            </div>
            @endif
        </div>

        @if(auth()->check())
        <div class="p-4 border-t border-slate-100 bg-slate-50 space-y-2">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold flex items-center justify-center gap-2 transition-all">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>Keluar / Logout</span>
                </button>
            </form>
            <div class="text-center">
                <span class="text-[10px] text-slate-400 font-medium">Nochi Farm &bull; Peternak Ayam Petelur &copy; {{ date('Y') }}</span>
            </div>
        </div>
        @endif
    </div>

    <!-- Flash Notifications -->
    <div id="flashNotificationContainer" class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-3">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm shadow-sm animate-fade-in">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm shadow-sm animate-fade-in">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <!-- Main Content Container -->
    <main id="mainContent" class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 pb-28 md:pb-12 transition-opacity duration-150">
        @yield('content')
    </main>

    <!-- Bottom Navigation Bar Mobile (Sesuai Hak Akses) -->
    <nav id="mobileBottomNav" class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] sm:max-w-md z-40 bg-white border-t border-slate-200 shadow-2xl px-2 py-1 md:hidden">
        <div class="flex items-center justify-around text-center">
            
            @canExpense('menu_dashboard')
            <!-- 1. Beranda -->
            <a href="{{ route('dashboard') }}" class="flex-1 flex flex-col items-center justify-center py-1 {{ request()->routeIs('dashboard') ? 'text-nochi-orange font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }} transition-transform active:scale-95">
                <i data-lucide="home" class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'stroke-[2.5]' : 'stroke-2' }}"></i>
                <span class="text-[10px] mt-1">Beranda</span>
            </a>
            @endcanExpense

            @canExpense('menu_production')
            <!-- 2. Produksi -->
            <a href="{{ route('production.index') }}" class="flex-1 flex flex-col items-center justify-center py-1 {{ request()->routeIs('production.*') ? 'text-nochi-orange font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }} transition-transform active:scale-95">
                <div class="relative">
                    <i data-lucide="egg" class="w-5 h-5 {{ request()->routeIs('production.*') ? 'stroke-[2.5]' : 'stroke-2' }}"></i>
                    @if(request()->routeIs('production.*'))
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-4 h-0.5 bg-nochi-orange rounded-full"></span>
                    @endif
                </div>
                <span class="text-[10px] mt-1">Produksi</span>
            </a>
            @endcanExpense

            @canExpense('menu_expenses_create')
            <!-- 3. Tambah (Center Large Orange Button) -->
            <div class="flex-1 flex flex-col items-center justify-center -mt-5">
                <a href="{{ route('expenses.create') }}" class="w-12 h-12 rounded-full bg-nochi-orange hover:bg-nochi-orangeDark text-white flex items-center justify-center shadow-lg shadow-orange-500/40 border-2 border-white transition-transform active:scale-90" title="Tambah Pengeluaran">
                    <i data-lucide="plus" class="w-7 h-7 stroke-[2.8]"></i>
                </a>
                <span class="text-[10px] mt-1 font-bold text-nochi-orange">Tambah</span>
            </div>
            @endcanExpense

            @canExpense('menu_expenses_index')
            <!-- 4. Pengeluaran (Active Indicator) -->
            <a href="{{ route('expenses.index') }}" class="flex-1 flex flex-col items-center justify-center py-1 {{ request()->routeIs('expenses.*') ? 'text-nochi-orange font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }} transition-transform active:scale-95">
                <div class="relative">
                    <i data-lucide="wallet" class="w-5 h-5 {{ request()->routeIs('expenses.*') ? 'stroke-[2.5]' : 'stroke-2' }}"></i>
                    @if(request()->routeIs('expenses.*'))
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-4 h-0.5 bg-nochi-orange rounded-full"></span>
                    @endif
                </div>
                <span class="text-[10px] mt-1">Pengeluaran</span>
            </a>
            @endcanExpense

            <!-- 5. Lainnya (Drawer Toggle) -->
            <button onclick="toggleMobileDrawer()" class="flex-1 flex flex-col items-center justify-center py-1 text-slate-400 hover:text-slate-600 font-medium transition-transform active:scale-95">
                <i data-lucide="more-horizontal" class="w-5 h-5 stroke-2"></i>
                <span class="text-[10px] mt-1">Lainnya</span>
            </button>
        </div>
    </nav>

    <script>
        // Init Lucide Icons
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });

        function toggleMobileDrawer() {
            const drawer = document.getElementById('mobileDrawer');
            const backdrop = document.getElementById('mobileDrawerBackdrop');
            if (!drawer || !backdrop) return;
            if (drawer.classList.contains('translate-x-full')) {
                drawer.classList.remove('translate-x-full');
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
            } else {
                drawer.classList.add('translate-x-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
            }
        }

        function toggleUserDropdown() {
            const menu = document.getElementById('userDropdownMenu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        document.addEventListener('click', function(event) {
            const container = document.getElementById('userMenuContainer');
            const menu = document.getElementById('userDropdownMenu');
            if (container && menu && !container.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        // -------------------------------------------------------------
        // SPA Instant Navigation (Ganti Halaman / Pagination Tanpa Refresh)
        // -------------------------------------------------------------
        let isSpaNavigating = false;

        function startProgressBar() {
            const bar = document.getElementById('spaProgressBar');
            if (!bar) return;
            bar.style.transition = 'width 0.25s ease-out, opacity 0.2s ease';
            bar.style.opacity = '1';
            bar.style.width = '35%';
            setTimeout(() => {
                if (bar.style.opacity === '1' && parseFloat(bar.style.width) < 80) {
                    bar.style.width = '75%';
                }
            }, 150);
        }

        function finishProgressBar() {
            const bar = document.getElementById('spaProgressBar');
            if (!bar) return;
            bar.style.width = '100%';
            setTimeout(() => {
                bar.style.opacity = '0';
                setTimeout(() => {
                    bar.style.width = '0%';
                }, 250);
            }, 150);
        }

        async function spaNavigate(url, pushState = true, scrollTarget = null) {
            if (isSpaNavigating) return;
            isSpaNavigating = true;
            startProgressBar();

            // Tutup drawer mobile jika sedang terbuka
            const drawer = document.getElementById('mobileDrawer');
            if (drawer && !drawer.classList.contains('translate-x-full')) {
                toggleMobileDrawer();
            }

            const mainContent = document.getElementById('mainContent');
            if (mainContent) {
                mainContent.classList.add('opacity-40');
            }

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok && response.status !== 404 && response.status !== 422) {
                    window.location.href = url;
                    return;
                }

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // 1. Update Title Dokumen
                document.title = doc.title;

                // 2. Sinkronkan Navigasi (Desktop, Drawer, Bottom Bar) agar status aktif terupdate
                const newDesktopNav = doc.getElementById('desktopNavMenu');
                const currentDesktopNav = document.getElementById('desktopNavMenu');
                if (newDesktopNav && currentDesktopNav) {
                    currentDesktopNav.innerHTML = newDesktopNav.innerHTML;
                }

                const newDrawerMenu = doc.getElementById('mobileDrawerMenu');
                const currentDrawerMenu = document.getElementById('mobileDrawerMenu');
                if (newDrawerMenu && currentDrawerMenu) {
                    currentDrawerMenu.innerHTML = newDrawerMenu.innerHTML;
                }

                const newBottomNav = doc.getElementById('mobileBottomNav');
                const currentBottomNav = document.getElementById('mobileBottomNav');
                if (newBottomNav && currentBottomNav) {
                    currentBottomNav.innerHTML = newBottomNav.innerHTML;
                }

                // 3. Update Flash Notification jika ada
                const newFlash = doc.getElementById('flashNotificationContainer');
                const currentFlash = document.getElementById('flashNotificationContainer');
                if (newFlash && currentFlash) {
                    currentFlash.innerHTML = newFlash.innerHTML;
                }

                // 4. Update Konten Utama Halaman
                const newMain = doc.getElementById('mainContent');
                if (newMain && mainContent) {
                    mainContent.innerHTML = newMain.innerHTML;
                    mainContent.classList.remove('opacity-40');
                }

                // 5. Update Browser URL History
                if (pushState) {
                    window.history.pushState({ spa: true, url: url }, '', url);
                }

                // 6. Eksekusi Script Baru yang ada di halaman target
                const scripts = doc.querySelectorAll('main script, body script:not([src])');
                scripts.forEach(oldScript => {
                    if (oldScript.textContent.includes('spaNavigate')) return;
                    const newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.textContent = oldScript.textContent;
                    document.body.appendChild(newScript);
                    setTimeout(() => newScript.remove(), 50);
                });

                // 7. Refresh Icons Lucide
                if (window.lucide) {
                    window.lucide.createIcons();
                }

                // 8. Trigger Global Event untuk Inisialisasi Chart dsb
                window.dispatchEvent(new CustomEvent('page:loaded'));
                if (typeof window.initDashboardCharts === 'function') {
                    setTimeout(() => window.initDashboardCharts(), 60);
                }

                // 9. Smooth Scroll ke Elemen Target / Atas Halaman
                setTimeout(() => {
                    let targetEl = null;
                    if (typeof scrollTarget === 'string' && scrollTarget) {
                        targetEl = document.querySelector(scrollTarget);
                    }

                    if (targetEl) {
                        const headerOffset = 85;
                        const elementPosition = targetEl.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                        window.scrollTo({
                            top: Math.max(0, offsetPosition),
                            behavior: 'smooth'
                        });
                    } else if (!scrollTarget) {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                }, 50);

            } catch (error) {
                console.error('SPA Navigation Error:', error);
                window.location.href = url;
            } finally {
                isSpaNavigating = false;
                finishProgressBar();
            }
        }

        // Intercept Klik Semua Link Navigasi & Pagination Internal
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link || !link.href) return;

            // Kondisi dikecualikan (external, new tab, download, modal trigger, js link)
            if (link.target === '_blank' || link.hasAttribute('download') || link.hasAttribute('data-no-spa')) return;
            if (link.href.startsWith('javascript:') || link.getAttribute('href')?.startsWith('#')) return;

            // Pastikan domain sama
            const url = new URL(link.href, window.location.origin);
            if (url.origin !== window.location.origin) return;

            // Kecualikan link download file / cetak laporan
            if (url.pathname.includes('/laporan/export-csv') || url.pathname.includes('/laporan/cetak')) return;

            e.preventDefault();

            // Tentukan target scroll cerdas (Dashboard transaksi vs Pengeluaran table)
            const isPagination = url.searchParams.has('page');
            const hasType = url.searchParams.has('type');
            let scrollTarget = null;

            if (link.closest('#semuaDataTransaksiSection') || isPagination || hasType) {
                if (url.pathname === '/' || url.pathname.endsWith('/dashboard') || url.pathname === '') {
                    scrollTarget = '#semuaDataTransaksiSection';
                } else if (url.pathname.includes('/pengeluaran')) {
                    scrollTarget = '#expensesTableSection';
                }
            } else if (link.closest('#expensesTableSection')) {
                scrollTarget = '#expensesTableSection';
            }

            spaNavigate(url.href, true, scrollTarget);
        });

        // Intercept Submit Form Filter GET (Pencarian & Filter Tanggal)
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || form.method.toUpperCase() !== 'GET') return;
            if (form.hasAttribute('data-no-spa')) return;

            e.preventDefault();
            const formData = new FormData(form);
            const searchParams = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                if (value !== '') {
                    searchParams.append(key, value);
                }
            }

            const actionUrl = new URL(form.action || window.location.href, window.location.origin);
            actionUrl.search = searchParams.toString();

            let scrollTarget = null;
            if (form.closest('#semuaDataTransaksiSection')) {
                scrollTarget = '#semuaDataTransaksiSection';
            } else if (form.closest('#expensesTableSection') || form.closest('.space-y-6')) {
                scrollTarget = '#expensesTableSection';
            }

            spaNavigate(actionUrl.href, true, scrollTarget);
        });

        // Handle Tombol Back / Forward di Browser
        window.addEventListener('popstate', function() {
            spaNavigate(window.location.href, false);
        });
    </script>
    @stack('scripts')
</body>
</html>
