<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencatatanInvestasi extends Model
{
    protected $table = 'pencatatan_investasi';
    protected $guarded = ['id'];

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'id_pengeluaran');
    }

    // Accessor / Dynamic Property
    public function getModalAwalAttribute()
    {
        return $this->id_pengeluaran
            ? $this->pengeluaran?->jumlah
            : $this->modal_awal_manual;
    }

    public function getTanggalAttribute()
    {
        return $this->id_pengeluaran
            ? $this->pengeluaran?->tanggal
            : $this->tanggal_manual;
    }

    public function getSumberDanaNamaAttribute()
    {
        return $this->id_pengeluaran
            ? $this->pengeluaran?->sumberDana?->nama_sumber_dana
            : ($this->sumber_dana_manual ?? 'Uang Investasi');
    }
}
