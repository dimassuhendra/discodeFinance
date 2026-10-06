<section aria-label="Filter Transaksi" class="rounded-2xl border border-brand-teal/15 bg-white p-4 shadow-sm">
    <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
        <div>
            <label for="search" class="sr-only">Cari</label>
            <input
                type="text"
                id="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari transaksi..."
                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm outline-none transition focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20"
            >
        </div>

        <div>
            <label for="sumber_dana_pengeluaran_id" class="sr-only">Sumber Dana</label>
            <select
                id="sumber_dana_pengeluaran_id"
                name="sumber_dana_pengeluaran_id"
                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm outline-none transition focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20"
            >
                <option value="">Semua Pos Pengeluaran</option>
                @foreach ($sumberDanaPengeluaranList as $sumber)
                    <option value="{{ $sumber->id }}" {{ request('sumber_dana_pengeluaran_id') == $sumber->id ? 'selected' : '' }}>
                        {{ $sumber->nama_sumber_dana }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="tipe" class="sr-only">Tipe Transaksi</label>
            <select
                id="tipe"
                name="tipe"
                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm outline-none transition focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20"
            >
                <option value="">Semua Tipe</option>
                <option value="pemasukkan" {{ request('tipe') === 'pemasukkan' ? 'selected' : '' }}>Pemasukan</option>
                <option value="pengeluaran" {{ request('tipe') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                <option value="mutasi" {{ request('tipe') === 'mutasi' ? 'selected' : '' }}>Mutasi</option>
            </select>
        </div>

        <div>
            <label for="start_date" class="sr-only">Tanggal Mulai</label>
            <input
                type="date"
                id="start_date"
                name="start_date"
                value="{{ request('start_date') }}"
                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm outline-none transition focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20"
            >
        </div>

        <div class="flex gap-2">
            <input
                type="date"
                name="end_date"
                value="{{ request('end_date') }}"
                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm outline-none transition focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20"
            >
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-xl bg-brand-dark px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-dark/90 focus:outline-none focus:ring-2 focus:ring-brand-dark"
            >
                Filter
            </button>
        </div>
    </form>
</section>