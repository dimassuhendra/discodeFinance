<div x-data="{ 
        isOpen: false, 
        modeInput: 'total', // 'satuan' atau 'total'
        isEdit: false,
        formData: {
            id: '',
            id_pengeluaran: '',
            nama_instrumen: '',
            jenis_instrumen: '',
            platform: '',
            tanggal_beli: '{{ date('Y-m-d') }}',
            tanggal_jual: '',
            jumlah_unit: '',
            harga_beli_satuan: '',
            harga_jual_satuan: '',
            total_beli: '',
            total_jual: '',
            catatan: ''
        },
        resetForm() {
            this.isEdit = false;
            this.modeInput = 'total';
            this.formData = {
                id: '', id_pengeluaran: '', nama_instrumen: '', jenis_instrumen: '', platform: '',
                tanggal_beli: '{{ date('Y-m-d') }}', tanggal_jual: '', jumlah_unit: '',
                harga_beli_satuan: '', harga_jual_satuan: '', total_beli: '', total_jual: '', catatan: ''
            };
        }
     }"
     x-on:open-modal-investasi.window="
        isOpen = true;
        resetForm();
        if ($event.detail.mode === 'edit') {
            isEdit = true;
            Object.assign(formData, $event.detail.data);
            if (formData.harga_beli_satuan) modeInput = 'satuan';
        } else if ($event.detail.mode === 'complete') {
            formData.id_pengeluaran = $event.detail.pengeluaran_id;
            formData.nama_instrumen = $event.detail.nama_instrumen;
            formData.total_beli = $event.detail.total_beli;
            formData.tanggal_beli = $event.detail.tanggal_beli;
        }
     "
     x-on:keydown.escape.window="isOpen = false"
     x-show="isOpen"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 font-sans"
     style="display: none;">

    {{-- Backdrop --}}
    <div x-show="isOpen" 
         x-transition.opacity 
         @click="isOpen = false" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    {{-- Dialog Box --}}
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="relative w-full max-w-lg max-h-[90vh] overflow-hidden rounded-2xl bg-white shadow-2xl flex flex-col z-10">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 shrink-0">
            <div>
                <h3 class="text-lg font-bold text-brand-dark" x-text="isEdit ? 'Edit Catatan Investasi' : 'Catat Investasi Baru'"></h3>
                <p class="text-xs text-slate-400">Jurnal histori transaksi investasi pribadi</p>
            </div>
            <button type="button" @click="isOpen = false" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center hover:bg-slate-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Form Content --}}
        <form :action="isEdit ? '/investasi/' + formData.id : '{{ route('investasi.store') }}'" method="POST" class="overflow-y-auto p-5 space-y-4 flex-1">
            @csrf
            <template x-if="isEdit">
                <input type="hidden" name="_method" value="PUT">
            </template>
            <input type="hidden" name="id_pengeluaran" x-model="formData.id_pengeluaran">

            {{-- Tab Mode Input --}}
            <div class="flex items-center gap-1 p-1 bg-slate-100 rounded-xl">
                <button type="button" @click="modeInput = 'total'"
                        :class="modeInput === 'total' ? 'bg-white text-brand-teal font-bold shadow-sm' : 'text-slate-500 font-medium'"
                        class="flex-1 py-2 text-xs rounded-lg transition-all text-center">
                    Total Modal Real
                </button>
                <button type="button" @click="modeInput = 'satuan'"
                        :class="modeInput === 'satuan' ? 'bg-white text-brand-teal font-bold shadow-sm' : 'text-slate-500 font-medium'"
                        class="flex-1 py-2 text-xs rounded-lg transition-all text-center">
                    Harga Per Unit (Saham/Crypto)
                </button>
            </div>

            {{-- Detail Instrumen --}}
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Instrumen / Aset <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_instrumen" x-model="formData.nama_instrumen" required placeholder="Contoh: Saham BBCA, Bitcoin, Deposito BSI" class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl text-sm border-0 focus:ring-2 focus:ring-brand-teal/30">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jenis Investasi</label>
                        <input type="text" name="jenis_instrumen" x-model="formData.jenis_instrumen" placeholder="Saham, Crypto, P2P" class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl text-sm border-0 focus:ring-2 focus:ring-brand-teal/30">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Platform / Broker</label>
                        <input type="text" name="platform" x-model="formData.platform" placeholder="Ajaib, Stockbit, Bibit" class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl text-sm border-0 focus:ring-2 focus:ring-brand-teal/30">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Beli <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_beli" x-model="formData.tanggal_beli" required class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl text-sm border-0 focus:ring-2 focus:ring-brand-teal/30">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Jual (Opsional)</label>
                        <input type="date" name="tanggal_jual" x-model="formData.tanggal_jual" class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl text-sm border-0 focus:ring-2 focus:ring-brand-teal/30">
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Dynamic Inputs Berdasarkan Mode --}}
            {{-- Mode 1: Total Modal Real --}}
            <div x-show="modeInput === 'total'" class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Total Modal Beli (Rp)</label>
                        <input type="number" name="total_beli" x-model="formData.total_beli" placeholder="0" class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl text-sm border-0 focus:ring-2 focus:ring-brand-teal/30">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Total Hasil Jual (Rp)</label>
                        <input type="number" name="total_jual" x-model="formData.total_jual" placeholder="0" class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl text-sm border-0 focus:ring-2 focus:ring-brand-teal/30">
                    </div>
                </div>
            </div>

            {{-- Mode 2: Harga Per Unit --}}
            <div x-show="modeInput === 'satuan'" class="space-y-3" style="display: none;">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah Unit / Lembar</label>
                    <input type="number" step="any" name="jumlah_unit" x-model="formData.jumlah_unit" placeholder="0" class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl text-sm border-0 focus:ring-2 focus:ring-brand-teal/30">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Harga Beli / Unit (Rp)</label>
                        <input type="number" step="any" name="harga_beli_satuan" x-model="formData.harga_beli_satuan" placeholder="0" class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl text-sm border-0 focus:ring-2 focus:ring-brand-teal/30">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Harga Jual / Unit (Rp)</label>
                        <input type="number" step="any" name="harga_jual_satuan" x-model="formData.harga_jual_satuan" placeholder="0" class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl text-sm border-0 focus:ring-2 focus:ring-brand-teal/30">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan Tambahan</label>
                <textarea name="catatan" x-model="formData.catatan" rows="2" placeholder="Catatan strategi atau pengingat..." class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl text-sm border-0 focus:ring-2 focus:ring-brand-teal/30"></textarea>
            </div>

            {{-- Actions --}}
            <div class="pt-2 flex gap-2 shrink-0">
                <button type="button" @click="isOpen = false" class="w-1/3 py-2.5 rounded-xl text-xs font-semibold text-slate-500 bg-slate-100 hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="w-2/3 py-2.5 bg-brand-teal hover:bg-brand-teal/90 text-white rounded-xl text-xs font-bold shadow-md active:scale-95 transition-all">Simpan Catatan</button>
            </div>
        </form>
    </div>
</div>