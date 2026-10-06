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