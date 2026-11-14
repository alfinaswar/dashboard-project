<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePembayaranProyekRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'tahapan_pembayaran' => ['required', 'string', 'max:100'],
            'jumlah_bayar' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'tanggal_bayar' => ['required', 'date'],
            'metode_pembayaran' => ['nullable', 'string', 'max:100'],
            'bukti_transfer' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
