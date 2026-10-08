<!-- Modal Form Investasi -->
<div x-data="{
    open: false,
    isEdit: false,
    actionUrl: '{{ route('investasi.store') }}',
    formData: {
        id: '', nama_instrumen: '', jenis_instrumen: '', platform: '',
        jumlah_unit: '', harga_beli_satuan: '', total_beli: '',
        tanggal_beli: '{{ date('Y-m-d') }}', harga_jual_satuan: '',
        total_jual: '', tanggal_jual: '', catatan: ''
    },
    calculateBeli() {
        let unit = parseFloat(this.formData.jumlah_unit) || 0;
        let harga = parseFloat(this.formData.harga_beli_satuan) || 0;
        if (unit > 0 && harga > 0) this.formData.total_beli = unit * harga;
    },
    calculateJual() {
        let unit = parseFloat(this.formData.jumlah_unit) || 0;
        let harga = parseFloat(this.formData.harga_jual_satuan) || 0;
        if (unit > 0 && harga > 0) this.formData.total_jual = unit * harga;
    },
    get profitLoss() {
        let beli = parseFloat(this.formData.total_beli) || 0;
        let jual = parseFloat(this.formData.total_jual) || 0;
        return jual > 0 ? jual - beli : 0;
    }
}"
    @open-modal-investasi.window="
        open = true;
        if ($event.detail && $event.detail.item) {
            isEdit = true;
            actionUrl = '/investasi/' + $event.detail.item.id;
            formData = { ...$event.detail.item };
        } else {
            isEdit = false;
            actionUrl = '{{ route('investasi.store') }}';
            formData = {
                id: '', nama_instrumen: '', jenis_instrumen: '', platform: '',
                jumlah_unit: '', harga_beli_satuan: '', total_beli: '',
                tanggal_beli: '{{ date('Y-m-d') }}', harga_jual_satuan: '',
                total_jual: '', tanggal_jual: '', catatan: ''
            };
        }
    "
    x-show="open"
    x-transition.opacity
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5"
    style="display: none;"
    x-cloak>

    <!-- Backdrop -->
    <div @click="open = false"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

    <!-- Modal -->
    <div @click.stop
        class="relative flex w-full max-w-2xl max-h-[90dvh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl sm:max-h-[85vh]"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2 scale-[0.98]"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100">

        <!-- Header tetap terlihat -->
        <div class="flex shrink-0 items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 sm:px-5">
            <div class="min-w-0">
                <h3 class="text-sm font-bold text-brand-dark sm:text-base"
                    x-text="isEdit ? 'Edit Riwayat Investasi' : 'Tambah Riwayat Investasi'"></h3>
                <p class="mt-0.5 text-[10px] text-slate-500 sm:text-xs">Lengkapi informasi transaksi investasi.</p>
            </div>
            <button type="button" @click="open = false"
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                aria-label="Tutup modal">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Isi form yang bisa di-scroll -->
        <form :action="actionUrl" method="POST" class="flex min-h-0 flex-1 flex-col overflow-hidden">
            @csrf
            <template x-if="isEdit">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="min-h-0 flex-1 space-y-3 overflow-y-auto overscroll-contain px-4 py-3 sm:px-5 sm:py-4">
                <!-- Informasi Instrumen -->
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600">Nama Instrumen <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_instrumen" x-model="formData.nama_instrumen" required placeholder="Contoh: BBCA, Obligasi ORI022"
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600">Jenis Instrumen</label>
                        <select name="jenis_instrumen" x-model="formData.jenis_instrumen"
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                            <option value="">Pilih Jenis</option>
                            <option value="Saham">Saham</option>
                            <option value="Reksadana">Reksadana</option>
                            <option value="Kripto">Kripto</option>
                            <option value="Emas">Emas</option>
                            <option value="Obligasi/SBR">Obligasi/SBR</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600">Platform / Broker</label>
                        <input type="text" name="platform" x-model="formData.platform" placeholder="Contoh: Stockbit, Bibit, Indodax"
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600">Tanggal Beli <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_beli" x-model="formData.tanggal_beli" required
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                    </div>
                </div>

                <!-- Rincian Pembelian -->
                <section class="space-y-2 rounded-xl border border-slate-200 bg-slate-50/70 p-3">
                    <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Rincian Pembelian</h4>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-[11px] font-medium text-slate-600">Jumlah Unit</label>
                            <input type="number" step="any" name="jumlah_unit" x-model="formData.jumlah_unit" @input="calculateBeli(); calculateJual();" placeholder="0"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                        </div>
                        <div>
                            <label class="mb-1 block text-[11px] font-medium text-slate-600">Harga Beli Satuan (Rp)</label>
                            <input type="number" step="any" name="harga_beli_satuan" x-model="formData.harga_beli_satuan" @input="calculateBeli()" placeholder="0"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                        </div>
                        <div>
                            <label class="mb-1 block text-[11px] font-medium text-slate-600">Total Beli (Rp)</label>
                            <input type="number" step="any" name="total_beli" x-model="formData.total_beli" placeholder="0"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-brand-dark focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                        </div>
                    </div>
                </section>

                <!-- Rincian Penjualan -->
                <section class="space-y-2 rounded-xl border border-slate-200 bg-slate-50/70 p-3">
                    <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Rincian Penjualan <span class="font-normal normal-case tracking-normal">(Opsional)</span></h4>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-[11px] font-medium text-slate-600">Harga Jual Satuan (Rp)</label>
                            <input type="number" step="any" name="harga_jual_satuan" x-model="formData.harga_jual_satuan" @input="calculateJual()" placeholder="0"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                        </div>
                        <div>
                            <label class="mb-1 block text-[11px] font-medium text-slate-600">Total Jual (Rp)</label>
                            <input type="number" step="any" name="total_jual" x-model="formData.total_jual" placeholder="0"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-brand-dark focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                        </div>
                        <div>
                            <label class="mb-1 block text-[11px] font-medium text-slate-600">Tanggal Jual</label>
                            <input type="date" name="tanggal_jual" x-model="formData.tanggal_jual"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                        </div>
                    </div>
                </section>

                <!-- Catatan -->
                <div>
                    <label class="mb-1 block text-[11px] font-semibold text-slate-600">Catatan</label>
                    <textarea name="catatan" x-model="formData.catatan" rows="2" placeholder="Catatan opsional..."
                        class="w-full resize-y rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20"></textarea>
                </div>
            </div>

            <!-- Footer tetap terlihat -->
            <div class="flex shrink-0 justify-end gap-2 border-t border-slate-100 bg-white px-4 py-3 sm:px-5">
                <button type="button" @click="open = false"
                    class="rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit"
                    class="rounded-lg bg-brand-teal px-5 py-2 text-xs font-semibold text-white hover:brightness-105">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
