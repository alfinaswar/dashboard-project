<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilFreelancerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'nomor_ktp' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
            'email' => ['required', 'email', 'max:150'],
            'nomor_whatsapp' => ['required', 'string', 'max:30'],
            'nama_bank' => ['nullable', 'string', 'max:100'],
            'nomor_rekening' => ['nullable', 'string', 'max:50'],
            'atas_nama_rekening' => ['nullable', 'string', 'max:150'],
            'tanda_tangan' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ];
    }
}
