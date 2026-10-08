<x-layouts.app title="Detail Investasi" subtitle="Kelola riwayat dan catatan portofolio investasi Anda">
    <div class="space-y-6" x-data="{ tabAktif: 'riwayat' }">
        
        {{-- Flash Message Alert --}}
        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 text-sm font-medium flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        {{-- Navigasi Tab Modern & Tombol Aksi --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white p-2 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl">
                <button type="button"
                        @click="tabAktif = 'riwayat'"
                        :class="tabAktif === 'riwayat' ? 'bg-white text-brand-teal font-bold shadow-sm' : 'text-slate-500 hover:text-slate-700 font-medium'"
                        class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-xs sm:text-sm transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Riwayat Jurnal
                </button>
                <button type="button"
                        @click="tabAktif = 'pending'"
                        :class="tabAktif === 'pending' ? 'bg-white text-brand-orange font-bold shadow-sm' : 'text-slate-500 hover:text-slate-700 font-medium'"
                        class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-xs sm:text-sm transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Perlu Dilengkapi
                    @if(count($unlinkedPengeluaran ?? []) > 0)
                        <span class="px-1.5 py-0.5 text-[10px] bg-brand-orange text-white rounded-full font-bold">{{ count($unlinkedPengeluaran) }}</span>
                    @endif
                </button>
            </div>

            <button type="button"
                    @click="$dispatch('open-modal-investasi', { mode: 'create' })"
                    class="w-full sm:w-auto px-4 py-2.5 bg-brand-teal hover:bg-brand-teal/90 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md active:scale-95 transition-all flex items-center justify-center gap-2">
                Tambah Catatan
            </button>
        </div>

        {{-- TAB 1: Tabel Riwayat Investasi --}}
        <div x-show="tabAktif === 'riwayat'" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-bold text-base text-brand-dark">Jurnal Riwayat Investasi</h2>
                <span class="text-xs text-slate-400">Total: {{ $riwayatList->total() ?? 0 }} Data</span>
            </div>

            {{-- Table Desktop --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] uppercase font-bold text-slate-400 tracking-wider border-b border-slate-100">
                            <th class="py-3 px-4">Instrumen & Platform</th>
                            <th class="py-3 px-4">Tgl Beli / Jual</th>
                            <th class="py-3 px-4 text-center">Durasi Hold</th>
                            <th class="py-3 px-4 text-right">Modal Real / Satuan</th>
                            <th class="py-3 px-4 text-right">Hasil Jual / Satuan</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                        @forelse ($riwayatList ?? [] as $item)
                            @php
                                $tglBeli = \Carbon\Carbon::parse($item->tanggal_beli);
                                $tglJual = $item->tanggal_jual ? \Carbon\Carbon::parse($item->tanggal_jual) : null;
                                $holdDurasi = $tglJual ? $tglBeli->diffForHumans($tglJual, ['syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE, 'parts' => 2]) : 'Masih Di-hold';
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-brand-dark">{{ $item->nama_instrumen }}</div>
                                    <div class="text-xs text-slate-400 flex items-center gap-1">
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 font-medium text-slate-500">{{ $item->jenis_investasi ?? 'Lainnya' }}</span>
                                        <span>•</span>
                                        <span>{{ $item->platform ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-xs">
                                    <div class="text-slate-700 font-medium">{{ $tglBeli->format('d M Y') }}</div>
                                    <div class="text-slate-400">{{ $tglJual ? $tglJual->format('d M Y') : 'Belum Dijual' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $tglJual ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                        {{ $holdDurasi }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @if ($item->total_beli)
                                        <div class="font-bold text-slate-800">Rp {{ number_format($item->total_beli, 0, ',', '.') }}</div>
                                        <span class="text-[10px] text-slate-400">Total Modal</span>
                                    @elseif($item->harga_beli_satuan)
                                        <div class="font-bold text-slate-800">Rp {{ number_format($item->harga_beli_satuan, 0, ',', '.') }}</div>
                                        <span class="text-[10px] text-slate-400">/ unit ({{ $item->jumlah_unit ?? '-' }} unit)</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @if ($item->total_jual)
                                        <div class="font-bold text-emerald-600">Rp {{ number_format($item->total_jual, 0, ',', '.') }}</div>
                                        <span class="text-[10px] text-slate-400">Total Hasil</span>
                                    @elseif($item->harga_jual_satuan)
                                        <div class="font-bold text-emerald-600">Rp {{ number_format($item->harga_jual_satuan, 0, ',', '.') }}</div>
                                        <span class="text-[10px] text-slate-400">/ unit</span>
                                    @else
                                        <span class="text-slate-400 font-italic text-xs">Belum Dijual</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button type="button" @click="$dispatch('open-modal-investasi', { mode: 'edit', data: {{ json_encode($item) }} })" class="p-1.5 text-slate-400 hover:text-brand-teal hover:bg-slate-100 rounded-lg transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 text-sm">Belum ada riwayat jurnal investasi yang dicatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards View --}}
            <div class="block md:hidden divide-y divide-slate-100">
                @forelse ($riwayatList ?? [] as $item)
                    @php
                        $tglBeli = \Carbon\Carbon::parse($item->tanggal_beli);
                        $tglJual = $item->tanggal_jual ? \Carbon\Carbon::parse($item->tanggal_jual) : null;
                        $holdDurasi = $tglJual ? $tglBeli->diffForHumans($tglJual, ['syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE, 'parts' => 2]) : 'Masih Di-hold';
                    @endphp
                    <div class="p-4 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-brand-dark text-base leading-tight">{{ $item->nama_instrumen }}</h3>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $item->jenis_investasi ?? 'Lainnya' }} • {{ $item->platform ?? '-' }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $tglJual ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                {{ $holdDurasi }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 p-2.5 bg-slate-50 rounded-xl text-xs">
                            <div>
                                <span class="text-slate-400 block text-[10px]">Beli ({{ $tglBeli->format('d/m/Y') }})</span>
                                <span class="font-bold text-slate-700">
                                    {{ $item->total_beli ? 'Rp '.number_format($item->total_beli, 0, ',', '.') : ($item->harga_beli_satuan ? 'Rp '.number_format($item->harga_beli_satuan, 0, ',', '.').'/unit' : '-') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Jual ({{ $tglJual ? $tglJual->format('d/m/Y') : '-' }})</span>
                                <span class="font-bold text-emerald-600">
                                    {{ $item->total_jual ? 'Rp '.number_format($item->total_jual, 0, ',', '.') : ($item->harga_jual_satuan ? 'Rp '.number_format($item->harga_jual_satuan, 0, ',', '.').'/unit' : 'Belum Jual') }}
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-end pt-1">
                            <button type="button" @click="$dispatch('open-modal-investasi', { mode: 'edit', data: {{ json_encode($item) }} })" class="text-xs font-semibold text-brand-teal flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit Catatan
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 text-sm">Belum ada riwayat jurnal investasi yang dicatat.</div>
                @endforelse
            </div>
        </div>

        {{-- TAB 2: Subview Pending Pengeluaran --}}
        <div x-show="tabAktif === 'pending'">
            @include('menu.investasi.partial-pending')
        </div>

    </div>

    {{-- Subview Modal Form --}}
    @include('menu.investasi.modal-form')
</x-layouts.app>