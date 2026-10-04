<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SumberDanaPemasukkan extends Model
{
    protected $table = 'sumber_dana_pemasukkan';
    protected $guarded = ['id'];

    public function pemasukkan()
    {
        return $this->hasMany(Pemasukkan::class, 'sumber_dana_id');
    }
}
