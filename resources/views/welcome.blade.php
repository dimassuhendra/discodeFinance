@component('components.layouts.app', ['title' => 'Dashboard Keuangan'])
<div class="space-y-6 font-sans">

    {{-- HEADER --}}
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</p>
            <h1 class="mt-1 font-heading text-3xl font-bold tracking-tight text-brand-dark sm:text-4xl">Dashboard Keuangan</h1>
            <p class="mt-1 text-sm text-slate-500">Ringkasan kondisi keuangan {{ $summary['period_label'] }}.</p>
        </div>
        <div class="rounded-xl border border-brand-teal/15 bg-white px-4 py-3 shadow-sm">
            <p class="text-xs font-medium text-slate-500">Periode</p>
            <p class="mt-0.5 font-heading text-sm font-bold text-brand-dark">{{ $summary['period_label'] }}</p>
        </div>
    </header>

    {{-- ALERT --}}
    @if (session('success'))
        <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
            <p class="font-bold">Terjadi kesalahan:</p>
            <ul class="mt-1 list-inside list-disc space-y-0.5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SUMMARY --}}
    <section
        aria-label="Ringkasan keuangan"
        x-data="{
            activeSlide: 0,
            goToSlide(index) {
                this.activeSlide = index;
                this.$refs.summarySlides.scrollTo({
                    left: this.$refs.summarySlides.clientWidth * index,
                    behavior: 'smooth'
                });
            },
            updateSlide() {
                this.activeSlide = Math.round(
                    this.$refs.summarySlides.scrollLeft / this.$refs.summarySlides.clientWidth
                );
            }
        }"
        class="space-y-3">
        <div
            x-ref="summarySlides"
            x-on:scroll.debounce.100ms="updateSlide()"
            class="flex snap-x snap-mandatory gap-0 overflow-x-auto scroll-smooth pb-2 md:grid md:grid-cols-3 md:gap-5 md:overflow-visible md:pb-0"
        >
            {{-- SLIDE 1 --}}
            <div class="w-full shrink-0 snap-center space-y-4 px-1 md:w-auto">
                {{-- Total Saldo --}}
                <article class="flex min-h-36 flex-col justify-between rounded-3xl bg-brand-teal p-5 text-white shadow-md shadow-brand-teal/15 sm:p-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-white/80">Total Saldo</p>
                    <p class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">
                        Rp {{ number_format((float) ($summary['total_saldo'] ?? 0), 0, ',', '.') }}
                    </p>
                    <p class="mt-2 text-xs font-medium text-white/80">Saldo berjalan dari seluruh sumber dana</p>
                </article>

                {{-- Pemasukan --}}
                <article class="flex min-h-36 flex-col justify-between rounded-3xl bg-brand-teal-light p-5 text-teal-950 shadow-sm sm:p-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-teal-900/80">Pemasukan Bulan Ini</p>
                    <p class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">
                        Rp {{ number_format((float) ($summary['total_pemasukkan'] ?? 0), 0, ',', '.') }}
                    </p>
                    <p class="mt-2 text-xs font-medium text-teal-900/80">Akumulasi dari awal bulan</p>
                </article>

                {{-- Pengeluaran --}}
                <article class="flex min-h-36 flex-col justify-between rounded-3xl bg-brand-orange p-5 text-white shadow-sm sm:p-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-white/80">Pengeluaran Bulan Ini</p>
                    <p class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">
                        Rp {{ number_format((float) ($summary['total_pengeluaran'] ?? 0), 0, ',', '.') }}
                    </p>
                    <p class="mt-2 text-xs font-medium text-white/80">Akumulasi dari awal bulan</p>
                </article>
            </div>

            {{-- SLIDE 2 --}}
            <div class="w-full shrink-0 snap-center space-y-4 px-1 md:w-auto">
                {{-- Pengeluaran Tertinggi --}}
                <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-brand-orange/20 bg-brand-cream/70 p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-brand-orange">Pengeluaran Tertinggi</p>
                    <p class="mt-3 text-2xl font-extrabold text-brand-dark">
                        Rp {{ number_format((float) ($summary['stats_all']['highest']['total'] ?? 0), 0, ',', '.') }}
                    </p>
                    <p class="mt-2 text-xs font-medium text-slate-500">
                        {{ !empty($summary['stats_all']['highest']['tanggal']) ? \Carbon\Carbon::parse($summary['stats_all']['highest']['tanggal'])->translatedFormat('d F Y') : 'Belum ada data' }}
                    </p>
                </article>

                {{-- ARUS KAS BERSIH --}}
                <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-brand-teal/20 bg-brand-teal-light/30 p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-brand-teal">Arus Kas Bersih</p>
                    <p class="mt-3 text-2xl font-extrabold tracking-tight {{ ($summary['cash_flow'] ?? 0) >= 0 ? 'text-brand-teal' : 'text-brand-orange' }}">
                        {{ ($summary['cash_flow'] ?? 0) >= 0 ? '+' : '-' }}Rp {{ number_format(abs((float) ($summary['cash_flow'] ?? 0)), 0, ',', '.') }}
                    </p>
                    <p class="mt-2 text-xs font-medium text-slate-500">Pemasukan dikurangi pengeluaran bulan ini</p>
                </article>

                {{-- Rata-rata --}}
                <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-brand-teal/15 bg-brand-teal/10 p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-brand-teal">Rata-rata Pengeluaran / Hari</p>
                    <p class="mt-3 text-2xl font-extrabold text-brand-dark">
                        Rp {{ number_format((float) ($summary['stats_all']['average'] ?? 0), 0, ',', '.') }}
                    </p>
                    <p class="mt-2 text-xs font-medium text-slate-500">Berdasarkan hari dengan transaksi</p>
                </article>
            </div>

            {{-- SLIDE 3 --}}
            <div class="w-full shrink-0 snap-center space-y-4 px-1 md:w-auto">
                {{-- Uang Makan Tertinggi --}}
                <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-brand-orange/20 bg-brand-cream p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-brand-orange">Pengeluaran Tertinggi - Uang Makan</p>
                    <p class="mt-3 text-2xl font-extrabold text-brand-dark">
                        Rp {{ number_format((float) ($summary['stats_uang_makan']['highest']['total'] ?? 0), 0, ',', '.') }}
                    </p>
                    <p class="mt-2 text-xs font-medium text-slate-500">
                        {{ !empty($summary['stats_uang_makan']['highest']['tanggal']) ? \Carbon\Carbon::parse($summary['stats_uang_makan']['highest']['tanggal'])->translatedFormat('d F Y') : 'Belum ada data' }}
                    </p>
                </article>

                {{-- Uang Makan Terendah --}}
                <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-brand-teal/20 bg-brand-teal-light/30 p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-brand-teal">Pengeluaran Terendah - Uang Makan</p>
                    <p class="mt-3 text-2xl font-extrabold text-brand-dark">
                        Rp {{ number_format((float) ($summary['stats_uang_makan']['lowest']['total'] ?? 0), 0, ',', '.') }}
                    </p>
                    <p class="mt-2 text-xs font-medium text-slate-500">
                        {{ !empty($summary['stats_uang_makan']['lowest']['tanggal']) ? \Carbon\Carbon::parse($summary['stats_uang_makan']['lowest']['tanggal'])->translatedFormat('d F Y') : 'Belum ada data' }}
                    </p>
                </article>

                {{-- Rata-rata Uang Makan --}}
                <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-brand-orange/20 bg-brand-orange/10 p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-brand-orange">Rata-rata / Hari - Uang Makan</p>
                    <p class="mt-3 text-2xl font-extrabold text-brand-dark">
                        Rp {{ number_format((float) ($summary['stats_uang_makan']['average'] ?? 0), 0, ',', '.') }}
                    </p>
                    <p class="mt-2 text-xs font-medium text-slate-500">Berdasarkan hari dengan transaksi</p>
                </article>
            </div>
        </div>

        {{-- MOBILE SLIDE CONTROL --}}
        <div class="flex items-center justify-center gap-4 md:hidden">
            <button
                type="button"
                x-on:click="goToSlide(Math.max(0, activeSlide - 1))"
                :disabled="activeSlide === 0"
                class="rounded-full p-2 text-brand-teal transition hover:bg-brand-teal/10 disabled:cursor-not-allowed disabled:opacity-40"
                aria-label="Slide sebelumnya"
            >
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
            </button>

            <div class="flex items-center gap-2" aria-label="Pilih kelompok ringkasan">
                <button
                    type="button"
                    x-on:click="goToSlide(0)"
                    :aria-current="activeSlide === 0 ? 'true' : null"
                    :class="activeSlide === 0 ? 'w-6 bg-brand-teal' : 'w-2 bg-brand-teal/25'"
                    class="h-2 rounded-full transition-all"
                    aria-label="Ringkasan saldo dan bulan ini"
                ></button>
                <button
                    type="button"
                    x-on:click="goToSlide(1)"
                    :aria-current="activeSlide === 1 ? 'true' : null"
                    :class="activeSlide === 1 ? 'w-6 bg-brand-teal' : 'w-2 bg-brand-teal/25'"
                    class="h-2 rounded-full transition-all"
                    aria-label="Statistik pengeluaran"
                ></button>
                <button
                    type="button"
                    x-on:click="goToSlide(2)"
                    :aria-current="activeSlide === 2 ? 'true' : null"
                    :class="activeSlide === 2 ? 'w-6 bg-brand-teal' : 'w-2 bg-brand-teal/25'"
                    class="h-2 rounded-full transition-all"
                    aria-label="Statistik uang makan"
                ></button>
            </div>

            <button
                type="button"
                x-on:click="goToSlide(Math.min(2, activeSlide + 1))"
                :disabled="activeSlide === 2"
                class="rounded-full p-2 text-brand-teal transition hover:bg-brand-teal/10 disabled:cursor-not-allowed disabled:opacity-40"
                aria-label="Slide berikutnya"
            >
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
            </button>
        </div>
    </section>

    {{-- LINE CHART FULL WIDTH --}}
    <section class="rounded-3xl border border-brand-teal/15 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-brand-teal">Tren Pengeluaran</p>
                <h2 class="mt-1 font-heading text-xl font-bold text-brand-dark">Pengeluaran Harian</h2>
                <p class="mt-1 text-sm text-slate-500">Pergerakan total pengeluaran setiap hari selama {{ $summary['period_label'] }}.</p>
            </div>
            <div class="rounded-xl bg-brand-bg px-4 py-2">
                <p class="text-xs text-slate-500">Total Pengeluaran</p>
                <p class="font-heading text-sm font-bold text-brand-dark">Rp {{ number_format($summary['total_pengeluaran'], 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="mt-6 h-[320px] w-full">
            <canvas id="dailyExpenseChart"></canvas>
        </div>
    </section>

    {{-- BUDGET CHARTS --}}
    <section class="rounded-3xl border border-brand-teal/15 bg-white p-5 shadow-sm sm:p-6 w-full">
        <!-- Header Section -->
        <div class="border-b border-slate-100 pb-4">
            <p class="text-xs font-bold uppercase tracking-wider text-brand-teal">Budget Sumber Dana</p>
            <h2 class="mt-1 font-heading text-xl font-bold text-brand-dark">Analisis & Penggunaan Budget</h2>
            <p class="mt-0.5 text-xs text-slate-400">Perbandingan budget, pengeluaran, dan porsi distribusi berdasarkan sumber dana.</p>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 w-full max-w-full overflow-hidden">
            <!-- ITEM 1: BAR CHART -->
            <div class="flex flex-col justify-between rounded-2xl border border-slate-100 bg-slate-50/50 p-4 w-full min-w-0 overflow-hidden">
                <p class="mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wide text-center">Perbandingan Nominal</p>
                <!-- Pembatas Canvas Ekstrem untuk Mobile -->
                <div class="relative h-[210px] w-full max-w-full min-w-0 overflow-hidden">
                    <canvas id="budgetSourceChart"></canvas>
                </div>
            </div>

            <!-- ITEM 2: DONUT CHART -->
            <div class="flex flex-col justify-between rounded-2xl border border-slate-100 bg-slate-50/50 p-4 w-full min-w-0 overflow-hidden">
                <p class="mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wide text-center">Porsi Distribusi Pengeluaran</p>
                <!-- Pembatas Canvas Ekstrem untuk Mobile -->
                <div class="relative h-[210px] w-full max-w-full min-w-0 overflow-hidden">
                    <canvas id="budgetDonutChart"></canvas>
                </div>
            </div>

            <!-- ITEM 3: PROGRESS BAR LIST -->
            <div class="flex flex-col justify-between rounded-2xl border border-slate-100 bg-slate-50/50 p-4 w-full min-w-0 overflow-hidden">
                <p class="mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wide text-center">Tingkat Penggunaan (%)</p>
                <div class="h-[210px] w-full space-y-3.5 overflow-y-auto pr-1">
                    @foreach ($summary['budget_usage'] as $budget)
                        <div>
                            <div class="mb-1 flex items-center justify-between gap-2 text-xs">
                                <span class="truncate font-semibold text-brand-dark">{{ $budget['nama'] }}</span>
                                <div class="flex items-center gap-1 shrink-0">
                                    <span class="font-bold {{ $budget['over_budget'] ? 'text-brand-orange' : 'text-brand-teal' }}">
                                        {{ $budget['persentase'] }}%
                                    </span>
                                </div>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-slate-200/70">
                                <div class="h-full rounded-full transition-all duration-500 {{ $budget['over_budget'] ? 'bg-brand-orange' : 'bg-brand-teal' }}" style="width: {{ min(100, $budget['persentase']) }}%"></div>
                            </div>
                            <div class="mt-0.5 text-[10px] text-slate-400 text-right">
                                Rp {{ number_format($budget['pengeluaran'] ?? $budget['terpakai'] ?? 0, 0, ',', '.') }} / {{ number_format($budget['budget'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- INSIGHT --}}
   <section class="rounded-3xl border border-brand-teal/15 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-5">
            <p class="text-xs font-bold uppercase tracking-wider text-brand-orange">Insight Keuangan</p>
            <h2 class="mt-1 font-heading text-xl font-bold text-brand-dark">Kondisi Keuangan Saat Ini</h2>
            <p class="mt-1 text-sm text-slate-500">Informasi yang dihasilkan berdasarkan kondisi pemasukan, pengeluaran, dan penggunaan budget.</p>
        </div>

        @if (count($summary['insights']))
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                @foreach ($summary['insights'] as $insight)
                    @php
                        $style = match ($insight['type']) {
                            'positive' => ['box' => 'border-brand-teal/20 bg-brand-teal-light/20', 'icon' => 'bg-brand-teal text-white', 'title' => 'text-brand-teal', 'symbol' => '✓'],
                            'warning' => ['box' => 'border-brand-orange/20 bg-brand-orange/10', 'icon' => 'bg-brand-orange text-white', 'title' => 'text-brand-orange', 'symbol' => '!'],
                            default => ['box' => 'border-brand-orange/20 bg-brand-orange/10', 'icon' => 'bg-brand-orange text-white', 'title' => 'text-brand-orange', 'symbol' => 'i'],
                        };
                    @endphp

                    <div class="rounded-2xl border p-4 {{ $style['box'] }}">
                        <div class="flex items-start gap-3">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-xl {{ $style['icon'] }} font-bold">{{ $style['symbol'] }}</span>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-bold {{ $style['title'] }}">{{ $insight['title'] }}</h3>
                                <p class="mt-1 text-xs leading-5 text-slate-600">{{ $insight['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-xl bg-brand-bg p-5 text-center text-sm text-slate-500">Belum ada insight keuangan.</div>
        @endif
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start w-full max-w-full min-w-0 overflow-hidden">
        {{-- KARTU 1: BUDGET MAKAN (LEFT) --}}
        <section class="rounded-3xl border border-brand-orange/15 bg-white shadow-sm flex flex-col h-full w-full min-w-0 overflow-hidden">
            <!-- Header -->
            <div class="border-b border-slate-100 px-4 py-4 sm:px-6">
                <p class="text-xs font-bold uppercase tracking-wider text-brand-orange">Budget Makan</p>
                <h2 class="mt-1 font-heading text-lg sm:text-xl font-bold text-brand-dark">Riwayat & Proyeksi Uang Makan</h2>
                <p class="mt-0.5 text-xs text-slate-400">
                    Budget harian dasar: <span class="font-semibold text-brand-dark">Rp {{ number_format($summary['charts']['food_budget']['daily_budget'] ?? 0, 0, ',', '.') }}</span>
                </p>
            </div>

            @php
                $foodForecast = $summary['charts']['food_budget_forecast'] ?? [];
            @endphp

            <!-- Content Table Wrapper dengan Scroll Horizontal Halus Jika Dibutuhkan -->
            <div class="flex-1 overflow-x-auto overflow-y-auto max-h-[420px] w-full min-w-0" id="foodTableContainer">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="sticky top-0 bg-slate-50 text-slate-500 font-semibold border-b border-slate-100 z-10">
                        <tr>
                            <th class="py-3 px-3 sm:px-5">Tanggal</th>
                            <th class="py-3 px-2 sm:px-3 text-right">Jatah</th>
                            <th class="py-3 px-2 sm:px-3 text-right">Terpakai</th>
                            <th class="py-3 px-3 sm:px-5 text-right">Sisa / Over</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($foodForecast as $item)
                            @php
                                $dateObj = \Carbon\Carbon::parse($item['date']);
                                $isToday = $dateObj->isToday();
                                $isTomorrow = $dateObj->isTomorrow();
                                $hasOver = $item['over_budget'] > 0;
                            @endphp
                            <tr id="{{ $isToday ? 'row-today' : '' }}" class="transition hover:bg-brand-bg {{ $isToday ? 'bg-brand-orange/10 font-semibold' : ($isTomorrow ? 'bg-slate-50/50' : '') }}">
                                <!-- Tanggal -->
                                <td class="py-3.5 px-3 sm:px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-1">
                                        <span class="{{ $isToday ? 'text-brand-orange font-bold' : 'text-slate-700' }}">
                                            {{ $dateObj->translatedFormat('d M') }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Jatah / Allowance -->
                                <td class="py-3.5 px-2 sm:px-3 text-right text-slate-600 font-medium whitespace-nowrap">
                                    Rp {{ number_format($item['allowance'], 0, ',', '.') }}
                                </td>

                                <!-- Terpakai / Spent -->
                                <td class="py-3.5 px-2 sm:px-3 text-right font-medium whitespace-nowrap {{ $item['spent'] > 0 ? 'text-brand-orange' : 'text-slate-400' }}">
                                    Rp {{ number_format($item['spent'], 0, ',', '.') }}
                                </td>

                                <!-- Sisa / Over Budget -->
                                <td class="py-3.5 px-3 sm:px-5 text-right font-medium whitespace-nowrap">
                                    @if($hasOver)
                                        <span class="text-red-500 font-semibold">
                                            +Rp {{ number_format($item['over_budget'], 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-brand-teal">
                                            Rp {{ number_format($item['remaining'], 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-500">
                                    Tidak ada data proyeksi uang makan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>            
        </section>

        {{-- KARTU 2: TRANSAKSI TERBARU (RIGHT) --}}
        <section class="rounded-3xl border border-brand-teal/15 bg-white shadow-sm flex flex-col h-full w-full min-w-0 overflow-hidden">
            <!-- Header -->
            <div class="border-b border-slate-100 px-4 py-4 sm:px-6">
                <p class="text-xs font-bold uppercase tracking-wider text-brand-teal">Aktivitas</p>
                <h2 class="mt-1 font-heading text-lg sm:text-xl font-bold text-brand-dark">Transaksi Terbaru</h2>
                <p class="mt-0.5 text-xs text-slate-400">Mutasi keuangan paling akhir bulan ini</p>
            </div>

            <!-- Content List -->
            <div class="divide-y divide-slate-100 flex-1 overflow-y-auto max-h-[420px] w-full min-w-0">
                @forelse ($recentTransactions as $transaction)
                    @php $isIncome = $transaction['tipe'] === 'pemasukkan'; @endphp
                    <div class="flex items-center gap-3 sm:gap-4 px-4 py-3.5 transition hover:bg-brand-bg sm:px-6">
                        <span class="flex size-9 sm:size-10 shrink-0 items-center justify-center rounded-xl {{ $isIncome ? 'bg-brand-teal-light/40 text-brand-teal' : 'bg-brand-orange/15 text-brand-orange' }} text-base sm:text-lg font-bold">
                            {{ $isIncome ? '↑' : '↓' }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs sm:text-sm font-semibold text-brand-dark">{{ $transaction['nama'] }}</p>
                            <p class="mt-0.5 text-[11px] sm:text-xs text-slate-400 truncate">
                                {{ \Carbon\Carbon::parse($transaction['tanggal'])->translatedFormat('d M Y') }} · {{ $transaction['sumber_dana'] }}
                            </p>
                        </div>
                        <p class="shrink-0 text-xs sm:text-sm font-bold {{ $isIncome ? 'text-brand-teal' : 'text-brand-orange' }}">
                            {{ $isIncome ? '+' : '-' }}Rp {{ number_format($transaction['jumlah'], 0, ',', '.') }}
                        </p>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-sm text-slate-500">Belum ada transaksi bulan ini.</div>
                @endforelse
            </div>
        </section>

    </div>

    <!-- Script Auto Scroll ke Baris Hari Ini -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const todayRow = document.getElementById('row-today');
            const container = document.getElementById('foodTableContainer');
            if (todayRow && container) {
                container.scrollTop = todayRow.offsetTop - container.offsetTop - 40;
            }
        });
    </script>

    {{-- SALDO PER SUMBER DANA --}}
    <section>
        <div class="mb-4">
            <p class="text-xs font-bold uppercase tracking-wider text-brand-teal">Sumber Dana</p>
            <h2 class="mt-1 font-heading text-xl font-bold text-brand-dark">Saldo Per Sumber Dana</h2>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 xl:grid-cols-3">
            @foreach ($summary['sumber_dana'] as $source)
                <div class="rounded-2xl border border-brand-teal/15 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-brand-dark">{{ $source['nama'] }}</p>
                            @if ($source['keterangan'])
                                <p class="mt-1 text-xs text-slate-400">{{ $source['keterangan'] }}</p>
                            @endif
                        </div>
                        <span class="size-2.5 shrink-0 rounded-full bg-brand-teal"></span>
                    </div>
                    <p class="mt-5 text-xs text-slate-500">Saldo</p>
                    <p class="mt-1 font-heading text-xl font-bold text-brand-dark">Rp {{ number_format($source['saldo'], 0, ',', '.') }}</p>
                    <div class="mt-3 flex justify-between text-xs">
                        <span class="text-slate-400">Budget</span>
                        <span class="font-semibold text-slate-600">Rp {{ number_format($source['budget'], 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

</div>

{{-- CHART.JS --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const charts = @js($summary['charts'] ?? []);
        const budgetUsage = @js($summary['budget_usage'] ?? []);
        const teal = '#359FA0', tealLight = '#8AD6D1', cream = '#FFF0C5', orange = '#FF8C52', dark = '#1E293B', grid = '#E2E8F0';
        const money = value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value ?? 0);

        const paletteColors = [teal, orange, '#3B82F6', '#8B5CF6', '#EC4899', tealLight];

        Chart.defaults.font.family = "'Outfit', sans-serif";
        Chart.defaults.color = '#64748B';

        // 1. Daily Expense Chart (Harian)
        const dailyCanvas = document.getElementById('dailyExpenseChart');
        if (dailyCanvas) {
            new Chart(dailyCanvas, {
                type: 'line',
                data: {
                    labels: (charts.daily_expenses || []).map(item => `${item.day} ${item.month_name}`),
                    datasets: [{
                        label: 'Pengeluaran',
                        data: (charts.daily_expenses || []).map(item => item.amount),
                        borderColor: teal,
                        backgroundColor: 'rgba(53,159,160,.10)',
                        borderWidth: 3,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: teal,
                        pointBorderWidth: 2,
                        fill: true,
                        tension: .35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: dark,
                            padding: 12,
                            displayColors: false,
                            callbacks: { label: context => money(context.raw) }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, border: { display: false } },
                        y: {
                            beginAtZero: true,
                            grid: { color: grid },
                            border: { display: false },
                            ticks: { callback: value => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value) }
                        }
                    }
                }
            });
        }

        // 2. Budget Source Chart (Bar)
        const sourceCanvas = document.getElementById('budgetSourceChart');
        if (sourceCanvas) {
            new Chart(sourceCanvas, {
                type: 'bar',
                data: {
                    labels: (budgetUsage || []).map(item => item.nama),
                    datasets: [
                        { label: 'Budget', data: (budgetUsage || []).map(item => item.budget), backgroundColor: tealLight, borderRadius: 6, borderSkipped: false },
                        { label: 'Pengeluaran', data: (budgetUsage || []).map(item => item.pengeluaran ?? item.terpakai), backgroundColor: orange, borderRadius: 6, borderSkipped: false }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    resizeDelay: 100,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 10, font: { size: 10 } } },
                        tooltip: {
                            backgroundColor: dark,
                            padding: 10,
                            callbacks: { label: context => `${context.dataset.label}: ${money(context.raw)}` }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10 } } },
                        y: {
                            beginAtZero: true,
                            grid: { color: grid },
                            border: { display: false },
                            ticks: { font: { size: 9 }, callback: value => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value) }
                        }
                    }
                }
            });
        }

        // 3. Budget Donut Chart (Distribusi Pengeluaran)
        const donutCanvas = document.getElementById('budgetDonutChart');
        if (donutCanvas) {
            new Chart(donutCanvas, {
                type: 'doughnut',
                data: {
                    labels: (budgetUsage || []).map(item => item.nama),
                    datasets: [{
                        data: (budgetUsage || []).map(item => item.pengeluaran ?? item.terpakai),
                        backgroundColor: paletteColors.slice(0, budgetUsage.length),
                        borderWidth: 2,
                        borderColor: '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    resizeDelay: 100,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 10, font: { size: 10 } } },
                        tooltip: {
                            backgroundColor: dark,
                            padding: 10,
                            callbacks: { label: context => ` ${context.label}: ${money(context.raw)}` }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

@endcomponent
