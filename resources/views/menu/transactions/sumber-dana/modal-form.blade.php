<div
    x-cloak
    x-show="showModal"
    x-transition.opacity
    x-on:click.self="showModal = false"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4"
>
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-bold text-slate-900" x-text="isEdit ? 'Edit Pos / Sumber Dana' : 'Tambah Pos / Sumber Dana'"></h3>
        
        <form :action="actionUrl" method="POST" class="mt-4 space-y-4">
            @csrf
            <template x-if="isEdit">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <input type="hidden" name="tipe" :value="tipe">

            <div>
                <label for="nama_sumber_dana" class="mb-1 block text-sm font-medium text-slate-700">Nama Sumber / Pos</label>
                <input
                    type="text"
                    id="nama_sumber_dana"
                    name="nama_sumber_dana"
                    x-model="nama"
                    required
                    placeholder="Contoh: Dompet Utama, Bank BCA, dll."
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20"
                >
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" x-on:click="showModal = false" class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Batal</button>
                <button type="submit" class="rounded-xl bg-brand-teal px-4 py-2 text-sm font-semibold text-white hover:bg-brand-teal/90" x-text="isEdit ? 'Simpan Perubahan' : 'Tambah'"></button>
            </div>
        </form>
    </div>
</div>