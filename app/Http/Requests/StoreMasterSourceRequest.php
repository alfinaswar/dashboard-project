<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return ['nama_sumber' => ['required', 'string', 'max:100', 'unique:sumber_order,nama_sumber'], 'persentase_potongan_default' => ['required', 'numeric', 'between:0,100'], 'apakah_platform' => ['required', 'boolean'], 'aktif' => ['required', 'boolean']];
    }
}
