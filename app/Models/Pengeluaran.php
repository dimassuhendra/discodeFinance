<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table = 'pengeluaran';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function sumberDana()
    {
        return $this->belongsTo(SumberDanaPengeluaran::class, 'sumber_dana_id');
    }

    public function detailBarang()
    {
        return $this->hasOne(PencatatanBarang::class, 'id_pengeluaran');
    }

    public function detailInvestasi()
    {
        return $this->hasOne(PencatatanInvestasi::class, 'id_pengeluaran');
    }

    public function riwayatInvestasi()
    {
        return $this->hasOne(RiwayatInvestasi::class, 'id_pengeluaran');
    }
}
