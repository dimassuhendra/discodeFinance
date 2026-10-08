<?php

namespace App\Services;

use App\Models\RiwayatInvestasi;
use App\Models\Pengeluaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InvestasiService
{
    /**
     * Menyimpan data riwayat investasi & menghitung total jika hanya ada unit & harga satuan.
     */
    public function storeRiwayat(array $data): RiwayatInvestasi
    {
        return DB::transaction(function () use ($data) {
            // Kalkulasi total beli otomatis jika harga_beli_satuan & jumlah_unit tersedia
            if (isset($data['harga_beli_satuan'], $data['jumlah_unit']) && empty($data['total_beli'])) {
                $data['total_beli'] = $data['harga_beli_satuan'] * $data['jumlah_unit'];
            }

            // Kalkulasi total jual otomatis jika harga_jual_satuan & jumlah_unit tersedia
            if (isset($data['harga_jual_satuan'], $data['jumlah_unit']) && empty($data['total_jual'])) {
                $data['total_jual'] = $data['harga_jual_satuan'] * $data['jumlah_unit'];
            }

            // Jika terhubung ke pengeluaran dan total_beli belum diisi manual
            if (!empty($data['id_pengeluaran']) && empty($data['total_beli'])) {
                $pengeluaran = Pengeluaran::find($data['id_pengeluaran']);
                if ($pengeluaran) {
                    $data['total_beli'] = $pengeluaran->jumlah;
                    $data['tanggal_beli'] = $data['tanggal_beli'] ?? $pengeluaran->tanggal;
                }
            }

            return RiwayatInvestasi::create($data);
        });
    }

    /**
     * Memperbarui data riwayat investasi.
     */
    public function updateRiwayat(RiwayatInvestasi $riwayat, array $data): RiwayatInvestasi
    {
        return DB::transaction(function () use ($riwayat, $data) {
            $unit = $data['jumlah_unit'] ?? $riwayat->jumlah_unit;

            if (isset($data['harga_beli_satuan']) && $unit && empty($data['total_beli'])) {
                $data['total_beli'] = $data['harga_beli_satuan'] * $unit;
            }

            if (isset($data['harga_jual_satuan']) && $unit && empty($data['total_jual'])) {
                $data['total_jual'] = $data['harga_jual_satuan'] * $unit;
            }

            $riwayat->update($data);
            return $riwayat;
        });
    }

    /**
     * Mendapatkan daftar pengeluaran dari sumber dana "Dana Investasi" / "Uang Investasi"
     * yang BELUM dihubungkan ke riwayat_investasi.
     */
    public function getUnlinkedPengeluaranInvestasi(): Collection
    {
        return Pengeluaran::whereHas('sumberDana', function ($q) {
            $q->where('nama_sumber_dana', 'LIKE', '%investasi%');
        })
            ->whereDoesntHave('riwayatInvestasi')
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    /**
     * Menghitung Ringkasan Performa Portofolio (Closed Positions).
     */
    public function getPortfolioSummary(): array
    {
        $closed = RiwayatInvestasi::whereNotNull('total_jual')->get();

        $totalInvested = $closed->sum('total_beli');
        $totalReturned = $closed->sum('total_jual');
        $totalProfit = $totalReturned - $totalInvested;

        $roiPercentage = $totalInvested > 0 ? ($totalProfit / $totalInvested) * 100 : 0;

        $winTrades = $closed->filter(fn($item) => ($item->total_jual - $item->total_beli) > 0)->count();
        $lossTrades = $closed->filter(fn($item) => ($item->total_jual - $item->total_beli) < 0)->count();
        $totalTrades = $closed->count();

        $winRate = $totalTrades > 0 ? ($winTrades / $totalTrades) * 100 : 0;

        return [
            'total_realized_profit' => $totalProfit,
            'roi_percentage'        => round($roiPercentage, 2),
            'total_trades'          => $totalTrades,
            'win_trades'            => $winTrades,
            'loss_trades'           => $lossTrades,
            'win_rate'              => round($winRate, 2),
        ];
    }

    /**
     * Histori & Analisis Spesifik Per Instrumen (misal: BBCA) + Acuan Beli Ulang (Re-entry Zone).
     */
    public function getInstrumenDetailInsight(string $namaInstrumen): array
    {
        $list = RiwayatInvestasi::where('nama_instrumen', $namaInstrumen)
            ->orderBy('tanggal_beli', 'desc')
            ->get();

        $closed = $list->whereNotNull('total_jual');

        // Beli harga terendah yang memberikan profit (Re-entry zone)
        $profitableBuys = $closed->filter(fn($i) => ($i->total_jual - $i->total_beli) > 0);

        $bestReentryPrice = $profitableBuys->min('harga_beli_satuan');
        $averageBuyPrice = $list->whereNotNull('harga_beli_satuan')->avg('harga_beli_satuan');

        // Holding Period
        $holdingDaysList = $closed->map(fn($i) => $i->holding_days)->filter();

        return [
            'nama_instrumen'      => $namaInstrumen,
            'total_transaksi'     => $list->count(),
            'rata_harga_beli'     => round($averageBuyPrice, 2),
            'best_reentry_price'  => $bestReentryPrice, // Acuan Beli Ulang
            'fastest_holding_day' => $holdingDaysList->min(),
            'longest_holding_day' => $holdingDaysList->max(),
            'avg_holding_day'     => round($holdingDaysList->avg(), 1),
            'histori'             => $list,
        ];
    }

    /**
     * Analisis Durasi Kepemilikan (Holding Period Analysis).
     */
    public function getHoldingPeriodAnalysis(): array
    {
        $closed = RiwayatInvestasi::whereNotNull('tanggal_jual')->get();

        $scalping = $closed->filter(fn($i) => $i->holding_days < 7);
        $swing    = $closed->filter(fn($i) => $i->holding_days >= 7 && $i->holding_days <= 30);
        $investing = $closed->filter(fn($i) => $i->holding_days > 30);

        return [
            'scalping'  => [
                'count'        => $scalping->count(),
                'total_profit' => $scalping->sum('profit_loss'),
                'win_rate'     => $this->calculateWinRate($scalping),
            ],
            'swing'     => [
                'count'        => $swing->count(),
                'total_profit' => $swing->sum('profit_loss'),
                'win_rate'     => $this->calculateWinRate($swing),
            ],
            'investing' => [
                'count'        => $investing->count(),
                'total_profit' => $investing->sum('profit_loss'),
                'win_rate'     => $this->calculateWinRate($investing),
            ],
        ];
    }

    private function calculateWinRate(Collection $collection): float
    {
        if ($collection->isEmpty()) return 0.0;
        $wins = $collection->filter(fn($i) => $i->profit_loss > 0)->count();
        return round(($wins / $collection->count()) * 100, 2);
    }
}
