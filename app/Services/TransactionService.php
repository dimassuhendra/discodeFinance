<?php

namespace App\Services;

use App\Models\Pemasukkan;
use App\Models\Pengeluaran;
use App\Models\MutasiSaldo; // atau Mutasi tergantung nama Model Anda
use App\Models\SumberDanaPengeluaran;
use App\Models\SumberDanaPemasukkan;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TransactionService
{
    public function getPaginatedTransactions(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $search = $request->input('search');
        $sumberDanaPengeluaranId = $request->input('sumber_dana_pengeluaran_id');
        $tipe = $request->input('tipe');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // 1. Query Pemasukkan (Sumber Dana dari `sumber_dana_pemasukkan`)
        $pemasukkanQuery = DB::table('pemasukkan as p')
            ->join('sumber_dana_pemasukkan as sp', 'p.sumber_dana_id', '=', 'sp.id')
            ->select([
                'p.id',
                DB::raw("'pemasukkan' as tipe_transaksi"),
                'p.tanggal',
                'p.nama_pemasukkan as nama',
                'p.jumlah',
                DB::raw("NULL as sumber_asal"),
                'sp.nama_sumber_dana as sumber_tujuan',
                'p.sumber_dana_id as sumber_tujuan_id',
                DB::raw("NULL as sumber_asal_id"),
                DB::raw("NULL as keterangan"),
                'p.created_at'
            ]);

        // 2. Query Pengeluaran (Sumber Dana dari `sumber_dana_pengeluaran`)
        $pengeluaranQuery = DB::table('pengeluaran as pe')
            ->join('sumber_dana_pengeluaran as sa', 'pe.sumber_dana_id', '=', 'sa.id')
            ->select([
                'pe.id',
                DB::raw("'pengeluaran' as tipe_transaksi"),
                'pe.tanggal',
                'pe.nama_pengeluaran as nama',
                'pe.jumlah',
                'sa.nama_sumber_dana as sumber_asal',
                DB::raw("NULL as sumber_tujuan"),
                DB::raw("NULL as sumber_tujuan_id"),
                'pe.sumber_dana_id as sumber_asal_id',
                DB::raw("NULL as keterangan"),
                'pe.created_at'
            ]);

        // 3. Query Mutasi Saldo (Relasi ke `sumber_dana_pengeluaran`)
        $mutasiQuery = DB::table('mutasi_saldo as m')
            ->join('sumber_dana_pengeluaran as sa', 'm.dari_sumber_dana_id', '=', 'sa.id')
            ->join('sumber_dana_pengeluaran as st', 'm.ke_sumber_dana_id', '=', 'st.id')
            ->select([
                'm.id',
                DB::raw("'mutasi' as tipe_transaksi"),
                'm.tanggal',
                DB::raw("'Transfer / Mutasi Saldo' as nama"),
                'm.jumlah',
                'sa.nama_sumber_dana as sumber_asal',
                'st.nama_sumber_dana as sumber_tujuan',
                'm.ke_sumber_dana_id as sumber_tujuan_id',
                'm.dari_sumber_dana_id as sumber_asal_id',
                'm.keterangan',
                'm.created_at'
            ]);

        // Filter Rentang Tanggal
        if ($startDate && $endDate) {
            $pemasukkanQuery->whereBetween('p.tanggal', [$startDate, $endDate]);
            $pengeluaranQuery->whereBetween('pe.tanggal', [$startDate, $endDate]);
            $mutasiQuery->whereBetween('m.tanggal', [$startDate, $endDate]);
        }

        // Filter Sumber Dana Pengeluaran (untuk Pengeluaran & Mutasi)
        if ($sumberDanaPengeluaranId) {
            $pengeluaranQuery->where('pe.sumber_dana_id', $sumberDanaPengeluaranId);
            $mutasiQuery->where(function ($q) use ($sumberDanaPengeluaranId) {
                $q->where('m.dari_sumber_dana_id', $sumberDanaPengeluaranId)
                    ->orWhere('m.ke_sumber_dana_id', $sumberDanaPengeluaranId);
            });
        }

        // Filter Kata Kunci (Search)
        if ($search) {
            $pemasukkanQuery->where('p.nama_pemasukkan', 'like', "\%{$search}%");
            $pengeluaranQuery->where('pe.nama_pengeluaran', 'like', "\%{$search}%");
            $mutasiQuery->where('m.keterangan', 'like', "\%{$search}%");
        }

        // Filter Tipe Transaksi
        if ($tipe === 'pemasukkan') {
            $combinedQuery = $pemasukkanQuery;
        } elseif ($tipe === 'pengeluaran') {
            $combinedQuery = $pengeluaranQuery;
        } elseif ($tipe === 'mutasi') {
            $combinedQuery = $mutasiQuery;
        } else {
            $combinedQuery = $pemasukkanQuery
                ->unionAll($pengeluaranQuery)
                ->unionAll($mutasiQuery);
        }

        // Ordering & Pagination
        $finalQuery = DB::table(DB::raw("({$combinedQuery->toSql()}) as transactions"))
            ->mergeBindings($combinedQuery)
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');

        $page = LengthAwarePaginator::resolveCurrentPage('page');
        $total = $finalQuery->count();
        $results = $finalQuery->forPage($page, $perPage)->get();

        return new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );
    }

    public function createPemasukkan(array $data)
    {
        return DB::transaction(fn() => DB::table('pemasukkan')->insert(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ])));
    }

    public function createPengeluaran(array $data)
    {
        return DB::transaction(fn() => DB::table('pengeluaran')->insert(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ])));
    }

    public function createMutasi(array $data)
    {
        return DB::transaction(fn() => DB::table('mutasi_saldo')->insert(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ])));
    }

    public function deleteTransaction(string $type, int $id): bool
    {
        return DB::transaction(function () use ($type, $id) {
            return match ($type) {
                'pemasukkan'  => DB::table('pemasukkan')->where('id', $id)->delete(),
                'pengeluaran' => DB::table('pengeluaran')->where('id', $id)->delete(),
                'mutasi'      => DB::table('mutasi_saldo')->where('id', $id)->delete(),
                default       => false,
            };
        });
    }

    public function getSumberDanaPengeluaran()
    {
        return DB::table('sumber_dana_pengeluaran')->orderBy('nama_sumber_dana', 'asc')->get();
    }

    public function getSumberDanaPemasukkan()
    {
        return DB::table('sumber_dana_pemasukkan')->orderBy('nama_sumber_dana', 'asc')->get();
    }
}
