@component('components.layouts.app', ['title' => 'Kelola Pos / Sumber Dana'])
    {{-- Inisialisasi Chart.js via CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div
        class="space-y-8 font-sans"
        x-data="{
            showModal: false,
            isEdit: false,
            tipe: 'pemasukkan',
            id: null,
            nama: '',
            budget: 0,
            actionUrl: '{{ route('sumber-dana.store') }}',

            openCreateModal(selectedTipe) {
                this.isEdit = false;
                this.tipe = selectedTipe;
                this.nama = '';
                this.budget = 0;
                this.actionUrl = '{{ route('sumber-dana.store') }}';
                this.showModal = true;
            },

            openEditModal(selectedTipe, item) {
                this.isEdit = true;
                this.tipe = selectedTipe;
                this.id = item.id;
                this.nama = item.nama_sumber_dana;
                this.budget = item.budget || 0;
                this.actionUrl = `/sumber-dana/${selectedTipe}/${item.id}`;
                this.showModal = true;
            }
        }"
    >
        {{-- HEADER SECTION --}}
        <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
            <div>
                <a href="{{ route('transactions.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-brand-teal transition-colors hover:underline">
                    ← Kembali ke Transaksi
                </a>
                <h1 class="mt-1 text-2xl font-bold text-slate-800">Kelola Pos / Sumber Dana</h1>
            </div>
        </header>

        {{-- ALERT SECTION --}}
        @include('menu.transactions.partials.alert')

        {{-- CHART ANALYTICS SECTION --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-800 sm:text-lg">Analisis Alokasi Budget Pengeluaran</h2>
                    <p class="text-xs text-slate-400">Pantau perbandingan alokasi budget dan realisasi pengeluaran bulan ini.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:items-center">
                
                {{-- DOUGHNUT CHART --}}
                <div class="relative flex h-52 w-full items-center justify-center p-2 sm:h-64">
                    <canvas id="budgetDoughnutChart"></canvas>
                </div>

                {{-- PROGRESS METRICS (LIST REALISASI) --}}
                <div class="space-y-3 lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Realisasi vs Budget Bulan Ini</h3>
                        <span class="text-[11px] font-medium text-slate-400">{{ count($pengeluaran) }} Pos</span>
                    </div>

                    <div class="custom-scrollbar max-h-64 space-y-3 overflow-y-auto pr-1 sm:pr-2">
                        @forelse ($pengeluaran as $item)
                            @php
                                $realisasi = $item->realisasi ?? 0;
                                $budget = $item->budget ?? 0;
                                $persentase = $item->persentase_realisasi ?? ($budget > 0 ? round(($realisasi / $budget) * 100) : 0);
                                
                                // Warna status indicator
                                if ($persentase >= 90) {
                                    $barColor = 'bg-rose-500';
                                    $badgeColor = 'bg-rose-50 text-rose-600';
                                } elseif ($persentase >= 70) {
                                    $barColor = 'bg-amber-500';
                                    $badgeColor = 'bg-amber-50 text-amber-600';
                                } else {
                                    $barColor = 'bg-brand-teal';
                                    $badgeColor = 'bg-emerald-50 text-brand-teal';
                                }
                            @endphp

                            <div class="rounded-xl bg-slate-50/70 p-3.5 transition-colors hover:bg-slate-100/60">
                                <div class="mb-2 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold text-slate-700 sm:text-sm">{{ $item->nama_sumber_dana }}</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between gap-2 sm:justify-end">
                                        <div class="text-xs font-medium text-slate-600">
                                            <span>Rp {{ number_format($realisasi, 0, ',', '.') }}</span>
                                            <span class="text-slate-400">/ Rp {{ number_format($budget, 0, ',', '.') }}</span>
                                        </div>
                                        <span class="rounded-md px-1.5 py-0.5 text-[11px] font-bold {{ $badgeColor }}">
                                            {{ $persentase }}%
                                        </span>
                                    </div>
                                </div>

                                {{-- PROGRESS BAR CONTAINER --}}
                                <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200/80">
                                    <div 
                                        class="h-2 rounded-full transition-all duration-500 {{ $barColor }}" 
                                        style="width: {{ min($persentase, 100) }}%"
                                    ></div>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                <div class="mb-2 rounded-full bg-slate-100 p-3 text-slate-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-medium text-slate-400">Belum ada data budget pengeluaran.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

        {{-- MAIN CONTENT GRID --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            
            {{-- SUMBER DANA PEMASUKAN --}}
            <section class="flex flex-col justify-between rounded-2xl border border-brand-teal/20 bg-white p-6 shadow-sm transition-all hover:shadow-md">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-teal/10 text-brand-teal">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m7-7H5"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-800">Sumber Dana Pemasukan</h2>
                                <p class="text-xs text-slate-400">{{ count($pemasukkan) }} Item Terdaftar</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            x-on:click="openCreateModal('pemasukkan')"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-brand-teal px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:bg-brand-teal/90 active:scale-95"
                        >
                            + Tambah
                        </button>
                    </div>

                    <div class="mt-3 divide-y divide-slate-100">
                        @forelse ($pemasukkan as $item)
                            <div class="group flex items-center justify-between rounded-xl px-2 py-3 transition-colors hover:bg-slate-50">
                                <div class="flex items-center gap-3">
                                    <div class="h-2 w-2 rounded-full bg-brand-teal"></div>
                                    <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">{{ $item->nama_sumber_dana }}</span>
                                </div>
                                <div class="flex items-center gap-3 opacity-90 transition-opacity group-hover:opacity-100">
                                    <button 
                                        type="button" 
                                        x-on:click="openEditModal('pemasukkan', @json($item))" 
                                        class="rounded-lg px-2 py-1 text-xs font-semibold text-slate-500 transition-colors hover:bg-brand-teal/10 hover:text-brand-teal"
                                    >
                                        Edit
                                    </button>
                                    <form action="{{ route('sumber-dana.destroy', ['tipe' => 'pemasukkan', 'id' => $item->id]) }}" method="POST" onsubmit="return confirm('Hapus sumber dana ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg px-2 py-1 text-xs font-semibold text-rose-500 transition-colors hover:bg-rose-50 hover:text-rose-700">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <p class="text-xs font-medium text-slate-400">Belum ada data sumber dana pemasukan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            {{-- POS PENGELUARAN --}}
            <section class="flex flex-col justify-between rounded-2xl border border-brand-orange/20 bg-white p-6 shadow-sm transition-all hover:shadow-md">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-orange/10 text-brand-orange">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-800">Pos Pengeluaran</h2>
                                <p class="text-xs text-slate-400">{{ count($pengeluaran) }} Item Terdaftar</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            x-on:click="openCreateModal('pengeluaran')"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-brand-orange px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:bg-brand-orange/90 active:scale-95"
                        >
                            + Tambah
                        </button>
                    </div>

                    <div class="mt-3 divide-y divide-slate-100">
                        @forelse ($pengeluaran as $item)
                            <div class="group flex items-center justify-between rounded-xl px-2 py-3 transition-colors hover:bg-slate-50">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">{{ $item->nama_sumber_dana }}</span>
                                    <span class="inline-flex items-center text-xs font-medium text-slate-400">
                                        Budget: <span class="ml-1 text-slate-600">Rp {{ number_format($item->budget, 0, ',', '.') }}</span>
                                    </span>
                                </div>
                                <div class="flex items-center gap-3 opacity-90 transition-opacity group-hover:opacity-100">
                                    <button 
                                        type="button" 
                                        x-on:click="openEditModal('pengeluaran', @json($item))" 
                                        class="rounded-lg px-2 py-1 text-xs font-semibold text-slate-500 transition-colors hover:bg-brand-orange/10 hover:text-brand-orange"
                                    >
                                        Edit
                                    </button>
                                    <form action="{{ route('sumber-dana.destroy', ['tipe' => 'pengeluaran', 'id' => $item->id]) }}" method="POST" onsubmit="return confirm('Hapus pos pengeluaran ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg px-2 py-1 text-xs font-semibold text-rose-500 transition-colors hover:bg-rose-50 hover:text-rose-700">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <p class="text-xs font-medium text-slate-400">Belum ada data pos pengeluaran.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

        </div>

        {{-- MODAL FORM --}}
        @include('menu.transactions.sumber-dana.modal-form')
    </div>

    {{-- SCRIPT INIT DOUGHNUT CHART --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('budgetDoughnutChart').getContext('2d');
            const chartData = @json($chartData);

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        data: chartData.budgets,
                        backgroundColor: [
                            '#0d9488', '#f97316', '#6366f1', '#ec4899', 
                            '#8b5cf6', '#14b8a6', '#f59e0b', '#06b6d4'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw || 0;
                                    return ' ' + context.label + ': Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        });
    </script>
@endcomponent