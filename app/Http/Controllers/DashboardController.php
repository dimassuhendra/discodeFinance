<?php

namespace App\Http\Controllers;

use App\Services\FinanceDashboardService;
use App\Http\Requests\QuickPengeluaranRequest;
use App\Models\Pengeluaran;
use App\Models\SumberDanaPengeluaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected FinanceDashboardService $dashboardService
    ) {}

    public function index(): View
    {
        $summary = $this->dashboardService->getSummaryData();
        $recentTransactions = $this->dashboardService->getRecentTransactions(5);
        $sumberDanaOptions = SumberDanaPengeluaran::all();

        return view('welcome', compact('summary', 'recentTransactions', 'sumberDanaOptions'));
    }

    public function storeQuickPengeluaran(QuickPengeluaranRequest $request): RedirectResponse
    {
        Pengeluaran::create($request->validated());

        return redirect()->route('dashboard')
            ->with('success', 'Transaksi pengeluaran berhasil dicatat!');
    }
}
