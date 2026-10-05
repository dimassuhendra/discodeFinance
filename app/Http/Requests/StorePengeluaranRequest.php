<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePengeluaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal'          => 'required|date',
            'nama_pengeluaran' => 'required|string|max:255',
            'jumlah'           => 'required|numeric|min:1',
            'sumber_dana_id'   => 'required|exists:sumber_dana_pengeluaran,id',
        ];
    }
}