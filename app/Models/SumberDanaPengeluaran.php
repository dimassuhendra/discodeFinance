<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SumberDanaPengeluaran extends Model
{
    protected $table = 'sumber_dana_pengeluaran';
    protected $guarded = ['id'];

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'sumber_dana_id');
    }

    public function mutasiKeluar()
    {
        return $this->hasMany(MutasiSaldo::class, 'dari_sumber_dana_id');
    }

    public function mutasiMasuk()
    {
        return $this->hasMany(MutasiSaldo::class, 'ke_sumber_dana_id');
    }
}