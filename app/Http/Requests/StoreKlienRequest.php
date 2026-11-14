<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKlienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'nama_klien' => ['required', 'string', 'max:150'],
            'nama_perusahaan' => ['nullable', 'string', 'max:150'],
            'nomor_whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'alamat' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
        ];
    }
}
