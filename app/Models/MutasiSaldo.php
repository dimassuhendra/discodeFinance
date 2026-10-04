<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiSaldo extends Model
{
    protected $table = 'mutasi_saldo';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function dariSumberDana()
    {
        return $this->belongsTo(SumberDanaPengeluaran::class, 'dari_sumber_dana_id');
    }

    public function keSumberDana()
    {
        return $this->belongsTo(SumberDanaPengeluaran::class, 'ke_sumber_dana_id');
    }
}
