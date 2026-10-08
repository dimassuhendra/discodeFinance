<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvestasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mode_input' => 'nullable|in:satuan,total',
            'nama_instrumen' => 'required|string|max:255',
            'jenis_instrumen' => 'nullable|string|max:100',
            'platform' => 'nullable|string|max:100',
            'tanggal_beli' => 'required|date',
            'tanggal_jual' => 'nullable|date|after_or_equal:tanggal_beli',

            'id_pengeluaran' => 'nullable|exists:pengeluaran,id',

            'jumlah_unit' => 'nullable|numeric|min:0',
            'harga_beli_satuan' => 'nullable|numeric|min:0',
            'harga_jual_satuan' => 'nullable|numeric|min:0',
            'total_beli' => 'nullable|numeric|min:0',
            'total_jual' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_instrumen.required' => 'Nama instrumen investasi wajib diisi.',
            'tanggal_beli.required' => 'Tanggal beli wajib diisi.',
            'tanggal_jual.after_or_equal' => 'Tanggal jual tidak boleh sebelum tanggal beli.',
        ];
    }
}
