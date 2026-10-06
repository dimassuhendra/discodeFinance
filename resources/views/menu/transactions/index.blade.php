@component('components.layouts.app', [
    'title' => 'Riwayat Transaksi',
    'subtitle' => 'Kelola pencatatan pemasukan, pengeluaran, dan pemindahan saldo Anda.'
])
    <div
        class="space-y-8 font-sans"
        x-data="{ 
            openPemasukkan: @js($errors->has('pemasukkan_*') || old('type') === 'pemasukkan'), 
            openPengeluaran: @js($errors->has('pengeluaran_*') || old('type') === 'pengeluaran'), 
            openMutasi: @js($errors->has('mutasi_*') || old('type') === 'mutasi') 
        }"
        x-on:keydown.escape.window="openPemasukkan = false; openPengeluaran = false; openMutasi = false"
    >
        {{-- ACTION BUTTONS TRANSAKSI --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a
                href="{{ route('sumber-dana.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2"
            >
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Kelola Pos / Sumber Dana
            </a>

            <div class="flex flex-wrap items-center gap-2.5">
                <button
                    type="button"
                    x-on:click="openPemasukkan = true"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand-teal px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-teal/90 focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2"
                >
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14m-7-7h14" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Pemasukan
                </button>

                <button
                    type="button"
                    x-on:click="openPengeluaran = true"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand-orange px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-orange/90 focus:outline-none focus:ring-2 focus:ring-brand-orange focus:ring-offset-2"
                >
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Pengeluaran
                </button>

                <button
                    type="button"
                    x-on:click="openMutasi = true"
                    class="inline-flex items-center gap-2 rounded-xl border border-brand-teal/20 bg-brand-teal-light/40 px-4 py-2.5 text-sm font-semibold text-brand-dark transition hover:bg-brand-teal-light/70 focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2"
                >
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 7h10M8 7l4-4M8 7l4 4M16 17H6M16 17l-4 4M16 17l-4-4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Mutasi Saldo
                </button>
            </div>
        </div>

        {{-- ALERT MESSAGES --}}
        @include('menu.transactions.partials.alert')

        {{-- FILTER & SEARCH --}}
        @include('menu.transactions.partials.filter')

        {{-- TABEL TRANSAKSI --}}
        @include('menu.transactions.partials.table')

        {{-- MODALS --}}
        @include('menu.transactions.modals.modal-pemasukkan')
        @include('menu.transactions.modals.modal-pengeluaran')
        @include('menu.transactions.modals.modal-mutasi')
    </div>
@endcomponent