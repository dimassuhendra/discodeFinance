<?php

namespace App\Http\Controllers;

use App\Services\SumberDanaService;
use Illuminate\Http\Request;

class SumberDanaController extends Controller
{
    protected $sumberDanaService;

    public function __construct(SumberDanaService $sumberDanaService)
    {
        $this->sumberDanaService = $sumberDanaService;
    }

    public function index()
    {
        $data = $this->sumberDanaService->getAllData();

        return view('menu.transactions.sumber-dana.index', [
            'pemasukkan' => $data['pemasukkan'],
            'pengeluaran' => $data['pengeluaran'],
            'chartData' => $data['chartData'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipe' => 'required|in:pemasukkan,pengeluaran',
            'nama_sumber_dana' => 'required|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $this->sumberDanaService->storeData($validated);

        return redirect()->back()->with('success', 'Sumber dana berhasil ditambahkan.');
    }

    public function update(Request $request, $tipe, $id)
    {
        $validated = $request->validate([
            'nama_sumber_dana' => 'required|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $this->sumberDanaService->updateData($validated, $tipe, $id);

        return redirect()->back()->with('success', 'Sumber dana berhasil diperbarui.');
    }

    public function destroy($tipe, $id)
    {
        $this->sumberDanaService->deleteData($tipe, $id);

        return redirect()->back()->with('success', 'Sumber dana berhasil dihapus.');
    }
}
