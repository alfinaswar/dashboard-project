<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterKategoriModal extends Model
{
    use SoftDeletes;

    protected $table = 'master_kategori_modal';

    protected $guarded = ['id'];

    public function modalProyek(): HasMany
    {
        return $this->hasMany(ModalProyek::class, 'kategori_modal_id');
    }
}
