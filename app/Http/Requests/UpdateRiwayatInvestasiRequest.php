<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRiwayatInvestasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_pengeluaran'    => 'nullable|exists:pengeluaran,id',
            'nama_instrumen'    => 'sometimes|required|string|max:255',
            'jenis_instrumen'   => 'nullable|string|max:255',
            'platform'          => 'nullable|string|max:255',
            'jumlah_unit'       => 'nullable|numeric|min:0',
            'harga_beli_satuan' => 'nullable|numeric|min:0',
            'total_beli'        => 'nullable|numeric|min:0',
            'tanggal_beli'      => 'sometimes|required|date',
            'harga_jual_satuan' => 'nullable|numeric|min:0',
            'total_jual'        => 'nullable|numeric|min:0',
            'tanggal_jual'      => 'nullable|date|after_or_equal:tanggal_beli',
            'catatan'           => 'nullable|string',
        ];
    }
}
