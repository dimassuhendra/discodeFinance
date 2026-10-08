<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRiwayatInvestasiRequest;
use App\Http\Requests\InvestasiRequest;
use App\Http\Requests\UpdateRiwayatInvestasiRequest;
use App\Models\RiwayatInvestasi;
use App\Services\InvestasiService;
use Illuminate\Http\Request;

class InvestasiController extends Controller
{
    protected InvestasiService $investasiService;

    public function __construct(InvestasiService $investasiService)
    {
        $this->investasiService = $investasiService;
    }

    /**
     * Halaman Utama: Dashboard Insight & List Riwayat Investasi (dengan Filter)
     */
    public function index(Request $request)
    {
        $query = RiwayatInvestasi::with('pengeluaran');

        // Filter Multikriteria
        if ($request->filled('nama_instrumen')) {
            $query->where('nama_instrumen', 'LIKE', '%' . $request->nama_instrumen . '%');
        }

        if ($request->filled('jenis_instrumen')) {
            $query->where('jenis_instrumen', $request->jenis_instrumen);
        }

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        if ($request->filled('status')) {
            if ($request->status === 'closed') {
                $query->whereNotNull('total_jual');
            } elseif ($request->status === 'open') {
                $query->whereNull('total_jual');
            }
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal_beli', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        $riwayatList = $query->orderBy('tanggal_beli', 'desc')->paginate(15);

        // Ambil Insight & Summary
        $portfolioSummary = $this->investasiService->getPortfolioSummary();
        $holdingAnalysis  = $this->investasiService->getHoldingPeriodAnalysis();
        $unlinkedPengeluaran = $this->investasiService->getUnlinkedPengeluaranInvestasi();

        return view('menu.investasi.index', compact(
            'riwayatList',
            'portfolioSummary',
            'holdingAnalysis',
            'unlinkedPengeluaran'
        ));
    }

    /**
     * Form Tambah Riwayat Investasi
     */
    public function create()
    {
        $unlinkedPengeluaran = $this->investasiService->getUnlinkedPengeluaranInvestasi();
        return view('investasi.create', compact('unlinkedPengeluaran'));
    }

    /**
     * Simpan Transaksi Baru
     */
    public function store(InvestasiRequest $request)
    {
        $this->investasiService->storeRiwayat($request->validated());

        return redirect()->route('investasi.index')
            ->with('success', 'Riwayat investasi berhasil ditambahkan.');
    }

    /**
     * Detail Insight per Instrumen (misal: BBCA, USD, BTC)
     */
    public function show(string $namaInstrumen)
    {
        $insight = $this->investasiService->getInstrumenDetailInsight($namaInstrumen);
        return view('investasi.show', compact('insight'));
    }

    /**
     * Form Edit / Melengkapi Detail Investasi (Termasuk melengkapi Pengeluaran)
     */
    public function edit(RiwayatInvestasi $investasi)
    {
        $unlinkedPengeluaran = $this->investasiService->getUnlinkedPengeluaranInvestasi();
        return view('investasi.edit', compact('investasi', 'unlinkedPengeluaran'));
    }

    /**
     * Update Transaksi
     */
    public function update(UpdateRiwayatInvestasiRequest $request, RiwayatInvestasi $investasi)
    {
        $this->investasiService->updateRiwayat($investasi, $request->validated());

        return redirect()->route('investasi.index')
            ->with('success', 'Riwayat investasi berhasil diperbarui.');
    }

    /**
     * Hapus Riwayat
     */
    public function destroy(RiwayatInvestasi $investasi)
    {
        $investasi->delete();

        return redirect()->route('investasi.index')
            ->with('success', 'Riwayat investasi berhasil dihapus.');
    }
}
