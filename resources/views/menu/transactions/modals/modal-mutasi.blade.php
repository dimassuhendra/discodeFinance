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