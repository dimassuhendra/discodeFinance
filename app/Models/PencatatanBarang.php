<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencatatanBarang extends Model
{
    protected $table = 'pencatatan_barang';
    protected $guarded = ['id'];

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'id_pengeluaran');
    }

    // Accessor / Dynamic Property
    public function getNamaBarangAttribute()
    {
        return $this->id_pengeluaran
            ? $this->pengeluaran?->nama_pengeluaran
            : $this->nama_barang_manual;
    }

    public function getTanggalPembelianAttribute()
    {
        return $this->id_pengeluaran
            ? $this->pengeluaran?->tanggal
            : $this->tanggal_pembelian_manual;
    }

    public function getHargaSatuanAttribute()
    {
        return $this->id_pengeluaran
            ? $this->pengeluaran?->jumlah
            : $this->harga_satuan_manual;
    }
}
