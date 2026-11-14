<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModalProyek extends Model
{
    use SoftDeletes;

    protected $table = 'modal_proyek';

    protected $guarded = ['id'];

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(MasterKategoriModal::class, 'kategori_modal_id');
    }
}
