@component('components.layouts.app', ['title' => 'Dashboard'])
    <div
        class="space-y-8"
        x-data="{ quickExpenseOpen: @js($errors->any()) }"
        x-on:keydown.escape.window="quickExpenseOpen = false"
    >
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">Ringkasan keuangan</h1>
                <p class="mt-1 text-sm text-slate-500">Pantau saldo dan transaksi keuangan Anda.</p>
            </div>

            <button
                type="button"
                x-on:click="quickExpenseOpen = true"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#359FA0] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#2d898a] focus:outline-none focus:ring-2 focus:ring-[#359FA0] focus:ring-offset-2"
            >
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M12 5v14m-7-7h14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                Catat pengeluaran
            </button>
        </header>

        @if (session('success'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-8 font-sans">
            {{-- SECTION RINGKASAN UTAMA --}}
            <section aria-label="Ringkasan saldo" class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                
                {{-- Total Saldo --}}
                <article class="flex flex-col justify-between rounded-2xl bg-[#359FA0] p-6 text-white shadow-xs sm:col-span-2 xl:col-span-1">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-white/80">Total Saldo</p>
                            <p class="mt-3 text-3xl font-extrabold tracking-tight text-white">
                                Rp {{ number_format((float) ($summary['total_saldo'] ?? 0), 0, ',', '.') }}
                            </p>
                        </div>
                        <span class="flex size-11 items-center justify-center rounded-xl bg-white/20 text-white backdrop-blur-xs" aria-hidden="true">
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="5" width="20" height="14" rx="2" />
                                <line x1="2" y1="10" x2="22" y2="10" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-xs font-medium text-white/80">Saldo tersedia dari seluruh sumber dana</p>
                </article>

                {{-- Total Pemasukan --}}
                <article class="flex flex-col justify-between rounded-2xl bg-[#8AD6D1] p-6 text-slate-900 shadow-xs">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-teal-950/70">Total Pemasukan</p>
                            <p class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900">
                                Rp {{ number_format((float) ($summary['total_pemasukkan'] ?? 0), 0, ',', '.') }}
                            </p>
                        </div>
                        <span class="flex size-11 items-center justify-center rounded-xl bg-white/40 text-teal-950 shadow-2xs" aria-hidden="true">
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 14l5-5 5 5M12 9v11M5 4h14" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-xs font-medium text-teal-950/70">Akumulasi seluruh pemasukan</p>
                </article>

                {{-- Total Pengeluaran --}}
                <article class="flex flex-col justify-between rounded-2xl bg-[#FF8C52] p-6 text-white shadow-xs">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-white/80">Total Pengeluaran</p>
                            <p class="mt-3 text-2xl font-extrabold tracking-tight text-white">
                                Rp {{ number_format((float) ($summary['total_pengeluaran'] ?? 0), 0, ',', '.') }}
                            </p>
                        </div>
                        <span class="flex size-11 items-center justify-center rounded-xl bg-white/20 text-white backdrop-blur-xs" aria-hidden="true">
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 10l5 5 5-5M12 15V4M5 20h14" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-xs font-medium text-white/80">Akumulasi seluruh pengeluaran</p>
                </article>

            </section>

            {{-- SECTION 1: STATISTIK PENGELUARAN (SELURUH SUMBER DANA) --}}
            <div>
                <div class="mb-4 flex items-center gap-2">
                    <span class="h-5 w-1.5 rounded-full bg-[#359FA0]"></span>
                    <h2 class="text-lg font-bold text-slate-900 font-sans">Statistik Pengeluaran (Seluruh Sumber Dana)</h2>
                </div>

                {{-- Pastikan class grid & md:grid-cols-3 aktif di sini --}}
                <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                    {{-- Card Pengeluaran Tertinggi --}}
                    <article class="flex flex-col justify-between rounded-2xl bg-white p-5 border border-slate-200/80 shadow-xs">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pengeluaran Tertinggi</p>
                            <p class="mt-2 text-2xl font-extrabold text-rose-600">
                                Rp {{ number_format((float) ($summary['stats_all']['highest']['total'] ?? 0), 0, ',', '.') }}
                            </p>
                        </div>
                        <p class="mt-3 text-xs font-medium text-slate-500">
                            @if (!empty($summary['stats_all']['highest']['tanggal']))
                                {{ \Carbon\Carbon::parse($summary['stats_all']['highest']['tanggal'])->translatedFormat('l, d F Y') }}
                            @else
                                Belum ada data
                            @endif
                        </p>
                    </article>

                    {{-- Card Pengeluaran Terendah --}}
                    <article class="flex flex-col justify-between rounded-2xl bg-white p-5 border border-slate-200/80 shadow-xs">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pengeluaran Terendah</p>
                            <p class="mt-2 text-2xl font-extrabold text-emerald-600">
                                Rp {{ number_format((float) ($summary['stats_all']['lowest']['total'] ?? 0), 0, ',', '.') }}
                            </p>
                        </div>
                        <p class="mt-3 text-xs font-medium text-slate-500">
                            @if (!empty($summary['stats_all']['lowest']['tanggal']))
                                {{ \Carbon\Carbon::parse($summary['stats_all']['lowest']['tanggal'])->translatedFormat('l, d F Y') }}
                            @else
                                Belum ada data
                            @endif
                        </p>
                    </article>

                    {{-- Card Rata-rata Pengeluaran --}}
                    <article class="flex flex-col justify-between rounded-2xl bg-white p-5 border border-slate-200/80 shadow-xs">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Rata-rata / Hari</p>
                            <p class="mt-2 text-2xl font-extrabold text-slate-900">
                                Rp {{ number_format((float) ($summary['stats_all']['average'] ?? 0), 0, ',', '.') }}
                            </p>
                        </div>
                        <p class="mt-3 text-xs font-medium text-slate-500">Berdasarkan hari aktif transaksi</p>
                    </article>
                </div>
            </div>

            {{-- SECTION 2: STATISTIK PENGELUARAN (KHUSUS UANG MAKAN) --}}
            <div>
                <div class="mb-4 flex items-center gap-2">
                    <span class="h-5 w-1.5 rounded-full bg-[#FF8C52]"></span>
                    <h2 class="text-lg font-bold text-slate-900 font-sans">Statistik Pengeluaran (Uang Makan)</h2>
                </div>

                {{-- Pastikan class grid & md:grid-cols-3 aktif di sini --}}
                <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                    {{-- Card Pengeluaran Tertinggi Uang Makan --}}
                    <article class="flex flex-col justify-between rounded-2xl bg-[#FFF0C5] p-5 border border-[#FF8C52]/20 shadow-xs">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-[#FF8C52]">Pengeluaran Tertinggi</p>
                            <p class="mt-2 text-2xl font-extrabold text-rose-600">
                                Rp {{ number_format((float) ($summary['stats_uang_makan']['highest']['total'] ?? 0), 0, ',', '.') }}
                            </p>
                        </div>
                        <p class="mt-3 text-xs font-medium text-amber-900/70">
                            @if (!empty($summary['stats_uang_makan']['highest']['tanggal']))
                                {{ \Carbon\Carbon::parse($summary['stats_uang_makan']['highest']['tanggal'])->translatedFormat('l, d F Y') }}
                            @else
                                Belum ada data
                            @endif
                        </p>
                    </article>

                    {{-- Card Pengeluaran Terendah Uang Makan --}}
                    <article class="flex flex-col justify-between rounded-2xl bg-[#FFF0C5] p-5 border border-[#FF8C52]/20 shadow-xs">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-[#FF8C52]">Pengeluaran Terendah</p>
                            <p class="mt-2 text-2xl font-extrabold text-emerald-600">
                                Rp {{ number_format((float) ($summary['stats_uang_makan']['lowest']['total'] ?? 0), 0, ',', '.') }}
                            </p>
                        </div>
                        <p class="mt-3 text-xs font-medium text-amber-900/70">
                            @if (!empty($summary['stats_uang_makan']['lowest']['tanggal']))
                                {{ \Carbon\Carbon::parse($summary['stats_uang_makan']['lowest']['tanggal'])->translatedFormat('l, d F Y') }}
                            @else
                                Belum ada data
                            @endif
                        </p>
                    </article>

                    {{-- Card Rata-rata Pengeluaran Uang Makan --}}
                    <article class="flex flex-col justify-between rounded-2xl bg-[#FFF0C5] p-5 border border-[#FF8C52]/20 shadow-xs">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-[#FF8C52]">Rata-rata / Hari</p>
                            <p class="mt-2 text-2xl font-extrabold text-slate-900">
                                Rp {{ number_format((float) ($summary['stats_uang_makan']['average'] ?? 0), 0, ',', '.') }}
                            </p>
                        </div>
                        <p class="mt-3 text-xs font-medium text-amber-900/70">Khusus sumber dana Uang Makan</p>
                    </article>
                </div>
            </div>
        </div>

        {{-- Section Saldo per Sumber Dana --}}
        <section aria-labelledby="funds-heading" class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <h2 id="funds-heading" class="text-lg font-bold text-slate-900">Saldo per sumber dana</h2>
                    <p class="mt-1 text-sm text-slate-500">Rincian budget, pengeluaran, dan saldo setiap sumber.</p>
                </div>
            </div>

            @if (($summary['sumber_dana'] ?? collect())->isNotEmpty())
                <div class="divide-y divide-slate-100">
                    @foreach ($summary['sumber_dana'] as $sumberDana)
                        <article class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                            <div class="min-w-0">
                                <h3 class="truncate font-semibold text-slate-900">{{ $sumberDana['nama'] }}</h3>
                                @if (filled($sumberDana['keterangan'] ?? null))
                                    <p class="mt-1 truncate text-sm text-slate-500">{{ $sumberDana['keterangan'] }}</p>
                                @endif
                                <p class="mt-1 text-xs text-slate-500">
                                    Budget Rp {{ number_format((float) $sumberDana['budget'], 0, ',', '.') }}
                                    <span class="px-1">·</span>
                                    Pengeluaran Rp {{ number_format((float) $sumberDana['pengeluaran'], 0, ',', '.') }}
                                </p>
                            </div>
                            <p class="shrink-0 text-lg font-bold text-slate-900">
                                Rp {{ number_format((float) $sumberDana['saldo'], 0, ',', '.') }}
                            </p>
                        </article>
                    @endforeach
                </div>
            @else
                <p class="px-5 py-8 text-center text-sm text-slate-500 sm:px-6">
                    Belum ada sumber dana. Tambahkan sumber dana untuk mulai melihat rincian saldo.
                </p>
            @endif
        </section>

        {{-- Section Transaksi Terbaru --}}
        <section aria-labelledby="transactions-heading" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <h2 id="transactions-heading" class="text-lg font-bold text-slate-900">Transaksi terbaru</h2>
                    <p class="mt-1 text-sm text-slate-500">Pemasukan dan pengeluaran terakhir yang tercatat.</p>
                </div>
            </div>

            @if ($recentTransactions->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-semibold">Transaksi</th>
                                <th scope="col" class="px-6 py-3 font-semibold">Sumber dana</th>
                                <th scope="col" class="px-6 py-3 font-semibold">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-right font-semibold">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($recentTransactions as $transaction)
                                @php($isIncome = $transaction['tipe'] === 'pemasukkan')
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full {{ $isIncome ? 'bg-emerald-50 text-emerald-600' : 'bg-orange-50 text-[#FF8C52]' }}" aria-hidden="true">
                                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    @if ($isIncome)
                                                        <path d="M7 14l5-5 5 5M12 9v11M5 4h14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                                    @else
                                                        <path d="M7 10l5 5 5-5M12 15V4M5 20h14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                                    @endif
                                                </svg>
                                            </span>
                                            <div>
                                                <p class="font-semibold text-slate-900">{{ $transaction['nama'] }}</p>
                                                <p class="mt-0.5 text-xs text-slate-500">{{ $isIncome ? 'Pemasukan' : 'Pengeluaran' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">{{ $transaction['sumber_dana'] }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $transaction['tanggal']->translatedFormat('d M Y') }}</td>
                                    <td class="px-6 py-4 text-right font-semibold {{ $isIncome ? 'text-emerald-700' : 'text-slate-900' }}">
                                        {{ $isIncome ? '+' : '-' }}Rp {{ number_format((float) $transaction['jumlah'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="px-5 py-8 text-center text-sm text-slate-500 sm:px-6">
                    Belum ada transaksi yang tercatat.
                </p>
            @endif
        </section>

        {{-- Modal Quick Expense --}}
        <div
            x-cloak
            x-show="quickExpenseOpen"
            x-transition.opacity
            x-on:click.self="quickExpenseOpen = false"
            class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/50 p-0 sm:items-center sm:p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="quick-expense-heading"
        >
            <section
                x-show="quickExpenseOpen"
                x-transition
                class="max-h-[90vh] w-full overflow-y-auto rounded-t-2xl bg-white p-5 shadow-xl sm:max-w-lg sm:rounded-2xl sm:p-6"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="quick-expense-heading" class="text-xl font-bold text-slate-900">Catat pengeluaran</h2>
                        <p class="mt-1 text-sm text-slate-500">Transaksi akan langsung ditambahkan ke riwayat Anda.</p>
                    </div>
                    <button
                        type="button"
                        x-on:click="quickExpenseOpen = false"
                        class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Tutup formulir"
                    >
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path d="m6 6 12 12M18 6 6 18" stroke-linecap="round" stroke-width="2" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('quick-pengeluaran.store') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="tanggal" class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal</label>
                        <input
                            id="tanggal"
                            name="tanggal"
                            type="date"
                            value="{{ old('tanggal', now()->toDateString()) }}"
                            required
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-[#359FA0] focus:ring-2 focus:ring-[#359FA0]/20"
                        >
                        @error('tanggal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nama_pengeluaran" class="mb-1.5 block text-sm font-medium text-slate-700">Nama pengeluaran</label>
                        <input
                            id="nama_pengeluaran"
                            name="nama_pengeluaran"
                            type="text"
                            value="{{ old('nama_pengeluaran') }}"
                            maxlength="255"
                            required
                            placeholder="Contoh: Makan siang"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-[#359FA0] focus:ring-2 focus:ring-[#359FA0]/20"
                        >
                        @error('nama_pengeluaran')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jumlah" class="mb-1.5 block text-sm font-medium text-slate-700">Jumlah (Rp)</label>
                        <input
                            id="jumlah"
                            name="jumlah"
                            type="number"
                            min="1"
                            step="1"
                            value="{{ old('jumlah') }}"
                            required
                            placeholder="0"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-[#359FA0] focus:ring-2 focus:ring-[#359FA0]/20"
                        >
                        @error('jumlah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sumber_dana_id" class="mb-1.5 block text-sm font-medium text-slate-700">Sumber dana</label>
                        <select
                            id="sumber_dana_id"
                            name="sumber_dana_id"
                            required
                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-[#359FA0] focus:ring-2 focus:ring-[#359FA0]/20"
                        >
                            <option value="">Pilih sumber dana</option>
                            @foreach ($sumberDanaOptions as $sumberDana)
                                <option value="{{ $sumberDana->id }}" @selected((string) old('sumber_dana_id') === (string) $sumberDana->id)>
                                    {{ $sumberDana->nama_sumber_dana }}
                                </option>
                            @endforeach
                        </select>
                        @error('sumber_dana_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            x-on:click="quickExpenseOpen = false"
                            class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="rounded-xl bg-[#359FA0] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#2d898a] focus:outline-none focus:ring-2 focus:ring-[#359FA0] focus:ring-offset-2"
                        >
                            Simpan pengeluaran
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endcomponent