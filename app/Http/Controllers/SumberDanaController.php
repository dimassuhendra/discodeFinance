<?php

namespace App\Http\Controllers;

use App\Models\SumberDanaPemasukkan;
use App\Models\SumberDanaPengeluaran;
use Illuminate\Http\Request;

class SumberDanaController extends Controller
{
    public function index()
    {
        $pemasukkan = SumberDanaPemasukkan::latest()->get();
        $pengeluaran = SumberDanaPengeluaran::latest()->get();

        return view('transactions.sumber-dana.index', compact('pemasukkan', 'pengeluaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:pemasukkan,pengeluaran',
            'nama_sumber_dana' => 'required|string|max:255',
        ]);

        if ($request->tipe === 'pemasukkan') {
            SumberDanaPemasukkan::create(['nama_sumber_dana' => $request->nama_sumber_dana]);
        } else {
            SumberDanaPengeluaran::create(['nama_sumber_dana' => $request->nama_sumber_dana]);
        }

        return redirect()->back()->with('success', 'Sumber dana berhasil ditambahkan.');
    }

    public function update(Request $request, $tipe, $id)
    {
        $request->validate([
            'nama_sumber_dana' => 'required|string|max:255',
        ]);

        if ($tipe === 'pemasukkan') {
            $sumber = SumberDanaPemasukkan::findOrFail($id);
        } else {
            $sumber = SumberDanaPengeluaran::findOrFail($id);
        }

        $sumber->update(['nama_sumber_dana' => $request->nama_sumber_dana]);

        return redirect()->back()->with('success', 'Sumber dana berhasil diperbarui.');
    }

    public function destroy($tipe, $id)
    {
        if ($tipe === 'pemasukkan') {
            SumberDanaPemasukkan::findOrFail($id)->delete();
        } else {
            SumberDanaPengeluaran::findOrFail($id)->delete();
        }

        return redirect()->back()->with('success', 'Sumber dana berhasil dihapus.');
    }
}
