<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatInvestasi extends Model
{
    use HasFactory;

    protected $table = 'riwayat_investasi';
    protected $guarded = ['id'];

    /**
     * Type casting untuk atribut.
     */
    protected $casts = [
        'tanggal_beli' => 'date',
        'tanggal_jual' => 'date',
        'jumlah_unit' => 'decimal:4',
        'harga_beli_satuan' => 'decimal:2',
        'total_beli' => 'decimal:2',
        'harga_jual_satuan' => 'decimal:2',
        'total_jual' => 'decimal:2',
    ];

    /**
     * Relasi opsional ke model Pengeluaran.
     */
    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'id_pengeluaran');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Helper Methods (Opsional untuk kemudahan mengambil insight)
    |--------------------------------------------------------------------------
    */

    /**
     * Menghitung Laba/Rugi (Profit or Loss) nominal.
     * Mengembalikan NULL jika transaksi belum di-closed (belum dijual).
     */
    public function getProfitLossAttribute()
    {
        if (is_null($this->total_jual) || is_null($this->total_beli)) {
            return null;
        }

        return $this->total_jual - $this->total_beli;
    }

    /**
     * Menghitung persentase keuntungan (% Gain/Loss).
     * Mengembalikan NULL jika transaksi belum dijual atau total_beli = 0.
     */
    public function getProfitLossPercentageAttribute()
    {
        if (is_null($this->total_jual) || is_null($this->total_beli) || $this->total_beli == 0) {
            return null;
        }

        return (($this->total_jual - $this->total_beli) / $this->total_beli) * 100;
    }

    /**
     * Menghitung durasi kepemilikan aset dalam jumlah hari (Holding Period).
     * Mengembalikan NULL jika belum dijual.
     */
    public function getHoldingDaysAttribute()
    {
        if (!$this->tanggal_jual || !$this->tanggal_beli) {
            return null;
        }

        return $this->tanggal_beli->diffInDays($this->tanggal_jual);
    }
}
