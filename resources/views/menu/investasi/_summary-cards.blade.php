@php
    // Menggunakan $portfolioSummary dari Controller, atau fallback aman jika berbentuk Array / Object
    $totalModal = $portfolioSummary['total_modal'] ?? $portfolioSummary->total_modal ?? 0;
    $totalNilaiSekarang = $portfolioSummary['total_nilai_sekarang'] ?? $portfolioSummary->total_nilai_sekarang ?? 0;
    
    // Jika service belum menghitung profit/loss & persentase, hitung secara otomatis
    $totalProfitLoss = $portfolioSummary['total_profit_loss'] ?? ($totalNilaiSekarang - $totalModal);
    $persenTotal = $portfolioSummary['persen_total'] ?? ($totalModal > 0 ? ($totalProfitLoss / $totalModal) * 100 : 0);
    
    // Hitung total instrumen dari riwayatList atau portfolioSummary
    $totalInstrumen = $portfolioSummary['total_instrumen'] ?? (isset($riwayatList) ? $riwayatList->total() : 0);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    
    <!-- Card 1: Total Portofolio -->
    <div class="p-5 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-brand-teal/10 text-brand-teal flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Nilai Portofolio</p>
            <h3 class="text-xl sm:text-2xl font-extrabold text-brand-dark tracking-tight truncate">
                Rp {{ number_format($totalNilaiSekarang, 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-slate-500 mt-0.5">
                Modal Awal: <span class="font-medium">Rp {{ number_format($totalModal, 0, ',', '.') }}</span>
            </p>
        </div>
    </div>

    <!-- Card 2: Total Profit / Loss -->
    <div class="p-5 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl {{ $totalProfitLoss >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if($totalProfitLoss >= 0)
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/>
                @endif
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Profit / Loss</p>
            <h3 class="text-xl sm:text-2xl font-extrabold {{ $totalProfitLoss >= 0 ? 'text-emerald-600' : 'text-rose-600' }} tracking-tight truncate">
                {{ $totalProfitLoss >= 0 ? '+' : '' }}Rp {{ number_format($totalProfitLoss, 0, ',', '.') }}
            </h3>
            <p class="text-[11px] font-semibold {{ $totalProfitLoss >= 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-0.5">
                {{ sprintf('%.2f', $persenTotal) }}% <span class="text-slate-400 font-normal">keseluruhan</span>
            </p>
        </div>
    </div>

    <!-- Card 3: Total Instrumen Aset -->
    <div class="p-5 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center gap-4 sm:col-span-2 lg:col-span-1">
        <div class="w-12 h-12 rounded-xl bg-brand-cream/40 text-brand-orange flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Instrumen Aktif</p>
            <h3 class="text-xl sm:text-2xl font-extrabold text-brand-dark tracking-tight">
                {{ $totalInstrumen }} <span class="text-sm font-medium text-slate-500">Aset</span>
            </h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Tersebar di berbagai platform</p>
        </div>
    </div>

</div>