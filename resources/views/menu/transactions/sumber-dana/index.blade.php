@component('components.layouts.app', ['title' => 'Kelola Pos / Sumber Dana'])
    <div
        class="space-y-8 font-sans"
        x-data="{
            showModal: false,
            isEdit: false,
            tipe: 'pemasukkan',
            id: null,
            nama: '',
            actionUrl: '{{ route('sumber-dana.store') }}',

            openCreateModal(selectedTipe) {
                this.isEdit = false;
                this.tipe = selectedTipe;
                this.nama = '';
                this.actionUrl = '{{ route('sumber-dana.store') }}';
                this.showModal = true;
            },

            openEditModal(selectedTipe, item) {
                this.isEdit = true;
                this.tipe = selectedTipe;
                this.id = item.id;
                this.nama = item.nama_sumber_dana;
                this.actionUrl = `/sumber-dana/${selectedTipe}/${item.id}`;
                this.showModal = true;
            }
        }"
    >
        <header class="flex items-center justify-between">
            <div>
                <a href="{{ route('transactions.index') }}" class="text-sm font-medium text-brand-teal hover:underline">← Kembali ke Transaksi</a>
                <h1 class="mt-2 font-heading text-3xl font-bold tracking-tight text-brand-dark">Kelola Pos / Sumber Dana</h1>
            </div>
        </header>

        @include('transactions.partials.alert')

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            {{-- TABEL SUMBER DANA PEMASUKAN --}}
            <section class="rounded-2xl border border-brand-teal/15 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between pb-4">
                    <h2 class="text-lg font-bold text-slate-800">Sumber Dana Pemasukan</h2>
                    <button
                        type="button"
                        x-on:click="openCreateModal('pemasukkan')"
                        class="rounded-xl bg-brand-teal px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-brand-teal/90"
                    >
                        + Tambah
                    </button>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($pemasukkan as$item)
                        <div class="flex items-center justify-between py-3">
                            <span class="font-medium text-slate-700">{{ $item->nama_sumber_dana }}</span>
                            <div class="flex items-center gap-2">
                                <button type="button" x-on:click="openEditModal('pemasukkan', {{ $item }})" class="text-xs font-semibold text-slate-500 hover:text-brand-teal">Edit</button>
                                <form action="{{ route('sumber-dana.destroy', ['tipe' => 'pemasukkan', 'id' => $item->id]) }}" method="POST" onsubmit="return confirm('Hapus sumber dana ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-500 hover:text-rose-700">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-center text-xs text-slate-400">Belum ada data sumber dana pemasukan.</p>
                    @endforelse
                </div>
            </section>

            {{-- TABEL SUMBER DANA PENGELUARAN --}}
            <section class="rounded-2xl border border-brand-teal/15 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between pb-4">
                    <h2 class="text-lg font-bold text-slate-800">Pos Pengeluaran</h2>
                    <button
                        type="button"
                        x-on:click="openCreateModal('pengeluaran')"
                        class="rounded-xl bg-brand-orange px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-brand-orange/90"
                    >
                        + Tambah
                    </button>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($pengeluaran as$item)
                        <div class="flex items-center justify-between py-3">
                            <span class="font-medium text-slate-700">{{ $item->nama_sumber_dana }}</span>
                            <div class="flex items-center gap-2">
                                <button type="button" x-on:click="openEditModal('pengeluaran', {{ $item }})" class="text-xs font-semibold text-slate-500 hover:text-brand-orange">Edit</button>
                                <form action="{{ route('sumber-dana.destroy', ['tipe' => 'pengeluaran', 'id' => $item->id]) }}" method="POST" onsubmit="return confirm('Hapus pos pengeluaran ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-500 hover:text-rose-700">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-center text-xs text-slate-400">Belum ada data pos pengeluaran.</p>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- MODAL FORM (TAMBAH / EDIT) --}}
        @include('transactions.sumber-dana.modal-form')
    </div>
@endcomponent