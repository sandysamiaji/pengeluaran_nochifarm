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

    <!-- Chart.js for Financial Trends & Breakdown -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                <nav class="hidden md:flex items-center gap-2 bg-black/15 p-1 rounded-xl backdrop-blur-sm border border-white/10">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard Transaksi</span>
                    </a>
                    <a href="{{ route('expenses.create') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('expenses.create') ? 'bg-nochi-orange text-white shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Catat Pengeluaran</span>
                    </a>
                    <a href="{{ route('expenses.index') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('expenses.index') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="receipt" class="w-4 h-4"></i>
                        <span>Riwayat Pengeluaran</span>
                    </a>
                    <a href="{{ route('master.templates.index') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('master.templates.*') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                        <span>Master Template</span>
                    </a>
                </nav>

                <!-- Right Header Actions (Quick Links & Burger Button) -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('expenses.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-nochi-orange hover:bg-nochi-orangeDark text-white text-xs font-bold shadow-md hover:shadow-lg transition-all active:scale-95">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Pengeluaran Baru</span>
                    </a>

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
        <div class="p-4 flex-1 overflow-y-auto space-y-2">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('dashboard') ? 'bg-rose-50 text-maroon-800' : 'text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="home" class="w-5 h-5 text-maroon-700"></i>
                <span>Beranda & Transaksi</span>
            </a>
            <a href="{{ route('expenses.create') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('expenses.create') ? 'bg-orange-50 text-nochi-orange' : 'text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="plus-circle" class="w-5 h-5 text-nochi-orange"></i>
                <span>Catat Pengeluaran Kandang</span>
            </a>
            <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('expenses.index') ? 'bg-rose-50 text-maroon-800' : 'text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="receipt" class="w-5 h-5 text-maroon-700"></i>
                <span>Daftar Pengeluaran</span>
            </a>
            <a href="{{ route('master.templates.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('master.templates.*') ? 'bg-rose-50 text-maroon-800' : 'text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="settings" class="w-5 h-5 text-maroon-700"></i>
                <span>Master Template Pengeluaran</span>
            </a>
            <hr class="my-3 border-slate-100">
            <div class="px-3.5 py-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aplikasi Terintegrasi</div>
            <a href="http://127.0.0.1:8000" target="_blank" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-50">
                <span class="flex items-center gap-2.5">
                    <i data-lucide="arrow-up-right" class="w-4 h-4 text-emerald-600"></i>
                    <span>Nochi Fram (Penjualan / Omzet)</span>
                </span>
                <span class="text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-bold">Realtime</span>
            </a>
            <a href="http://localhost/ayam/nochifarminput/public" target="_blank" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-50">
                <span class="flex items-center gap-2.5">
                    <i data-lucide="egg" class="w-4 h-4 text-amber-600"></i>
                    <span>Nochi Farm Input (Produksi)</span>
                </span>
            </a>
        </div>
        <div class="p-4 border-t border-slate-100 bg-slate-50 text-center">
            <span class="text-[11px] text-slate-400 font-medium">Nochi Farm &bull; Peternak Ayam Petelur &copy; {{ date('Y') }}</span>
        </div>
    </div>

    <!-- Flash Notifications -->
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-3">
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

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 pb-28 md:pb-12">
        @yield('content')
    </main>

    <!-- Bottom Navigation Bar Mobile (Presisi Sesuai Mockup) -->
    <nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] sm:max-w-md z-40 bg-white border-t border-slate-200 shadow-2xl px-2 py-1 md:hidden">
        <div class="grid grid-cols-5 items-center text-center">
            
            <!-- 1. Beranda -->
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center py-1 {{ request()->routeIs('dashboard') ? 'text-nochi-orange font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }} transition-transform active:scale-95">
                <i data-lucide="home" class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'stroke-[2.5]' : 'stroke-2' }}"></i>
                <span class="text-[10px] mt-1">Beranda</span>
            </a>

            <!-- 2. Produksi -->
            <a href="http://localhost/ayam/nochifarminput/public" target="_blank" class="flex flex-col items-center justify-center py-1 text-slate-400 hover:text-slate-600 font-medium transition-transform active:scale-95">
                <i data-lucide="egg" class="w-5 h-5 stroke-2"></i>
                <span class="text-[10px] mt-1">Produksi</span>
            </a>

            <!-- 3. Tambah (Center Large Orange Button) -->
            <div class="flex flex-col items-center justify-center -mt-5">
                <a href="{{ route('expenses.create') }}" class="w-12 h-12 rounded-full bg-nochi-orange hover:bg-nochi-orangeDark text-white flex items-center justify-center shadow-lg shadow-orange-500/40 border-2 border-white transition-transform active:scale-90" title="Tambah Pengeluaran">
                    <i data-lucide="plus" class="w-7 h-7 stroke-[2.8]"></i>
                </a>
                <span class="text-[10px] mt-1 font-bold text-nochi-orange">Tambah</span>
            </div>

            <!-- 4. Pengeluaran (Active Indicator) -->
            <a href="{{ route('expenses.index') }}" class="flex flex-col items-center justify-center py-1 {{ request()->routeIs('expenses.*') ? 'text-nochi-orange font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }} transition-transform active:scale-95">
                <div class="relative">
                    <i data-lucide="wallet" class="w-5 h-5 {{ request()->routeIs('expenses.*') ? 'stroke-[2.5]' : 'stroke-2' }}"></i>
                    @if(request()->routeIs('expenses.*'))
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-4 h-0.5 bg-nochi-orange rounded-full"></span>
                    @endif
                </div>
                <span class="text-[10px] mt-1">Pengeluaran</span>
            </a>

            <!-- 5. Lainnya -->
            <button onclick="toggleMobileDrawer()" class="flex flex-col items-center justify-center py-1 text-slate-400 hover:text-slate-600 font-medium transition-transform active:scale-95">
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
            if (drawer.classList.contains('translate-x-full')) {
                drawer.classList.remove('translate-x-full');
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
            } else {
                drawer.classList.add('translate-x-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
