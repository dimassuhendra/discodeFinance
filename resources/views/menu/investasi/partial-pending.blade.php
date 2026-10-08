<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-4 sm:p-5 border-b border-slate-100">
        <h2 class="font-bold text-base text-brand-dark">Transaksi Pengeluaran "Dana Investasi"</h2>
        <p class="text-xs text-slate-500 mt-0.5">Lengkapi detail instrumen investasi dari alokasi pengeluaran dana yang sudah pernah dicatat.</p>
    </div>

    <div class="divide-y divide-slate-100">
        @forelse ($pendingPengeluaran ?? [] as $pengeluaran)
            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/80 transition-colors">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-brand-orange/10 text-brand-orange">
                            {{ \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d M Y') }}
                        </span>
                        <h3 class="font-bold text-brand-dark text-base">{{ $pengeluaran->nama_pengeluaran }}</h3>
                    </div>
                    <p class="text-xs text-slate-400">Nominal Keluar: <strong class="text-slate-700">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</strong></p>
                </div>

                <button type="button"
                        @click="$dispatch('open-modal-investasi', { 
                            mode: 'complete', 
                            pengeluaran_id: {{ $pengeluaran->id }},
                            nama_instrumen: '{{ $pengeluaran->nama_pengeluaran }}',
                            total_beli: {{ $pengeluaran->jumlah }},
                            tanggal_beli: '{{ $pengeluaran->tanggal }}'
                        })"
                        class="w-full sm:w-auto px-4 py-2 bg-brand-orange hover:bg-brand-orange/90 text-white font-bold text-xs rounded-xl shadow active:scale-95 transition-all flex items-center justify-center gap-1.5 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Lengkapi Detail
                </button>
            </div>
        @empty
            <div class="py-12 text-center space-y-2">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="text-slate-500 text-sm font-medium">Semua pengeluaran dana investasi sudah dilengkapi!</p>
            </div>
        @endforelse
    </div>
</div>