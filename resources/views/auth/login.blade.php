<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - NOCHI FARM Pengeluaran & Transaksi Kandang</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-nochi.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
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
                            800: '#800020', // Primary deep maroon
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

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(145deg, #38040d 0%, #680d1e 35%, #800020 70%, #9e192f 100%);
            min-height: 100vh;
        }

        /* Ambient Glow Effect */
        .ambient-glow-1 {
            position: absolute;
            top: -10%;
            right: -5%;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(249, 87, 33, 0.25) 0%, rgba(128, 0, 32, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
            filter: blur(40px);
        }

        .ambient-glow-2 {
            position: absolute;
            bottom: -10%;
            left: -5%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(251, 113, 133, 0.2) 0%, rgba(82, 11, 22, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
            filter: blur(50px);
        }

        .login-card {
            background: #ffffff;
            box-shadow: 0 25px 50px -12px rgba(40, 4, 10, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.2);
            border-radius: 28px;
        }

        .custom-input:focus {
            outline: none;
            border-color: #800020;
            box-shadow: 0 0 0 4px rgba(128, 0, 32, 0.12);
        }
    </style>
</head>
<body class="flex items-center justify-center p-4 sm:p-6 relative overflow-x-hidden selection:bg-maroon-800 selection:text-white">

    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="w-full max-w-md relative z-10 my-auto">
        
        <!-- Header Branding & Farm Icon -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-white shadow-2xl border-4 border-white p-1 mb-3.5 transform hover:scale-105 transition-transform duration-300 overflow-hidden">
                <img src="{{ asset('images/logo-nochi.png') }}" alt="Nochi Farm Logo" class="w-full h-full object-contain rounded-full bg-white">
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-wide drop-shadow-md uppercase">
                NOCHI FARM
            </h1>
            <p class="text-xs sm:text-sm font-semibold text-orange-200 mt-0.5 tracking-wider uppercase">
                Sistem Keuangan & Pengeluaran
            </p>
        </div>

        <!-- Main Login Card -->
        <div class="login-card p-7 sm:p-9 relative">

            <!-- Card Header Badge -->
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-800">Silakan Masuk</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Gunakan akun database Nochi Farm Anda</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-maroon-800 flex items-center justify-center shrink-0 border border-rose-100 shadow-xs">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Flash Error Notification -->
            @if(session('error'))
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-start gap-2.5 shadow-xs">
                    <i data-lucide="alert-circle" class="w-4 h-4 sm:w-5 sm:h-5 text-rose-600 shrink-0 mt-0.5"></i>
                    <div class="font-medium leading-relaxed">{{ session('error') }}</div>
                </div>
            @endif

            <!-- Flash Success Notification -->
            @if(session('success'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-start gap-2.5 shadow-xs">
                    <i data-lucide="check-circle" class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                    <div class="font-medium leading-relaxed">{{ session('success') }}</div>
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs space-y-1">
                    @foreach ($errors->all() as $err)
                        <div class="flex items-center gap-1.5 font-medium">
                            <i data-lucide="x" class="w-3.5 h-3.5 text-rose-600 shrink-0"></i>
                            <span>{{ $err }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4 sm:space-y-5" autocomplete="on">
                @csrf

                <!-- Input Username / Email -->
                <div>
                    <label for="loginInput" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Username atau Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        <input type="text"
                               id="loginInput"
                               name="login"
                               value="{{ old('login') }}"
                               required
                               autofocus
                               autocomplete="username"
                               placeholder="Contoh: admin atau nama_user"
                               class="custom-input w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-800 placeholder-slate-400 transition-all">
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <label for="passwordInput" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input type="password"
                               id="passwordInput"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="Masukkan password Anda"
                               class="custom-input w-full pl-10 pr-11 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-800 placeholder-slate-400 transition-all">
                        <button type="button"
                                id="togglePasswordBtn"
                                onclick="togglePasswordVisibility()"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors focus:outline-none"
                                title="Lihat password">
                            <i data-lucide="eye" id="eyeIcon" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox"
                               name="remember"
                               id="remember"
                               {{ old('remember') ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-maroon-800 focus:ring-maroon-800 border-slate-300">
                        <span class="text-xs text-slate-600 font-medium">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                            id="submitLoginBtn"
                            class="w-full py-3.5 px-4 bg-gradient-to-r from-maroon-900 via-maroon-800 to-rose-700 hover:from-maroon-950 hover:to-rose-800 text-white font-bold rounded-2xl shadow-lg shadow-maroon-950/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2 text-sm tracking-wide">
                        <i data-lucide="log-in" class="w-4 h-4 stroke-[2.5]"></i>
                        <span>MASUK KE SISTEM</span>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                <p class="text-[11px] text-slate-400">
                    Akun staf/petugas harus diizinkan terlebih dahulu oleh Administrator untuk dapat login.
                </p>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="text-center mt-6">
            <p class="text-xs text-rose-200/80 font-medium">
                &copy; {{ date('Y') }} NOCHI FARM &bull; Peternak Ayam Petelur Modern
            </p>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');
            if (!passwordInput || !eyeIcon) return;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
