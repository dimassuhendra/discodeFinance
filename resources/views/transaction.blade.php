@component('components.layouts.app', ['title' => 'Riwayat Transaksi'])
    <div
        class="space-y-8 font-sans"
        x-data="{ 
            openPemasukkan: @js($errors->has('pemasukkan_*') || old('type') === 'pemasukkan'), 
            openPengeluaran: @js($errors->has('pengeluaran_*') || old('type') === 'pengeluaran'), 
            openMutasi: @js($errors->has('mutasi_*') || old('type') === 'mutasi') 
        }"
        x-on:keydown.escape.window="openPemasukkan = false; openPengeluaran = false; openMutasi = false"
    >
        {{-- HEADER PAGE --}}
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</p>
                <h1 class="mt-1 font-heading text-3xl font-bold tracking-tight text-brand-dark sm:text-4xl">Riwayat Transaksi</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola pencatatan pemasukan, pengeluaran, dan pemindahan saldo Anda.</p>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex flex-wrap gap-2.5">
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
        </header>

        {{-- ALERT MESSAGES --}}
        @if (session('success'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                <p class="font-bold">Terjadi kesalahan pada input data:</p>
                <ul class="mt-1 list-disc list-inside space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FILTER & SEARCH --}}
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

        {{-- TABEL TRANSAKSI --}}
        <section aria-label="Daftar Transaksi" class="overflow-hidden rounded-3xl border border-brand-teal/15 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="bg-brand-teal-light/30 text-xs uppercase tracking-wide text-slate-600">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Tanggal</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Tipe</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Deskripsi</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Sumber Dana</th>
                            <th scope="col" class="px-6 py-4 text-right font-semibold">Nominal</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Keterangan</th>
                            <th scope="col" class="px-6 py-4 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($transactions as $trx)
                            @php
                                $isIncome = $trx->tipe_transaksi === 'pemasukkan';
                                $isExpense = $trx->tipe_transaksi === 'pengeluaran';
                            @endphp
                            <tr class="transition hover:bg-brand-bg">
                                <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-600">
                                    {{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($isIncome)
                                        <span class="inline-flex items-center rounded-full bg-brand-teal-light/40 px-2.5 py-0.5 text-xs font-bold text-brand-teal">Pemasukan</span>
                                    @elseif ($isExpense)
                                        <span class="inline-flex items-center rounded-full bg-brand-orange/20 px-2.5 py-0.5 text-xs font-bold text-brand-orange">Pengeluaran</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-bold text-sky-800">Mutasi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-semibold text-brand-dark">
                                    {{ $trx->nama }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-slate-600">
                                    @if ($isIncome)
                                        <span class="font-medium text-brand-teal">➔ {{ $trx->sumber_tujuan }}</span>
                                    @elseif ($isExpense)
                                        <span class="font-medium text-brand-orange">{{ $trx->sumber_asal }} ➔</span>
                                    @else
                                        <span class="text-slate-600">{{ $trx->sumber_asal }} ➔ {{ $trx->sumber_tujuan }}</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right font-extrabold {{ $isIncome ? 'text-brand-teal' : ($isExpense ? 'text-brand-orange' : 'text-brand-dark') }}">
                                    {{ $isIncome ? '+' : ($isExpense ? '-' : '') }}Rp {{ number_format((float) $trx->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500">
                                    {{ $trx->keterangan ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <form action="{{ route('transactions.destroy', ['type' => $trx->tipe_transaksi, 'id' => $trx->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg p-1 text-slate-400 transition hover:text-rose-600" aria-label="Hapus transaksi">
                                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-500">
                                    Belum ada data transaksi yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transactions->hasPages())
                <div class="border-t border-slate-100 p-4">
                    {{ $transactions->links() }}
                </div>
            @endif
        </section>

        {{-- MODAL PEMASUKAN --}}
        <div
            x-cloak
            x-show="openPemasukkan"
            x-transition.opacity
            x-on:click.self="openPemasukkan = false"
            class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/50 p-0 sm:items-center sm:p-4"
            role="dialog"
            aria-modal="true"
        >
            <section x-show="openPemasukkan" x-transition class="max-h-[90vh] w-full overflow-y-auto rounded-t-2xl bg-white p-5 shadow-xl sm:max-w-lg sm:rounded-2xl sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-heading text-xl font-bold text-slate-900">Tambah Pemasukan</h2>
                        <p class="mt-1 text-sm text-slate-500">Catat sumber dana masuk ke akun Anda.</p>
                    </div>
                    <button type="button" x-on:click="openPemasukkan = false" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 6 12 12M18 6 6 18" stroke-linecap="round" /></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('transactions.store.pemasukkan') }}" class="mt-6 space-y-4">
                    @csrf
                    <input type="hidden" name="type" value="pemasukkan">

                    <div>
                        <label for="pemasukkan_tanggal" class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal</label>
                        <input type="date" id="pemasukkan_tanggal" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20">
                    </div>

                    <div>
                        <label for="nama_pemasukkan" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Pemasukan</label>
                        <input type="text" id="nama_pemasukkan" name="nama_pemasukkan" value="{{ old('nama_pemasukkan') }}" required placeholder="Contoh: Gaji, Bonus, Cashback" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20">
                    </div>

                    <div>
                        <label for="pemasukkan_jumlah" class="mb-1.5 block text-sm font-medium text-slate-700">Jumlah (Rp)</label>
                        <input type="number" id="pemasukkan_jumlah" name="jumlah" min="1" value="{{ old('jumlah') }}" required placeholder="0" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20">
                    </div>

                    <div>
                        <label for="pemasukkan_sumber_dana_id" class="mb-1.5 block text-sm font-medium text-slate-700">Sumber Dana Pemasukan</label>
                        <select id="pemasukkan_sumber_dana_id" name="sumber_dana_id" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20">
                            <option value="">-- Pilih Sumber Pemasukan --</option>
                            @foreach ($sumberDanaPemasukkanList as $sumber)
                                <option value="{{ $sumber->id }}" {{ old('sumber_dana_id') == $sumber->id ? 'selected' : '' }}>{{ $sumber->nama_sumber_dana }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-2">
                        <button type="button" x-on:click="openPemasukkan = false" class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200">Batal</button>
                        <button type="submit" class="rounded-xl bg-brand-teal px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-teal/90">Simpan Pemasukan</button>
                    </div>
                </form>
            </section>
        </div>

        {{-- MODAL PENGELUARAN --}}
        <div
            x-cloak
            x-show="openPengeluaran"
            x-transition.opacity
            x-on:click.self="openPengeluaran = false"
            class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/50 p-0 sm:items-center sm:p-4"
            role="dialog"
            aria-modal="true"
        >
            <section x-show="openPengeluaran" x-transition class="max-h-[90vh] w-full overflow-y-auto rounded-t-2xl bg-white p-5 shadow-xl sm:max-w-lg sm:rounded-2xl sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-heading text-xl font-bold text-slate-900">Tambah Pengeluaran</h2>
                        <p class="mt-1 text-sm text-slate-500">Catat transaksi pengeluaran baru.</p>
                    </div>
                    <button type="button" x-on:click="openPengeluaran = false" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 6 12 12M18 6 6 18" stroke-linecap="round" /></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('transactions.store.pengeluaran') }}" class="mt-6 space-y-4">
                    @csrf
                    <input type="hidden" name="type" value="pengeluaran">

                    <div>
                        <label for="pengeluaran_tanggal" class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal</label>
                        <input type="date" id="pengeluaran_tanggal" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20">
                    </div>

                    <div>
                        <label for="nama_pengeluaran" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Pengeluaran</label>
                        <input type="text" id="nama_pengeluaran" name="nama_pengeluaran" value="{{ old('nama_pengeluaran') }}" required placeholder="Contoh: Belanja Bulanan, Makan Siang" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20">
                    </div>

                    <div>
                        <label for="pengeluaran_jumlah" class="mb-1.5 block text-sm font-medium text-slate-700">Jumlah (Rp)</label>
                        <input type="number" id="pengeluaran_jumlah" name="jumlah" min="1" value="{{ old('jumlah') }}" required placeholder="0" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20">
                    </div>

                    <div>
                        <label for="pengeluaran_sumber_dana_id" class="mb-1.5 block text-sm font-medium text-slate-700">Pos Pengeluaran</label>
                        <select id="pengeluaran_sumber_dana_id" name="sumber_dana_id" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20">
                            <option value="">-- Pilih Pos Pengeluaran --</option>
                            @foreach ($sumberDanaPengeluaranList as $sumber)
                                <option value="{{ $sumber->id }}" {{ old('sumber_dana_id') == $sumber->id ? 'selected' : '' }}>{{ $sumber->nama_sumber_dana }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-2">
                        <button type="button" x-on:click="openPengeluaran = false" class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200">Batal</button>
                        <button type="submit" class="rounded-xl bg-brand-orange px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-orange/90">Simpan Pengeluaran</button>
                    </div>
                </form>
            </section>
        </div>

        {{-- MODAL MUTASI SALDO --}}
        <div
            x-cloak
            x-show="openMutasi"
            x-transition.opacity
            x-on:click.self="openMutasi = false"
            class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/50 p-0 sm:items-center sm:p-4"
            role="dialog"
            aria-modal="true"
        >
            <section x-show="openMutasi" x-transition class="max-h-[90vh] w-full overflow-y-auto rounded-t-2xl bg-white p-5 shadow-xl sm:max-w-lg sm:rounded-2xl sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-heading text-xl font-bold text-slate-900">Mutasi / Transfer Saldo</h2>
                        <p class="mt-1 text-sm text-slate-500">Pindahkan saldo antar pos/sumber dana.</p>
                    </div>
                    <button type="button" x-on:click="openMutasi = false" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 6 12 12M18 6 6 18" stroke-linecap="round" /></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('transactions.store.mutasi') }}" class="mt-6 space-y-4">
                    @csrf
                    <input type="hidden" name="type" value="mutasi">

                    <div>
                        <label for="mutasi_tanggal" class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal</label>
                        <input type="date" id="mutasi_tanggal" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20">
                    </div>

                    <div>
                        <label for="mutasi_jumlah" class="mb-1.5 block text-sm font-medium text-slate-700">Jumlah (Rp)</label>
                        <input type="number" id="mutasi_jumlah" name="jumlah" min="1" value="{{ old('jumlah') }}" required placeholder="0" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20">
                    </div>

                    <div>
                        <label for="dari_sumber_dana_id" class="mb-1.5 block text-sm font-medium text-slate-700">Dari Sumber Dana</label>
                        <select id="dari_sumber_dana_id" name="dari_sumber_dana_id" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20">
                            <option value="">-- Pilih Asal Saldo --</option>
                            @foreach ($sumberDanaPengeluaranList as $sumber)
                                <option value="{{ $sumber->id }}" {{ old('dari_sumber_dana_id') == $sumber->id ? 'selected' : '' }}>{{ $sumber->nama_sumber_dana }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="ke_sumber_dana_id" class="mb-1.5 block text-sm font-medium text-slate-700">Ke Sumber Dana</label>
                        <select id="ke_sumber_dana_id" name="ke_sumber_dana_id" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20">
                            <option value="">-- Pilih Tujuan Saldo --</option>
                            @foreach ($sumberDanaPengeluaranList as $sumber)
                                <option value="{{ $sumber->id }}" {{ old('ke_sumber_dana_id') == $sumber->id ? 'selected' : '' }}>{{ $sumber->nama_sumber_dana }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="mutasi_keterangan" class="mb-1.5 block text-sm font-medium text-slate-700">Keterangan</label>
                        <textarea id="mutasi_keterangan" name="keterangan" rows="2" placeholder="Catatan mutasi..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-2">
                        <button type="button" x-on:click="openMutasi = false" class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200">Batal</button>
                        <button type="submit" class="rounded-xl bg-brand-dark px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-dark/90">Proses Mutasi</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endcomponent