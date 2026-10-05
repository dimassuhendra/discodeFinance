<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Pencatatan Keuangan' }}</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @fluxAppearance
</head>
<body class="h-full flex flex-col antialiased text-slate-800">

    <div class="min-h-full flex flex-col lg:flex-row pb-24 lg:pb-0">
        
        <!-- ========================================================= -->
        <!-- 1. DESKTOP SIDEBAR (Tampil di Layar Besar >= lg)          -->
        <!-- ========================================================= -->
        <aside class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0 bg-[#359FA0] text-white shadow-xl z-30">
            <!-- Header App / Logo -->
            <div class="flex items-center gap-3 h-20 px-6 border-b border-white/10 bg-[#359FA0]">
                <div class="w-10 h-10 rounded-xl bg-[#FFF0C5] flex items-center justify-center text-[#359FA0] shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-heading font-bold tracking-wide text-[#FFF0C5]">discodeFinance</h1>
                    <p class="text-xs text-[#8AD6D1]">Pencatatan Pribadi</p>
                </div>
            </div>

            <!-- Navigasi Sidebar -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->is('dashboard*') ? 'bg-[#FFF0C5] text-[#359FA0] font-bold shadow-md' : 'text-white hover:bg-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>

                <a href="/transactions" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->is('transactions*') ? 'bg-[#FFF0C5] text-[#359FA0] font-bold shadow-md' : 'text-white hover:bg-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2-2 4 4m0-6l-2 2-4-4M3 6h18M3 12h18M3 18h18"/></svg>
                    Transaksi
                </a>

                <a href="/barang" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->is('barang*') ? 'bg-[#FFF0C5] text-[#359FA0] font-bold shadow-md' : 'text-white hover:bg-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Detail Barang
                </a>

                <a href="/investasi" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->is('investasi*') ? 'bg-[#FFF0C5] text-[#359FA0] font-bold shadow-md' : 'text-white hover:bg-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Detail Investasi
                </a>

                <a href="/mutasi" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->is('mutasi*') ? 'bg-[#FFF0C5] text-[#359FA0] font-bold shadow-md' : 'text-white hover:bg-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Mutasi Saldo
                </a>
            </nav>

            <!-- Footer Sidebar / Profile -->
            <div class="p-4 border-t border-white/10 bg-black/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-[#8AD6D1] text-[#359FA0] font-bold flex items-center justify-center">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-sm font-semibold truncate">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                            <p class="text-xs text-[#8AD6D1] truncate">{{ auth()->user()->email ?? 'Personal' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ========================================================= -->
        <!-- 2. MOBILE TOP HEADER (Tampil di Layar Kecil < lg)          -->
        <!-- ========================================================= -->
        <header class="lg:hidden bg-[#359FA0] text-white p-4 sticky top-0 z-20 shadow-md flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-[#FFF0C5] flex items-center justify-center text-[#359FA0] font-bold shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-lg font-heading font-bold text-[#FFF0C5]">discodeFinance</h1>
            </div>
            <div class="w-8 h-8 rounded-full bg-[#8AD6D1] text-[#359FA0] font-bold text-xs flex items-center justify-center">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
        </header>

        <!-- ========================================================= -->
        <!-- 3. KONTEN UTAMA (CONTENT AREA)                            -->
        <!-- ========================================================= -->
        <main class="flex-1 lg:pl-64 min-w-0">
            <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </div>
        </main>

        <!-- ========================================================= -->
        <!-- 4. MOBILE BOTTOM NAVIGATION BAR (Floating Bottom Menu)    -->
        <!-- ========================================================= -->
        <nav class="lg:hidden fixed bottom-3 left-3 right-3 bg-[#359FA0] text-white rounded-2xl shadow-2xl z-40 border border-white/20 backdrop-blur-md">
            <div class="flex items-center justify-around h-16 px-2">
                
                <!-- Dashboard -->
                <a href="/dashboard" class="flex flex-col items-center justify-center flex-1 py-1 text-xs transition-all {{ request()->is('dashboard*') ? 'text-[#FFF0C5] font-bold scale-105' : 'text-white/80 hover:text-white' }}">
                    <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Home</span>
                </a>

                <!-- Transaksi -->
                <a href="/transactions" class="flex flex-col items-center justify-center flex-1 py-1 text-xs transition-all {{ request()->is('transactions*') ? 'text-[#FFF0C5] font-bold scale-105' : 'text-white/80 hover:text-white' }}">
                    <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2-2 4 4m0-6l-2 2-4-4M3 6h18M3 12h18M3 18h18"/></svg>
                    <span>Transaksi</span>
                </a>

                <!-- Quick Action Button / Aksi Cepat di Tengah -->
                <div class="relative -top-5">
                    <a href="/transaksi/create" class="w-12 h-12 rounded-full bg-[#FF8C52] text-white flex items-center justify-center shadow-lg hover:bg-[#ff7b38] transition-transform active:scale-95 border-2 border-white">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </a>
                </div>

                <!-- Barang -->
                <a href="/barang" class="flex flex-col items-center justify-center flex-1 py-1 text-xs transition-all {{ request()->is('barang*') ? 'text-[#FFF0C5] font-bold scale-105' : 'text-white/80 hover:text-white' }}">
                    <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span>Barang</span>
                </a>

                <!-- Investasi -->
                <a href="/investasi" class="flex flex-col items-center justify-center flex-1 py-1 text-xs transition-all {{ request()->is('investasi*') ? 'text-[#FFF0C5] font-bold scale-105' : 'text-white/80 hover:text-white' }}">
                    <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span>Investasi</span>
                </a>

            </div>
        </nav>

    </div>

    @fluxScripts
</body>
</html>