<?php

namespace App\Services;

use App\Models\MutasiSaldo;
use App\Models\Pemasukkan;
use App\Models\Pengeluaran;
use App\Models\SumberDanaPengeluaran;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinanceDashboardService
{
    private const DAILY_FOOD_BUDGET = 40000;

    /**
     * Build month-to-date transaction summaries while keeping current balances cumulative.
     */
    public function getSummaryData(): array
    {
        $today = now()->startOfDay();
        $periodStart = $today->copy()->startOfMonth();
        $startDate = $periodStart->toDateString();
        $endDate = $today->toDateString();
        $sumberDanaList = SumberDanaPengeluaran::all();

        $totalPemasukkan = Pemasukkan::whereDate('tanggal', '>=', $startDate)->whereDate('tanggal', '<=', $endDate)->sum('jumlah');
        $totalPengeluaran = Pengeluaran::whereDate('tanggal', '>=', $startDate)->whereDate('tanggal', '<=', $endDate)->sum('jumlah');

        $sumberDanaSummary = $sumberDanaList->map(function (SumberDanaPengeluaran $sumber) use ($startDate, $endDate) {
            $totalPengeluaran = Pengeluaran::where('sumber_dana_id', $sumber->id)->sum('jumlah');
            $pengeluaranBulanIni = Pengeluaran::where('sumber_dana_id', $sumber->id)
                ->whereDate('tanggal', '>=', $startDate)
                ->whereDate('tanggal', '<=', $endDate)
                ->sum('jumlah');
            $mutasiKeluar = MutasiSaldo::where('dari_sumber_dana_id', $sumber->id)->sum('jumlah');
            $mutasiMasuk = MutasiSaldo::where('ke_sumber_dana_id', $sumber->id)->sum('jumlah');

            return [
                'id' => $sumber->id,
                'nama' => $sumber->nama_sumber_dana,
                'budget' => $sumber->budget,
                'pengeluaran' => $pengeluaranBulanIni,
                'saldo' => $sumber->budget - $totalPengeluaran - $mutasiKeluar + $mutasiMasuk,
                'keterangan' => $sumber->keterangan,
            ];
        });

        $budgetUsage = $sumberDanaSummary
            ->map(function (array $sumber) {
                $budget = (float) $sumber['budget'];
                $pengeluaran = (float) $sumber['pengeluaran'];

                return [
                    'nama' => $sumber['nama'],
                    'budget' => $budget,
                    'pengeluaran' => $pengeluaran,
                    'sisa' => max(0, $budget - $pengeluaran),
                    'persentase' => $budget > 0
                        ? round(($pengeluaran / $budget) * 100, 1)
                        : 0,
                    'over_budget' => $budget > 0 && $pengeluaran > $budget,
                ];
            })
            ->values();

        $statsAll = $this->getExpenseStats(null, $startDate, $endDate);

        $insights = [];

        $cashFlow = $totalPemasukkan - $totalPengeluaran;

        if ($cashFlow > 0) {
            $insights[] = [
                'type' => 'positive',
                'title' => 'Arus kas positif',
                'description' => 'Pemasukan bulan ini masih lebih besar daripada total pengeluaran.',
            ];
        } elseif ($cashFlow < 0) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Arus kas negatif',
                'description' => 'Total pengeluaran bulan ini sudah lebih besar daripada pemasukan.',
            ];
        } else {
            $insights[] = [
                'type' => 'neutral',
                'title' => 'Arus kas seimbang',
                'description' => 'Total pemasukan dan pengeluaran bulan ini berada pada nilai yang sama.',
            ];
        }

        foreach ($budgetUsage as $budget) {
            if ($budget['over_budget']) {
                $insights[] = [
                    'type' => 'warning',
                    'title' => 'Budget ' . $budget['nama'] . ' terlampaui',
                    'description' => 'Pengeluaran sudah melebihi budget sebesar Rp '
                        . number_format(
                            $budget['pengeluaran'] - $budget['budget'],
                            0,
                            ',',
                            '.'
                        ) . '.',
                ];
            } elseif ($budget['persentase'] >= 80) {
                $insights[] = [
                    'type' => 'warning',
                    'title' => 'Budget ' . $budget['nama'] . ' hampir habis',
                    'description' => 'Sebanyak ' . $budget['persentase'] . '% budget sudah digunakan.',
                ];
            }
        }

        if ($statsAll['highest']) {
            $insights[] = [
                'type' => 'info',
                'title' => 'Pengeluaran terbesar bulan ini',
                'description' => 'Pengeluaran tertinggi dalam satu hari mencapai Rp '
                    . number_format(
                        $statsAll['highest']['total'],
                        0,
                        ',',
                        '.'
                    ) . '.',
            ];
        }

        $uangMakan = $sumberDanaList->firstWhere('nama_sumber_dana', 'Uang Makan');
        $emptyStats = ['highest' => null, 'lowest' => null, 'average' => 0];
        $foodExpenses = $uangMakan
            ? (float) Pengeluaran::where('sumber_dana_id', $uangMakan->id)
                ->whereDate('tanggal', '>=', $startDate)
                ->whereDate('tanggal', '<=', $endDate)
                ->sum('jumlah')
            : 0.0;
        $foodBudgetStart = $periodStart->copy();
        if ($uangMakan?->created_at && $uangMakan->created_at->gt($foodBudgetStart)) {
            $foodBudgetStart = $uangMakan->created_at->copy()->startOfDay();
        }
        $foodBudgetDays = $foodBudgetStart->gt($today) ? 0 : (int) $foodBudgetStart->diffInDays($today) + 1;
        $foodBudget = $uangMakan ? self::DAILY_FOOD_BUDGET * $foodBudgetDays : 0;

        return [
            'total_saldo' => $sumberDanaSummary->sum('saldo'),
            'total_pemasukkan' => $totalPemasukkan,
            'total_pengeluaran' => $totalPengeluaran,
            'cash_flow' => $totalPemasukkan - $totalPengeluaran,
            'period_label' => $periodStart->translatedFormat('F Y'),
            'sumber_dana' => $sumberDanaSummary,
            'budget_usage' => $budgetUsage,
            'insights' => $insights,
            'stats_all' => $statsAll,
            'stats_uang_makan' => $uangMakan
                ? $this->getExpenseStats($uangMakan->id, $startDate, $endDate)
                : $emptyStats,
            'charts' => [
                'food_budget' => [
                    'budget' => $foodBudget,
                    'spent' => $foodExpenses,
                    'remaining' => max(0, $foodBudget - $foodExpenses),
                    'daily_budget' => self::DAILY_FOOD_BUDGET,
                ],
                'daily_expenses' => $this->getDailyExpenses($startDate, $today),
                'food_budget_forecast' => $uangMakan
                    ? $this->getFoodBudgetForecast($uangMakan, $today)
                    : [],
            ],
        ];
    }

    /**
     * Get the highest, lowest, and average daily expenses for a period.
     */
    private function getExpenseStats(?int $sumberDanaId, string $startDate, string $endDate): array
    {
        $query = Pengeluaran::query()
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate);

        if ($sumberDanaId !== null) {
            $query->where('sumber_dana_id', $sumberDanaId);
        }

        $dailyExpenses = (clone $query)
            ->select('tanggal', DB::raw('SUM(jumlah) as total_harian'))
            ->groupBy('tanggal')
            ->orderBy('total_harian')
            ->get();

        if ($dailyExpenses->isEmpty()) {
            return ['highest' => null, 'lowest' => null, 'average' => 0];
        }

        $lowest = $dailyExpenses->first();
        $highest = $dailyExpenses->last();

        return [
            'highest' => ['tanggal' => $highest->tanggal, 'total' => $highest->total_harian],
            'lowest' => ['tanggal' => $lowest->tanggal, 'total' => $lowest->total_harian],
            'average' => $dailyExpenses->avg('total_harian'),
        ];
    }

    /**
     * Get the latest transactions recorded in the current month.
     */
    public function getRecentTransactions(int $limit = 5): Collection
    {
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->toDateString();
        $limit = max(0, $limit);

        $pengeluaran = Pengeluaran::with('sumberDana')
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->latest('tanggal')
            ->latest('id')
            ->take($limit)
            ->get()
            ->map(fn(Pengeluaran $item) => [
                'id' => $item->id,
                'tipe' => 'pengeluaran',
                'tanggal' => $item->tanggal,
                'nama' => $item->nama_pengeluaran,
                'jumlah' => $item->jumlah,
                'sumber_dana' => $item->sumberDana?->nama_sumber_dana ?? '-',
            ]);

        $pemasukkan = Pemasukkan::with('sumberDana')
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->latest('tanggal')
            ->latest('id')
            ->take($limit)
            ->get()
            ->map(fn(Pemasukkan $item) => [
                'id' => $item->id,
                'tipe' => 'pemasukkan',
                'tanggal' => $item->tanggal,
                'nama' => $item->nama_pemasukkan,
                'jumlah' => $item->jumlah,
                'sumber_dana' => $item->sumberDana?->nama_sumber_dana ?? '-',
            ]);

        return $pengeluaran->merge($pemasukkan)
            ->sortByDesc('tanggal')
            ->take($limit)
            ->values();
    }

    private function getDailyExpenses(string $startDate, CarbonInterface $today): array
    {
        $totals = Pengeluaran::query()
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $today->toDateString())
            ->select('tanggal', DB::raw('SUM(jumlah) as total'))
            ->groupBy('tanggal')
            ->get()
            ->mapWithKeys(fn($row) => [Carbon::parse($row->tanggal)->toDateString() => (float) $row->total]);

        $dailyExpenses = [];

        for ($day = 1; $day <= $today->day; $day++) {
            // Buat objek tanggal untuk iterasi hari ini
            $currentDate = $today->copy()->startOfMonth()->addDays($day - 1);

            $dailyExpenses[] = [
                'day'        => $day,
                'month'      => $currentDate->month,
                'month_name' => $currentDate->translatedFormat('M'),
                'date'       => $currentDate->toDateString(),
                'amount'     => (float) ($totals[$currentDate->toDateString()] ?? 0),
            ];
        }

        return $dailyExpenses;
    }

    private function getFoodBudgetForecast(SumberDanaPengeluaran $source, CarbonInterface $today): array
    {
        // 1. Tentukan rentang tanggal: 10 hari lalu s.d. Besok
        $startDate = $today->copy()->subDays(3);
        $endDate = $today->copy()->addDay(); // Besok

        // 2. Hitung carryOver historis dari awal akun dibuat sampai H-11 (sebelum rentang tabel)
        $createdAt = $source->created_at?->copy()->startOfDay() ?? $startDate->copy();
        $beforeRangeEnd = $startDate->copy()->subDay();

        $historicalExpenses = Pengeluaran::query()
            ->where('sumber_dana_id', $source->id)
            ->whereDate('tanggal', '>=', $createdAt->toDateString())
            ->whereDate('tanggal', '<=', $beforeRangeEnd->toDateString())
            ->select('tanggal', DB::raw('SUM(jumlah) as total'))
            ->groupBy('tanggal')
            ->get()
            ->mapWithKeys(fn($row) => [Carbon::parse($row->tanggal)->toDateString() => (float) $row->total]);

        $carryOver = 0.0;
        if ($createdAt->lte($beforeRangeEnd)) {
            for ($date = $createdAt->copy(); $date->lte($beforeRangeEnd); $date = $date->addDay()) {
                $spent = (float) ($historicalExpenses[$date->toDateString()] ?? 0);
                $carryOver = max(0, $carryOver + $spent - self::DAILY_FOOD_BUDGET);
            }
        }

        // 3. Ambil pengeluaran aktual untuk rentang tabel (H-10 s.d. Besok)
        $rangeExpenses = Pengeluaran::query()
            ->where('sumber_dana_id', $source->id)
            ->whereDate('tanggal', '>=', $startDate->toDateString())
            ->whereDate('tanggal', '<=', $endDate->toDateString())
            ->select('tanggal', DB::raw('SUM(jumlah) as total'))
            ->groupBy('tanggal')
            ->get()
            ->mapWithKeys(fn($row) => [Carbon::parse($row->tanggal)->toDateString() => (float) $row->total]);

        $forecast = [];

        // 4. Looping dari H-10 s.d. Besok
        for ($date = $startDate->copy(); $date->lte($endDate); $date = $date->addDay()) {
            $spent = (float) ($rangeExpenses[$date->toDateString()] ?? 0);
            $dailyAllowance = max(0, self::DAILY_FOOD_BUDGET - $carryOver);
            $dailyBalance = $dailyAllowance - $spent;

            $forecast[] = [
                'date' => $date->copy(),
                'allowance' => (float) $dailyAllowance,
                'spent' => $spent,
                'remaining' => (float) max(0, $dailyBalance),
                'over_budget' => (float) max(0, -$dailyBalance),
            ];

            // Hitung akumulasi carryOver untuk hari berikutnya
            $carryOver = max(0, $carryOver + $spent - self::DAILY_FOOD_BUDGET);
        }

        return $forecast;
    }
}
