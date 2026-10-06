<?php

namespace App\Services;

use App\Models\SumberDanaPemasukkan;
use App\Models\SumberDanaPengeluaran;

class SumberDanaService
{
    /**
     * Mengambil seluruh data pos beserta kalkulasi realisasi dari tabel pengeluaran
     */
    public function getAllData()
    {
        $pemasukkan = SumberDanaPemasukkan::latest()->get();
        $pengeluaran = SumberDanaPengeluaran::latest()->get();

        // Hitung realisasi pengeluaran bulan ini berdasarkan tabel 'pengeluaran'
        $pengeluaranWithRealisasi = $pengeluaran->map(function ($item) {
            $realisasi = $item->pengeluaran()
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->sum('jumlah') ?? 0;

            $budget = (float) $item->budget;
            $persentase = $budget > 0 ? min(round(($realisasi / $budget) * 100, 1), 100) : 0;

            $item->realisasi = $realisasi;
            $item->persentase_realisasi = $persentase;

            return $item;
        });

        // Format data untuk Chart Doughnut (Labels & Budgets)
        $chartData = [
            'labels' => $pengeluaran->pluck('nama_sumber_dana')->toArray(),
            'budgets' => $pengeluaran->pluck('budget')->map(fn($b) => (float) $b)->toArray(),
        ];

        return [
            'pemasukkan' => $pemasukkan,
            'pengeluaran' => $pengeluaranWithRealisasi,
            'chartData' => $chartData,
        ];
    }

    /**
     * Simpan data pos baru
     */
    public function storeData(array $data)
    {
        if ($data['tipe'] === 'pemasukkan') {
            return SumberDanaPemasukkan::create([
                'nama_sumber_dana' => $data['nama_sumber_dana'],
                'keterangan' => $data['keterangan'] ?? null,
            ]);
        }

        return SumberDanaPengeluaran::create([
            'nama_sumber_dana' => $data['nama_sumber_dana'],
            'budget' => $data['budget'] ?? 0,
            'keterangan' => $data['keterangan'] ?? null,
        ]);
    }

    /**
     * Update data pos
     */
    public function updateData(array $data, string $tipe, int $id)
    {
        if ($tipe === 'pemasukkan') {
            $sumber = SumberDanaPemasukkan::findOrFail($id);
            return $sumber->update([
                'nama_sumber_dana' => $data['nama_sumber_dana'],
                'keterangan' => $data['keterangan'] ?? null,
            ]);
        }

        $sumber = SumberDanaPengeluaran::findOrFail($id);
        return $sumber->update([
            'nama_sumber_dana' => $data['nama_sumber_dana'],
            'budget' => $data['budget'] ?? 0,
            'keterangan' => $data['keterangan'] ?? null,
        ]);
    }

    /**
     * Hapus data pos
     */
    public function deleteData(string $tipe, int $id)
    {
        if ($tipe === 'pemasukkan') {
            return SumberDanaPemasukkan::findOrFail($id)->delete();
        }

        return SumberDanaPengeluaran::findOrFail($id)->delete();
    }
}
