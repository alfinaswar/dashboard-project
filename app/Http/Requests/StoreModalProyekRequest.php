<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModalProyekRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'kategori_modal_id' => ['required', 'exists:master_kategori_modal,id'],
            'nama_item' => ['required', 'string', 'max:200'],
            'jumlah_biaya' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'tanggal_pengeluaran' => ['required', 'date'],
            'bukti_nota' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'catatan' => ['nullable', 'string'],
        ];
    }
}
