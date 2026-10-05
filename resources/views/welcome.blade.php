@component('components.layouts.app', ['title' => 'Dashboard'])
    <div
        class="space-y-10 font-sans"
        x-data="{ quickExpenseOpen: @js($errors->any()) }"
        x-on:keydown.escape.window="quickExpenseOpen = false"
    >
        {{-- HEADER --}}
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</p>
                <h1 class="mt-1 font-heading text-3xl font-bold tracking-tight text-brand-dark sm:text-4xl">Ringkasan Keuangan</h1>
                <p class="mt-1 text-sm text-slate-500">Pantau saldo dan transaksi keuangan Anda secara realtime.</p>
                <span class="mt-3 inline-flex rounded-full border border-brand-teal/15 bg-brand-teal-light/20 px-3 py-1 text-xs font-semibold text-brand-teal">{{ $summary['period_label'] }} bulan berjalan</span>
            </div>

            <button
                type="button"
                x-on:click="quickExpenseOpen = true"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-teal px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-teal/90 focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2"
            >
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M12 5v14m-7-7h14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                Catat Pengeluaran
            </button>
        </header>

        {{-- ALERT SUCCESS --}}
        @if (session('success'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- RINGKASAN DALAM TIGA SLIDE --}}
        <section aria-label="Ringkasan keuangan" x-data="{ activeSlide: 0, goToSlide(index) { this.activeSlide = index; this.$refs.summarySlides.scrollTo({ left: this.$refs.summarySlides.clientWidth * index, behavior: 'smooth' }) }, updateSlide() { this.activeSlide = Math.round(this.$refs.summarySlides.scrollLeft / this.$refs.summarySlides.clientWidth) } }" class="space-y-3">
            <div x-ref="summarySlides" x-on:scroll.debounce.100ms="updateSlide()" class="flex snap-x snap-mandatory gap-0 overflow-x-auto scroll-smooth pb-2 md:grid md:grid-cols-3 md:gap-5 md:overflow-visible md:pb-0">
                <div class="w-full shrink-0 snap-center space-y-4 px-1 md:w-auto">
                    <article class="flex min-h-36 flex-col justify-between rounded-3xl bg-brand-teal p-5 text-white shadow-md shadow-brand-teal/15 sm:p-6">
                        <p class="text-xs font-bold uppercase tracking-wider text-white/80">Total Saldo</p>
                        <p class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">Rp {{ number_format((float) ($summary['total_saldo'] ?? 0), 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs font-medium text-white/80">Saldo berjalan dari seluruh sumber dana</p>
                    </article>
                    <article class="flex min-h-36 flex-col justify-between rounded-3xl bg-brand-teal-light p-5 text-teal-950 shadow-sm sm:p-6">
                        <p class="text-xs font-bold uppercase tracking-wider text-teal-900/80">Pemasukan Bulan Ini</p>
                        <p class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">Rp {{ number_format((float) ($summary['total_pemasukkan'] ?? 0), 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs font-medium text-teal-900/80">Akumulasi dari awal bulan</p>
                    </article>
                    <article class="flex min-h-36 flex-col justify-between rounded-3xl bg-brand-orange p-5 text-white shadow-sm sm:p-6">
                        <p class="text-xs font-bold uppercase tracking-wider text-white/80">Pengeluaran Bulan Ini</p>
                        <p class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">Rp {{ number_format((float) ($summary['total_pengeluaran'] ?? 0), 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs font-medium text-white/80">Akumulasi dari awal bulan</p>
                    </article>
                </div>

                <div class="w-full shrink-0 snap-center space-y-4 px-1 md:w-auto">
                    <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-brand-orange/20 bg-brand-cream/70 p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-brand-orange">Pengeluaran Tertinggi</p>
                        <p class="mt-3 text-2xl font-extrabold text-brand-dark">Rp {{ number_format((float) ($summary['stats_all']['highest']['total'] ?? 0), 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs font-medium text-slate-500">{{ !empty($summary['stats_all']['highest']['tanggal']) ? \Carbon\Carbon::parse($summary['stats_all']['highest']['tanggal'])->translatedFormat('d F Y') : 'Belum ada data' }}</p>
                    </article>
                    <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-brand-teal/20 bg-brand-teal-light/30 p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-brand-teal">Pengeluaran Terendah</p>
                        <p class="mt-3 text-2xl font-extrabold text-brand-dark">Rp {{ number_format((float) ($summary['stats_all']['lowest']['total'] ?? 0), 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs font-medium text-slate-500">{{ !empty($summary['stats_all']['lowest']['tanggal']) ? \Carbon\Carbon::parse($summary['stats_all']['lowest']['tanggal'])->translatedFormat('d F Y') : 'Belum ada data' }}</p>
                    </article>
                    <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-brand-teal/15 bg-brand-teal/10 p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-brand-teal">Rata-rata Pengeluaran / Hari</p>
                        <p class="mt-3 text-2xl font-extrabold text-brand-dark">Rp {{ number_format((float) ($summary['stats_all']['average'] ?? 0), 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs font-medium text-slate-500">Berdasarkan hari dengan transaksi</p>
                    </article>
                </div>

                <div class="w-full shrink-0 snap-center space-y-4 px-1 md:w-auto">
                    <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-brand-orange/20 bg-brand-cream p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-brand-orange">Pengeluaran Tertinggi - Uang Makan</p>
                        <p class="mt-3 text-2xl font-extrabold text-brand-dark">Rp {{ number_format((float) ($summary['stats_uang_makan']['highest']['total'] ?? 0), 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs font-medium text-slate-500">{{ !empty($summary['stats_uang_makan']['highest']['tanggal']) ? \Carbon\Carbon::parse($summary['stats_uang_makan']['highest']['tanggal'])->translatedFormat('d F Y') : 'Belum ada data' }}</p>
                    </article>
                    <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-brand-teal/20 bg-brand-teal-light/30 p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-brand-teal">Pengeluaran Terendah - Uang Makan</p>
                        <p class="mt-3 text-2xl font-extrabold text-brand-dark">Rp {{ number_format((float) ($summary['stats_uang_makan']['lowest']['total'] ?? 0), 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs font-medium text-slate-500">{{ !empty($summary['stats_uang_makan']['lowest']['tanggal']) ? \Carbon\Carbon::parse($summary['stats_uang_makan']['lowest']['tanggal'])->translatedFormat('d F Y') : 'Belum ada data' }}</p>
                    </article>
                    <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-brand-orange/20 bg-brand-orange/10 p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-brand-orange">Rata-rata / Hari - Uang Makan</p>
                        <p class="mt-3 text-2xl font-extrabold text-brand-dark">Rp {{ number_format((float) ($summary['stats_uang_makan']['average'] ?? 0), 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs font-medium text-slate-500">Berdasarkan hari dengan transaksi</p>
                    </article>
                </div>
            </div>

            <div class="flex items-center justify-center gap-4 md:hidden">
                <button type="button" x-on:click="goToSlide(Math.max(0, activeSlide - 1))" :disabled="activeSlide === 0" class="rounded-full p-2 text-brand-teal transition hover:bg-brand-teal/10 disabled:cursor-not-allowed disabled:opacity-40" aria-label="Slide sebelumnya">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" /></svg>
                </button>
                <div class="flex items-center gap-2" aria-label="Pilih kelompok ringkasan">
                    <button type="button" x-on:click="goToSlide(0)" :aria-current="activeSlide === 0 ? 'true' : null" :class="activeSlide === 0 ? 'w-6 bg-brand-teal' : 'w-2 bg-brand-teal/25'" class="h-2 rounded-full transition-all" aria-label="Ringkasan saldo dan bulan ini"></button>
                    <button type="button" x-on:click="goToSlide(1)" :aria-current="activeSlide === 1 ? 'true' : null" :class="activeSlide === 1 ? 'w-6 bg-brand-teal' : 'w-2 bg-brand-teal/25'" class="h-2 rounded-full transition-all" aria-label="Statistik pengeluaran"></button>
                    <button type="button" x-on:click="goToSlide(2)" :aria-current="activeSlide === 2 ? 'true' : null" :class="activeSlide === 2 ? 'w-6 bg-brand-teal' : 'w-2 bg-brand-teal/25'" class="h-2 rounded-full transition-all" aria-label="Statistik uang makan"></button>
                </div>
                <button type="button" x-on:click="goToSlide(Math.min(2, activeSlide + 1))" :disabled="activeSlide === 2" class="rounded-full p-2 text-brand-teal transition hover:bg-brand-teal/10 disabled:cursor-not-allowed disabled:opacity-40" aria-label="Slide berikutnya">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" /></svg>
                </button>
            </div>
        </section>

        {{-- VISUALISASI KEUANGAN BULAN INI --}}
        @php
            $foodBudget = $summary['charts']['food_budget'];
            $foodSpentPercent = $foodBudget['budget'] > 0
                ? min(100, round($foodBudget['spent'] / $foodBudget['budget'] * 100, 1))
                : 0;
            $donutSegments = 'var(--color-brand-orange) 0% '.$foodSpentPercent.'%, var(--color-brand-teal-light) '.$foodSpentPercent.'% 100%';
            $dailyExpenses = $summary['charts']['daily_expenses'];
            $maxDailyExpense = max(1, max(array_column($dailyExpenses, 'amount')));
            $dailyLinePoints = collect($dailyExpenses)->map(function ($day, $index) use ($dailyExpenses, $maxDailyExpense) {
                $x = count($dailyExpenses) > 1 ? $index * 600 / (count($dailyExpenses) - 1) : 300;
                $y = 190 - ($day['amount'] / $maxDailyExpense * 160);

                return round($x, 1).','.round($y, 1);
            })->implode(' ');
        @endphp
        <section aria-labelledby="charts-heading" class="space-y-4">
            <div>
                <h2 id="charts-heading" class="text-xl font-bold text-brand-dark">Visualisasi Keuangan</h2>
                <p class="mt-1 text-sm text-slate-500">Pemakaian budget uang makan dan pengeluaran harian {{ $summary['period_label'] }}.</p>
            </div>

            <div class="grid gap-5 lg:grid-cols-2">
                <article class="rounded-3xl border border-brand-teal/15 bg-white p-5 shadow-sm sm:p-6">
                    <h3 class="font-semibold text-brand-dark">Budget Uang Makan</h3>
                    <p class="mt-1 text-sm text-slate-500">Alokasi Rp {{ number_format($foodBudget['daily_budget'], 0, ',', '.') }} per hari hingga hari ini.</p>
                    @if ($foodBudget['budget'] > 0)
                        <div class="mt-6 flex flex-col items-center gap-6 sm:flex-row sm:justify-center">
                            <div class="flex size-44 shrink-0 items-center justify-center rounded-full" role="img" aria-label="Diagram donat penyerapan budget uang makan" style="background: conic-gradient({{ $donutSegments }})">
                                <div class="flex size-28 flex-col items-center justify-center rounded-full bg-white text-center">
                                    <span class="text-xs text-slate-500">Budget MTD</span>
                                    <span class="mt-1 max-w-24 text-sm font-bold leading-tight text-brand-dark">Rp {{ number_format($foodBudget['budget'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="w-full space-y-4">
                                <div class="flex items-start gap-3 text-sm">
                                    <span class="mt-1 size-3 shrink-0 rounded-sm bg-brand-orange"></span>
                                    <div class="flex-1"><p class="text-slate-600">Terserap</p><p class="font-semibold text-brand-dark">Rp {{ number_format($foodBudget['spent'], 0, ',', '.') }} ({{ number_format($foodSpentPercent, 1, ',', '.') }}%)</p></div>
                                </div>
                                <div class="flex items-start gap-3 text-sm">
                                    <span class="mt-1 size-3 shrink-0 rounded-sm bg-brand-teal-light"></span>
                                    <div class="flex-1"><p class="text-slate-600">Sisa alokasi bulan ini</p><p class="font-semibold text-brand-dark">Rp {{ number_format($foodBudget['remaining'], 0, ',', '.') }}</p></div>
                                </div>
                                @if ($foodBudget['spent'] > $foodBudget['budget'])
                                    <p class="rounded-xl bg-brand-orange/10 px-3 py-2 text-xs font-medium text-brand-dark">Penyerapan melewati alokasi berjalan sebesar Rp {{ number_format($foodBudget['spent'] - $foodBudget['budget'], 0, ',', '.') }}.</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="mt-8 rounded-2xl bg-brand-cream/50 px-4 py-8 text-center text-sm text-slate-500">Belum ada alokasi budget uang makan untuk bulan ini.</p>
                    @endif
                </article>

                <article class="rounded-3xl border border-brand-teal/15 bg-white p-5 shadow-sm sm:p-6">
                    <h3 class="font-semibold text-brand-dark">Pengeluaran harian</h3>
                    <p class="mt-1 text-sm text-slate-500">Total pengeluaran semua sumber dana per hari.</p>
                    <div class="mt-5 overflow-hidden">
                        <svg viewBox="0 0 600 230" class="w-full" role="img" aria-label="Grafik garis total pengeluaran harian bulan ini" preserveAspectRatio="none">
                            <line x1="0" y1="30" x2="600" y2="30" stroke="currentColor" class="text-slate-100" />
                            <line x1="0" y1="83" x2="600" y2="83" stroke="currentColor" class="text-slate-100" />
                            <line x1="0" y1="137" x2="600" y2="137" stroke="currentColor" class="text-slate-100" />
                            <line x1="0" y1="190" x2="600" y2="190" stroke="currentColor" class="text-slate-200" />
                            <polyline points="{{ $dailyLinePoints }}" fill="none" stroke="var(--color-brand-teal)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                            @foreach ($dailyExpenses as $index => $day)
                                @php
                                    $pointX = count($dailyExpenses) > 1 ? $index * 600 / (count($dailyExpenses) - 1) : 300;
                                    $pointY = 190 - ($day['amount'] / $maxDailyExpense * 160);
                                @endphp
                                <circle cx="{{ round($pointX, 1) }}" cy="{{ round($pointY, 1) }}" r="3.5" fill="var(--color-brand-orange)">
                                    <title>{{ $day['day'] }} {{ $summary['period_label'] }}: Rp {{ number_format($day['amount'], 0, ',', '.') }}</title>
                                </circle>
                                @if ($day['day'] === 1 || $day['day'] % 5 === 0 || $day['day'] === count($dailyExpenses))
                                    <text x="{{ round($pointX, 1) }}" y="218" text-anchor="middle" class="fill-slate-500 text-[11px]">{{ $day['day'] }}</text>
                                @endif
                            @endforeach
                        </svg>
                    </div>
                    <p class="mt-2 text-center text-xs text-slate-500">Tanggal pada bulan ini - jumlah dalam rupiah</p>
                </article>
            </div>
        </section>

        {{-- RENCANA BUDGET UANG MAKAN --}}
        <section aria-labelledby="food-budget-heading" class="overflow-hidden rounded-3xl border border-brand-orange/20 bg-brand-cream/40 shadow-sm">
            <div class="border-b border-brand-orange/10 px-5 py-5 sm:px-6">
                <h2 id="food-budget-heading" class="text-lg font-bold text-brand-dark">Rencana budget uang makan - 10 hari</h2>
                <p class="mt-1 text-sm text-slate-600">Jatah harian Rp {{ number_format($foodBudget['daily_budget'], 0, ',', '.') }}. Sisa harian tidak dibawa ke hari berikutnya; kelebihan belanja mengurangi jatah hari selanjutnya.</p>
            </div>
            @if ($summary['charts']['food_budget_forecast'])
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px] text-left text-sm">
                        <thead class="bg-brand-teal-light/30 text-xs uppercase tracking-wide text-slate-600">
                            <tr>
                                <th scope="col" class="px-5 py-3 font-semibold sm:px-6">Tanggal</th>
                                <th scope="col" class="px-5 py-3 text-right font-semibold sm:px-6">Jatah setelah potongan</th>
                                <th scope="col" class="px-5 py-3 text-right font-semibold sm:px-6">Pengeluaran</th>
                                <th scope="col" class="px-5 py-3 text-right font-semibold sm:px-6">Sisa / defisit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-orange/10">
                            @foreach ($summary['charts']['food_budget_forecast'] as $day)
                                <tr class="{{ $loop->first ? 'bg-white/70' : '' }}">
                                    <th scope="row" class="whitespace-nowrap px-5 py-3.5 font-medium text-brand-dark sm:px-6">{{ $day['date']->translatedFormat('D, d M') }} @if ($loop->first)<span class="ml-1 text-xs font-normal text-brand-teal">Hari ini</span>@endif</th>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right text-slate-600 sm:px-6">Rp {{ number_format($day['allowance'], 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right text-slate-600 sm:px-6">{{ $day['spent'] > 0 ? 'Rp '.number_format($day['spent'], 0, ',', '.') : '-' }}</td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right font-semibold {{ $day['over_budget'] > 0 ? 'text-brand-orange' : 'text-brand-teal' }} sm:px-6">{{ $day['over_budget'] > 0 ? '-Rp '.number_format($day['over_budget'], 0, ',', '.') : 'Rp '.number_format($day['remaining'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="px-5 py-8 text-center text-sm text-slate-600 sm:px-6">Tambahkan sumber dana Uang Makan untuk melihat proyeksi jatah harian.</p>
            @endif
        </section>

        {{-- SECTION 5: SALDO PER SUMBER DANA --}}
        <section aria-labelledby="funds-heading" class="overflow-hidden rounded-3xl border border-brand-teal/15 bg-brand-teal-light/20 shadow-sm">
            <div class="flex items-center justify-between border-b border-brand-teal/10 bg-brand-teal-light/30 px-5 py-5 sm:px-6">
                <div>
                    <h2 id="funds-heading" class="text-lg font-bold text-brand-dark">Saldo per Sumber Dana</h2>
                    <p class="mt-1 text-sm text-slate-500">Rincian budget, pengeluaran bulan ini, dan saldo berjalan setiap sumber.</p>
                </div>
            </div>

            @if (($summary['sumber_dana'] ?? collect())->isNotEmpty())
                <div class="grid gap-3 p-4 sm:grid-cols-2 sm:p-5 xl:grid-cols-3">
                    @foreach ($summary['sumber_dana'] as $sumberDana)
                        <article class="flex min-w-0 flex-col justify-between gap-4 rounded-2xl border border-brand-teal/10 bg-brand-cream/70 p-4 shadow-xs transition duration-200 hover:-translate-y-0.5 hover:shadow-sm sm:p-5">
                            <div class="min-w-0">
                                <h3 class="truncate font-semibold text-slate-900">{{ $sumberDana['nama'] }}</h3>
                                @if (filled($sumberDana['keterangan'] ?? null))
                                    <p class="mt-1 truncate text-sm text-slate-500">{{ $sumberDana['keterangan'] }}</p>
                                @endif
                                <p class="mt-1 text-xs text-slate-500">
                                    Budget Rp {{ number_format((float) $sumberDana['budget'], 0, ',', '.') }}
                                    <span class="px-1 font-bold text-slate-300">·</span>
                                    Pengeluaran bulan ini Rp {{ number_format((float) $sumberDana['pengeluaran'], 0, ',', '.') }}
                                </p>
                            </div>
                            <p class="shrink-0 text-lg font-bold text-brand-teal">
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

        {{-- SECTION 6: TRANSAKSI TERBARU --}}
        <section aria-labelledby="transactions-heading" class="overflow-hidden rounded-3xl border border-brand-teal/15 bg-brand-cream/50 shadow-sm">
            <div class="flex items-center justify-between border-b border-brand-teal/10 bg-brand-teal-light/30 px-5 py-5 sm:px-6">
                <div>
                    <h2 id="transactions-heading" class="text-lg font-bold text-brand-dark">Transaksi Terbaru</h2>
                    <p class="mt-1 text-sm text-slate-500">5 transaksi terakhir pada bulan ini.</p>
                </div>
            </div>

            @if ($recentTransactions->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-left text-sm">
                        <thead class="bg-brand-teal-light/40 text-xs uppercase tracking-wide text-slate-600">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 font-semibold">Transaksi</th>
                                <th scope="col" class="px-6 py-3.5 font-semibold">Sumber Dana</th>
                                <th scope="col" class="px-6 py-3.5 font-semibold">Tanggal</th>
                                <th scope="col" class="px-6 py-3.5 text-right font-semibold">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($recentTransactions as $transaction)
                                @php($isIncome = $transaction['tipe'] === 'pemasukkan')
                                <tr class="transition hover:bg-brand-bg">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full {{ $isIncome ? 'bg-brand-teal-light/30 text-brand-teal' : 'bg-brand-orange/20 text-brand-orange' }}" aria-hidden="true">
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
                                    <td class="px-6 py-4 text-right font-semibold {{ $isIncome ? 'text-brand-teal' : 'text-slate-900' }}">
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

        {{-- MODAL QUICK EXPENSE --}}
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
                        <h2 id="quick-expense-heading" class="font-heading text-xl font-bold text-slate-900">Catat Pengeluaran</h2>
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
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20"
                        >
                        @error('tanggal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nama_pengeluaran" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Pengeluaran</label>
                        <input
                            id="nama_pengeluaran"
                            name="nama_pengeluaran"
                            type="text"
                            value="{{ old('nama_pengeluaran') }}"
                            maxlength="255"
                            required
                            placeholder="Contoh: Makan siang"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20"
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
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20"
                        >
                        @error('jumlah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sumber_dana_id" class="mb-1.5 block text-sm font-medium text-slate-700">Sumber Dana</label>
                        <select
                            id="sumber_dana_id"
                            name="sumber_dana_id"
                            required
                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20"
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
                            class="rounded-xl bg-brand-teal px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-teal/90 focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2"
                        >
                            Simpan Pengeluaran
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endcomponent