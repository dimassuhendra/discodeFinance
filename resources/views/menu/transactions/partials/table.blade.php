<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th scope="col" class="px-6 py-4">Tanggal</th>
                    <th scope="col" class="px-6 py-4">Tipe</th>
                    <th scope="col" class="px-6 py-4">Keterangan / Pos</th>
                    <th scope="col" class="px-6 py-4">Sumber Dana</th>
                    <th scope="col" class="px-6 py-4 text-right">Nominal</th>
                    <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                @forelse ($transactions as $transaction)
                    <tr class="transition hover:bg-slate-50/80">
                        {{-- TANGGAL --}}
                        <td class="whitespace-nowrap px-6 py-4 text-slate-700">
                            {{ \Carbon\Carbon::parse($transaction->tanggal)->translatedFormat('d M Y') }}
                        </td>

                        {{-- TIPE TRANSAKSI BADGE --}}
                        <td class="whitespace-nowrap px-6 py-4">
                            @if ($transaction->tipe_transaksi === 'pemasukkan')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                    <span class="size-1.5 rounded-full bg-emerald-600"></span>
                                    Pemasukan
                                </span>
                            @elseif ($transaction->tipe_transaksi === 'pengeluaran')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                    <span class="size-1.5 rounded-full bg-rose-600"></span>
                                    Pengeluaran
                                </span>
                            @elseif ($transaction->tipe_transaksi === 'mutasi')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                    <span class="size-1.5 rounded-full bg-blue-600"></span>
                                    Mutasi Saldo
                                </span>
                            @endif
                        </td>

                        {{-- KETERANGAN & POS/KATEGORI --}}
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-800">
                                {{ $transaction->nama ?? '-' }}
                            </div>
                            @if (!empty($transaction->keterangan))
                                <div class="text-xs text-slate-400">
                                    Catatan: {{ $transaction->keterangan }}
                                </div>
                            @endif
                        </td>

                        {{-- SUMBER DANA --}}
                        <td class="whitespace-nowrap px-6 py-4 text-slate-700">
                            @if ($transaction->tipe_transaksi === 'mutasi')
                                <div class="flex items-center gap-1.5 text-xs">
                                    <span class="font-semibold text-slate-700">{{ $transaction->sumber_asal ?? '-' }}</span>
                                    <svg class="size-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span class="font-semibold text-slate-700">{{ $transaction->sumber_tujuan ?? '-' }}</span>
                                </div>
                            @elseif ($transaction->tipe_transaksi === 'pemasukkan')
                                <span class="font-medium text-slate-700">
                                    {{ $transaction->sumber_tujuan ?? '-' }}
                                </span>
                            @else
                                <span class="font-medium text-slate-700">
                                    {{ $transaction->sumber_asal ?? '-' }}
                                </span>
                            @endif
                        </td>

                        {{-- NOMINAL TRANSAKSI --}}
                        <td class="whitespace-nowrap px-6 py-4 text-right font-bold">
                            @if ($transaction->tipe_transaksi === 'pemasukkan')
                                <span class="text-emerald-600">+ Rp {{ number_format($transaction->jumlah, 0, ',', '.') }}</span>
                            @elseif ($transaction->tipe_transaksi === 'pengeluaran')
                                <span class="text-rose-600">- Rp {{ number_format($transaction->jumlah, 0, ',', '.') }}</span>
                            @else
                                <span class="text-slate-700">Rp {{ number_format($transaction->jumlah, 0, ',', '.') }}</span>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td class="whitespace-nowrap px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Form Hapus --}}
                                <form action="{{ route('transactions.destroy', ['id' => $transaction->id, 'type' => $transaction->tipe_transaksi]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="rounded-lg p-1.5 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600"
                                        title="Hapus Transaksi"
                                    >
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <svg class="mx-auto size-12 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="mt-2 text-sm font-semibold text-slate-600">Belum ada riwayat transaksi</p>
                            <p class="text-xs text-slate-400">Silakan tambahkan pemasukan, pengeluaran, atau mutasi saldo.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINASI --}}
    @if (method_exists($transactions, 'hasPages') && $transactions->hasPages())
        <div class="border-t border-slate-200 bg-white px-6 py-4">
            {{ $transactions->links() }}
        </div>
    @endif
</div>