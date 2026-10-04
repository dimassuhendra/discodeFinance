<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuickPengeluaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'nama_pengeluaran' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'numeric', 'min:1'],
            'sumber_dana_id' => ['required', 'exists:sumber_dana_pengeluaran,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal transaksi wajib diisi.',
            'nama_pengeluaran.required' => 'Nama pengeluaran wajib diisi.',
            'jumlah.required' => 'Nominal pengeluaran wajib diisi.',
            'sumber_dana_id.required' => 'Pilih sumber dana yang sesuai.',
        ];
    }
}