<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProyekRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'klien_id' => ['required', 'exists:klien,id'],
            'sumber_order_id' => ['required', 'exists:sumber_order,id'],
            'status_id' => ['required', 'exists:master_status_proyek,id'],
            'judul_proyek' => ['required', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string'],
            'tech_stack' => ['nullable', 'string', 'max:255'],
            'url_produksi' => ['nullable', 'url', 'max:255'],
            'url_repository' => ['nullable', 'url', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_deadline' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'mata_uang' => ['required', 'string', 'size:3'],
            'nilai_kotor' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'persentase_potongan_platform' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
