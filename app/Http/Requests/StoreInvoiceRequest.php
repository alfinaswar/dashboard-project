<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'nomor_invoice' => ['required', 'string', 'max:100', 'unique:invoice_proyek,nomor_invoice'],
            'tanggal_terbit' => ['required', 'date'],
            'tanggal_jatuh_tempo' => ['nullable', 'date', 'after_or_equal:tanggal_terbit'],
            'nominal_tagihan' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'file_invoice' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'catatan' => ['nullable', 'string'],
        ];
    }
}
