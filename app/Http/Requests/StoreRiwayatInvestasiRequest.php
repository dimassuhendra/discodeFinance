<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRiwayatInvestasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_pengeluaran'    => 'nullable|exists:pengeluaran,id',
            'nama_instrumen'    => 'required|string|max:255',
            'jenis_instrumen'   => 'nullable|string|max:255',
            'platform'          => 'nullable|string|max:255',
            'jumlah_unit'       => 'nullable|numeric|min:0',
            'harga_beli_satuan' => 'nullable|numeric|min:0',
            'total_beli'        => 'required_without:harga_beli_satuan|nullable|numeric|min:0',
            'tanggal_beli'      => 'required|date',
            'harga_jual_satuan' => 'nullable|numeric|min:0',
            'total_jual'        => 'nullable|numeric|min:0',
            'tanggal_jual'      => 'nullable|date|after_or_equal:tanggal_beli',
            'catatan'           => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_instrumen.required'     => 'Nama instrumen investasi wajib diisi.',
            'total_beli.required_without' => 'Total beli wajib diisi jika harga beli satuan tidak dimasukkan.',
            'tanggal_beli.required'       => 'Tanggal beli wajib diisi.',
            'tanggal_jual.after_or_equal' => 'Tanggal jual tidak boleh lebih awal dari tanggal beli.',
        ];
    }
}
