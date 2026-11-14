<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return ['nama_kategori' => ['required', 'string', 'max:100', 'unique:master_kategori_modal,nama_kategori'], 'keterangan' => ['nullable', 'string']];
    }
}
