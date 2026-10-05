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
     * Hitung ringkasan saldo per sumber dana & total keseluruhan beserta statistik pengeluaran
     */
    public function getSummaryData(): array
    {
        $sumberDanaList = SumberDanaPengeluaran::all();

        $totalPemasukkan = Pemasukkan::sum('jumlah');
        $totalPengeluaran = Pengeluaran::sum('jumlah');

        // Kalkulasi Saldo per Sumber Dana
        $sumberDanaSummary = $sumberDanaList->map(function ($sumber) {
            $pengeluaran = Pengeluaran::where('sumber_dana_id', $sumber->id)->sum('jumlah');
            $mutasiKeluar = MutasiSaldo::where('dari_sumber_dana_id', $sumber->id)->sum('jumlah');
            $mutasiMasuk = MutasiSaldo::where('ke_sumber_dana_id', $sumber->id)->sum('jumlah');

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

        // --- STATISTIK PENGELUARAN SELURUH SUMBER DANA ---
        $statsAll = $this->getExpenseStats();

        // --- STATISTIK PENGELUARAN KHUSUS "UANG MAKAN" ---
        $uangMakan = $sumberDanaList->firstWhere('nama_sumber_dana', 'Uang Makan');
        $statsUangMakan = $uangMakan ? $this->getExpenseStats($uangMakan->id) : [
            'highest' => null,
            'lowest' => null,
            'average' => 0,
        ];

        return [
            'total_saldo' => $totalSaldo,
            'total_pemasukkan' => $totalPemasukkan,
            'total_pengeluaran' => $totalPengeluaran,
            'sumber_dana' => $sumberDanaSummary,
            'stats_all' => $statsAll,
            'stats_uang_makan' => $statsUangMakan,
        ];
    }

    /**
     * Helper untuk menghitung pengeluaran tertinggi per hari, terendah per hari, dan rata-rata pengeluaran per hari
     */
    private function getExpenseStats(?int $sumberDanaId = null): array
    {
        $query = Pengeluaran::query();

        if ($sumberDanaId) {
            $query->where('sumber_dana_id', $sumberDanaId);
        }

        // Group pengeluaran berdasarkan tanggal
        $dailyExpenses = (clone $query)
            ->select('tanggal', DB::raw('SUM(jumlah) as total_harian'))
            ->groupBy('tanggal')
            ->orderBy('total_harian', 'desc')
            ->get();

        if ($dailyExpenses->isEmpty()) {
            return [
                'highest' => null,
                'lowest' => null,
                'average' => 0,
            ];
        }

        $highest = $dailyExpenses->first();
        $lowest = $dailyExpenses->last();
        $average = $dailyExpenses->avg('total_harian');

        return [
            'highest' => [
                'tanggal' => $highest->tanggal,
                'total' => $highest->total_harian,
            ],
            'lowest' => [
                'tanggal' => $lowest->tanggal,
                'total' => $lowest->total_harian,
            ],
            'average' => $average,
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