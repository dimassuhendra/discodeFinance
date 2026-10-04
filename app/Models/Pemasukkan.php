<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasukkan extends Model
{
    protected $table = 'pemasukkan';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function sumberDana()
    {
        return $this->belongsTo(SumberDanaPemasukkan::class, 'sumber_dana_id');
    }
}
