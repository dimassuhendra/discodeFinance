<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePemasukkanRequest;
use App\Http\Requests\StorePengeluaranRequest;
use App\Http\Requests\StoreMutasiRequest;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $transactions             = $this->transactionService->getPaginatedTransactions($request);
        $sumberDanaPengeluaranList = $this->transactionService->getSumberDanaPengeluaran();
        $sumberDanaPemasukkanList  = $this->transactionService->getSumberDanaPemasukkan();

        return view('menu.transactions.index', compact(
            'transactions',
            'sumberDanaPengeluaranList',
            'sumberDanaPemasukkanList'
        ));
    }

    public function storePemasukkan(StorePemasukkanRequest $request)
    {
        $this->transactionService->createPemasukkan($request->validated());

        return redirect()->back()->with('success', 'Pemasukkan berhasil disimpan!');
    }

    public function storePengeluaran(StorePengeluaranRequest $request)
    {
        $this->transactionService->createPengeluaran($request->validated());

        return redirect()->back()->with('success', 'Pengeluaran berhasil disimpan!');
    }

    public function storeMutasi(StoreMutasiRequest $request)
    {
        $this->transactionService->createMutasi($request->validated());

        return redirect()->back()->with('success', 'Mutasi Saldo berhasil diproses!');
    }

    public function destroy(string $type, int $id)
    {
        $deleted = $this->transactionService->deleteTransaction($type, $id);

        if ($deleted) {
            return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Gagal menghapus transaksi.');
    }
}
