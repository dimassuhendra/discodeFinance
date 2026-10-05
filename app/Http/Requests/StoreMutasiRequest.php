<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMutasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal'              => 'required|date',
            'jumlah'               => 'required|numeric|min:1',
            'dari_sumber_dana_id'  => 'required|exists:sumber_dana_pengeluaran,id',
            'ke_sumber_dana_id'    => 'required|exists:sumber_dana_pengeluaran,id|different:dari_sumber_dana_id',
            'keterangan'           => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'ke_sumber_dana_id.different' => 'Sumber dana tujuan tidak boleh sama dengan sumber dana asal.',
        ];
    }
}
