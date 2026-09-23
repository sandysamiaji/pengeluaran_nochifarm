<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Dibatasi | NOCHI FARM</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-nochi.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 border border-slate-100 shadow-xl text-center space-y-4">
        <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto border border-rose-100">
            <i data-lucide="shield-alert" class="w-8 h-8"></i>
        </div>
        <h2 class="text-xl font-black text-slate-800">Akses Dibatasi</h2>
        <p class="text-xs text-slate-500 leading-relaxed">
            {{ $message ?? 'Anda tidak memiliki hak akses untuk halaman atau fitur ini. Silakan hubungi Administrator peternakan untuk mengaktifkan izin menu akun Anda.' }}
        </p>
        <div class="pt-3 flex flex-col gap-2">
            <a href="{{ route('dashboard') }}" class="w-full py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all">
                Kembali ke Beranda
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-2 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-bold transition-all">
                    Keluar / Logout Akun
                </button>
            </form>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
