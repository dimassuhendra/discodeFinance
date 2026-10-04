<?php

namespace App\Services;

use App\Models\Pemasukkan;
use App\Models\Pengeluaran;
use App\Models\MutasiSaldo;
use App\Models\SumberDanaPengeluaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinanceDashboardService
{
    /**
     * Hitung ringkasan saldo per sumber dana & total keseluruhan
     */
    public function getSummaryData(): array
    {
        $sumberDanaList = SumberDanaPengeluaran::all();

        $totalPemasukkan = Pemasukkan::sum('jumlah');
        $totalPengeluaran = Pengeluaran::sum('jumlah');

        // Kalkulasi Saldo per Sumber Dana
        $sumberDanaSummary = $sumberDanaList->map(function ($sumber) {
            // 1. Total Pengeluaran langsung dari sumber dana ini
            $pengeluaran = Pengeluaran::where('sumber_dana_id', $sumber->id)->sum('jumlah');

            // 2. Total Mutasi Keluar dari sumber dana ini
            $mutasiKeluar = MutasiSaldo::where('dari_sumber_dana_id', $sumber->id)->sum('jumlah');

            // 3. Total Mutasi Masuk ke sumber dana ini
            $mutasiMasuk = MutasiSaldo::where('ke_sumber_dana_id', $sumber->id)->sum('jumlah');

            // Hitung estimasi alokasi/saldo
            $saldoSaatIni = $sumber->budget - $pengeluaran - $mutasiKeluar + $mutasiMasuk;

            return [
                'id' => $sumber->id,
                'nama' => $sumber->nama_sumber_dana,
                'budget' => $sumber->budget,
                'pengeluaran' => $pengeluaran,
                'saldo' => $saldoSaatIni,
                'keterangan' => $sumber->keterangan,
            ];
        });

        $totalSaldo = $sumberDanaSummary->sum('saldo');

        return [
            'total_saldo' => $totalSaldo,
            'total_pemasukkan' => $totalPemasukkan,
            'total_pengeluaran' => $totalPengeluaran,
            'sumber_dana' => $sumberDanaSummary,
        ];
    }

    /**
     * Ambil 10 riwayat transaksi terbaru (Pengeluaran & Pemasukkan)
     */
    public function getRecentTransactions(int $limit = 10): Collection
    {
        $pengeluaran = Pengeluaran::with('sumberDana')
            ->latest('tanggal')
            ->latest('id')
            ->take($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tipe' => 'pengeluaran',
                    'tanggal' => $item->tanggal,
                    'nama' => $item->nama_pengeluaran,
                    'jumlah' => $item->jumlah,
                    'sumber_dana' => $item->sumberDana?->nama_sumber_dana ?? '-',
                ];
            });

        $pemasukkan = Pemasukkan::with('sumberDana')
            ->latest('tanggal')
            ->latest('id')
            ->take($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tipe' => 'pemasukkan',
                    'tanggal' => $item->tanggal,
                    'nama' => $item->nama_pemasukkan,
                    'jumlah' => $item->jumlah,
                    'sumber_dana' => $item->sumberDana?->nama_sumber_dana ?? '-',
                ];
            });

        return $pengeluaran->merge($pemasukkan)
            ->sortByDesc('tanggal')
            ->take($limit)
            ->values();
    }
}
